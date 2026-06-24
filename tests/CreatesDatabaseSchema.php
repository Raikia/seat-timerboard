<?php

namespace Raikia\SeatTimerboard\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

trait CreatesDatabaseSchema
{
    protected function createDatabaseSchema(): void
    {
        $this->dropKnownTables();

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->unsignedBigInteger('main_character_id')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('admin')->default(false);
            $table->string('remember_token')->nullable();
            $table->timestamps();
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('logo')->nullable();
        });

        Schema::create('global_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->mediumText('value')->nullable();
            $table->timestamps();
            $table->index('name');
        });

        Schema::create('notification_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->timestamps();
        });

        Schema::create('group_alerts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('notification_group_id');
            $table->string('alert');
            $table->timestamps();
        });

        Schema::create('integrations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('type');
            $table->text('settings')->nullable();
            $table->timestamps();
        });

        Schema::create('integration_notification_group', function (Blueprint $table) {
            $table->unsignedInteger('integration_id');
            $table->unsignedInteger('notification_group_id');
        });

        Schema::create('notification_groups_mentions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('notification_group_id');
            $table->string('type')->nullable();
            $table->text('data')->nullable();
        });

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
            $table->string('structure_name')->nullable();
            $table->text('notes')->nullable();
            $table->string('owner_corporation');
            $table->string('attacker_corporation')->nullable();
            $table->timestamp('eve_time');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('role_id')->nullable();
            $table->string('import_fingerprint')->nullable()->unique();
            $table->string('source_notification_type')->nullable();
            $table->string('sync_origin_instance_uuid')->nullable();
            $table->unsignedInteger('sync_origin_timer_id')->nullable();
            $table->string('sync_source_name')->nullable();
            $table->timestamps();
        });

        Schema::create('seat_timerboard_timer_tag', function (Blueprint $table) {
            $table->unsignedInteger('timer_id');
            $table->unsignedInteger('tag_id');
            $table->primary(['timer_id', 'tag_id']);
        });

        Schema::create('seat_timerboard_settings', function (Blueprint $table) {
            $table->string('setting')->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        Schema::create('seat_timerboard_notification_group_tag_filters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('notification_group_id');
            $table->json('allowed_tag_ids')->nullable();
            $table->json('blocked_tag_ids')->nullable();
            $table->timestamps();
            $table->unique('notification_group_id', 'tb_ngtf_group_unique');
        });

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
            $table->unique(['local_timer_id', 'peer_id'], 'seat_timerboard_sync_deliveries_local_timer_peer_unique');
        });

        Schema::create('character_infos', function (Blueprint $table) {
            $table->unsignedBigInteger('character_id')->primary();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        Schema::create('character_affiliations', function (Blueprint $table) {
            $table->unsignedBigInteger('character_id')->primary();
            $table->unsignedBigInteger('corporation_id')->nullable();
            $table->unsignedBigInteger('alliance_id')->nullable();
            $table->unsignedBigInteger('faction_id')->nullable();
            $table->timestamps();
        });

        Schema::create('refresh_tokens', function (Blueprint $table) {
            $table->unsignedBigInteger('character_id')->primary();
            $table->unsignedInteger('version')->default(2);
            $table->unsignedInteger('user_id')->nullable();
            $table->string('character_owner_hash')->nullable();
            $table->text('refresh_token')->nullable();
            $table->text('scopes_profile')->nullable();
            $table->text('scopes')->nullable();
            $table->timestamp('expires_on')->nullable();
            $table->text('token')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('character_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('character_id');
            $table->unsignedBigInteger('notification_id');
            $table->string('type');
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->string('sender_type')->nullable();
            $table->timestamp('timestamp')->nullable();
            $table->boolean('is_read')->default(false);
            $table->text('text')->nullable();
            $table->timestamps();
        });

        Schema::create('corporation_infos', function (Blueprint $table) {
            $table->unsignedBigInteger('corporation_id')->primary();
            $table->string('name')->nullable();
            $table->unsignedBigInteger('alliance_id')->nullable();
            $table->timestamps();
        });

        Schema::create('alliances', function (Blueprint $table) {
            $table->unsignedBigInteger('alliance_id')->primary();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        Schema::create('universe_names', function (Blueprint $table) {
            $table->unsignedBigInteger('entity_id')->primary();
            $table->string('name')->nullable();
            $table->string('category')->nullable();
            $table->timestamps();
        });

        Schema::create('universe_structures', function (Blueprint $table) {
            $table->unsignedBigInteger('structure_id')->primary();
            $table->string('name')->nullable();
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->unsignedBigInteger('solar_system_id')->nullable();
            $table->unsignedBigInteger('type_id')->nullable();
            $table->double('x')->nullable();
            $table->double('y')->nullable();
            $table->double('z')->nullable();
            $table->timestamps();
        });

        Schema::create('mapDenormalize', function (Blueprint $table) {
            $table->unsignedBigInteger('itemID')->primary();
            $table->unsignedBigInteger('typeID')->nullable();
            $table->unsignedBigInteger('groupID')->nullable();
            $table->unsignedBigInteger('solarSystemID')->nullable();
            $table->unsignedBigInteger('constellationID')->nullable();
            $table->unsignedBigInteger('regionID')->nullable();
            $table->unsignedBigInteger('orbitID')->nullable();
            $table->double('x')->nullable();
            $table->double('y')->nullable();
            $table->double('z')->nullable();
            $table->double('radius')->nullable();
            $table->string('itemName')->nullable();
            $table->double('security')->nullable();
            $table->unsignedBigInteger('celestialIndex')->nullable();
            $table->unsignedBigInteger('orbitIndex')->nullable();
        });
    }

    protected function dropKnownTables(): void
    {
        $tables = [
            'mapDenormalize',
            'universe_structures',
            'universe_names',
            'alliances',
            'corporation_infos',
            'character_notifications',
            'refresh_tokens',
            'character_affiliations',
            'character_infos',
            'seat_timerboard_sync_deliveries',
            'seat_timerboard_sync_peers',
            'seat_timerboard_notification_group_tag_filters',
            'seat_timerboard_settings',
            'seat_timerboard_timer_tag',
            'seat_timerboard_timers',
            'seat_timerboard_tags',
            'notification_groups_mentions',
            'integration_notification_group',
            'integrations',
            'group_alerts',
            'notification_groups',
            'global_settings',
            'roles',
            'users',
        ];

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }
}
