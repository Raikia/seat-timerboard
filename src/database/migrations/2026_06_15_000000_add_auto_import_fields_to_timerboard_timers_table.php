<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAutoImportFieldsToTimerboardTimersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('seat_timerboard_timers', function (Blueprint $table) {
            $table->string('import_fingerprint')->nullable()->unique()->after('role_id');
            $table->string('source_notification_type')->nullable()->after('import_fingerprint');
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
            $table->dropUnique(['import_fingerprint']);
            $table->dropColumn(['import_fingerprint', 'source_notification_type']);
        });
    }
}
