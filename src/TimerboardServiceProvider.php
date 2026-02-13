<?php

namespace Raikia\SeatTimerboard;

use Seat\Services\AbstractSeatPlugin;

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
}
