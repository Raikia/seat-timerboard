<?php

namespace Raikia\SeatTimerboard\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public const DEFAULT_TAGS = [
        ['name' => 'Armor', 'color' => '#f39c12'],
        ['name' => 'Hull', 'color' => '#e74c3c'],
        ['name' => 'Final', 'color' => '#8e44ad'],
        ['name' => 'Anchoring', 'color' => '#3498db'],
        ['name' => 'Unanchoring', 'color' => '#95a5a6'],
        ['name' => 'Mining', 'color' => '#27ae60'],
        ['name' => 'Reinforced', 'color' => '#dc3545'],
        ['name' => 'Auto Imported', 'color' => '#6c757d'],
        ['name' => 'Hostile', 'color' => '#dc3545'],
        ['name' => 'Friendly', 'color' => '#28a745'],
        ['name' => 'Remote Synced', 'color' => '#6c757d'],
    ];

    public const PROTECTED_TAG_NAMES = [
        'Auto Imported',
        'Friendly',
        'Anchoring',
        'Reinforced',
        'Remote Synced',
    ];

    protected $table = 'seat_timerboard_tags';

    protected $fillable = ['name', 'color'];

    public function timers()
    {
        return $this->belongsToMany(Timer::class, 'seat_timerboard_timer_tag', 'tag_id', 'timer_id');
    }

    public static function defaultColorFor(string $name, string $fallback = '#6c757d'): string
    {
        foreach (self::DEFAULT_TAGS as $tag) {
            if ($tag['name'] === $name) {
                return $tag['color'];
            }
        }

        return $fallback;
    }

    public function isProtectedSystemTag(): bool
    {
        return in_array($this->name, self::PROTECTED_TAG_NAMES, true);
    }
}
