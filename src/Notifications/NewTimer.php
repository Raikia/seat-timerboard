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
        $structureId = $this->getStructureRecallId($this->timer->structure_type);
        $imageUrl = "https://images.evetech.net/types/{$structureId}/render?size=64";

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

    private function getStructureRecallId($type)
    {
        $mapping = [
            'Ansiblex' => 35841,
            'Astrahus' => 35832,
            'Athanor' => 35835,
            'Azbel' => 35826,
            'POCO' => 2233,
            'Fortizar' => 35833,
            'Keepstar' => 35834,
            'Metenox' => 81826,
            'Pharolux' => 35840,
            'POS' => 16213,
            'Raitaru' => 35825,
            'Skyhook' => 81824, // Using Magmatic Skyhook as generic
            'Sotiyo' => 35827,
            'Tatara' => 35836,
            'Tenebrex' => 37534,
        ];

        return $mapping[$type] ?? 35832; // Default to Astrahus if unknown
    }
}
