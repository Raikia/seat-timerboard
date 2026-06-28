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

        $settings = TimerboardSetting::whereIn('setting', ['notification_enabled'])
            ->pluck('value', 'setting');

        $enabled = filter_var($settings['notification_enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);

        if (! $enabled) {
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

        $groups = $this->filterGroupsByTimerRules($groups, $timer);

        if ($groups->isEmpty()) {
            logger()->debug(sprintf('[Timerboard][%d] No notification groups matched the timer notification filters.', $timer->id));

            return;
        }

        logger()->debug(sprintf('[Timerboard][%d] Queuing new timer notification.', $timer->id));

        $this->dispatchNotifications('seat_timerboard_new_timer', $groups, function ($notificationClass) use ($timer) {
            return new $notificationClass($timer);
        });
    }

    private function filterGroupsByTimerRules($groups, Timer $timer)
    {
        $timerRole = $timer->role_id ? (string) $timer->role_id : 'public';
        $timerStructureType = (string) $timer->structure_type;
        $timerTagIds = $timer->tags->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $filters = NotificationGroupTagFilter::query()
            ->whereIn('notification_group_id', $groups->pluck('id'))
            ->get()
            ->keyBy('notification_group_id');

        return $groups->filter(function (NotificationGroup $group) use ($filters, $timerRole, $timerStructureType, $timerTagIds) {
            $filter = $filters->get($group->id);

            $allowedRoleIds = collect($filter?->allowed_role_ids ?? ['public'])
                ->filter(fn ($roleId) => filled($roleId))
                ->map(fn ($roleId) => (string) $roleId)
                ->unique()
                ->values()
                ->all();

            if (empty($allowedRoleIds)) {
                $allowedRoleIds = ['public'];
            }

            if (! in_array($timerRole, $allowedRoleIds, true)) {
                return false;
            }

            if (! $filter) {
                return true;
            }

            $allowedStructureTypes = collect($filter->allowed_structure_types ?? [])
                ->filter(fn ($structureType) => filled($structureType))
                ->map(fn ($structureType) => (string) $structureType)
                ->unique()
                ->values()
                ->all();
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

            if (!empty($allowedStructureTypes) && !in_array($timerStructureType, $allowedStructureTypes, true)) {
                return false;
            }

            if (empty($allowedTagIds)) {
                return true;
            }

            return ! empty(array_intersect($timerTagIds, $allowedTagIds));
        })->values();
    }
}
