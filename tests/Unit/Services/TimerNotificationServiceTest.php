<?php

namespace Raikia\SeatTimerboard\Tests\Unit\Services;

use Illuminate\Support\Facades\Notification;
use Raikia\SeatTimerboard\Models\NotificationGroupTagFilter;
use Raikia\SeatTimerboard\Models\Tag;
use Raikia\SeatTimerboard\Notifications\Slack\NewTimer;
use Raikia\SeatTimerboard\Services\TimerNotificationService;
use Raikia\SeatTimerboard\Tests\TestCase;
use Seat\Notifications\Models\Integration;
use Seat\Notifications\Models\NotificationGroup;

class TimerNotificationServiceTest extends TestCase
{
    public function test_it_sends_new_timer_notifications_for_matching_groups(): void
    {
        Notification::fake();

        $friendly = Tag::create(['name' => 'Friendly', 'color' => '#28a745']);
        $timer = $this->createTimerWithTags(['Friendly'], [
            'structure_name' => 'Notify Fort',
        ]);
        $group = $this->createSlackNotificationGroup();
        NotificationGroupTagFilter::create([
            'notification_group_id' => $group->id,
            'allowed_tag_ids' => [$friendly->id],
            'blocked_tag_ids' => [],
        ]);

        $this->seedTimerboardSettings([
            'notification_enabled' => true,
            'notification_role_ids' => ['public'],
        ]);

        app(TimerNotificationService::class)->sendNewTimer($timer);

        Notification::assertSentOnDemand(NewTimer::class);
    }

    public function test_it_blocks_notifications_when_group_filters_exclude_the_timer(): void
    {
        Notification::fake();

        $hostile = Tag::create(['name' => 'Hostile', 'color' => '#dc3545']);
        $timer = $this->createTimerWithTags(['Hostile'], [
            'structure_name' => 'Notify Fort',
        ]);
        $group = $this->createSlackNotificationGroup();
        NotificationGroupTagFilter::create([
            'notification_group_id' => $group->id,
            'allowed_tag_ids' => [],
            'blocked_tag_ids' => [$hostile->id],
        ]);

        $this->seedTimerboardSettings([
            'notification_enabled' => true,
            'notification_role_ids' => ['public'],
        ]);

        app(TimerNotificationService::class)->sendNewTimer($timer);

        Notification::assertNothingSent();
    }

    private function createSlackNotificationGroup(): NotificationGroup
    {
        $group = NotificationGroup::create(['name' => 'Friends']);
        $group->alerts()->create([
            'alert' => 'seat_timerboard_new_timer',
        ]);

        $integration = Integration::create([
            'name' => 'Slack',
            'type' => 'slack',
            'settings' => ['url' => 'https://hooks.slack.test'],
        ]);

        $group->integrations()->attach($integration->id);

        return $group;
    }
}
