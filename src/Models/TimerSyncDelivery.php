<?php

namespace Raikia\SeatTimerboard\Models;

use Illuminate\Database\Eloquent\Model;

class TimerSyncDelivery extends Model
{
    protected $table = 'seat_timerboard_sync_deliveries';

    protected $fillable = [
        'local_timer_id',
        'peer_id',
        'last_synced_at',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
    ];

    public function peer()
    {
        return $this->belongsTo(TimerSyncPeer::class, 'peer_id');
    }
}
