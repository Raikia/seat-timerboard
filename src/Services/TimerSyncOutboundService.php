<?php

namespace Raikia\SeatTimerboard\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Raikia\SeatTimerboard\Jobs\ProcessTimerSyncDelete;
use Raikia\SeatTimerboard\Jobs\ProcessTimerSyncUpsert;
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

        TimerSyncPeer::query()
            ->where('is_enabled', true)
            ->get()
            ->each(function (TimerSyncPeer $peer) use ($timer) {
                ProcessTimerSyncUpsert::dispatch($timer->id, $peer->id);
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

        foreach ($deliveries as $delivery) {
            $peer = $delivery->peer;

            if (! $peer || ! $peer->is_enabled || ! $peer->allow_remote_delete) {
                $delivery->delete();
                continue;
            }

            ProcessTimerSyncDelete::dispatch($delivery->id, $localTimerId);
        }
    }

    public function syncTimerToPeer(int $timerId, int $peerId): void
    {
        $timer = Timer::with('tags')->find($timerId);

        if (! $timer || $timer->isRemoteSynced()) {
            return;
        }

        $peer = TimerSyncPeer::query()
            ->whereKey($peerId)
            ->where('is_enabled', true)
            ->first();

        if (! $peer) {
            return;
        }

        $tagIds = $timer->tags->pluck('id')->map(fn ($id) => (int) $id)->all();
        $delivery = TimerSyncDelivery::query()
            ->where('local_timer_id', $timer->id)
            ->where('peer_id', $peer->id)
            ->first();

        if (empty($tagIds) || ! $this->shouldSyncToPeer($peer, $tagIds)) {
            if ($delivery) {
                $this->removeDeliveryForPeer($peer, $delivery, $timer->id);
            }

            return;
        }

        $payload = $this->payloadFactory->build($timer);
        $response = $this->request($peer)
            ->post($peer->base_url . '/api/v2/timerboard-sync/timers', $payload);

        if (! $response->successful()) {
            Log::warning('Timerboard sync upsert failed.', [
                'timer_id' => $timer->id,
                'peer_id' => $peer->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \RuntimeException(sprintf(
                'Timerboard sync upsert failed for timer %d to peer %d with status %d.',
                $timer->id,
                $peer->id,
                $response->status()
            ));
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
    }

    public function deleteDeliveryFromPeer(int $deliveryId, int $localTimerId): void
    {
        $delivery = TimerSyncDelivery::with('peer')->find($deliveryId);

        if (! $delivery) {
            return;
        }

        $peer = $delivery->peer;

        $this->removeDeliveryForPeer($peer, $delivery, $localTimerId);
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

    private function removeDeliveryForPeer(?TimerSyncPeer $peer, TimerSyncDelivery $delivery, int $localTimerId): void
    {
        if (! $peer || ! $peer->is_enabled || ! $peer->allow_remote_delete) {
            $delivery->delete();

            return;
        }

        $payload = [
            'source_instance_uuid' => $this->identity->getUuid(),
            'origin_timer_id' => $localTimerId,
        ];

        $response = $this->request($peer)
            ->delete($peer->base_url . '/api/v2/timerboard-sync/timers', $payload);

        if (! $response->successful()) {
            Log::warning('Timerboard sync delete failed.', [
                'local_timer_id' => $localTimerId,
                'peer_id' => $peer->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \RuntimeException(sprintf(
                'Timerboard sync delete failed for timer %d to peer %d with status %d.',
                $localTimerId,
                $peer->id,
                $response->status()
            ));
        }

        $delivery->delete();
    }
}
