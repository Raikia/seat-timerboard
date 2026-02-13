<?php

namespace Raikia\SeatTimerboard\Models;

use Illuminate\Database\Eloquent\Model;

class TimerboardSetting extends Model
{
    protected $table = 'seat_timerboard_settings';

    protected $primaryKey = 'setting';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['setting', 'value'];
}
