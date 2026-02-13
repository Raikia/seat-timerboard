<?php

namespace Raikia\SeatTimerboard\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Seat\Notifications\Services\Discord\Messages\DiscordMessage;
use Raikia\SeatTimerboard\Models\Timer;

class NewTimer extends Notification implements ShouldQueue
{
    use Queueable;

    protected $timer;

    public function __construct(Timer $timer)
    {
        $this->timer = $timer;
    }

    public function via($notifiable)
    {
        return ['discord'];
    }

    public function toDiscord($notifiable)
    {
        $imageUrl = $this->timer->getStructureImage();

        return (new DiscordMessage)
            ->embed(function ($embed) use ($imageUrl) {
                $embed->title('New Timer Created');
                $embed->thumb($imageUrl);
                $embed->color(0x00FF00); // Green
                
                // Safe URL encoding
                $systemUrl = 'http://evemaps.dotlan.net/system/' . rawurlencode(str_replace(' ', '_', $this->timer->mapDenormalize->solarSystemID==null?$this->timer->mapDenormalize->itemName:$this->timer->mapDenormalize->system->itemName));
                $regionName = $this->timer->mapDenormalize->region->itemName ?? 'Unknown';
                
                $embed->field('Location', sprintf("[%s](%s)\n(%s)", 
                        $this->timer->system,
                        $systemUrl,
                        $regionName), true);
                
                $embed->field('Structure Type', $this->timer->structure_type, true);
                $embed->field('Structure Name', $this->timer->structure_name ?: 'N/A', true);
                
                $ownerUrl = "https://evemaps.dotlan.net/corp/" . rawurlencode(str_replace(' ', '_', $this->timer->owner_corporation));
                $embed->field('Owner', "[{$this->timer->owner_corporation}]({$ownerUrl})", true);
                
                if (!empty($this->timer->attacker_corporation)) {
                    $attackerUrl = "https://evemaps.dotlan.net/corp/" . rawurlencode(str_replace(' ', '_', $this->timer->attacker_corporation));
                    $embed->field('Attacker', "[{$this->timer->attacker_corporation}]({$attackerUrl})", true);
                }

                $embed->field('EVE Time', $this->timer->eve_time->format('Y-m-d H:i:s') . "\n(" . $this->timer->eve_time->diffForHumans() . ")", true);
                $embed->field('Created By', $this->timer->user->name, true);
                $embed->field('Role Access', $this->timer->role ? $this->timer->role->title : 'Public', true);
            });
    }
}
