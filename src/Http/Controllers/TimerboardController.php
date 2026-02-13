<?php

namespace Raikia\SeatTimerboard\Http\Controllers;

use Seat\Web\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Raikia\SeatTimerboard\Models\Timer;
use Raikia\SeatTimerboard\Models\Tag;
use Carbon\Carbon;
use Seat\Eveapi\Models\RefreshToken;
use Seat\Eveapi\Services\EseyeClient;

class TimerboardController extends Controller
{
    public function index()
    {
        $timers = Timer::with('tags', 'user', 'mapDenormalize.region')->orderBy('eve_time', 'asc')->get();
        return view('seat-timerboard::index', compact('timers'));
    }

    public function create()
    {
        $tags = Tag::all();
        return view('seat-timerboard::create', compact('tags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'system' => 'required|string',
            'structure_type' => 'required|string',
            'structure_name' => 'required|string',
            'owner_corporation' => 'required|string',
            'attacker_corporation' => 'nullable|string',
            'time_input' => 'required|string',
            'tags' => 'array',
        ]);

        $eveTime = $this->parseTimeInput($request->input('time_input'));

        if (!$eveTime) {
            return redirect()->back()->withErrors(['time_input' => 'Invalid time format. Use YYYY.MM.DD HH:MM:SS or "X days Y hours".'])->withInput();
        }

        $timer = new Timer([
            'system' => $request->input('system'),
            'structure_type' => $request->input('structure_type'),
            'structure_name' => $request->input('structure_name'),
            'owner_corporation' => $request->input('owner_corporation'),
            'attacker_corporation' => $request->input('attacker_corporation'),
            'user_id' => auth()->id(),
        ]);

        $timer->eve_time = $eveTime;
        $timer->save();

        if ($request->has('tags')) {
            $timer->tags()->sync($request->input('tags'));
        }

        return redirect()->route('timerboard.index')->with('success', 'Timer created successfully.');
    }

    private function parseTimeInput($input)
    {
        // Try absolute format "YYYY.MM.DD HH:MM:SS"
        try {
            return Carbon::createFromFormat('Y.m.d H:i:s', $input, 'UTC');
        } catch (\Exception $e) {
            // Continue to relative parsing
        }

        // Try relative format "2 days, 13 minutes"
        // Simple regex for days, hours, minutes
        $now = Carbon::now('UTC');
        
        if (preg_match('/(\d+)\s*d(ays?)?/', $input, $matches)) {
            $now->addDays((int)$matches[1]);
        }
        if (preg_match('/(\d+)\s*h(ours?)?/', $input, $matches)) {
            $now->addHours((int)$matches[1]);
        }
        if (preg_match('/(\d+)\s*m(in(utes?)?)?/', $input, $matches)) {
            $now->addMinutes((int)$matches[1]);
        }
        
        // If we added nothing, and checking if regex matched anything at all is tricky without a flag.
        // But if the input wasn't absolute and we are here, we verify if it looks like relative.
        // A better check might be needed, but for now assuming if it has digits it's relative.
        if (preg_match('/\d/', $input)) {
             return $now;
        }

        return null;
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

    public function edit($id)
    {
        $timer = Timer::with('tags')->findOrFail($id);
        $tags = Tag::all();
        return view('seat-timerboard::edit', compact('timer', 'tags'));
    }

    public function update(Request $request, $id)
    {
        $timer = Timer::findOrFail($id);

        $request->validate([
            'system' => 'required|string',
            'structure_type' => 'required|string',
            'structure_name' => 'required|string',
            'owner_corporation' => 'required|string',
            'attacker_corporation' => 'nullable|string',
            'time_input' => 'required|string',
            'tags' => 'array',
        ]);

        $eveTime = $this->parseTimeInput($request->input('time_input'));

        if (!$eveTime) {
            return redirect()->back()->withErrors(['time_input' => 'Invalid time format. Use YYYY.MM.DD HH:MM:SS or "X days Y hours".'])->withInput();
        }

        $timer->update([
            'system' => $request->input('system'),
            'structure_type' => $request->input('structure_type'),
            'structure_name' => $request->input('structure_name'),
            'owner_corporation' => $request->input('owner_corporation'),
            'attacker_corporation' => $request->input('attacker_corporation'),
        ]);

        $timer->eve_time = $eveTime;
        $timer->save();

        if ($request->has('tags')) {
            $timer->tags()->sync($request->input('tags'));
        } else {
            $timer->tags()->detach();
        }

        return redirect()->route('timerboard.index')->with('success', 'Timer updated successfully.');
    }

    public function destroy($id)
    {
        $timer = Timer::findOrFail($id);
        $timer->delete();

        return redirect()->route('timerboard.index')->with('success', 'Timer deleted successfully.');
    }
}
