<?php

namespace Raikia\SeatTimerboard\Tests\Unit\Services;

use Carbon\Carbon;
use Raikia\SeatTimerboard\Models\Timer;
use Raikia\SeatTimerboard\Services\TimerSyncInboundService;
use Raikia\SeatTimerboard\Tests\TestCase;

class TimerSyncInboundServiceTest extends TestCase
{
    public function test_it_creates_a_remote_synced_timer_from_peer_payload(): void
    {
        $peer = $this->createSyncPeer([
            'name' => 'Friendly Seat',
            'base_url' => 'https://seat.example.test',
        ]);

        $service = new TimerSyncInboundService();

        [$timer, $isNew] = $service->upsertFromPeer($peer, [
            'source_instance_uuid' => '11111111-1111-1111-1111-111111111111',
            'source_instance_name' => 'Friendly Seat',
            'origin_timer_id' => 55,
            'timer' => [
                'system' => 'Jita',
                'structure_type' => 'Fortizar',
                'structure_name' => 'Friendly Fort',
                'notes' => 'Bring logi',
                'owner_corporation' => 'Friendly Corp',
                'attacker_corporation' => 'Bad Corp',
                'eve_time' => '2026-06-25T01:02:03+00:00',
                'tags' => [
                    ['name' => 'Friendly', 'color' => '#28a745'],
                    ['name' => 'Armor', 'color' => '#f39c12'],
                ],
            ],
        ]);

        $this->assertTrue($isNew);
        $this->assertSame('Jita', $timer->system);
        $this->assertSame('Friendly Seat', $timer->sync_source_name);
        $this->assertSame('11111111-1111-1111-1111-111111111111', $timer->sync_origin_instance_uuid);
        $this->assertSame(55, $timer->sync_origin_timer_id);
        $this->assertNull($timer->user_id);
        $this->assertSame(Carbon::parse('2026-06-25T01:02:03+00:00')->timestamp, $timer->eve_time->timestamp);
        $this->assertStringContainsString('Synced from remote SeAT: Friendly Seat', $timer->notes);
        $this->assertStringContainsString('Bring logi', $timer->notes);

        $this->assertEqualsCanonicalizing(
            ['Friendly', 'Armor', 'Remote Synced'],
            $timer->tags()->pluck('name')->all()
        );
    }

    public function test_it_updates_an_existing_remote_timer_in_place(): void
    {
        $peer = $this->createSyncPeer([
            'name' => 'Friendly Seat',
            'base_url' => 'https://seat.example.test',
        ]);

        $timer = Timer::create([
            'system' => 'Old',
            'structure_type' => 'Astrahus',
            'structure_name' => 'Old Name',
            'owner_corporation' => 'Old Corp',
            'eve_time' => now()->subDay(),
            'sync_origin_instance_uuid' => '11111111-1111-1111-1111-111111111111',
            'sync_origin_timer_id' => 55,
            'sync_source_name' => 'Friendly Seat',
        ]);

        $service = new TimerSyncInboundService();

        [$updated, $isNew] = $service->upsertFromPeer($peer, [
            'source_instance_uuid' => '11111111-1111-1111-1111-111111111111',
            'source_instance_name' => 'Friendly Seat',
            'origin_timer_id' => 55,
            'timer' => [
                'system' => 'Amarr',
                'structure_type' => 'Keepstar',
                'structure_name' => 'New Name',
                'notes' => 'Updated note',
                'owner_corporation' => 'New Corp',
                'attacker_corporation' => null,
                'eve_time' => '2026-06-26T04:05:06+00:00',
                'tags' => [
                    ['name' => 'Friendly', 'color' => '#28a745'],
                ],
            ],
        ]);

        $this->assertFalse($isNew);
        $this->assertSame($timer->id, $updated->id);
        $this->assertSame('Amarr', $updated->fresh()->system);
        $this->assertEqualsCanonicalizing(
            ['Friendly', 'Remote Synced'],
            $updated->fresh()->tags()->pluck('name')->all()
        );
    }

    public function test_it_deletes_a_remote_timer_when_requested_by_peer(): void
    {
        $peer = $this->createSyncPeer([
            'name' => 'Friendly Seat',
            'base_url' => 'https://seat.example.test',
        ]);

        $timer = Timer::create([
            'system' => 'Jita',
            'structure_type' => 'Fortizar',
            'structure_name' => 'Friendly Fort',
            'owner_corporation' => 'Friendly Corp',
            'eve_time' => now()->addHour(),
            'sync_origin_instance_uuid' => '11111111-1111-1111-1111-111111111111',
            'sync_origin_timer_id' => 99,
            'sync_source_name' => 'Friendly Seat',
        ]);

        $deleted = (new TimerSyncInboundService())->deleteFromPeer(
            $peer,
            '11111111-1111-1111-1111-111111111111',
            99
        );

        $this->assertTrue($deleted);
        $this->assertNull(Timer::find($timer->id));
    }
}
