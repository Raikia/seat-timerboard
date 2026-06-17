<?php

namespace Raikia\SeatTimerboard\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Raikia\SeatTimerboard\Models\Timer;
use Raikia\SeatTimerboard\Models\TimerboardSetting;
use Raikia\SeatTimerboard\Notifications\NewTimer;

class TimerNotificationService
{
    public function sendNewTimer(Timer $timer): void
    {
        try {
            $settings = TimerboardSetting::whereIn('setting', ['notification_enabled', 'notification_role_ids'])
                ->pluck('value', 'setting');

            $enabled = filter_var($settings['notification_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);

            if (! $enabled) {
                return;
            }

            $roleIds = json_decode($settings['notification_role_ids'] ?? '[]', true) ?? [];
            $timerRole = $timer->role_id ? (string) $timer->role_id : 'public';

            if (! in_array($timerRole, $roleIds, true)) {
                return;
            }

            $groupIds = DB::table('group_alerts')
                ->where('alert', 'seat_timerboard_new_timer')
                ->pluck('notification_group_id');

            if ($groupIds->isEmpty()) {
                return;
            }

            $integrations = DB::table('integration_notification_group')
                ->whereIn('notification_group_id', $groupIds)
                ->join('integrations', 'integration_notification_group.integration_id', '=', 'integrations.id')
                ->select('integrations.type', 'integrations.settings')
                ->get();

            foreach ($integrations as $integration) {
                if ($integration->type !== 'discord') {
                    continue;
                }

                $settings = json_decode($integration->settings);
                $target = $settings->channel_id ?? $settings->url ?? null;

                if (! $target) {
                    Log::debug('Timerboard notification skipped: Discord integration missing channel target.', [
                        'settings' => $settings,
                    ]);
                    continue;
                }

                Notification::route('discord', $target)
                    ->notify(new NewTimer($timer));
            }
        } catch (\Throwable $exception) {
            Log::error('Timerboard notification send failed: ' . $exception->getMessage(), [
                'timer_id' => $timer->id,
            ]);
        }
    }
}
