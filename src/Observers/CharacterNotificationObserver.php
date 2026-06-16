<?php

namespace Raikia\SeatTimerboard\Observers;

use Illuminate\Support\Facades\Log;
use Raikia\SeatTimerboard\Services\NotificationTimerImporter;
use Seat\Eveapi\Models\Character\CharacterNotification;

class CharacterNotificationObserver
{
    /**
     * Handle the CharacterNotification "created" event.
     */
    public function created(CharacterNotification $notification): void
    {
        try {
            app(NotificationTimerImporter::class)->import($notification);
        } catch (\Throwable $exception) {
            Log::error('Timerboard notification auto-import failed: ' . $exception->getMessage(), [
                'notification_id' => $notification->notification_id,
                'type' => $notification->type,
            ]);
        }
    }
}
