<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const INDEX_NAME = 'tb_mapdenormalize_itemname_groupid_idx';

    public function up(): void
    {
        // Replaces 2026_08_29, which the service provider no longer loads. On a new SeAT the table
        // doesn't exist until the first SDE import, and the SDE listener adds the index then.
        // Installs that lost the index to an SDE update get it back here.
        if (!Schema::hasTable('mapDenormalize') || $this->indexExists()) {
            return;
        }

        Schema::table('mapDenormalize', function (Blueprint $table) {
            $table->index(['itemName', 'groupID'], self::INDEX_NAME);
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('mapDenormalize') || !$this->indexExists()) {
            return;
        }

        Schema::table('mapDenormalize', function (Blueprint $table) {
            $table->dropIndex(self::INDEX_NAME);
        });
    }

    private function indexExists(): bool
    {
        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', 'mapDenormalize')
            ->where('index_name', self::INDEX_NAME)
            ->exists();
    }
};
