<?php

namespace Raikia\SeatTimerboard\Integrations;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Raikia\SeatTimerboard\Handlers\ManagerCoreNotificationHandler;
use Raikia\SeatTimerboard\Models\TimerboardSetting;
use Raikia\SeatTimerboard\Services\NotificationTimerImporter;
use Raikia\SeatTimerboard\TimerboardServiceProvider;

/**
 * The only place Timerboard names Manager Core classes. Manager Core is optional,
 * so every call is guarded and the plugin falls back to SeAT's own notifications.
 */
class ManagerCoreIntegration
{
    public const SETTING = 'notification_source';
    public const LAST_DELIVERY_SETTING = 'manager_core_last_delivery_at';

    public const MODE_AUTO = 'auto';
    public const MODE_MANAGER_CORE = 'manager_core';
    public const MODE_SEAT_NATIVE = 'seat_native';

    public const MODES = [
        self::MODE_AUTO,
        self::MODE_MANAGER_CORE,
        self::MODE_SEAT_NATIVE,
    ];

    public const PLUGIN_KEY = 'seat-timerboard';

    // Manager Core's page for adding directors to its ESI key pool (superusers only).
    public const KEY_POOL_ROUTE = 'manager-core.esi-key-pool.index';

    private static bool $handlerRegistered = false;

    public static function isAvailable(): bool
    {
        return class_exists('\ManagerCore\Services\ESI\EsiNotificationRegistry');
    }

    public static function mode(): string
    {
        try {
            $mode = optional(TimerboardSetting::find(self::SETTING))->value;
        } catch (\Throwable $exception) {
            // The provider boots before SeAT has run this plugin's migrations on a fresh install.
            return self::MODE_AUTO;
        }

        return in_array($mode, self::MODES, true) ? $mode : self::MODE_AUTO;
    }

    public static function registerNotificationHandler(): void
    {
        if (!self::isAvailable() || self::mode() === self::MODE_SEAT_NATIVE) {
            return;
        }

        try {
            // Manager Core binds the registry under its class name; a string with a
            // leading backslash would build a throwaway instance instead.
            app(\ManagerCore\Services\ESI\EsiNotificationRegistry::class)->register(
                NotificationTimerImporter::SUPPORTED_TYPES,
                ManagerCoreNotificationHandler::class,
                self::PLUGIN_KEY,
                // The importer can call ESI for a structure name, which Manager Core's
                // poll job shouldn't have to wait on.
                ['queued' => true]
            );

            self::$handlerRegistered = true;
        } catch (\Throwable $exception) {
            Log::warning('Timerboard could not register with Manager Core: ' . $exception->getMessage());
        }
    }

    /**
     * Lists Timerboard in Manager Core's plugin registry, including on builds that
     * don't have it among their compatible plugins.
     */
    public static function registerWithBridge(): void
    {
        if (!class_exists('\ManagerCore\Services\PluginBridge')) {
            return;
        }

        try {
            $bridge = app(\ManagerCore\Services\PluginBridge::class);

            if (!method_exists($bridge, 'registerSelf')) {
                return;
            }

            $bridge->registerSelf(self::PLUGIN_KEY, [
                'name' => 'SeAT Timerboard',
                'class' => TimerboardServiceProvider::class,
                'package' => 'raikia/seat-timerboard',
                'icon' => 'fa-clock',
                'subscription_name' => null,
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Timerboard could not register with the Manager Core plugin bridge: ' . $exception->getMessage());
        }
    }

    public static function acceptsManagerCoreDeliveries(): bool
    {
        return self::mode() !== self::MODE_SEAT_NATIVE;
    }

    public static function nativeListenerActive(): bool
    {
        if (self::mode() !== self::MODE_MANAGER_CORE) {
            return true;
        }

        // Keep listening until this process has registered with Manager Core, so
        // switching to Manager Core mode can't leave a gap before the workers restart.
        return !self::$handlerRegistered;
    }

    /**
     * Registration happens when the plugin boots, so turning Manager Core on only
     * reaches the queue workers once they restart.
     */
    public static function changeNeedsRestart(string $previousMode, string $newMode): bool
    {
        return self::isAvailable()
            && $previousMode === self::MODE_SEAT_NATIVE
            && $newMode !== self::MODE_SEAT_NATIVE;
    }

    public static function recordDelivery(): void
    {
        TimerboardSetting::updateOrCreate(
            ['setting' => self::LAST_DELIVERY_SETTING],
            ['value' => Carbon::now('UTC')->toDateTimeString()]
        );
    }

    public static function deliveryLabel(?string $source): string
    {
        return $source === 'seat_fallback' ? 'Manager Core sweep' : 'Manager Core fast poll';
    }

    public static function status(): array
    {
        $mode = self::mode();
        $available = self::isAvailable();
        $lastDelivery = optional(TimerboardSetting::find(self::LAST_DELIVERY_SETTING))->value;

        return [
            'available' => $available,
            'mode' => $mode,
            'uses_manager_core' => $available && $mode !== self::MODE_SEAT_NATIVE,
            'uses_seat_native' => !$available || $mode !== self::MODE_MANAGER_CORE,
            'last_delivery_at' => $lastDelivery ? Carbon::parse($lastDelivery, 'UTC') : null,
            'key_pool_url' => $available && Route::has(self::KEY_POOL_ROUTE) ? route(self::KEY_POOL_ROUTE) : null,
        ];
    }
}
