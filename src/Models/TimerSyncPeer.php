<?php

namespace Raikia\SeatTimerboard\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class TimerSyncPeer extends Model
{
    protected $table = 'seat_timerboard_sync_peers';

    protected $fillable = [
        'name',
        'instance_uuid',
        'base_url',
        'api_token',
        'sync_tag_ids',
        'incoming_role_id',
        'allow_remote_delete',
        'is_enabled',
    ];

    protected $casts = [
        'allow_remote_delete' => 'boolean',
        'is_enabled' => 'boolean',
    ];

    public function getSyncTagIdsAttribute($value): array
    {
        return array_values(array_filter(json_decode($value ?: '[]', true) ?: [], function ($tagId) {
            return filled($tagId);
        }));
    }

    public function setSyncTagIdsAttribute($value): void
    {
        $this->attributes['sync_tag_ids'] = json_encode(array_values(array_unique(array_map('intval', $value ?: []))));
    }

    public function getApiTokenAttribute($value): string
    {
        return Crypt::decryptString($value);
    }

    public function setApiTokenAttribute($value): void
    {
        if (blank($value)) {
            return;
        }

        $this->attributes['api_token'] = Crypt::encryptString(trim($value));
    }

    public function setBaseUrlAttribute($value): void
    {
        $this->attributes['base_url'] = rtrim(trim((string) $value), '/');
    }

    public function incomingRole()
    {
        return $this->belongsTo(\Seat\Web\Models\Acl\Role::class, 'incoming_role_id');
    }

    public function deliveries()
    {
        return $this->hasMany(TimerSyncDelivery::class, 'peer_id');
    }
}
