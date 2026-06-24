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
        foreach (Tag::DEFAULT_TAGS as $tag) {
            Tag::firstOrCreate(
                ['name' => $tag['name']],
                ['color' => $tag['color']]
            );
        }
    }
}
