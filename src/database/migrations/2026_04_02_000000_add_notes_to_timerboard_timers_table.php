<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotesToTimerboardTimersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('seat_timerboard_timers', function (Blueprint $table) {
            $table->text('notes')->nullable()->after('structure_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('seat_timerboard_timers', function (Blueprint $table) {
            $table->dropColumn('notes');
        });
    }
}
