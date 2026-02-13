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
        // Hostile (Red - Bootstrap Danger)
        Tag::firstOrCreate(
            ['name' => 'Hostile'],
            ['color' => '#dc3545']
        );

        // Friendly (Blue - Bootstrap Primary)
        Tag::firstOrCreate(
            ['name' => 'Friendly'],
            ['color' => '#007bff']
        );
    }
}
