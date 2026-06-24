<?php

namespace Raikia\SeatTimerboard\Models;

use Illuminate\Database\Eloquent\Model;
use Seat\Notifications\Models\NotificationGroup;

class NotificationGroupTagFilter extends Model
{
    protected $table = 'seat_timerboard_notification_group_tag_filters';

    protected $fillable = [
        'notification_group_id',
        'allowed_role_ids',
        'allowed_tag_ids',
        'blocked_tag_ids',
    ];

    protected $casts = [
        'allowed_role_ids' => 'array',
        'allowed_tag_ids' => 'array',
        'blocked_tag_ids' => 'array',
    ];

    public function notificationGroup()
    {
        return $this->belongsTo(NotificationGroup::class, 'notification_group_id');
    }
}
