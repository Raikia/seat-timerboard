<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seat_timerboard_notification_group_tag_filters', function (Blueprint $table) {
            $table->json('allowed_role_ids')->nullable()->after('notification_group_id');
        });

        $setting = DB::table('seat_timerboard_settings')
            ->where('setting', 'notification_role_ids')
            ->value('value');

        $allowedRoleIds = collect(json_decode($setting ?: '[]', true) ?: [])
            ->filter(fn ($roleId) => filled($roleId))
            ->map(function ($roleId) {
                return $roleId === 'public' ? 'public' : (string) (int) $roleId;
            })
            ->unique()
            ->values()
            ->all();

        if (empty($allowedRoleIds)) {
            return;
        }

        DB::table('seat_timerboard_notification_group_tag_filters')
            ->whereNull('allowed_role_ids')
            ->update([
                'allowed_role_ids' => json_encode($allowedRoleIds),
                'updated_at' => now(),
            ]);

        if ($allowedRoleIds === ['public']) {
            return;
        }

        $groupIds = DB::table('notification_groups')
            ->join('group_alerts', 'group_alerts.notification_group_id', '=', 'notification_groups.id')
            ->where('group_alerts.alert', 'seat_timerboard_new_timer')
            ->pluck('notification_groups.id');

        $existingFilterGroupIds = DB::table('seat_timerboard_notification_group_tag_filters')
            ->whereIn('notification_group_id', $groupIds)
            ->pluck('notification_group_id')
            ->all();

        $missingGroupIds = collect($groupIds)
            ->reject(fn ($groupId) => in_array((int) $groupId, array_map('intval', $existingFilterGroupIds), true))
            ->values();

        if ($missingGroupIds->isEmpty()) {
            return;
        }

        $timestamp = now();

        DB::table('seat_timerboard_notification_group_tag_filters')->insert(
            $missingGroupIds->map(fn ($groupId) => [
                'notification_group_id' => (int) $groupId,
                'allowed_role_ids' => json_encode($allowedRoleIds),
                'allowed_tag_ids' => null,
                'blocked_tag_ids' => null,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ])->all()
        );
    }

    public function down(): void
    {
        Schema::table('seat_timerboard_notification_group_tag_filters', function (Blueprint $table) {
            $table->dropColumn('allowed_role_ids');
        });
    }
};
