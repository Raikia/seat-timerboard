<?php

namespace Raikia\SeatTimerboard\Http\Controllers;

use Seat\Web\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
        $request->validate([
            'system' => 'required|string',
            'structure_type' => 'required|string',
            'structure_name' => 'nullable|string',
            'owner_corporation' => 'required|string',
            'attacker_corporation' => 'nullable|string',
            'time_input' => 'required|string',
            'tags' => 'nullable|array',
            'tags.*' => 'integer|exists:seat_timerboard_tags,id',
            'role_id' => 'nullable|integer|exists:roles,id',
        ]);

        $eveTime = $this->parseTimeInput($request->input('time_input'));

        if (!$eveTime) {
            return redirect()->back()->withErrors(['time_input' => 'Invalid time format. Use YYYY.MM.DD HH:MM or YYYY.MM.DD HH:MM:SS, or "X days Y hours".'])->withInput();
        }

        $this->persistTimer(new Timer(), $request->only([
            'system',
            'structure_type',
            'structure_name',
            'owner_corporation',
            'attacker_corporation',
            'time_input',
            'tags',
            'role_id',
        ]), $eveTime);

        return redirect()->route('timerboard.index')->with('success', 'Timer created successfully.');
    }

    public function storeMany(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'timers' => 'required|array|min:1',
            'timers.*.system' => 'required|string',
            'timers.*.structure_type' => 'required|string',
            'timers.*.structure_name' => 'nullable|string',
            'timers.*.owner_corporation' => 'required|string',
            'timers.*.attacker_corporation' => 'nullable|string',
            'timers.*.time_input' => 'required|string',
            'timers.*.tags' => 'nullable|array',
            'timers.*.tags.*' => 'integer|exists:seat_timerboard_tags,id',
            'timers.*.role_id' => 'nullable|integer|exists:roles,id',
        ]);

        $validator->after(function ($validator) use ($request) {
            foreach ($request->input('timers', []) as $index => $timerData) {
                $eveTime = $this->parseTimeInput($timerData['time_input'] ?? null);

                if (!$eveTime) {
                    $validator->errors()->add(
                        "timers.$index.time_input",
                        'Timer #' . ($index + 1) . ' has an invalid time format. Use YYYY.MM.DD HH:MM or YYYY.MM.DD HH:MM:SS, or "X days Y hours".'
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
        $query = $request->input('q');

        if (strlen($query) < 3) {
            return response()->json([]);
        }

        // groupIDs: 5 = Solar System, 7 = Planet, 8 = Moon
        $results = \Seat\Eveapi\Models\Sde\MapDenormalize::where('itemName', 'like', "%$query%")
            ->whereIn('groupID', [5, 7, 8])
            ->select('itemID', 'itemName', 'typeID', 'solarSystemID', 'groupID')
            ->with('type')
            ->limit(20)
            ->get();

        $formatted = $results->map(function ($item) {
            $type = $item->type ? $item->type->typeName : 'Unknown';
            $system = $item->solarSystem ? $item->solarSystem->solarSystemName : '';
            
            $text = $item->itemName;
            if ($item->groupID != 5) { // Not a Solar System
                $text .= " ($type)";
                if ($system && $system !== $item->itemName) {
                     $text .= " - $system";
                }
            } else {
                $text .= " (System)";
            }

            return [
                'id' => $item->itemName,
                'text' => $text
            ];
        });

        return response()->json(['results' => $formatted]);
    }

    public function searchCorporations(Request $request)
    {
        $query = $request->input('q');

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
                return response()->json(['results' => []]);
            }

            // Limit to 20 results total
            $ids = array_slice($ids, 0, 20);

            // Resolve IDs to Names (Public endpoint)
            $namesResponse = $esiClient->setBody($ids)->invoke('post', '/universe/names/');

            $formatted = collect($namesResponse->getBody())->map(function ($item) {
                return [
                    'id' => $item->name,
                    'text' => $item->name . ' (' . ucfirst($item->category) . ')'
                ];
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

        $request->validate([
            'system' => 'required|string',
            'structure_type' => 'required|string',
            'structure_name' => 'nullable|string',
            'owner_corporation' => 'required|string',
            'attacker_corporation' => 'nullable|string',
            'time_input' => 'required|string',
            'tags' => 'nullable|array',
            'tags.*' => 'integer|exists:seat_timerboard_tags,id',
            'role_id' => 'nullable|integer|exists:roles,id',
        ]);

        $eveTime = $this->parseTimeInput($request->input('time_input'));

        if (!$eveTime) {
            return redirect()->back()->withErrors(['time_input' => 'Invalid time format. Use YYYY.MM.DD HH:MM or YYYY.MM.DD HH:MM:SS, or "X days Y hours".'])->withInput();
        }

        $this->persistTimer($timer, $request->only([
            'system',
            'structure_type',
            'structure_name',
            'owner_corporation',
            'attacker_corporation',
            'time_input',
            'tags',
            'role_id',
        ]), $eveTime);

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
}
