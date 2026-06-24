<?php

namespace Raikia\SeatTimerboard\Services;

use Illuminate\Support\Facades\DB;
use Raikia\SeatTimerboard\Models\NotificationGroupTagFilter;
use Raikia\SeatTimerboard\Models\Timer;
use Raikia\SeatTimerboard\Models\TimerboardSetting;
use Seat\Notifications\Models\NotificationGroup;
use Seat\Notifications\Traits\NotificationDispatchTool;

class TimerNotificationService
{
    use NotificationDispatchTool;

    public function sendNewTimer(Timer $timer): void
    {
        if (! $timer->exists) {
            return;
        }

        $this->dispatchNewTimerNotification($timer->id);
    }

    public function queueNewTimerNotification(Timer $timer): void
    {
        if (! $timer->exists) {
            return;
        }

        if (DB::transactionLevel() > 0 && method_exists(DB::connection(), 'afterCommit')) {
            DB::afterCommit(function () use ($timer) {
                $this->dispatchNewTimerNotification($timer->id);
            });

            return;
        }

        $this->dispatchNewTimerNotification($timer->id);
    }

    private function dispatchNewTimerNotification(int $timerId): void
    {
        $timer = Timer::with(['tags', 'user', 'role', 'mapDenormalize.region', 'mapDenormalize.system'])
            ->find($timerId);

        if (! $timer) {
            return;
        }

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

        $groups = NotificationGroup::with(['alerts', 'integrations', 'mentions'])
            ->whereHas('alerts', function ($query) {
                $query->where('alert', 'seat_timerboard_new_timer');
            })
            ->get();

        if ($groups->isEmpty()) {
            return;
        }

        $groups = $this->filterGroupsByTimerTags($groups, $timer);

        if ($groups->isEmpty()) {
            logger()->debug(sprintf('[Timerboard][%d] No notification groups matched the timer tag filters.', $timer->id));

            return;
        }

        logger()->debug(sprintf('[Timerboard][%d] Queuing new timer notification.', $timer->id));

        $this->dispatchNotifications('seat_timerboard_new_timer', $groups, function ($notificationClass) use ($timer) {
            return new $notificationClass($timer);
        });
    }

    private function filterGroupsByTimerTags($groups, Timer $timer)
    {
        $timerTagIds = $timer->tags->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $filters = NotificationGroupTagFilter::query()
            ->whereIn('notification_group_id', $groups->pluck('id'))
            ->get()
            ->keyBy('notification_group_id');

        return $groups->filter(function (NotificationGroup $group) use ($filters, $timerTagIds) {
            $filter = $filters->get($group->id);

            if (! $filter) {
                return true;
            }

            $allowedTagIds = collect($filter->allowed_tag_ids ?? [])
                ->map(fn ($id) => (int) $id)
                ->all();
            $blockedTagIds = collect($filter->blocked_tag_ids ?? [])
                ->map(fn ($id) => (int) $id)
                ->all();

            $hasBlockedTag = ! empty(array_intersect($timerTagIds, $blockedTagIds));

            if ($hasBlockedTag) {
                return false;
            }

            if (empty($allowedTagIds)) {
                return true;
            }

            return ! empty(array_intersect($timerTagIds, $allowedTagIds));
        })->values();
    }
}
