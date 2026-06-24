<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Raikia\SeatTimerboard\Models\Tag;

return new class extends Migration
{
    public function up(): void
    {
        foreach (Tag::DEFAULT_TAGS as $tag) {
            $exists = DB::table('seat_timerboard_tags')
                ->where('name', $tag['name'])
                ->exists();

            if ($exists) {
                continue;
            }

            DB::table('seat_timerboard_tags')->insert([
                'name' => $tag['name'],
                'color' => $tag['color'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Intentionally left blank. These tags may already be in active use.
    }
};
