<?php

namespace Raikia\SeatTimerboard\Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Raikia\SeatTimerboard\Models\Tag;
use Raikia\SeatTimerboard\Models\Timer;
use Raikia\SeatTimerboard\Models\TimerSyncPeer;
use Raikia\SeatTimerboard\Models\TimerboardSetting;

abstract class TestCase extends BaseTestCase
{
    use CreatesDatabaseSchema;

    public function createApplication()
    {
        $app = require dirname(__DIR__, 3) . '/bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('database.default', 'sqlite');
        Config::set('database.connections.sqlite.database', ':memory:');
        Config::set('cache.default', 'array');
        Config::set('queue.default', 'sync');

        $this->createDatabaseSchema();

        DB::table('global_settings')->insert([
            [
                'name' => 'admin_contact',
                'value' => 'timerboard-tests@example.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'allow_tracking',
                'value' => '0',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    protected function seedTimerboardSettings(array $settings): void
    {
        foreach ($settings as $setting => $value) {
            TimerboardSetting::updateOrCreate(
                ['setting' => $setting],
                ['value' => is_array($value) ? json_encode($value) : $value]
            );
        }
    }

    protected function insertMapDenormalize(array $attributes): void
    {
        DB::table('mapDenormalize')->insert($attributes);
    }

    protected function createTimerWithTags(array $tagNames, array $overrides = []): Timer
    {
        $tagIds = collect($tagNames)
            ->map(fn (string $name) => Tag::firstOrCreate([
                'name' => $name,
            ], [
                'color' => '#28a745',
            ])->id)
            ->all();

        $timer = Timer::create(array_merge([
            'system' => 'Jita',
            'structure_type' => 'Fortizar',
            'structure_name' => 'Test Fort',
            'owner_corporation' => 'Friendly Corp',
            'eve_time' => now()->addHour(),
            'user_id' => 1,
        ], $overrides));

        $timer->tags()->sync($tagIds);

        return $timer->fresh('tags');
    }

    protected function createSyncPeer(array $overrides = []): TimerSyncPeer
    {
        return TimerSyncPeer::create(array_merge([
            'name' => 'Peer Seat',
            'instance_uuid' => '11111111-1111-1111-1111-111111111111',
            'base_url' => 'https://peer.example.test',
            'api_token' => 'secret-token',
            'sync_tag_ids' => [1],
            'incoming_role_id' => null,
            'allow_remote_delete' => true,
            'is_enabled' => true,
        ], $overrides));
    }

    protected function readPrivateProperty(object $object, string $property): mixed
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);

        return $property->getValue($object);
    }
}
