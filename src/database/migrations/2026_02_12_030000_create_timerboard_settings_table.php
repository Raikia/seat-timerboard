<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimerboardSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seat_timerboard_settings', function (Blueprint $table) {
            $table->string('setting')->primary();
            $table->string('value')->nullable();
            $table->timestamps();
        });

        // Insert default setting
        DB::table('seat_timerboard_settings')->insert([
            'setting' => 'default_timer_role',
            'value' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seat_timerboard_settings');
    }
}
