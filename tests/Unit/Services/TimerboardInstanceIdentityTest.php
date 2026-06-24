<?php

namespace Raikia\SeatTimerboard\Tests\Unit\Services;

use Illuminate\Support\Str;
use Raikia\SeatTimerboard\Models\TimerboardSetting;
use Raikia\SeatTimerboard\Services\TimerboardInstanceIdentity;
use Raikia\SeatTimerboard\Tests\TestCase;

class TimerboardInstanceIdentityTest extends TestCase
{
    public function test_it_persists_and_reuses_the_instance_uuid(): void
    {
        $service = new TimerboardInstanceIdentity();

        $first = $service->getUuid();
        $second = $service->getUuid();

        $this->assertTrue(Str::isUuid($first));
        $this->assertSame($first, $second);
        $this->assertSame($first, TimerboardSetting::find('sync_instance_uuid')->value);
    }
}
