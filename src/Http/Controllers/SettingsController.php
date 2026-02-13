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

        return view('seat-timerboard::settings', compact('tags', 'roles', 'defaultRoleId'));
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
