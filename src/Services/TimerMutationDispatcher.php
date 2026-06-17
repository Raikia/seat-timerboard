<?php

namespace Raikia\SeatTimerboard\Services;

use Raikia\SeatTimerboard\Jobs\ProcessTimerDelete;
use Raikia\SeatTimerboard\Jobs\ProcessTimerSave;
use Raikia\SeatTimerboard\Models\Timer;

class TimerMutationDispatcher
{
    public function dispatchSaved(Timer $timer, bool $isNew): void
    {
        ProcessTimerSave::dispatch($timer->id, $isNew)->afterCommit();
    }

    public function dispatchDeleted(array $snapshot): void
    {
        ProcessTimerDelete::dispatch($snapshot)->afterCommit();
    }

    public function deletionSnapshot(Timer $timer): array
    {
        return [
            'id' => (int) $timer->id,
            'is_remote_synced' => $timer->isRemoteSynced(),
        ];
    }
}
