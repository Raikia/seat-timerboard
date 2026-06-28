<?php

namespace Raikia\SeatTimerboard\Tests\Unit\Http;

use Raikia\SeatTimerboard\Http\Controllers\SettingsController;
use Raikia\SeatTimerboard\Models\NotificationGroupTagFilter;
use Raikia\SeatTimerboard\Models\Tag;
use Raikia\SeatTimerboard\Tests\TestCase;
use Seat\Notifications\Models\NotificationGroup;
use Seat\Web\Models\Acl\Role;

class SettingsControllerTest extends TestCase
{
    public function test_destroying_a_tag_preserves_non_default_role_filters(): void
    {
        $role = Role::create([
            'title' => 'Alliance Ops',
            'description' => 'Restricted notifications',
            'logo' => null,
        ]);

        $group = NotificationGroup::create([
            'name' => 'Friends',
        ]);

        $tag = Tag::create([
            'name' => 'Temporary',
            'color' => '#123456',
        ]);

        $filter = NotificationGroupTagFilter::create([
            'notification_group_id' => $group->id,
            'allowed_role_ids' => [(string) $role->id],
            'allowed_structure_types' => ['Skyhook'],
            'allowed_tag_ids' => [$tag->id],
            'blocked_tag_ids' => [],
        ]);

        $response = app(SettingsController::class)->destroyTag($tag);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertNull(Tag::find($tag->id));

        $filter->refresh();

        $this->assertSame([(string) $role->id], $filter->allowed_role_ids);
        $this->assertSame(['Skyhook'], $filter->allowed_structure_types);
        $this->assertSame([], $filter->allowed_tag_ids);
        $this->assertSame([], $filter->blocked_tag_ids);
    }
}
