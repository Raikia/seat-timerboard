<?php

namespace Raikia\SeatTimerboard\Handlers;

use Raikia\SeatTimerboard\Integrations\ManagerCoreIntegration;
use Raikia\SeatTimerboard\Services\NotificationTimerImporter;
use Seat\Eveapi\Models\Character\CharacterInfo;
use Seat\Eveapi\Models\Character\CharacterNotification;

class ManagerCoreNotificationHandler
{
    /**
     * Called by Manager Core with its own notification row for each type Timerboard registered.
     */
    public static function handle($notification): void
    {
        if (!ManagerCoreIntegration::acceptsManagerCoreDeliveries()) {
            return;
        }

        ManagerCoreIntegration::recordDelivery();

        // The importer reads SeAT's own notification model, so wrap Manager Core's
        // row in one. It is never saved.
        $seatNotification = new CharacterNotification([
            'notification_id' => $notification->notification_id,
            'character_id' => $notification->character_id,
            'type' => $notification->type,
            'sender_id' => $notification->sender_id ?? null,
            'sender_type' => $notification->sender_type ?? null,
            'timestamp' => $notification->timestamp,
            'text' => $notification->text,
        ]);
        $seatNotification->setRelation(
            'recipient',
            CharacterInfo::with('affiliation')->where('character_id', $notification->character_id)->first()
        );

        app(NotificationTimerImporter::class)->import(
            $seatNotification,
            ManagerCoreIntegration::deliveryLabel($notification->source ?? null)
        );
    }
}
