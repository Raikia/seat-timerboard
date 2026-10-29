<?php

namespace Raikia\SeatTimerboard\Listeners;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class EnsureMapDenormalizeIndex
{
    private const INDEX_NAME = 'tb_mapdenormalize_itemname_groupid_idx';

    // eve:update:sde is SeAT's SDE import. Its newer CCP importer runs as db:seed (CcpSdeSeeder)
    // and is due to become the default, so the index is checked after both.
    private const SDE_COMMANDS = ['eve:update:sde', 'db:seed'];

    /**
     * Add the system search index back after an SDE update. The import drops and recreates
     * mapDenormalize, and the migrations that add the index only ever run once.
     */
    public function handle(CommandFinished $event): void
    {
        if (!in_array($event->command, self::SDE_COMMANDS, true)) {
            return;
        }

        try {
            if (!Schema::hasTable('mapDenormalize') || $this->indexExists()) {
                return;
            }

            Schema::table('mapDenormalize', function (Blueprint $table) {
                $table->index(['itemName', 'groupID'], self::INDEX_NAME);
            });

            Log::info('Timerboard added its mapDenormalize index back after the SDE update.');
        } catch (\Throwable $e) {
            // Throwing here would fail the SDE command, and SeAT's startup script stops when it fails.
            Log::warning('Timerboard could not add its mapDenormalize index back.', [
                'message' => $e->getMessage(),
            ]);
        }
    }

    private function indexExists(): bool
    {
        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', 'mapDenormalize')
            ->where('index_name', self::INDEX_NAME)
            ->exists();
    }
}
