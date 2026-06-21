<?php

namespace Raikia\SeatTimerboard\Http\Controllers;

use Seat\Web\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Raikia\SeatTimerboard\Models\NotificationGroupTagFilter;
use Raikia\SeatTimerboard\Models\Tag;
use Seat\Eveapi\Models\Alliances\Alliance;
use Seat\Eveapi\Models\Corporation\CorporationInfo;
use Seat\Eveapi\Models\Universe\UniverseName;
use Seat\Notifications\Models\NotificationGroup;
use Seat\Web\Models\Acl\Role;

class SettingsController extends Controller
{
    public function index()
    {
        $tags = Tag::orderBy('name')->get();
        $roles = \Seat\Web\Models\Acl\Role::all();
        $defaultRole = \Raikia\SeatTimerboard\Models\TimerboardSetting::find('default_timer_role');
        $defaultRoleId = $defaultRole ? $defaultRole->value : null;
        $localTimeFormat = optional(\Raikia\SeatTimerboard\Models\TimerboardSetting::find('local_time_format'))->value ?? '24h';

        $notifEnabled = \Raikia\SeatTimerboard\Models\TimerboardSetting::find('notification_enabled');
        $notificationEnabled = $notifEnabled ? filter_var($notifEnabled->value, FILTER_VALIDATE_BOOLEAN) : false;

        $notifRoles = \Raikia\SeatTimerboard\Models\TimerboardSetting::find('notification_role_ids');
        $notificationRoleIds = $notifRoles ? json_decode($notifRoles->value, true) : [];
        $notificationGroups = NotificationGroup::with(['alerts', 'integrations'])
            ->whereHas('alerts', function ($query) {
                $query->where('alert', 'seat_timerboard_new_timer');
            })
            ->orderBy('name')
            ->get();
        $notificationGroupTagFilters = NotificationGroupTagFilter::query()
            ->whereIn('notification_group_id', $notificationGroups->pluck('id'))
            ->get()
            ->keyBy('notification_group_id');

        $trackedCorporationIds = $this->jsonSetting('tracked_corporation_ids');
        $trackedAllianceIds = $this->jsonSetting('tracked_alliance_ids');
        $trackedCorporations = $this->resolveTrackedCorporations($trackedCorporationIds);
        $trackedAlliances = $this->resolveTrackedAlliances($trackedAllianceIds);

        return view('seat-timerboard::settings', compact(
            'tags',
            'roles',
            'defaultRoleId',
            'localTimeFormat',
            'notificationEnabled',
            'notificationRoleIds',
            'notificationGroups',
            'notificationGroupTagFilters',
            'trackedCorporations',
            'trackedAlliances'
        ));
    }

    public function storeDefaultRole(Request $request)
    {
        $request->validate([
            'default_timer_role' => 'nullable|integer|exists:roles,id',
        ]);

        \Raikia\SeatTimerboard\Models\TimerboardSetting::updateOrCreate(
            ['setting' => 'default_timer_role'],
            ['value' => $request->input('default_timer_role')]
        );

        return redirect()->route('timerboard.settings')->with('success', 'Default role updated successfully.');
    }

    public function storeDisplaySettings(Request $request)
    {
        $request->validate([
            'local_time_format' => 'required|in:24h,ampm',
        ]);

        \Raikia\SeatTimerboard\Models\TimerboardSetting::updateOrCreate(
            ['setting' => 'local_time_format'],
            ['value' => $request->input('local_time_format', '24h')]
        );

        return redirect()->route('timerboard.settings')->with('success', 'Display settings updated successfully.');
    }

    public function storeAutoImportSettings(Request $request)
    {
        $request->validate([
            'tracked_corporation_ids' => 'nullable|array',
            'tracked_corporation_ids.*' => 'integer',
            'tracked_alliance_ids' => 'nullable|array',
            'tracked_alliance_ids.*' => 'integer',
        ]);

        \Raikia\SeatTimerboard\Models\TimerboardSetting::updateOrCreate(
            ['setting' => 'tracked_corporation_ids'],
            ['value' => json_encode(array_values(array_unique($request->input('tracked_corporation_ids', []))))]
        );

        \Raikia\SeatTimerboard\Models\TimerboardSetting::updateOrCreate(
            ['setting' => 'tracked_alliance_ids'],
            ['value' => json_encode(array_values(array_unique($request->input('tracked_alliance_ids', []))))]
        );

        return redirect()->route('timerboard.settings')->with('success', 'Auto-import tracking updated successfully.');
    }

    public function storeNotifications(Request $request)
    {
        $request->validate([
            'notification_enabled' => 'nullable|in:on,1,true',
            'notification_role_ids' => 'nullable|array',
            'notification_role_ids.*' => [
                'required',
                function ($attribute, $value, $fail) {
                    if ($value === 'public') {
                        return;
                    }

                    if (!ctype_digit((string) $value) || !Role::whereKey((int) $value)->exists()) {
                        $fail('The selected notification role is invalid.');
                    }
                },
            ],
            'notification_group_filters' => 'nullable|array',
            'notification_group_filters.*.notification_group_id' => 'required|integer|exists:notification_groups,id',
            'notification_group_filters.*.allowed_tag_ids' => 'nullable|array',
            'notification_group_filters.*.allowed_tag_ids.*' => 'integer|exists:seat_timerboard_tags,id',
            'notification_group_filters.*.blocked_tag_ids' => 'nullable|array',
            'notification_group_filters.*.blocked_tag_ids.*' => 'integer|exists:seat_timerboard_tags,id',
        ]);

        \Raikia\SeatTimerboard\Models\TimerboardSetting::updateOrCreate(
            ['setting' => 'notification_enabled'],
            ['value' => $request->has('notification_enabled')]
        );

        \Raikia\SeatTimerboard\Models\TimerboardSetting::updateOrCreate(
            ['setting' => 'notification_role_ids'],
            ['value' => json_encode($request->input('notification_role_ids', []))]
        );

        $groupFilters = collect($request->input('notification_group_filters', []))
            ->filter(fn ($filter) => !empty($filter['notification_group_id']))
            ->keyBy(fn ($filter) => (int) $filter['notification_group_id']);
        $groupIds = $groupFilters->pluck('notification_group_id')
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->all();

        if (!empty($groupIds)) {
            NotificationGroupTagFilter::whereIn('notification_group_id', $groupIds)->delete();
        }

        $groupFilters->each(function (array $filter) {
            $allowedTagIds = $this->normalizeTagIds($filter['allowed_tag_ids'] ?? []);
            $blockedTagIds = $this->normalizeTagIds($filter['blocked_tag_ids'] ?? []);

            if (empty($allowedTagIds) && empty($blockedTagIds)) {
                return;
            }

            NotificationGroupTagFilter::create([
                'notification_group_id' => (int) $filter['notification_group_id'],
                'allowed_tag_ids' => $allowedTagIds,
                'blocked_tag_ids' => $blockedTagIds,
            ]);
        });

        return redirect()->route('timerboard.settings')->with('success', 'Notification settings updated successfully.');
    }

    public function storeTag(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        Tag::create($request->only('name', 'color'));

        return redirect()->route('timerboard.settings')->with('success', 'Tag created successfully.');
    }

    public function updateTag(Request $request, Tag $tag)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $tag->update($request->only('name', 'color'));

        return redirect()->route('timerboard.settings')->with('success', 'Tag updated successfully.');
    }

    public function destroyTag(Tag $tag)
    {
        $tag->delete();

        return redirect()->route('timerboard.settings')->with('success', 'Tag deleted successfully.');
    }

    public function searchCorporations(Request $request)
    {
        $query = trim((string) $request->input('q', ''));

        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $escapedQuery = $this->escapeLike($query);

        $results = CorporationInfo::player()
            ->where('name', 'like', '%' . $escapedQuery . '%')
            ->orderBy('name')
            ->limit(100)
            ->get(['corporation_id', 'name'])
            ->sort(function ($left, $right) use ($query) {
                return $this->compareSearchLabels($left->name, $right->name, $query);
            })
            ->take(20)
            ->map(function ($corporation) {
                return [
                    'id' => $corporation->corporation_id,
                    'text' => $corporation->name,
                ];
            })
            ->values();

        return response()->json(['results' => $results]);
    }

    public function searchAlliances(Request $request)
    {
        $query = trim((string) $request->input('q', ''));

        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $escapedQuery = $this->escapeLike($query);

        $results = Alliance::query()
            ->where('name', 'like', '%' . $escapedQuery . '%')
            ->orderBy('name')
            ->limit(100)
            ->get(['alliance_id', 'name'])
            ->sort(function ($left, $right) use ($query) {
                return $this->compareSearchLabels($left->name, $right->name, $query);
            })
            ->take(20)
            ->map(function ($alliance) {
                return [
                    'id' => $alliance->alliance_id,
                    'text' => $alliance->name,
                ];
            })
            ->values();

        return response()->json(['results' => $results]);
    }

    private function jsonSetting(string $key): array
    {
        $setting = \Raikia\SeatTimerboard\Models\TimerboardSetting::find($key);

        if (!$setting || blank($setting->value)) {
            return [];
        }

        return array_values(array_filter(json_decode($setting->value, true) ?: [], function ($value) {
            return filled($value);
        }));
    }

    private function resolveTrackedCorporations(array $corporationIds): array
    {
        if (empty($corporationIds)) {
            return [];
        }

        $corporations = CorporationInfo::whereIn('corporation_id', $corporationIds)
            ->get(['corporation_id', 'name'])
            ->keyBy('corporation_id');

        $fallbackNames = UniverseName::whereIn('entity_id', $corporationIds)
            ->get(['entity_id', 'name'])
            ->keyBy('entity_id');

        return collect($corporationIds)->map(function ($corporationId) use ($corporations, $fallbackNames) {
            $name = optional($corporations->get($corporationId))->name
                ?? optional($fallbackNames->get($corporationId))->name
                ?? 'Corporation #' . $corporationId;

            return [
                'id' => (int) $corporationId,
                'text' => $name,
            ];
        })->values()->all();
    }

    private function resolveTrackedAlliances(array $allianceIds): array
    {
        if (empty($allianceIds)) {
            return [];
        }

        $alliances = Alliance::whereIn('alliance_id', $allianceIds)
            ->get(['alliance_id', 'name'])
            ->keyBy('alliance_id');

        $fallbackNames = UniverseName::whereIn('entity_id', $allianceIds)
            ->get(['entity_id', 'name'])
            ->keyBy('entity_id');

        return collect($allianceIds)->map(function ($allianceId) use ($alliances, $fallbackNames) {
            $name = optional($alliances->get($allianceId))->name
                ?? optional($fallbackNames->get($allianceId))->name
                ?? 'Alliance #' . $allianceId;

            return [
                'id' => (int) $allianceId,
                'text' => $name,
            ];
        })->values()->all();
    }

    private function escapeLike(string $value): string
    {
        return addcslashes($value, '\\%_');
    }

    private function normalizeTagIds(array $tagIds): array
    {
        return collect($tagIds)
            ->filter(fn ($id) => filled($id))
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    private function compareSearchLabels(string $left, string $right, string $query): int
    {
        $leftStartsWith = str_starts_with(strtolower($left), strtolower($query));
        $rightStartsWith = str_starts_with(strtolower($right), strtolower($query));

        if ($leftStartsWith !== $rightStartsWith) {
            return $leftStartsWith ? -1 : 1;
        }

        return strcasecmp($left, $right);
    }
}
