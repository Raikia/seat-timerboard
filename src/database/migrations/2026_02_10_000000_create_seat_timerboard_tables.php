<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeatTimerboardTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seat_timerboard_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('color')->default('#ffffff');
            $table->timestamps();
        });

        Schema::create('seat_timerboard_timers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('system');
            $table->string('structure_type');
            $table->string('structure_name');
            $table->string('owner_corporation');
            $table->timestamp('eve_time');
            $table->unsignedInteger('user_id');
            $table->timestamps();
        });

        Schema::create('seat_timerboard_timer_tag', function (Blueprint $table) {
            $table->unsignedInteger('timer_id');
            $table->unsignedInteger('tag_id');

            $table->foreign('timer_id')->references('id')->on('seat_timerboard_timers')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('seat_timerboard_tags')->onDelete('cascade');

            $table->primary(['timer_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seat_timerboard_timer_tag');
        Schema::dropIfExists('seat_timerboard_timers');
        Schema::dropIfExists('seat_timerboard_tags');
    }
}
