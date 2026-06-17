<?php

namespace Raikia\SeatTimerboard\Services;

use Raikia\SeatTimerboard\Models\Timer;

class TimerSyncPayloadFactory
{
    public function __construct(
        private TimerboardInstanceIdentity $identity
    ) {
    }

    public function build(Timer $timer): array
    {
        $timer->loadMissing('tags');

        return [
            'source_instance_uuid' => $this->identity->getUuid(),
            'source_instance_name' => $this->identity->getName(),
            'origin_timer_id' => (int) $timer->id,
            'timer' => [
                'system' => $timer->system,
                'structure_type' => $timer->structure_type,
                'structure_name' => $timer->structure_name,
                'notes' => $timer->notes,
                'owner_corporation' => $timer->owner_corporation,
                'attacker_corporation' => $timer->attacker_corporation,
                'eve_time' => $timer->eve_time->copy()->setTimezone('UTC')->toIso8601String(),
                'tags' => $timer->tags->map(function ($tag) {
                    return [
                        'name' => $tag->name,
                        'color' => $tag->color,
                    ];
                })->values()->all(),
            ],
        ];
    }
}
