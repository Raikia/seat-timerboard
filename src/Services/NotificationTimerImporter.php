<?php

namespace Raikia\SeatTimerboard\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Raikia\SeatTimerboard\Models\Tag;
use Raikia\SeatTimerboard\Models\Timer;
use Raikia\SeatTimerboard\Models\TimerboardSetting;
use Seat\Eveapi\Models\Alliances\Alliance;
use Seat\Eveapi\Models\Character\CharacterNotification;
use Seat\Eveapi\Models\Corporation\CorporationInfo;
use Seat\Eveapi\Models\RefreshToken;
use Seat\Eveapi\Models\Sde\MapDenormalize;
use Seat\Eveapi\Models\Universe\UniverseName;
use Seat\Eveapi\Models\Universe\UniverseStructure;
use Seat\Eveapi\Services\EseyeClient;

class NotificationTimerImporter
{
    private const UNIVERSE_STRUCTURES_SCOPE = 'esi-universe.read_structures.v1';

    private const TAGS = [
        'Auto Imported' => '#6c757d',
        'Friendly' => '#28a745',
        'Anchoring' => '#17a2b8',
        'Reinforced' => '#dc3545',
    ];

    /**
     * Import a timer from a newly created SeAT character notification when applicable.
     */
    public function import(CharacterNotification $notification): ?Timer
    {
        $trackingContext = $this->resolveTrackingContext($notification);

        if (!$trackingContext) {
            return null;
        }

        $payload = $this->buildPayload($notification, $trackingContext);

        if (!$payload) {
            return null;
        }

        $existing = Timer::where('import_fingerprint', $payload['import_fingerprint'])->first();

        if ($existing) {
            return $existing;
        }

        return DB::transaction(function () use ($payload) {
            $timer = new Timer();
            $timer->fill([
                'system' => $payload['system'],
                'structure_type' => $payload['structure_type'],
                'structure_name' => $payload['structure_name'],
                'notes' => $payload['notes'],
                'owner_corporation' => $payload['owner_corporation'],
                'attacker_corporation' => $payload['attacker_corporation'],
                'role_id' => $payload['role_id'],
                'import_fingerprint' => $payload['import_fingerprint'],
                'source_notification_type' => $payload['source_notification_type'],
            ]);
            $timer->user_id = $payload['user_id'];
            $timer->eve_time = $payload['eve_time'];
            $timer->save();
            $timer->tags()->sync($payload['tag_ids']);
            app(TimerNotificationService::class)->queueNewTimerNotification($timer);

            return $timer;
        });
    }

    private function resolveTrackingContext(CharacterNotification $notification): ?array
    {
        $trackedCorporationIds = $this->trackedCorporationIds();

        if (empty($trackedCorporationIds)) {
            return null;
        }

        $recipient = $notification->recipient;
        $affiliation = optional($recipient)->affiliation;
        $recipientCorporationId = $affiliation ? (int) $affiliation->corporation_id : null;

        if (!$recipientCorporationId || !in_array($recipientCorporationId, $trackedCorporationIds, true)) {
            return null;
        }

        $userId = RefreshToken::where('character_id', $notification->character_id)->value('user_id');

        if (!$userId) {
            Log::warning('Timerboard auto-import skipped: notification recipient is missing a SeAT user.', [
                'character_id' => $notification->character_id,
                'notification_id' => $notification->notification_id,
                'type' => $notification->type,
            ]);

            return null;
        }

        $allianceId = $affiliation && $affiliation->alliance_id ? (int) $affiliation->alliance_id : null;

        return [
            'user_id' => (int) $userId,
            'recipient_corporation_id' => $recipientCorporationId,
            'recipient_corporation_name' => $this->resolveCorporationName($recipientCorporationId),
            'recipient_alliance_id' => $allianceId,
            'recipient_alliance_name' => $allianceId ? $this->resolveAllianceName($allianceId) : null,
        ];
    }

    private function buildPayload(CharacterNotification $notification, array $trackingContext): ?array
    {
        switch ($notification->type) {
            case 'StructureAnchoring':
                return $this->buildStructureAnchoringPayload($notification, $trackingContext);
            case 'StructureLostArmor':
            case 'StructureLostShields':
                return $this->buildStructureReinforcementPayload($notification, $trackingContext);
            case 'SkyhookLostShields':
                return $this->buildSkyhookReinforcementPayload($notification, $trackingContext);
            case 'OrbitalReinforced':
                return $this->buildOrbitalReinforcedPayload($notification, $trackingContext);
            case 'SovStructureReinforced':
                return $this->buildSovReinforcedPayload($notification, $trackingContext);
            default:
                return null;
        }
    }

    private function buildStructureAnchoringPayload(CharacterNotification $notification, array $trackingContext): ?array
    {
        $text = $this->notificationText($notification);
        $eveTime = $this->eveDurationFromNotificationTimestamp($notification, $this->notificationValue($text, ['timeLeft']));
        $systemId = $this->notificationValue($text, ['solarsystemID', 'solarSystemID']);
        $typeId = $this->notificationValue($text, ['structureTypeID', 'typeID']);
        $structureId = $this->notificationValue($text, ['structureID']);
        $structureType = $this->structureTypeFromTypeId($typeId);

        if (!$eveTime || !$systemId || !$structureType) {
            return null;
        }

        return $this->buildBasePayload($notification, $trackingContext, [
            'system' => $this->resolveSystemName($systemId),
            'structure_type' => $structureType,
            'structure_name' => $this->resolveStructureName($structureId, $notification->character_id, $trackingContext['recipient_corporation_id'] ?? null),
            'owner_corporation' => $this->notificationValue($text, ['ownerCorpName']) ?: $trackingContext['recipient_corporation_name'],
            'attacker_corporation' => null,
            'eve_time' => $eveTime,
            'tag_names' => ['Auto Imported', 'Friendly', 'Anchoring'],
            'note_lines' => [
                'Owner corporation: ' . ($this->notificationValue($text, ['ownerCorpName']) ?: $trackingContext['recipient_corporation_name']),
                $structureId ? 'Structure ID: ' . $structureId : null,
                $this->notificationValue($text, ['vulnerableTime']) ? 'Vulnerability window: ' . $this->eveDurationToString($this->notificationValue($text, ['vulnerableTime'])) : null,
            ],
            'fingerprint_parts' => [
                $structureId,
                $systemId,
                $typeId,
                $eveTime->timestamp,
            ],
        ]);
    }

    private function buildStructureReinforcementPayload(CharacterNotification $notification, array $trackingContext): ?array
    {
        $text = $this->notificationText($notification);
        $eveTime = $this->eveDurationFromNotificationTimestamp($notification, $this->notificationValue($text, ['timeLeft']));
        $systemId = $this->notificationValue($text, ['solarsystemID', 'solarSystemID']);
        $typeId = $this->notificationValue($text, ['structureTypeID', 'typeID']);
        $structureId = $this->notificationValue($text, ['structureID']);
        $structureType = $this->structureTypeFromTypeId($typeId);

        if (!$eveTime || !$systemId || !$structureType) {
            return null;
        }

        return $this->buildBasePayload($notification, $trackingContext, [
            'system' => $this->resolveSystemName($systemId),
            'structure_type' => $structureType,
            'structure_name' => $this->resolveStructureName($structureId, $notification->character_id, $trackingContext['recipient_corporation_id'] ?? null),
            'owner_corporation' => $trackingContext['recipient_corporation_name'],
            'attacker_corporation' => null,
            'eve_time' => $eveTime,
            'tag_names' => ['Auto Imported', 'Friendly', 'Reinforced'],
            'note_lines' => [
                $structureId ? 'Structure ID: ' . $structureId : null,
                $this->notificationValue($text, ['vulnerableTime']) ? 'Vulnerability window: ' . $this->eveDurationToString($this->notificationValue($text, ['vulnerableTime'])) : null,
            ],
            'fingerprint_parts' => [
                $structureId,
                $systemId,
                $typeId,
                $eveTime->timestamp,
            ],
        ]);
    }

    private function buildSkyhookReinforcementPayload(CharacterNotification $notification, array $trackingContext): ?array
    {
        $text = $this->notificationText($notification);
        $eveTime = $this->eveDurationFromNotificationTimestamp($notification, $this->notificationValue($text, ['timeLeft']));
        $systemId = $this->notificationValue($text, ['solarsystemID', 'solarSystemID']);
        $itemId = $this->notificationValue($text, ['itemID']);

        if (!$eveTime || !$systemId) {
            return null;
        }

        return $this->buildBasePayload($notification, $trackingContext, [
            'system' => $this->resolveLocationName($this->notificationValue($text, ['planetID']), $systemId),
            'structure_type' => 'Skyhook',
            'structure_name' => $this->resolveStructureName($itemId, $notification->character_id, $trackingContext['recipient_corporation_id'] ?? null),
            'owner_corporation' => $trackingContext['recipient_corporation_name'],
            'attacker_corporation' => null,
            'eve_time' => $eveTime,
            'tag_names' => ['Auto Imported', 'Friendly', 'Reinforced'],
            'note_lines' => [
                $itemId ? 'Structure ID: ' . $itemId : null,
                $this->notificationValue($text, ['planetID']) ? 'Planet ID: ' . $this->notificationValue($text, ['planetID']) : null,
                $this->notificationValue($text, ['vulnerableTime']) ? 'Vulnerability window: ' . $this->eveDurationToString($this->notificationValue($text, ['vulnerableTime'])) : null,
            ],
            'fingerprint_parts' => [
                $itemId,
                $systemId,
                $eveTime->timestamp,
            ],
        ]);
    }

    private function buildOrbitalReinforcedPayload(CharacterNotification $notification, array $trackingContext): ?array
    {
        $text = $this->notificationText($notification);
        $mssqlTime = $this->notificationValue($text, ['reinforceExitTime']);
        $eveTime = $mssqlTime ? $this->mssqlTimestampToDate($mssqlTime) : null;
        $systemId = $this->notificationValue($text, ['solarSystemID', 'solarsystemID']);
        $planetId = $this->notificationValue($text, ['planetID']);
        $typeId = $this->notificationValue($text, ['typeID']);

        if (!$eveTime || !$systemId) {
            return null;
        }

        return $this->buildBasePayload($notification, $trackingContext, [
            'system' => $this->resolveLocationName($planetId, $systemId),
            'structure_type' => $this->structureTypeFromTypeId($typeId) ?: 'POCO',
            'structure_name' => null,
            'owner_corporation' => $trackingContext['recipient_corporation_name'],
            'attacker_corporation' => $this->resolveCorporationName($this->notificationValue($text, ['aggressorCorpID'])),
            'eve_time' => $eveTime,
            'tag_names' => ['Auto Imported', 'Friendly', 'Reinforced'],
            'note_lines' => [
                $planetId ? 'Planet ID: ' . $planetId : null,
                $this->resolveAllianceName($this->notificationValue($text, ['aggressorAllianceID'])) ? 'Attacker alliance: ' . $this->resolveAllianceName($this->notificationValue($text, ['aggressorAllianceID'])) : null,
                $this->resolveCharacterName($this->notificationValue($text, ['aggressorID'])) ? 'Attacker character: ' . $this->resolveCharacterName($this->notificationValue($text, ['aggressorID'])) : null,
            ],
            'fingerprint_parts' => [
                $planetId,
                $systemId,
                $typeId,
                $mssqlTime,
            ],
        ]);
    }

    private function buildSovReinforcedPayload(CharacterNotification $notification, array $trackingContext): ?array
    {
        $text = $this->notificationText($notification);
        $decloakTime = $this->notificationValue($text, ['decloakTime']);
        $eveTime = $decloakTime ? $this->mssqlTimestampToDate($decloakTime) : null;
        $systemId = $this->notificationValue($text, ['solarSystemID', 'solarsystemID']);
        $campaignEventType = $this->notificationValue($text, ['campaignEventType']);
        $ownerAllianceName = $this->resolveAllianceName($notification->sender_id);

        if (!$eveTime || !$systemId) {
            return null;
        }

        return $this->buildBasePayload($notification, $trackingContext, [
            'system' => $this->resolveSystemName($systemId),
            'structure_type' => 'Sovereignty Hub',
            'structure_name' => null,
            'owner_corporation' => $ownerAllianceName ?: ($trackingContext['recipient_alliance_name'] ?: $trackingContext['recipient_corporation_name']),
            'attacker_corporation' => null,
            'eve_time' => $eveTime,
            'tag_names' => ['Auto Imported', 'Friendly', 'Reinforced'],
            'note_lines' => [
                $campaignEventType ? 'Campaign event type: ' . $campaignEventType : null,
                $trackingContext['recipient_alliance_name'] ? 'Tracked alliance: ' . $trackingContext['recipient_alliance_name'] : null,
            ],
            'fingerprint_parts' => [
                $notification->sender_id,
                $systemId,
                $campaignEventType,
                $decloakTime,
            ],
        ]);
    }

    private function buildBasePayload(CharacterNotification $notification, array $trackingContext, array $payload): array
    {
        $fingerprintParts = array_merge(
            [$notification->type],
            array_values(array_filter($payload['fingerprint_parts'], function ($value) {
                return $value !== null && $value !== '';
            }))
        );

        return [
            'system' => $payload['system'],
            'structure_type' => $payload['structure_type'],
            'structure_name' => $payload['structure_name'],
            'owner_corporation' => $payload['owner_corporation'],
            'attacker_corporation' => $payload['attacker_corporation'],
            'eve_time' => $payload['eve_time'],
            'user_id' => $trackingContext['user_id'],
            'role_id' => $this->defaultRoleId(),
            'source_notification_type' => $notification->type,
            'import_fingerprint' => sha1(json_encode($fingerprintParts)),
            'notes' => $this->buildAutoImportNote($notification, $trackingContext, $payload['note_lines'] ?? []),
            'tag_ids' => $this->tagIdsForNames($payload['tag_names'] ?? []),
        ];
    }

    private function buildAutoImportNote(CharacterNotification $notification, array $trackingContext, array $noteLines): string
    {
        $lines = array_filter(array_merge([
            'Auto-imported from SeAT notification.',
            'Source type: ' . $notification->type,
            'Notification timestamp (UTC): ' . $notification->timestamp->copy()->setTimezone('UTC')->format('Y.m.d H:i:s'),
            'Tracked corporation: ' . $trackingContext['recipient_corporation_name'],
            $trackingContext['recipient_alliance_name'] ? 'Tracked alliance: ' . $trackingContext['recipient_alliance_name'] : null,
        ], $noteLines));

        return implode("\n", $lines);
    }

    private function tagIdsForNames(array $tagNames): array
    {
        return collect($tagNames)
            ->filter()
            ->unique()
            ->map(function ($tagName) {
                return Tag::firstOrCreate(
                    ['name' => $tagName],
                    ['color' => self::TAGS[$tagName] ?? '#6c757d']
                )->id;
            })
            ->values()
            ->all();
    }

    private function trackedCorporationIds(): array
    {
        $corporationIds = $this->jsonSetting('tracked_corporation_ids');
        $allianceIds = $this->jsonSetting('tracked_alliance_ids');

        $allianceCorporationIds = [];
        if (!empty($allianceIds)) {
            $allianceCorporationIds = CorporationInfo::whereIn('alliance_id', $allianceIds)
                ->pluck('corporation_id')
                ->map(function ($corporationId) {
                    return (int) $corporationId;
                })
                ->all();
        }

        return array_values(array_unique(array_merge(
            array_map('intval', $corporationIds),
            $allianceCorporationIds
        )));
    }

    private function jsonSetting(string $key): array
    {
        $setting = TimerboardSetting::find($key);

        if (!$setting || blank($setting->value)) {
            return [];
        }

        return array_values(array_filter(json_decode($setting->value, true) ?: [], function ($value) {
            return filled($value);
        }));
    }

    private function defaultRoleId(): ?int
    {
        $setting = TimerboardSetting::find('default_timer_role');

        if (!$setting || blank($setting->value)) {
            return null;
        }

        return (int) $setting->value;
    }

    private function notificationText(CharacterNotification $notification): array
    {
        return is_array($notification->text) ? $notification->text : [];
    }

    private function notificationValue(array $text, array $keys)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $text)) {
                return $text[$key];
            }
        }

        return null;
    }

    private function eveDurationFromNotificationTimestamp(CharacterNotification $notification, $duration): ?Carbon
    {
        if ($duration === null || $duration === '') {
            return null;
        }

        $seconds = max(0, (int) floor(((int) $duration) / 10000000));

        return $notification->timestamp->copy()->setTimezone('UTC')->addSeconds($seconds);
    }

    private function eveDurationToString($duration): string
    {
        $seconds = max(0, (int) floor(((int) $duration) / 10000000));

        if ($seconds === 0) {
            return '0 seconds';
        }

        $parts = [];
        $days = intdiv($seconds, 86400);
        $seconds %= 86400;
        $hours = intdiv($seconds, 3600);
        $seconds %= 3600;
        $minutes = intdiv($seconds, 60);
        $seconds %= 60;

        if ($days > 0) {
            $parts[] = sprintf('%d day%s', $days, $days === 1 ? '' : 's');
        }
        if ($hours > 0) {
            $parts[] = sprintf('%d hour%s', $hours, $hours === 1 ? '' : 's');
        }
        if ($minutes > 0) {
            $parts[] = sprintf('%d minute%s', $minutes, $minutes === 1 ? '' : 's');
        }
        if ($seconds > 0 && count($parts) < 2) {
            $parts[] = sprintf('%d second%s', $seconds, $seconds === 1 ? '' : 's');
        }

        return implode(' ', array_slice($parts, 0, 3));
    }

    private function mssqlTimestampToDate($timestamp): Carbon
    {
        $seconds = ((int) $timestamp / 10000000) - 11644473600;

        return Carbon::createFromTimestampUTC((int) $seconds);
    }

    private function resolveSystemName($systemId): string
    {
        $system = MapDenormalize::find($systemId);

        return $system ? $system->itemName : 'System #' . $systemId;
    }

    private function resolveLocationName($locationId, $fallbackSystemId = null): string
    {
        if ($locationId) {
            $location = MapDenormalize::find($locationId);

            if ($location && filled($location->itemName)) {
                return $location->itemName;
            }
        }

        return $this->resolveSystemName($fallbackSystemId);
    }

    private function resolveCorporationName($corporationId): ?string
    {
        if (!$corporationId) {
            return null;
        }

        $corporation = CorporationInfo::find($corporationId);

        if ($corporation && filled($corporation->name)) {
            return $corporation->name;
        }

        return optional(UniverseName::where('entity_id', $corporationId)->first())->name
            ?: 'Corporation #' . $corporationId;
    }

    private function resolveAllianceName($allianceId): ?string
    {
        if (!$allianceId) {
            return null;
        }

        $alliance = Alliance::find($allianceId);

        if ($alliance && filled($alliance->name)) {
            return $alliance->name;
        }

        return optional(UniverseName::where('entity_id', $allianceId)->first())->name
            ?: 'Alliance #' . $allianceId;
    }

    private function resolveCharacterName($characterId): ?string
    {
        if (!$characterId) {
            return null;
        }

        return optional(UniverseName::where('entity_id', $characterId)->first())->name
            ?: 'Character #' . $characterId;
    }

    private function resolveStructureName($structureId, ?int $characterId = null, ?int $corporationId = null): ?string
    {
        $structureId = (int) $structureId;

        if ($structureId <= 0) {
            return null;
        }

        $structure = UniverseStructure::find($structureId);

        if ($structure && filled($structure->name)) {
            return $structure->name;
        }

        $token = $this->resolveStructureLookupToken($characterId, $corporationId);

        if (!$token) {
            return null;
        }

        try {
            $response = (new EseyeClient())
                ->setAuthentication($token)
                ->invoke('get', '/universe/structures/{structure_id}/', [
                    'structure_id' => $structureId,
                ]);

            $body = $response->getBody();

            if (!$body || empty($body->name)) {
                return null;
            }

            UniverseStructure::updateOrCreate(
                ['structure_id' => $structureId],
                [
                    'name' => $body->name,
                    'owner_id' => $body->owner_id ?? null,
                    'solar_system_id' => $body->solar_system_id ?? null,
                    'type_id' => $body->type_id ?? null,
                    'x' => $body->position->x ?? null,
                    'y' => $body->position->y ?? null,
                    'z' => $body->position->z ?? null,
                ]
            );

            return $body->name;
        } catch (\Throwable $e) {
            Log::debug('Timerboard structure name lookup failed.', [
                'structure_id' => $structureId,
                'character_id' => $characterId,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function resolveStructureLookupToken(?int $characterId, ?int $corporationId): ?RefreshToken
    {
        if ($characterId) {
            $preferredToken = RefreshToken::find($characterId);

            if ($preferredToken && $this->tokenHasUniverseStructureScope($preferredToken)) {
                return $preferredToken;
            }
        }

        if (!$corporationId) {
            return null;
        }

        return RefreshToken::query()
            ->whereHas('affiliation', function ($query) use ($corporationId) {
                $query->where('corporation_id', $corporationId);
            })
            ->get()
            ->first(function (RefreshToken $token) {
                return $this->tokenHasUniverseStructureScope($token);
            });
    }

    private function tokenHasUniverseStructureScope(RefreshToken $token): bool
    {
        return in_array(self::UNIVERSE_STRUCTURES_SCOPE, $token->scopes ?: [], true);
    }

    private function structureTypeFromTypeId($typeId): ?string
    {
        $mapping = [
            2233 => 'POCO',
            35825 => 'Raitaru',
            35826 => 'Azbel',
            35827 => 'Sotiyo',
            35832 => 'Astrahus',
            35833 => 'Fortizar',
            35834 => 'Keepstar',
            35835 => 'Athanor',
            35836 => 'Tatara',
            35840 => 'Pharolux',
            35841 => 'Ansiblex',
            37534 => 'Tenebrex',
            81080 => 'Skyhook',
            81824 => 'Skyhook',
            81826 => 'Metenox',
            85230 => 'Mercenary Den',
            32458 => 'Sovereignty Hub',
        ];

        if (!$typeId) {
            return null;
        }

        return $mapping[(int) $typeId] ?? null;
    }
}
