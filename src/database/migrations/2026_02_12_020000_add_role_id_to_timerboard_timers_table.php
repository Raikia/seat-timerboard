<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoleIdToTimerboardTimersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('seat_timerboard_timers', function (Blueprint $table) {
            $table->unsignedInteger('role_id')->nullable()->after('user_id');
            // We assume standard SeAT roles table is 'roles' and key is 'id'.
            // However, depending on SeAT version it might differ.
            // Safe bet is to add column but maybe not FK constraint if we are unsure of table name.
            // But usually it is 'roles'.
            // $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
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
            $table->dropColumn('role_id');
        });
    }
}
