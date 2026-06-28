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
            'allowed_role_ids' => ['public'],
            'allowed_structure_types' => [],
            'allowed_tag_ids' => [$friendly->id],
            'blocked_tag_ids' => [],
        ]);

        $this->seedTimerboardSettings([
            'notification_enabled' => true,
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
            'allowed_role_ids' => ['public'],
            'allowed_structure_types' => [],
            'allowed_tag_ids' => [],
            'blocked_tag_ids' => [$hostile->id],
        ]);

        $this->seedTimerboardSettings([
            'notification_enabled' => true,
        ]);

        app(TimerNotificationService::class)->sendNewTimer($timer);

        Notification::assertNothingSent();
    }

    public function test_it_defaults_groups_to_public_timers_only(): void
    {
        Notification::fake();

        $publicTimer = $this->createTimerWithTags(['Friendly'], [
            'structure_name' => 'Public Fort',
            'role_id' => null,
        ]);

        $restrictedTimer = $this->createTimerWithTags(['Friendly'], [
            'structure_name' => 'Restricted Fort',
            'role_id' => 7,
        ]);

        $this->createSlackNotificationGroup();

        $this->seedTimerboardSettings([
            'notification_enabled' => true,
        ]);

        app(TimerNotificationService::class)->sendNewTimer($publicTimer);
        Notification::assertSentOnDemand(NewTimer::class);

        Notification::fake();

        app(TimerNotificationService::class)->sendNewTimer($restrictedTimer);
        Notification::assertNothingSent();
    }

    public function test_it_allows_a_group_to_receive_a_specific_restricted_role(): void
    {
        Notification::fake();

        $timer = $this->createTimerWithTags(['Friendly'], [
            'structure_name' => 'Restricted Fort',
            'role_id' => 7,
        ]);

        $group = $this->createSlackNotificationGroup();
        NotificationGroupTagFilter::create([
            'notification_group_id' => $group->id,
            'allowed_role_ids' => ['7'],
            'allowed_structure_types' => [],
            'allowed_tag_ids' => [],
            'blocked_tag_ids' => [],
        ]);

        $this->seedTimerboardSettings([
            'notification_enabled' => true,
        ]);

        app(TimerNotificationService::class)->sendNewTimer($timer);

        Notification::assertSentOnDemand(NewTimer::class);
    }

    public function test_it_only_sends_notifications_to_groups_with_matching_structure_types(): void
    {
        Notification::fake();

        $timer = $this->createTimerWithTags(['Friendly'], [
            'structure_name' => 'Skyhook Timer',
            'structure_type' => 'Skyhook',
        ]);

        $matchingGroup = $this->createSlackNotificationGroup();
        NotificationGroupTagFilter::create([
            'notification_group_id' => $matchingGroup->id,
            'allowed_role_ids' => ['public'],
            'allowed_structure_types' => ['Skyhook'],
            'allowed_tag_ids' => [],
            'blocked_tag_ids' => [],
        ]);

        $nonMatchingGroup = $this->createSlackNotificationGroup('Cap Structures');
        NotificationGroupTagFilter::create([
            'notification_group_id' => $nonMatchingGroup->id,
            'allowed_role_ids' => ['public'],
            'allowed_structure_types' => ['Keepstar'],
            'allowed_tag_ids' => [],
            'blocked_tag_ids' => [],
        ]);

        $this->seedTimerboardSettings([
            'notification_enabled' => true,
        ]);

        app(TimerNotificationService::class)->sendNewTimer($timer);

        Notification::assertSentOnDemand(NewTimer::class, 1);
    }

    private function createSlackNotificationGroup(string $name = 'Friends'): NotificationGroup
    {
        $group = NotificationGroup::create(['name' => $name]);
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
