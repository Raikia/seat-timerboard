<?php

namespace Raikia\SeatTimerboard;

use Raikia\SeatTimerboard\Console\Commands\PullTimerSyncPeers;
use Raikia\SeatTimerboard\database\seeds\TimerboardSeeder;
use Raikia\SeatTimerboard\Database\Seeds\ScheduleSeeder;
use Raikia\SeatTimerboard\Integrations\ManagerCoreIntegration;
use Raikia\SeatTimerboard\Observers\CharacterNotificationObserver;
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
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->registerPermissions(__DIR__ . '/Config/timerboard.permissions.php', 'seat-timerboard');
        $this->registerCommands();

        CharacterNotification::observe(CharacterNotificationObserver::class);
        ManagerCoreIntegration::registerNotificationHandler();
        ManagerCoreIntegration::registerWithBridge();
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
}
