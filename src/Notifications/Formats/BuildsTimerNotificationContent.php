<?php

namespace Raikia\SeatTimerboard\Notifications\Formats;

use Raikia\SeatTimerboard\Models\Timer;

trait BuildsTimerNotificationContent
{
    protected function eveEpoch(Timer $timer): int
    {
        return $timer->eve_time->timestamp;
    }

    protected function tagNames(Timer $timer): array
    {
        return $timer->tags->pluck('name')
            ->filter()
            ->values()
            ->all();
    }

    protected function formattedEveTime(Timer $timer): string
    {
        return $timer->eve_time->copy()->setTimezone('UTC')->format('Y-m-d H:i:s');
    }

    protected function formattedLocalTime(Timer $timer): string
    {
        return $timer->eve_time->copy()->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s T');
    }

    protected function formattedCountdown(Timer $timer): string
    {
        return now()->diffForHumans($timer->eve_time, [
            'parts' => 3,
            'short' => true,
            'syntax' => \Carbon\CarbonInterface::DIFF_RELATIVE_TO_NOW,
        ]);
    }

    protected function discordColorForTimer(Timer $timer): int
    {
        $normalized = array_map('strtolower', $this->tagNames($timer));

        if (in_array('hostile', $normalized, true)) {
            return 0xDC3545;
        }

        if (in_array('friendly', $normalized, true)) {
            return 0x28A745;
        }

        return 0x6C757D;
    }

    protected function slackColorForTimer(Timer $timer): string
    {
        $normalized = array_map('strtolower', $this->tagNames($timer));

        if (in_array('hostile', $normalized, true)) {
            return 'danger';
        }

        if (in_array('friendly', $normalized, true)) {
            return 'good';
        }

        return '#6c757d';
    }

    protected function locationField(Timer $timer): string
    {
        $dotlanUrl = $timer->getDotlanMapUrl();
        $regionName = $timer->getRegionName();

        return $dotlanUrl
            ? sprintf('%s (%s) - %s', $timer->system, $regionName, $dotlanUrl)
            : sprintf('%s (%s)', $timer->system, $regionName);
    }

    protected function ownerUrl(Timer $timer): string
    {
        return 'https://evemaps.dotlan.net/corp/' . rawurlencode(str_replace(' ', '_', $timer->owner_corporation));
    }

    protected function attackerUrl(Timer $timer): ?string
    {
        if (blank($timer->attacker_corporation)) {
            return null;
        }

        return 'https://evemaps.dotlan.net/corp/' . rawurlencode(str_replace(' ', '_', $timer->attacker_corporation));
    }
}
