<?php

namespace Raikia\SeatTimerboard\Observers;

use Raikia\SeatTimerboard\Models\Timer;
use Raikia\SeatTimerboard\Models\TimerboardSetting;
use Raikia\SeatTimerboard\Notifications\NewTimer;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TimerObserver
{
    /**
     * Handle the Timer "created" event.
     *
     * @param  \Raikia\SeatTimerboard\Models\Timer  $timer
     * @return void
     */
    public function created(Timer $timer)
    {
        try {
            // Check if notification is enabled globally
            $settings = TimerboardSetting::whereIn('setting', ['notification_enabled', 'notification_role_ids'])
                ->pluck('value', 'setting');
            
            $enabled = filter_var($settings['notification_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);

            if (!$enabled) {
                return;
            }

            // Check if role matches
            $roleIds = json_decode($settings['notification_role_ids'] ?? '[]', true) ?? [];
            $timerRole = $timer->role_id ? (string)$timer->role_id : 'public';
            
            if (!in_array($timerRole, $roleIds)) {
                return;
            }

            // Find all integrations subscribed to this alert
            // 1. Find groups with this alert
            $groupIds = DB::table('group_alerts')
                ->where('alert', 'seat_timerboard_new_timer')
                ->pluck('notification_group_id');
            
            if ($groupIds->isEmpty()) {
                return;
            }

            // 2. Find integrations for these groups
            $integrations = DB::table('integration_notification_group')
                ->whereIn('notification_group_id', $groupIds)
                ->join('integrations', 'integration_notification_group.integration_id', '=', 'integrations.id')
                ->select('integrations.type', 'integrations.settings')
                ->get();

            foreach ($integrations as $integration) {
                // Only worry about Discord as requested
                if ($integration->type === 'discord') {
                    $settings = json_decode($integration->settings);
                    
                    // Support both channel_id (standard) and url (webhook/custom)
                    $target = $settings->channel_id ?? $settings->url ?? null;

                    if ($target) {
                         Notification::route('discord', $target)
                            ->notify(new NewTimer($timer));
                    } else {
                         Log::debug('Timerboard Observer: Discord Integration missing channel_id and url', ['settings' => $settings]);
                    }
                }
            }

        } catch (\Exception $e) {
            Log::error('Timerboard Observer Error: ' . $e->getMessage());
        }
    }
}
