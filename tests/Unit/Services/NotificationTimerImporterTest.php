<?php

namespace Raikia\SeatTimerboard\Tests\Unit\Services;

use Mockery;
use Raikia\SeatTimerboard\Models\Timer;
use Raikia\SeatTimerboard\Services\NotificationTimerImporter;
use Raikia\SeatTimerboard\Services\TimerMutationDispatcher;
use Raikia\SeatTimerboard\Tests\TestCase;
use Seat\Eveapi\Models\Character\CharacterAffiliation;
use Seat\Eveapi\Models\Character\CharacterInfo;
use Seat\Eveapi\Models\Character\CharacterNotification;
use Seat\Eveapi\Models\Corporation\CorporationInfo;
use Seat\Eveapi\Models\RefreshToken;
use Seat\Eveapi\Models\Universe\UniverseName;

class NotificationTimerImporterTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function test_it_imports_a_tracked_orbital_reinforced_notification(): void
    {
        $this->seedTimerboardSettings([
            'tracked_corporation_ids' => [98765],
            'default_timer_role' => 7,
        ]);
        $this->seedTrackedCharacterContext(includeAlliance: true);
        $this->seedReferenceLocationData();
        $this->mockSavedDispatch();

        $notification = $this->createNotification([
            'text' => implode("\n", [
                'aggressorAllianceID: 12345',
                'aggressorCorpID: 55555',
                'aggressorID: 77777',
                'planetID: 40121487',
                'typeID: 2233',
                'reinforceExitTime: 134259524910000000',
                'solarSystemID: 30000142',
            ]),
        ]);

        $timer = app(NotificationTimerImporter::class)->import($notification);

        $this->assertNotNull($timer);
        $this->assertSame('Jita IV', $timer->system);
        $this->assertSame('POCO', $timer->structure_type);
        $this->assertSame('Friendly Corp', $timer->owner_corporation);
        $this->assertSame('Bad Corp', $timer->attacker_corporation);
        $this->assertSame(1, $timer->user_id);
        $this->assertSame(7, $timer->role_id);
        $this->assertEqualsCanonicalizing(
            ['Auto Imported', 'Friendly', 'Reinforced'],
            $timer->tags()->pluck('name')->all()
        );
    }

    public function test_it_returns_existing_timer_for_duplicate_import_fingerprint(): void
    {
        $this->seedTimerboardSettings([
            'tracked_corporation_ids' => [98765],
        ]);
        $this->seedTrackedCharacterContext();
        $this->seedReferenceLocationData();
        $this->mockSavedDispatch();

        $payload = [
            'character_id' => 90000001,
            'notification_id' => 2000000001,
            'type' => 'OrbitalReinforced',
            'sender_id' => 1000125,
            'sender_type' => 'corporation',
            'timestamp' => now(),
            'is_read' => false,
            'text' => implode("\n", [
                'planetID: 40121487',
                'typeID: 2233',
                'reinforceExitTime: 134259524910000000',
                'solarSystemID: 30000142',
            ]),
        ];

        $first = app(NotificationTimerImporter::class)->import(
            CharacterNotification::withoutEvents(fn () => CharacterNotification::create($payload))
        );
        $second = app(NotificationTimerImporter::class)->import(
            CharacterNotification::withoutEvents(fn () => CharacterNotification::create(array_merge($payload, [
                'notification_id' => 2000000002,
            ])))
        );

        $this->assertSame($first->id, $second->id);
        $this->assertSame(1, Timer::count());
    }

    public function test_it_imports_a_mercenary_den_reinforcement_timer(): void
    {
        $this->seedTimerboardSettings([
            'tracked_corporation_ids' => [98765],
        ]);
        $this->seedTrackedCharacterContext();
        $this->seedReferenceLocationData();
        $this->mockSavedDispatch();

        $notification = $this->createNotification([
            'type' => 'MercenaryDenReinforced',
            'text' => implode("\n", [
                'aggressorAllianceName: Calculated Disorder',
                'aggressorCharacterID: 2114190616',
                'aggressorCorporationName: Sith Navy',
                'itemID: "&amp;id001 1054221260563"',
                'mercenaryDenShowInfoData:',
                '- showinfo',
                '- 85230',
                '- "*id001"',
                'planetID: 40121487',
                'planetShowInfoData:',
                '- showinfo',
                '- 11',
                '- 40121487',
                'solarsystemID: 30000142',
                'timestampEntered: 134267066949210942',
                'timestampExited: 134267924529210942',
                'typeID: 85230',
            ]),
        ]);

        $timer = app(NotificationTimerImporter::class)->import($notification);

        $this->assertNotNull($timer);
        $this->assertSame('Jita IV', $timer->system);
        $this->assertSame('Mercenary Den', $timer->structure_type);
        $this->assertSame('Friendly Corp', $timer->owner_corporation);
        $this->assertSame('Sith Navy', $timer->attacker_corporation);
        $this->assertSame('MercenaryDenReinforced', $timer->source_notification_type);
        $this->assertEqualsCanonicalizing(
            ['Auto Imported', 'Friendly', 'Reinforced'],
            $timer->tags()->pluck('name')->all()
        );
        $this->assertStringContainsString('Structure ID: 1054221260563', $timer->notes);
        $this->assertStringContainsString('Attacker alliance: Calculated Disorder', $timer->notes);
    }

    private function seedTrackedCharacterContext(bool $includeAlliance = false): void
    {
        CharacterInfo::create([
            'character_id' => 90000001,
            'name' => 'Timer Character',
        ]);

        CharacterAffiliation::withoutEvents(fn () => CharacterAffiliation::create([
            'character_id' => 90000001,
            'corporation_id' => 98765,
            'alliance_id' => $includeAlliance ? 12345 : null,
        ]));

        CorporationInfo::create([
            'corporation_id' => 98765,
            'name' => 'Friendly Corp',
            'alliance_id' => $includeAlliance ? 12345 : null,
        ]);

        RefreshToken::withoutEvents(fn () => RefreshToken::create([
            'character_id' => 90000001,
            'user_id' => 1,
            'scopes' => [],
            'expires_on' => now()->addDay(),
            'refresh_token' => 'refresh',
        ]));

        UniverseName::create(['entity_id' => 98765, 'name' => 'Friendly Corp', 'category' => 'corporation']);
        UniverseName::create(['entity_id' => 2114190616, 'name' => 'Test Aggressor', 'category' => 'character']);

        if ($includeAlliance) {
            UniverseName::create(['entity_id' => 12345, 'name' => 'Friendly Alliance', 'category' => 'alliance']);
            UniverseName::create(['entity_id' => 55555, 'name' => 'Bad Corp', 'category' => 'corporation']);
            UniverseName::create(['entity_id' => 77777, 'name' => 'Bad Pilot', 'category' => 'character']);
        }
    }

    private function seedReferenceLocationData(): void
    {
        $this->insertMapDenormalize([
            'itemID' => 30000142,
            'itemName' => 'Jita',
            'groupID' => 5,
            'solarSystemID' => 30000142,
            'regionID' => 10000002,
        ]);

        $this->insertMapDenormalize([
            'itemID' => 40121487,
            'itemName' => 'Jita IV',
            'groupID' => 7,
            'solarSystemID' => 30000142,
            'regionID' => 10000002,
        ]);
    }

    private function mockSavedDispatch(): void
    {
        $dispatcher = Mockery::mock(TimerMutationDispatcher::class);
        $dispatcher->shouldReceive('dispatchSaved')
            ->once()
            ->with(Mockery::type(Timer::class), true);

        $this->app->instance(TimerMutationDispatcher::class, $dispatcher);
    }

    private function createNotification(array $overrides = []): CharacterNotification
    {
        return CharacterNotification::withoutEvents(fn () => CharacterNotification::create(array_merge([
            'character_id' => 90000001,
            'notification_id' => 2000000001,
            'type' => 'OrbitalReinforced',
            'sender_id' => 1000125,
            'sender_type' => 'corporation',
            'timestamp' => now(),
            'is_read' => false,
            'text' => '',
        ], $overrides)));
    }
}
