<?php

namespace Raikia\SeatTimerboard\Notifications\Discord;

use Raikia\SeatTimerboard\Models\Timer;
use Raikia\SeatTimerboard\Notifications\Formats\BuildsTimerNotificationContent;
use Seat\Notifications\Notifications\AbstractDiscordNotification;
use Seat\Notifications\Services\Discord\Messages\DiscordEmbed;
use Seat\Notifications\Services\Discord\Messages\DiscordEmbedField;
use Seat\Notifications\Services\Discord\Messages\DiscordMessage;

class NewTimer extends AbstractDiscordNotification
{
    use BuildsTimerNotificationContent;

    private Timer $timer;

    public function __construct(Timer $timer)
    {
        $this->timer = $timer->loadMissing(['tags', 'user', 'role', 'mapDenormalize.region', 'mapDenormalize.system']);
    }

    protected function populateMessage(DiscordMessage $message, $notifiable)
    {
        $epoch = $this->eveEpoch($this->timer);
        $tags = collect($this->tagNames($this->timer));
        $attackerUrl = $this->attackerUrl($this->timer);

        $message
            ->content('A new timer was created!')
            ->embed(function (DiscordEmbed $embed) use ($epoch, $tags, $attackerUrl) {
                $embed->timestamp(carbon());
                $embed->title('New Timer Created');
                $embed->thumb($this->timer->getStructureImage());
                $embed->color($this->discordColorForTimer($this->timer));

                $embed->field(function (DiscordEmbedField $field) {
                    $field->name('Location')
                        ->value($this->locationField($this->timer));
                });

                $embed->field(function (DiscordEmbedField $field) {
                    $field->name('Structure Type')
                        ->value($this->timer->structure_type);
                });

                $embed->field(function (DiscordEmbedField $field) {
                    $field->name('Structure Name')
                        ->value($this->timer->structure_name ?: 'N/A');
                });

                $embed->field(function (DiscordEmbedField $field) {
                    $field->name('Owner')
                        ->value(sprintf('[%s](%s)', $this->timer->owner_corporation, $this->ownerUrl($this->timer)));
                });

                if ($attackerUrl) {
                    $embed->field(function (DiscordEmbedField $field) use ($attackerUrl) {
                        $field->name('Attacker')
                            ->value(sprintf('[%s](%s)', $this->timer->attacker_corporation, $attackerUrl));
                    });
                }

                $embed->field(function (DiscordEmbedField $field) use ($tags) {
                    $field->name('Tags')
                        ->value($tags->isNotEmpty() ? $tags->implode(', ') : 'None');
                });

                $embed->field(function (DiscordEmbedField $field) {
                    $field->name('EVE Time')
                        ->value($this->formattedEveTime($this->timer));
                });

                $embed->field(function (DiscordEmbedField $field) use ($epoch) {
                    $field->name('Local Time')
                        ->value("<t:{$epoch}:f>");
                });

                $embed->field(function (DiscordEmbedField $field) use ($epoch) {
                    $field->name('Countdown')
                        ->value("<t:{$epoch}:R>");
                });

                $embed->field(function (DiscordEmbedField $field) {
                    $field->name('Created By')
                        ->value(optional($this->timer->user)->name ?? 'Unknown');
                });
            });
    }
}
