<?php

namespace Raikia\SeatTimerboard\Notifications\Slack;

use Illuminate\Notifications\Messages\SlackMessage;
use Raikia\SeatTimerboard\Models\Timer;
use Raikia\SeatTimerboard\Notifications\Formats\BuildsTimerNotificationContent;
use Seat\Notifications\Notifications\AbstractSlackNotification;

class NewTimer extends AbstractSlackNotification
{
    use BuildsTimerNotificationContent;

    private Timer $timer;

    public function __construct(Timer $timer)
    {
        $this->timer = $timer->loadMissing(['tags', 'user', 'role', 'mapDenormalize.region', 'mapDenormalize.system']);
    }

    protected function populateMessage(SlackMessage $message, $notifiable)
    {
        $tags = collect($this->tagNames($this->timer));
        $attackerUrl = $this->attackerUrl($this->timer);

        $message
            ->content('A new timer was created!')
            ->from('SeAT Timerboard')
            ->attachment(function ($attachment) use ($tags, $attackerUrl) {
                $attachment->title('Timer Details')
                    ->color($this->slackColorForTimer($this->timer))
                    ->fields([
                        'Location' => $this->locationField($this->timer),
                        'Structure Type' => $this->timer->structure_type,
                        'Structure Name' => $this->timer->structure_name ?: 'N/A',
                        'Owner' => sprintf('%s (%s)', $this->timer->owner_corporation, $this->ownerUrl($this->timer)),
                        'Attacker' => $attackerUrl
                            ? sprintf('%s (%s)', $this->timer->attacker_corporation, $attackerUrl)
                            : 'N/A',
                        'Tags' => $tags->isNotEmpty() ? $tags->implode(', ') : 'None',
                        'EVE Time' => $this->formattedEveTime($this->timer),
                        'Local Time' => $this->formattedLocalTime($this->timer),
                        'Countdown' => $this->formattedCountdown($this->timer),
                        'Created By' => optional($this->timer->user)->name ?? 'Unknown',
                    ]);
            });
    }
}
