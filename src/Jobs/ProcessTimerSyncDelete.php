<?php

namespace Raikia\SeatTimerboard\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Raikia\SeatTimerboard\Services\TimerSyncOutboundService;

class ProcessTimerSyncDelete implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private int $deliveryId,
        private int $localTimerId
    ) {
    }

    public function handle(TimerSyncOutboundService $syncOutboundService): void
    {
        $syncOutboundService->deleteDeliveryFromPeer($this->deliveryId, $this->localTimerId);
    }
}
