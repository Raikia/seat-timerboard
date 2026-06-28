<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seat_timerboard_notification_group_tag_filters', function (Blueprint $table) {
            $table->json('allowed_structure_types')->nullable()->after('allowed_role_ids');
        });
    }

    public function down(): void
    {
        Schema::table('seat_timerboard_notification_group_tag_filters', function (Blueprint $table) {
            $table->dropColumn('allowed_structure_types');
        });
    }
};
