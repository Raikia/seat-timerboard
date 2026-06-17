<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimerboardSyncTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seat_timerboard_sync_peers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('instance_uuid')->unique();
            $table->string('base_url');
            $table->text('api_token');
            $table->text('sync_tag_ids')->nullable();
            $table->unsignedInteger('incoming_role_id')->nullable();
            $table->boolean('allow_remote_delete')->default(true);
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();
        });

        Schema::create('seat_timerboard_sync_deliveries', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('local_timer_id');
            $table->unsignedInteger('peer_id');
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();

            $table->unique(
                ['local_timer_id', 'peer_id'],
                'seat_timerboard_sync_deliveries_local_timer_peer_unique'
            );
            $table->index('local_timer_id');
            $table->index('peer_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seat_timerboard_sync_deliveries');
        Schema::dropIfExists('seat_timerboard_sync_peers');
    }
}
