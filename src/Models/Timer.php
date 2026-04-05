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
        'notes',
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

    public function getStructureImage()
    {
        $mapping = [
            'Ansiblex' => 35841,
            'Astrahus' => 35832,
            'Athanor' => 35835,
            'Azbel' => 35826,
            'POCO' => 2233,
            'Fortizar' => 35833,
            'Keepstar' => 35834,
            'Mercenary Den' => 85230,
            'Metenox' => 81826,
            'Pharolux' => 35840,
            'POS' => 16213,
            'Raitaru' => 35825,
            'Skyhook' => 81824, 
            'Sotiyo' => 35827,
            'Tatara' => 35836,
            'Tenebrex' => 37534,
        ];

        $typeId = $mapping[$this->structure_type] ?? 35832; // Default to Astrahus

        return "https://images.evetech.net/types/{$typeId}/render?size=64";
    }

    public function getMapLocationName(): ?string
    {
        if (!$this->mapDenormalize) {
            return null;
        }

        if ($this->mapDenormalize->solarSystemID === null) {
            return $this->mapDenormalize->itemName;
        }

        return optional($this->mapDenormalize->system)->itemName;
    }

    public function getRegionName(): string
    {
        return optional(optional($this->mapDenormalize)->region)->itemName ?? 'Unknown';
    }

    public function getDotlanMapUrl(): ?string
    {
        $locationName = $this->getMapLocationName();
        $regionName = optional(optional($this->mapDenormalize)->region)->itemName;

        if (!$locationName || !$regionName) {
            return null;
        }

        return 'https://evemaps.dotlan.net/map/' .
            str_replace(' ', '_', $regionName) . '/' .
            str_replace(' ', '_', $locationName);
    }
}
