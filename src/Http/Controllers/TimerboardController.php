<?php

namespace Raikia\SeatTimerboard\Http\Controllers;

use Seat\Web\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TimerboardController extends Controller
{
    public function index()
    {
        return view('seat-timerboard::index');
    }
}
