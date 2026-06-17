<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSyncFieldsToTimerboardTimersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('seat_timerboard_timers', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->nullable()->change();
            $table->string('sync_origin_instance_uuid')->nullable()->after('source_notification_type');
            $table->unsignedInteger('sync_origin_timer_id')->nullable()->after('sync_origin_instance_uuid');
            $table->string('sync_source_name')->nullable()->after('sync_origin_timer_id');
            $table->unique(
                ['sync_origin_instance_uuid', 'sync_origin_timer_id'],
                'seat_timerboard_timers_sync_origin_unique'
            );
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
            $table->dropUnique('seat_timerboard_timers_sync_origin_unique');
            $table->dropColumn([
                'sync_origin_instance_uuid',
                'sync_origin_timer_id',
                'sync_source_name',
            ]);
            $table->unsignedInteger('user_id')->nullable(false)->change();
        });
    }
}
