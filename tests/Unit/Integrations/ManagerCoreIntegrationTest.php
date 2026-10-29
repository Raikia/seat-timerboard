<?php

namespace Raikia\SeatTimerboard\Tests\Unit\Integrations;

use Raikia\SeatTimerboard\Integrations\ManagerCoreIntegration;
use Raikia\SeatTimerboard\Tests\TestCase;
use ReflectionProperty;

class ManagerCoreIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // The provider registers at boot when the test install has Manager Core.
        $this->setHandlerRegistered(false);
    }

    protected function tearDown(): void
    {
        $this->setHandlerRegistered(false);

        parent::tearDown();
    }

    public function test_mode_defaults_to_auto(): void
    {
        $this->assertSame(ManagerCoreIntegration::MODE_AUTO, ManagerCoreIntegration::mode());

        $this->seedTimerboardSettings(['notification_source' => 'something_else']);

        $this->assertSame(ManagerCoreIntegration::MODE_AUTO, ManagerCoreIntegration::mode());
    }

    public function test_native_listener_stays_on_outside_manager_core_mode(): void
    {
        $this->setHandlerRegistered(true);

        foreach ([ManagerCoreIntegration::MODE_AUTO, ManagerCoreIntegration::MODE_SEAT_NATIVE] as $mode) {
            $this->seedTimerboardSettings(['notification_source' => $mode]);

            $this->assertTrue(ManagerCoreIntegration::nativeListenerActive(), $mode);
        }
    }

    public function test_manager_core_mode_keeps_the_native_listener_until_registered(): void
    {
        $this->seedTimerboardSettings(['notification_source' => ManagerCoreIntegration::MODE_MANAGER_CORE]);

        $this->assertTrue(ManagerCoreIntegration::nativeListenerActive());

        $this->setHandlerRegistered(true);

        $this->assertFalse(ManagerCoreIntegration::nativeListenerActive());
    }

    public function test_seat_native_mode_refuses_manager_core_deliveries(): void
    {
        $this->assertTrue(ManagerCoreIntegration::acceptsManagerCoreDeliveries());

        $this->seedTimerboardSettings(['notification_source' => ManagerCoreIntegration::MODE_SEAT_NATIVE]);

        $this->assertFalse(ManagerCoreIntegration::acceptsManagerCoreDeliveries());
    }

    public function test_nothing_registers_without_manager_core(): void
    {
        if (ManagerCoreIntegration::isAvailable()) {
            $this->markTestSkipped('Manager Core is installed in this SeAT.');
        }

        $this->seedTimerboardSettings(['notification_source' => ManagerCoreIntegration::MODE_MANAGER_CORE]);

        ManagerCoreIntegration::registerNotificationHandler();

        $this->assertTrue(ManagerCoreIntegration::nativeListenerActive());
        $this->assertFalse(ManagerCoreIntegration::changeNeedsRestart(
            ManagerCoreIntegration::MODE_SEAT_NATIVE,
            ManagerCoreIntegration::MODE_AUTO
        ));
    }

    public function test_status_reports_seat_native_as_the_only_source_in_seat_native_mode(): void
    {
        $this->seedTimerboardSettings(['notification_source' => ManagerCoreIntegration::MODE_SEAT_NATIVE]);

        $status = ManagerCoreIntegration::status();

        $this->assertFalse($status['uses_manager_core']);
        $this->assertTrue($status['uses_seat_native']);
    }

    public function test_delivery_label_names_the_manager_core_path(): void
    {
        $this->assertSame('Manager Core fast poll', ManagerCoreIntegration::deliveryLabel('fast_poll'));
        $this->assertSame('Manager Core sweep', ManagerCoreIntegration::deliveryLabel('seat_fallback'));
    }

    private function setHandlerRegistered(bool $registered): void
    {
        $property = new ReflectionProperty(ManagerCoreIntegration::class, 'handlerRegistered');
        $property->setAccessible(true);
        $property->setValue(null, $registered);
    }
}
