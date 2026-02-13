<?php

namespace Raikia\SeatTimerboard\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'seat_timerboard_tags';

    protected $fillable = ['name', 'color'];

    public function timers()
    {
        return $this->belongsToMany(Timer::class, 'seat_timerboard_timer_tag', 'tag_id', 'timer_id');
    }
}
