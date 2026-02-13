<?php

namespace Raikia\SeatTimerboard\Http\Controllers;

use Seat\Web\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Raikia\SeatTimerboard\Models\Tag;

class SettingsController extends Controller
{
    public function index()
    {
        $tags = Tag::orderBy('name')->get();
        $roles = \Seat\Web\Models\Acl\Role::all();
        $defaultRole = \Raikia\SeatTimerboard\Models\TimerboardSetting::find('default_timer_role');
        $defaultRoleId = $defaultRole ? $defaultRole->value : null;

        $notifEnabled = \Raikia\SeatTimerboard\Models\TimerboardSetting::find('notification_enabled');
        $notificationEnabled = $notifEnabled ? filter_var($notifEnabled->value, FILTER_VALIDATE_BOOLEAN) : false;

        $notifRoles = \Raikia\SeatTimerboard\Models\TimerboardSetting::find('notification_role_ids');
        $notificationRoleIds = $notifRoles ? json_decode($notifRoles->value, true) : [];

        return view('seat-timerboard::settings', compact('tags', 'roles', 'defaultRoleId', 'notificationEnabled', 'notificationRoleIds'));
    }

    public function storeDefaultRole(Request $request)
    {
        $request->validate([
            'default_timer_role' => 'nullable|integer', // exists:roles,id might fail if roles table name is different, but assuming standard. Safest is just integer or strict validation if we are sure.
        ]);

        \Raikia\SeatTimerboard\Models\TimerboardSetting::updateOrCreate(
            ['setting' => 'default_timer_role'],
            ['value' => $request->input('default_timer_role')]
        );

        return redirect()->route('timerboard.settings')->with('success', 'Default role updated successfully.');
    }

    public function storeNotifications(Request $request)
    {
        $request->validate([
            'notification_enabled' => 'nullable|in:on,1,true',
            'notification_role_ids' => 'nullable|array',
        ]);

        \Raikia\SeatTimerboard\Models\TimerboardSetting::updateOrCreate(
            ['setting' => 'notification_enabled'],
            ['value' => $request->has('notification_enabled')]
        );

        \Raikia\SeatTimerboard\Models\TimerboardSetting::updateOrCreate(
            ['setting' => 'notification_role_ids'],
            ['value' => json_encode($request->input('notification_role_ids', []))]
        );

        return redirect()->route('timerboard.settings')->with('success', 'Notification settings updated successfully.');
    }

    public function storeTag(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
        ]);

        Tag::create($request->only('name', 'color'));

        return redirect()->route('timerboard.settings')->with('success', 'Tag created successfully.');
    }

    public function updateTag(Request $request, Tag $tag)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:7',
        ]);

        $tag->update($request->only('name', 'color'));

        return redirect()->route('timerboard.settings')->with('success', 'Tag updated successfully.');
    }

    public function destroyTag(Tag $tag)
    {
        $tag->delete();

        return redirect()->route('timerboard.settings')->with('success', 'Tag deleted successfully.');
    }
}
