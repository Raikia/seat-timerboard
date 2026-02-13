<?php

namespace Raikia\SeatTimerboard\Models;

use Illuminate\Database\Eloquent\Model;
use Seat\Web\Models\User;

class Timer extends Model
{
    protected $table = 'seat_timerboard_timers';

    protected $fillable = [
        'system',
        'structure_type',
        'structure_name',
        'owner_corporation',
        'attacker_corporation',
        'eve_time',
        'user_id',
        'role_id'
    ];

    public function role()
    {
        return $this->belongsTo(\Seat\Web\Models\Acl\Role::class, 'role_id');
    }

    protected $dates = ['eve_time'];

    protected $casts = [
        'eve_time' => 'datetime',
    ];

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'seat_timerboard_timer_tag', 'timer_id', 'tag_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function mapDenormalize()
    {
        return $this->hasOne(\Seat\Eveapi\Models\Sde\MapDenormalize::class, 'itemName', 'system');
    }
}
