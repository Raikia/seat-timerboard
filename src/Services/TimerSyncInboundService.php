<?php

namespace Raikia\SeatTimerboard\Services;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Raikia\SeatTimerboard\Models\Tag;
use Raikia\SeatTimerboard\Models\Timer;
use Raikia\SeatTimerboard\Models\TimerSyncPeer;

class TimerSyncInboundService
{
    public function upsertFromPeer(TimerSyncPeer $peer, array $payload): array
    {
        $originInstanceUuid = (string) $payload['source_instance_uuid'];
        $originTimerId = (int) $payload['origin_timer_id'];
        $timerData = $payload['timer'];

        $timer = Timer::where('sync_origin_instance_uuid', $originInstanceUuid)
            ->where('sync_origin_timer_id', $originTimerId)
            ->first();

        $isNew = ! $timer;
        $timer = $timer ?: new Timer();

        $timer->fill([
            'system' => (string) $timerData['system'],
            'structure_type' => (string) $timerData['structure_type'],
            'structure_name' => $timerData['structure_name'] ?? null,
            'notes' => $this->buildRemoteNotes($peer, $payload, $timerData['notes'] ?? null),
            'owner_corporation' => (string) $timerData['owner_corporation'],
            'attacker_corporation' => $timerData['attacker_corporation'] ?? null,
            'role_id' => $peer->incoming_role_id,
            'sync_origin_instance_uuid' => $originInstanceUuid,
            'sync_origin_timer_id' => $originTimerId,
            'sync_source_name' => $peer->name,
        ]);
        $timer->user_id = null;
        $timer->eve_time = Carbon::parse($timerData['eve_time'])->setTimezone('UTC');
        $timer->save();

        $timer->tags()->sync($this->resolveTagIds(Arr::get($timerData, 'tags', [])));

        return [$timer, $isNew];
    }

    public function deleteFromPeer(TimerSyncPeer $peer, string $originInstanceUuid, int $originTimerId): bool
    {
        if (! $peer->allow_remote_delete) {
            return false;
        }

        $timer = Timer::where('sync_origin_instance_uuid', $originInstanceUuid)
            ->where('sync_origin_timer_id', $originTimerId)
            ->first();

        if (! $timer) {
            return false;
        }

        $timer->delete();

        return true;
    }

    private function resolveTagIds(array $tags): array
    {
        $resolved = collect($tags)
            ->map(function ($tag) {
                $name = trim((string) Arr::get($tag, 'name', ''));

                if ($name === '') {
                    return null;
                }

                $existing = Tag::query()
                    ->where('name', $name)
                    ->orderBy('id')
                    ->first();

                if ($existing) {
                    return $existing->id;
                }

                return Tag::create([
                    'name' => $name,
                    'color' => Arr::get($tag, 'color', Tag::defaultColorFor($name)),
                ])->id;
            })
            ->filter()
            ->values();

        $remoteSyncedTag = Tag::query()
            ->where('name', 'Remote Synced')
            ->orderBy('id')
            ->first();

        if (! $remoteSyncedTag) {
            $remoteSyncedTag = Tag::create([
                'name' => 'Remote Synced',
                'color' => Tag::defaultColorFor('Remote Synced'),
            ]);
        }

        return $resolved
            ->push($remoteSyncedTag->id)
            ->unique()
            ->values()
            ->all();
    }

    private function buildRemoteNotes(TimerSyncPeer $peer, array $payload, ?string $notes): string
    {
        $lines = array_filter([
            'Synced from remote SeAT: ' . $peer->name,
            'Origin instance: ' . ($payload['source_instance_name'] ?? $payload['source_instance_uuid']),
            'Origin timer ID: ' . $payload['origin_timer_id'],
            $notes,
        ]);

        return implode("\n", $lines);
    }
}
