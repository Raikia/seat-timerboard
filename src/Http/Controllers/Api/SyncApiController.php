<?php

namespace Raikia\SeatTimerboard\Http\Controllers\Api;

use Illuminate\Http\Request;
use Raikia\SeatTimerboard\Models\TimerSyncPeer;
use Raikia\SeatTimerboard\Services\TimerMutationDispatcher;
use Raikia\SeatTimerboard\Services\TimerSyncInboundService;
use Raikia\SeatTimerboard\Services\TimerboardInstanceIdentity;
use Seat\Web\Http\Controllers\Controller;

class SyncApiController extends Controller
{
    public function info(TimerboardInstanceIdentity $identity)
    {
        return response()->json([
            'instance_uuid' => $identity->getUuid(),
            'instance_name' => $identity->getName(),
            'base_url' => $identity->getBaseUrl(),
        ]);
    }

    public function upsert(Request $request, TimerSyncInboundService $syncInboundService, TimerMutationDispatcher $dispatcher)
    {
        $payload = $request->validate([
            'source_instance_uuid' => 'required|uuid',
            'source_instance_name' => 'required|string|max:255',
            'origin_timer_id' => 'required|integer|min:1',
            'timer' => 'required|array',
            'timer.system' => 'required|string|max:255',
            'timer.structure_type' => 'required|string|max:255',
            'timer.structure_name' => 'nullable|string|max:255',
            'timer.notes' => 'nullable|string|max:20000',
            'timer.owner_corporation' => 'required|string|max:255',
            'timer.attacker_corporation' => 'nullable|string|max:255',
            'timer.eve_time' => 'required|date',
            'timer.tags' => 'nullable|array',
            'timer.tags.*.name' => 'required|string|max:255',
            'timer.tags.*.color' => 'nullable|string|max:7',
        ]);

        $peer = $this->resolvePeer($payload['source_instance_uuid']);

        if (! $peer) {
            return response()->json(['message' => 'Unknown or disabled sync peer.'], 403);
        }

        [$timer, $isNew] = $syncInboundService->upsertFromPeer($peer, $payload);
        $dispatcher->dispatchSaved($timer, $isNew);

        return response()->json([
            'status' => $isNew ? 'created' : 'updated',
            'timer_id' => $timer->id,
        ], $isNew ? 201 : 200);
    }

    public function delete(Request $request, TimerSyncInboundService $syncInboundService)
    {
        $payload = $request->validate([
            'source_instance_uuid' => 'required|uuid',
            'origin_timer_id' => 'required|integer|min:1',
        ]);

        $peer = $this->resolvePeer($payload['source_instance_uuid']);

        if (! $peer) {
            return response()->json(['message' => 'Unknown or disabled sync peer.'], 403);
        }

        if (! $peer->allow_remote_delete) {
            return response()->json(['status' => 'ignored'], 202);
        }

        $deleted = $syncInboundService->deleteFromPeer(
            $peer,
            $payload['source_instance_uuid'],
            (int) $payload['origin_timer_id']
        );

        return response()->json([
            'status' => $deleted ? 'deleted' : 'not_found',
        ]);
    }

    private function resolvePeer(string $instanceUuid): ?TimerSyncPeer
    {
        return TimerSyncPeer::query()
            ->where('instance_uuid', $instanceUuid)
            ->where('is_enabled', true)
            ->first();
    }
}
