<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimerboardNotificationGroupTagFiltersTable extends Migration
{
    public function up()
    {
        Schema::create('seat_timerboard_notification_group_tag_filters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('notification_group_id');
            $table->json('allowed_tag_ids')->nullable();
            $table->json('blocked_tag_ids')->nullable();
            $table->timestamps();

            $table->unique('notification_group_id', 'tb_ngtf_group_unique');

            $table->foreign('notification_group_id', 'tb_ngtf_group_fk')
                ->references('id')
                ->on('notification_groups')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('seat_timerboard_notification_group_tag_filters');
    }
}
