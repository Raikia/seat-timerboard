<?php

namespace Raikia\SeatTimerboard\Tests\Unit\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Raikia\SeatTimerboard\Jobs\ProcessTimerSyncDelete;
use Raikia\SeatTimerboard\Jobs\ProcessTimerSyncUpsert;
use Raikia\SeatTimerboard\Models\TimerSyncDelivery;
use Raikia\SeatTimerboard\Services\TimerSyncOutboundService;
use Raikia\SeatTimerboard\Tests\TestCase;

class TimerSyncOutboundServiceTest extends TestCase
{
    public function test_sync_timer_dispatches_peer_jobs_for_enabled_peers(): void
    {
        Queue::fake();

        $timer = $this->createTimerWithTags(['Friendly'], [
            'structure_name' => 'Sync Fort',
        ]);

        $enabledPeer = $this->createSyncPeer(['is_enabled' => true, 'sync_tag_ids' => [1]]);
        $disabledPeer = $this->createSyncPeer([
            'instance_uuid' => '22222222-2222-2222-2222-222222222222',
            'is_enabled' => false,
            'sync_tag_ids' => [1],
        ]);

        app(TimerSyncOutboundService::class)->syncTimer($timer);

        Queue::assertPushed(ProcessTimerSyncUpsert::class, function ($job) use ($timer, $enabledPeer) {
            return $this->readPrivateProperty($job, 'timerId') === $timer->id
                && $this->readPrivateProperty($job, 'peerId') === $enabledPeer->id;
        });
        Queue::assertNotPushed(ProcessTimerSyncUpsert::class, function ($job) use ($disabledPeer) {
            return $this->readPrivateProperty($job, 'peerId') === $disabledPeer->id;
        });
    }

    public function test_sync_timer_to_peer_records_a_delivery_on_success(): void
    {
        Http::fake([
            'https://peer.example.test/api/v2/timerboard-sync/timers' => Http::response(['status' => 'created'], 201),
        ]);

        $timer = $this->createTimerWithTags(['Friendly'], [
            'structure_name' => 'Sync Fort',
        ]);
        $peer = $this->createSyncPeer(['sync_tag_ids' => [1]]);

        app(TimerSyncOutboundService::class)->syncTimerToPeer($timer->id, $peer->id);

        $this->assertDatabaseHas('seat_timerboard_sync_deliveries', [
            'local_timer_id' => $timer->id,
            'peer_id' => $peer->id,
        ]);
    }

    public function test_sync_timer_to_peer_retracts_existing_delivery_when_tags_no_longer_match(): void
    {
        Http::fake([
            'https://peer.example.test/api/v2/timerboard-sync/timers' => Http::response(['status' => 'deleted'], 200),
        ]);

        $timer = $this->createTimerWithTags(['Hostile'], [
            'structure_name' => 'Sync Fort',
        ]);
        $peer = $this->createSyncPeer(['sync_tag_ids' => [999], 'allow_remote_delete' => true]);
        $delivery = TimerSyncDelivery::create([
            'local_timer_id' => $timer->id,
            'peer_id' => $peer->id,
        ]);

        app(TimerSyncOutboundService::class)->syncTimerToPeer($timer->id, $peer->id);

        $this->assertNull(TimerSyncDelivery::find($delivery->id));
        Http::assertSent(function ($request) {
            return $request->method() === 'DELETE'
                && $request->url() === 'https://peer.example.test/api/v2/timerboard-sync/timers';
        });
    }

    public function test_delete_local_timer_dispatches_delete_jobs_and_cleans_non_remote_delete_peers(): void
    {
        Queue::fake();

        $timer = $this->createTimerWithTags(['Friendly'], [
            'structure_name' => 'Sync Fort',
        ]);
        $deletePeer = $this->createSyncPeer(['sync_tag_ids' => [1], 'allow_remote_delete' => true]);
        $keepPeer = $this->createSyncPeer([
            'instance_uuid' => '33333333-3333-3333-3333-333333333333',
            'sync_tag_ids' => [1],
            'allow_remote_delete' => false,
        ]);

        $deleteDelivery = TimerSyncDelivery::create([
            'local_timer_id' => $timer->id,
            'peer_id' => $deletePeer->id,
        ]);
        $keepDelivery = TimerSyncDelivery::create([
            'local_timer_id' => $timer->id,
            'peer_id' => $keepPeer->id,
        ]);

        app(TimerSyncOutboundService::class)->deleteLocalTimer([
            'id' => $timer->id,
            'is_remote_synced' => false,
        ]);

        Queue::assertPushed(ProcessTimerSyncDelete::class, function ($job) use ($deleteDelivery, $timer) {
            return $this->readPrivateProperty($job, 'deliveryId') === $deleteDelivery->id
                && $this->readPrivateProperty($job, 'localTimerId') === $timer->id;
        });

        $this->assertNull(TimerSyncDelivery::find($keepDelivery->id));
        $this->assertNotNull(TimerSyncDelivery::find($deleteDelivery->id));
    }

}
