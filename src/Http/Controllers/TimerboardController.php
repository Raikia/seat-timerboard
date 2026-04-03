<?php

namespace Raikia\SeatTimerboard\Http\Controllers;

use Seat\Web\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Raikia\SeatTimerboard\Models\Timer;
use Raikia\SeatTimerboard\Models\Tag;
use Carbon\Carbon;
use Seat\Eveapi\Models\RefreshToken;
use Seat\Eveapi\Services\EseyeClient;

class TimerboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $user = auth()->user();
        $userRoles = $user->roles->pluck('id');
        
        $query = Timer::with('tags', 'user', 'mapDenormalize.region', 'mapDenormalize.system', 'role');

        if (!$user->isAdmin()) {
             $query->where(function ($q) use ($userRoles) {
                $q->whereNull('role_id')
                      ->orWhereIn('role_id', $userRoles);
            });
        }
            
        $allTimers = $query->orderBy('eve_time', 'asc')->get();

        $currentTimers = $allTimers->filter(function ($timer) use ($now) {
            // Keep timers in "Current" if they are in the future OR elapsed less than 2 hours ago
            return $timer->eve_time >= $now->copy()->subHours(2);
        });

        $elapsedTimers = $allTimers->filter(function ($timer) use ($now) {
            // "Elapsed" only contains timers older than 2 hours
            return $timer->eve_time < $now->copy()->subHours(2);
        });

        $tags = Tag::orderBy('name')->get();
        $roles = \Seat\Web\Models\Acl\Role::all();
        $defaultRole = \Raikia\SeatTimerboard\Models\TimerboardSetting::find('default_timer_role');
        $defaultRoleId = $defaultRole ? $defaultRole->value : null;
        $structureTypes = $this->getStructureTypes();

        return view('seat-timerboard::index', compact('currentTimers', 'elapsedTimers', 'tags', 'roles', 'defaultRoleId', 'structureTypes'));
    }



    public function store(Request $request)
    {
        $request->validate($this->timerRules());

        $eveTime = $this->parseTimeInput($request->input('time_input'));

        if (!$eveTime) {
            return $this->invalidTimeRedirect();
        }

        $this->persistTimer(new Timer(), $this->extractTimerData($request->all()), $eveTime);

        return redirect()->route('timerboard.index')->with('success', 'Timer created successfully.');
    }

    public function storeMany(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'timers' => 'required|array|min:1',
        ] + $this->timerRules('timers.*.'));

        $validator->after(function ($validator) use ($request) {
            foreach ($request->input('timers', []) as $index => $timerData) {
                $eveTime = $this->parseTimeInput($timerData['time_input'] ?? null);

                if (!$eveTime) {
                    $validator->errors()->add(
                        "timers.$index.time_input",
                        $this->timeInputErrorMessage($index + 1)
                    );
                }
            }
        });

        $validated = $validator->validate();
        $createdCount = 0;

        DB::transaction(function () use ($validated, &$createdCount) {
            foreach ($validated['timers'] as $timerData) {
                $eveTime = $this->parseTimeInput($timerData['time_input']);
                $this->persistTimer(new Timer(), $timerData, $eveTime);
                $createdCount++;
            }
        });

        $message = $createdCount === 1
            ? '1 timer created successfully.'
            : $createdCount . ' timers created successfully.';

        return redirect()->route('timerboard.index')->with('success', $message);
    }

    private function parseTimeInput($input)
    {
        if (!is_string($input)) {
            return null;
        }

        $input = trim($input);

        if ($input === '') {
            return null;
        }

        // Try absolute formats "YYYY.MM.DD HH:MM" and "YYYY.MM.DD HH:MM:SS"
        foreach (['Y.m.d H:i:s', 'Y.m.d H:i'] as $format) {
            try {
                $date = Carbon::createFromFormat($format, $input, 'UTC');

                if ($date && $date->format($format) === $input) {
                    return $date;
                }
            } catch (\Exception $e) {
                // Continue to the next format.
            }
        }

        $now = Carbon::now('UTC');
        $matches = [];

        preg_match_all('/(\d+)\s*(d(?:ays?)?|h(?:ours?)?|m(?:in(?:ute)?s?)?)/i', $input, $matches, PREG_SET_ORDER);

        if (empty($matches)) {
            return null;
        }

        $remaining = preg_replace('/(\d+)\s*(d(?:ays?)?|h(?:ours?)?|m(?:in(?:ute)?s?)?)/i', '', $input);

        if (preg_match('/[^\s,]/', $remaining)) {
            return null;
        }

        foreach ($matches as $match) {
            $value = (int) $match[1];
            $unit = strtolower($match[2]);

            if (strpos($unit, 'd') === 0) {
                $now->addDays($value);
                continue;
            }

            if (strpos($unit, 'h') === 0) {
                $now->addHours($value);
                continue;
            }

            if (strpos($unit, 'm') === 0) {
                $now->addMinutes($value);
            }
        }

        return $now;
    }

    public function searchSystems(Request $request)
    {
        $query = trim((string) $request->input('q', ''));

        if (strlen($query) < 3) {
            return response()->json(['results' => []]);
        }

        $escapedQuery = $this->escapeLike($query);

        // groupIDs: 5 = Solar System, 7 = Planet, 8 = Moon
        $results = \Seat\Eveapi\Models\Sde\MapDenormalize::where('itemName', 'like', '%' . $escapedQuery . '%')
            ->whereIn('groupID', [5, 7, 8])
            ->select('itemID', 'itemName', 'typeID', 'solarSystemID', 'groupID')
            ->orderByRaw('CASE WHEN itemName LIKE ? THEN 0 ELSE 1 END', [$escapedQuery . '%'])
            ->orderBy('itemName')
            ->limit(20)
            ->get();

        $formatted = $results->map(function ($item) {
            return [
                'id' => $item->itemName,
                'text' => $item->itemName,
            ];
        });

        return response()->json(['results' => $formatted]);
    }

    public function searchCorporations(Request $request)
    {
        $query = trim((string) $request->input('q', ''));

        if (strlen($query) < 3) {
             return response()->json(['results' => []]);
        }

        try {
            $user = auth()->user();
            if (!$user || !$user->main_character_id) {
                return response()->json(['results' => []]);
            }

            $characterId = $user->main_character_id;
            $refreshToken = RefreshToken::find($characterId);

            if (!$refreshToken) {
                 return response()->json(['results' => []]);
            }

            $searchCacheKey = sprintf(
                'seat_timerboard:entity_search:v1:%s:%s',
                $characterId,
                sha1(strtolower($query))
            );

            $formatted = Cache::remember($searchCacheKey, now()->addSeconds(90), function () use ($characterId, $query, $refreshToken) {
                $esiClient = new EseyeClient();
                $esiClient->setAuthentication($refreshToken);

                $searchResponse = $esiClient->setQueryString([
                    'categories' => ['corporation', 'alliance'],
                    'search' => $query,
                ])->invoke('get', '/characters/{character_id}/search/', [
                    'character_id' => $characterId,
                ]);

                $ids = [];
                if (isset($searchResponse->getBody()->corporation)) {
                    $ids = array_merge($ids, $searchResponse->getBody()->corporation);
                }
                if (isset($searchResponse->getBody()->alliance)) {
                    $ids = array_merge($ids, $searchResponse->getBody()->alliance);
                }

                if (empty($ids)) {
                    return [];
                }

                $ids = array_values(array_unique($ids));
                $ids = array_slice($ids, 0, 100);

                return collect($this->resolveUniverseNames($esiClient, $ids))
                    ->map(function ($item) {
                        return [
                            'id' => $item['name'],
                            'text' => $item['name'] . ' (' . ucfirst($item['category']) . ')',
                            'name' => $item['name'],
                        ];
                    })
                    ->sort(function ($left, $right) use ($query) {
                        return $this->compareSearchLabels($left['name'], $right['name'], $query);
                    })
                    ->take(20)
                    ->values()
                    ->map(function ($item) {
                        unset($item['name']);

                        return $item;
                    })
                    ->all();
            });

            return response()->json(['results' => $formatted]);

        } catch (\Exception $e) {
            \Log::error('Entity Search Error: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return response()->json(['results' => []]);
        }
    }



    public function update(Request $request, $id)
    {
        $timer = Timer::findOrFail($id);

        $request->validate($this->timerRules());

        $eveTime = $this->parseTimeInput($request->input('time_input'));

        if (!$eveTime) {
            return $this->invalidTimeRedirect();
        }

        $this->persistTimer($timer, $this->extractTimerData($request->all()), $eveTime);

        return redirect()->route('timerboard.index')->with('success', 'Timer updated successfully.');
    }

    public function destroy($id)
    {
        $timer = Timer::findOrFail($id);
        $timer->delete();

        return redirect()->route('timerboard.index')->with('success', 'Timer deleted successfully.');
    }

    public function destroyElapsed()
    {
        Timer::where('eve_time', '<', Carbon::now()->subHours(2))->delete();

        return redirect()->route('timerboard.settings')->with('success', 'All elapsed timers deleted successfully.');
    }

    public function truncate()
    {
        Timer::query()->delete();

        return redirect()->route('timerboard.settings')->with('success', 'All timers have been deleted.');
    }

    private function persistTimer(Timer $timer, array $data, Carbon $eveTime): void
    {
        $timer->fill([
            'system' => $data['system'],
            'structure_type' => $data['structure_type'],
            'structure_name' => $data['structure_name'] ?? null,
            'notes' => $this->normalizeNotes($data['notes'] ?? null),
            'owner_corporation' => $data['owner_corporation'],
            'attacker_corporation' => $data['attacker_corporation'] ?? null,
            'role_id' => $data['role_id'] ?? null,
        ]);

        if (!$timer->exists) {
            $timer->user_id = auth()->id();
        }

        $timer->eve_time = $eveTime;
        $timer->save();

        $timer->tags()->sync($data['tags'] ?? []);
    }

    private function timerRules(string $prefix = ''): array
    {
        return [
            $prefix . 'system' => 'required|string',
            $prefix . 'structure_type' => 'required|string',
            $prefix . 'structure_name' => 'nullable|string',
            $prefix . 'notes' => 'nullable|string|max:20000',
            $prefix . 'owner_corporation' => 'required|string',
            $prefix . 'attacker_corporation' => 'nullable|string',
            $prefix . 'time_input' => 'required|string',
            $prefix . 'tags' => 'nullable|array',
            $prefix . 'tags.*' => 'integer|exists:seat_timerboard_tags,id',
            $prefix . 'role_id' => 'nullable|integer|exists:roles,id',
        ];
    }

    private function extractTimerData(array $data): array
    {
        return [
            'system' => $data['system'],
            'structure_type' => $data['structure_type'],
            'structure_name' => $data['structure_name'] ?? null,
            'notes' => $data['notes'] ?? null,
            'owner_corporation' => $data['owner_corporation'],
            'attacker_corporation' => $data['attacker_corporation'] ?? null,
            'time_input' => $data['time_input'],
            'tags' => $data['tags'] ?? [],
            'role_id' => $data['role_id'] ?? null,
        ];
    }

    private function invalidTimeRedirect(string $field = 'time_input', ?int $timerNumber = null)
    {
        return redirect()->back()->withErrors([
            $field => $this->timeInputErrorMessage($timerNumber),
        ])->withInput();
    }

    private function timeInputErrorMessage(?int $timerNumber = null): string
    {
        $prefix = $timerNumber ? 'Timer #' . $timerNumber . ' has an invalid time format.' : 'Invalid time format.';

        return $prefix . ' Use YYYY.MM.DD HH:MM or YYYY.MM.DD HH:MM:SS, or "X days Y hours".';
    }

    private function getStructureTypes(): array
    {
        return [
            'Ansiblex' => 'Ansiblex Jump Gate',
            'Astrahus' => 'Astrahus',
            'Athanor' => 'Athanor',
            'Azbel' => 'Azbel',
            'POCO' => 'Customs Office',
            'Fortizar' => 'Fortizar',
            'Keepstar' => 'Keepstar',
            'Metenox' => 'Metenox Moon Drill',
            'Pharolux' => 'Pharolux Cyno Beacon',
            'POS' => 'POS',
            'Raitaru' => 'Raitaru',
            'Skyhook' => 'Skyhook',
            'Sotiyo' => 'Sotiyo',
            'Tatara' => 'Tatara',
            'Tenebrex' => 'Tenebrex Jammer',
            'Other' => 'Other',
        ];
    }

    private function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }

    private function resolveUniverseNames(EseyeClient $esiClient, array $ids): array
    {
        sort($ids);

        $cacheKey = 'seat_timerboard:universe_names:v1:' . sha1(implode(',', $ids));

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($esiClient, $ids) {
            $namesResponse = $esiClient->setBody($ids)->invoke('post', '/universe/names/');

            return collect($namesResponse->getBody())->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'category' => $item->category,
                ];
            })->all();
        });
    }

    private function compareSearchLabels(string $left, string $right, string $query): int
    {
        $leftScore = $this->searchMatchScore($left, $query);
        $rightScore = $this->searchMatchScore($right, $query);

        if ($leftScore !== $rightScore) {
            return $leftScore <=> $rightScore;
        }

        return strcasecmp($left, $right);
    }

    private function searchMatchScore(string $value, string $query): int
    {
        $value = strtolower($value);
        $query = strtolower($query);

        if ($value === $query) {
            return 0;
        }

        if (strpos($value, $query) === 0) {
            return 1;
        }

        return 2;
    }

    private function normalizeNotes($value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $notes = trim($value);

        return $notes === '' ? null : $notes;
    }
}
