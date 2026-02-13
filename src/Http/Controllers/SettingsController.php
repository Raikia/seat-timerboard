<?php

namespace Raikia\SeatTimerboard\Http\Controllers;

use Seat\Web\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Raikia\SeatTimerboard\Models\Tag;

class SettingsController extends Controller
{
    public function index()
    {
        $tags = Tag::all();
        return view('seat-timerboard::settings', compact('tags'));
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

    public function destroyTag(Tag $tag)
    {
        $tag->delete();

        return redirect()->route('timerboard.settings')->with('success', 'Tag deleted successfully.');
    }
}
