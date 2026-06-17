<?php

namespace Raikia\SeatTimerboard\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Raikia\SeatTimerboard\Models\Timer;
use Raikia\SeatTimerboard\Models\TimerSyncDelivery;
use Raikia\SeatTimerboard\Models\TimerSyncPeer;

class TimerSyncOutboundService
{
    public function __construct(
        private TimerSyncPayloadFactory $payloadFactory,
        private TimerboardInstanceIdentity $identity
    ) {
    }

    public function syncTimer(Timer $timer): void
    {
        if ($timer->isRemoteSynced()) {
            return;
        }

        $timer->loadMissing('tags');
        $tagIds = $timer->tags->pluck('id')->map(fn ($id) => (int) $id)->all();

        if (empty($tagIds)) {
            return;
        }

        $payload = $this->payloadFactory->build($timer);

        TimerSyncPeer::query()
            ->where('is_enabled', true)
            ->get()
            ->each(function (TimerSyncPeer $peer) use ($tagIds, $payload, $timer) {
                if (! $this->shouldSyncToPeer($peer, $tagIds)) {
                    return;
                }

                try {
                    $response = $this->request($peer)
                        ->post($peer->base_url . '/api/v2/timerboard-sync/timers', $payload);

                    if (! $response->successful()) {
                        Log::warning('Timerboard sync upsert failed.', [
                            'timer_id' => $timer->id,
                            'peer_id' => $peer->id,
                            'status' => $response->status(),
                            'body' => $response->body(),
                        ]);

                        return;
                    }

                    TimerSyncDelivery::updateOrCreate(
                        [
                            'local_timer_id' => $timer->id,
                            'peer_id' => $peer->id,
                        ],
                        [
                            'last_synced_at' => Carbon::now(),
                        ]
                    );
                } catch (\Throwable $exception) {
                    Log::error('Timerboard sync upsert exception: ' . $exception->getMessage(), [
                        'timer_id' => $timer->id,
                        'peer_id' => $peer->id,
                    ]);
                }
            });
    }

    public function deleteLocalTimer(array $snapshot): void
    {
        $localTimerId = (int) ($snapshot['id'] ?? 0);

        if ($localTimerId === 0) {
            return;
        }

        if (! empty($snapshot['is_remote_synced'])) {
            TimerSyncDelivery::where('local_timer_id', $localTimerId)->delete();

            return;
        }

        $deliveries = TimerSyncDelivery::with('peer')
            ->where('local_timer_id', $localTimerId)
            ->get();

        $payload = [
            'source_instance_uuid' => $this->identity->getUuid(),
            'origin_timer_id' => $localTimerId,
        ];

        foreach ($deliveries as $delivery) {
            $peer = $delivery->peer;

            if (! $peer || ! $peer->is_enabled || ! $peer->allow_remote_delete) {
                continue;
            }

            try {
                $response = $this->request($peer)
                    ->delete($peer->base_url . '/api/v2/timerboard-sync/timers', $payload);

                if (! $response->successful()) {
                    Log::warning('Timerboard sync delete failed.', [
                        'local_timer_id' => $localTimerId,
                        'peer_id' => $peer->id,
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);
                }
            } catch (\Throwable $exception) {
                Log::error('Timerboard sync delete exception: ' . $exception->getMessage(), [
                    'local_timer_id' => $localTimerId,
                    'peer_id' => $peer->id,
                ]);
            }
        }

        TimerSyncDelivery::where('local_timer_id', $localTimerId)->delete();
    }

    private function shouldSyncToPeer(TimerSyncPeer $peer, array $tagIds): bool
    {
        if (empty($peer->sync_tag_ids)) {
            return false;
        }

        return ! empty(array_intersect($tagIds, array_map('intval', $peer->sync_tag_ids)));
    }

    private function request(TimerSyncPeer $peer)
    {
        return Http::acceptJson()
            ->asJson()
            ->timeout(15)
            ->withHeaders([
                'X-Token' => $peer->api_token,
            ]);
    }
}
