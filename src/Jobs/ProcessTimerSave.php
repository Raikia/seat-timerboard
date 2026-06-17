<?php

namespace Raikia\SeatTimerboard\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Raikia\SeatTimerboard\Models\Timer;
use Raikia\SeatTimerboard\Services\TimerNotificationService;
use Raikia\SeatTimerboard\Services\TimerSyncOutboundService;

class ProcessTimerSave implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private int $timerId,
        private bool $isNew
    ) {
    }

    public function handle(
        TimerNotificationService $notificationService,
        TimerSyncOutboundService $syncOutboundService
    ): void {
        $timer = Timer::with(['tags', 'user', 'role', 'mapDenormalize.region', 'mapDenormalize.system'])
            ->find($this->timerId);

        if (! $timer) {
            return;
        }

        if ($this->isNew) {
            $notificationService->sendNewTimer($timer);
        }

        $syncOutboundService->syncTimer($timer);
    }
}
