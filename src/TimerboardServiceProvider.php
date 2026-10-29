<?php

namespace Raikia\SeatTimerboard;

use Illuminate\Console\Events\CommandFinished;
use Illuminate\Support\Facades\Event;
use Raikia\SeatTimerboard\Console\Commands\PullTimerSyncPeers;
use Raikia\SeatTimerboard\database\seeds\TimerboardSeeder;
use Raikia\SeatTimerboard\Database\Seeds\ScheduleSeeder;
use Raikia\SeatTimerboard\Observers\CharacterNotificationObserver;
use Raikia\SeatTimerboard\Listeners\EnsureMapDenormalizeIndex;
use Seat\Services\AbstractSeatPlugin;
use Seat\Eveapi\Models\Character\CharacterNotification;

class TimerboardServiceProvider extends AbstractSeatPlugin
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->addRoutes();
        $this->addViews();
        $this->addTranslations();
        $this->addMigrations();
        $this->registerPermissions(__DIR__ . '/Config/timerboard.permissions.php', 'seat-timerboard');
        $this->registerCommands();
        $this->registerListeners();

        CharacterNotification::observe(CharacterNotificationObserver::class);
        $this->registerDatabaseSeeders([
            TimerboardSeeder::class,
            ScheduleSeeder::class,
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/Config/timerboard.sidebar.php', 'package.sidebar'
        );

        $this->mergeConfigFrom(
            __DIR__ . '/Config/notifications.alerts.php', 'notifications.alerts'
        );
    }

    /**
     * Return the plugin name.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'SeAT Timerboard';
    }

    /**
     * Return the package repository URL.
     *
     * @return string
     */
    public function getPackageRepositoryUrl(): string
    {
        return 'https://github.com/raikia/seat-timerboard';
    }

    /**
     * Return the packagist package name.
     *
     * @return string
     */
    public function getPackagistPackageName(): string
    {
        return 'seat-timerboard';
    }

    /**
     * Return the packagist vendor name.
     *
     * @return string
     */
    public function getPackagistVendorName(): string
    {
        return 'raikia';
    }

    private function addRoutes()
    {
        if (!$this->app->routesAreCached()) {
            include __DIR__ . '/Http/routes.php';
        }
    }

    private function addViews()
    {
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'seat-timerboard');
    }

    private function addTranslations()
    {
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'seat-timerboard');
    }

    private function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                PullTimerSyncPeers::class,
            ]);
        }
    }

    private function registerListeners()
    {
        if ($this->app->runningInConsole()) {
            Event::listen(CommandFinished::class, EnsureMapDenormalizeIndex::class);
        }
    }

    private function addMigrations()
    {
        // 2026_08_29 shipped in 1.1.10 and fails on a new SeAT, where mapDenormalize only exists
        // after the first SDE import. 2026_10_28 does its job now, so it isn't loaded any more.
        // Installs that already ran it just keep it in their migration history.
        $retired = '2026_08_29_000000_add_item_name_group_id_index_to_map_denormalize_for_timerboard_search.php';

        $this->loadMigrationsFrom(array_values(array_filter(
            glob(__DIR__ . '/database/migrations/*.php') ?: [],
            function ($path) use ($retired) {
                return basename($path) !== $retired;
            }
        )));
    }
}
