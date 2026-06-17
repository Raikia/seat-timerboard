<?php

namespace Raikia\SeatTimerboard\Services;

use Illuminate\Support\Str;
use Raikia\SeatTimerboard\Models\TimerboardSetting;

class TimerboardInstanceIdentity
{
    public function getUuid(): string
    {
        $setting = TimerboardSetting::find('sync_instance_uuid');

        if ($setting && filled($setting->value)) {
            return $setting->value;
        }

        $uuid = (string) Str::uuid();

        TimerboardSetting::updateOrCreate(
            ['setting' => 'sync_instance_uuid'],
            ['value' => $uuid]
        );

        return $uuid;
    }

    public function getName(): string
    {
        return config('app.name', 'SeAT');
    }

    public function getBaseUrl(): string
    {
        return rtrim((string) config('app.url', url('/')), '/');
    }
}
