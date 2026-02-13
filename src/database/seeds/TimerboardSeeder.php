<?php

namespace Raikia\SeatTimerboard\database\seeds;

use Illuminate\Database\Seeder;
use Raikia\SeatTimerboard\Models\Tag;

class TimerboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tags = [
            ['name' => 'Armor', 'color' => '#f39c12'],       // Orange
            ['name' => 'Hull', 'color' => '#e74c3c'],        // Red
            ['name' => 'Final', 'color' => '#8e44ad'],       // Purple
            ['name' => 'Anchoring', 'color' => '#3498db'],   // Blue
            ['name' => 'Unanchoring', 'color' => '#95a5a6'], // Gray
            ['name' => 'Mining', 'color' => '#27ae60'],      // Green
            ['name' => 'Hostile', 'color' => '#dc3545'],     // Bootstrap Danger
            ['name' => 'Friendly', 'color' => '#007bff'],    // Bootstrap Primary
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(
                ['name' => $tag['name']],
                ['color' => $tag['color']]
            );
        }
    }
}
