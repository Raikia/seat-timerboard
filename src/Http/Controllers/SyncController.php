<?php

namespace Raikia\SeatTimerboard\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;
use Raikia\SeatTimerboard\Models\Tag;
use Raikia\SeatTimerboard\Models\TimerSyncPeer;
use Raikia\SeatTimerboard\Services\TimerboardInstanceIdentity;
use Seat\Web\Http\Controllers\Controller;
use Seat\Web\Models\Acl\Role;

class SyncController extends Controller
{
    public function index(TimerboardInstanceIdentity $identity)
    {
        $peers = TimerSyncPeer::with('incomingRole')
            ->orderBy('name')
            ->get();
        $tags = Tag::orderBy('name')->get();
        $roles = Role::orderBy('title')->get();

        return view('seat-timerboard::sync', [
            'peers' => $peers,
            'tags' => $tags,
            'roles' => $roles,
            'localInstanceUuid' => $identity->getUuid(),
            'localInstanceName' => $identity->getName(),
            'localBaseUrl' => $identity->getBaseUrl(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatePeerRequest($request);

        TimerSyncPeer::create($this->payloadFromRequest($data, true));

        return redirect()->route('timerboard.sync.index')->with('success', 'Sync peer created successfully.');
    }

    public function update(Request $request, TimerSyncPeer $peer)
    {
        $data = $this->validatePeerRequest($request, $peer);

        $payload = $this->payloadFromRequest($data, false);

        if (blank($data['api_token'] ?? null)) {
            unset($payload['api_token']);
        }

        $peer->update($payload);

        return redirect()->route('timerboard.sync.index')->with('success', 'Sync peer updated successfully.');
    }

    public function destroy(TimerSyncPeer $peer)
    {
        $peer->deliveries()->delete();
        $peer->delete();

        return redirect()->route('timerboard.sync.index')->with('success', 'Sync peer deleted successfully.');
    }

    private function validatePeerRequest(Request $request, ?TimerSyncPeer $peer = null): array
    {
        $identity = app(TimerboardInstanceIdentity::class);
        $validator = validator($request->all(), $this->rules($peer?->id, $peer === null));

        $validator->after(function (Validator $validator) use ($request, $identity) {
            $peerInstanceUuid = trim((string) $request->input('instance_uuid'));
            $peerBaseUrl = $this->normalizeUrl($request->input('base_url'));
            $localBaseUrl = $this->normalizeUrl($identity->getBaseUrl());

            if ($peerInstanceUuid !== '' && $peerInstanceUuid === $identity->getUuid()) {
                $validator->errors()->add('instance_uuid', 'You cannot configure this SeAT instance as its own sync peer.');
            }

            if ($peerBaseUrl !== '' && $localBaseUrl !== '' && $peerBaseUrl === $localBaseUrl) {
                $validator->errors()->add('base_url', 'You cannot configure this SeAT instance as its own sync peer.');
            }
        });

        return $validator->validate();
    }

    private function rules(?int $peerId = null, bool $requireToken = true): array
    {
        return [
            'name' => 'required|string|max:255',
            'instance_uuid' => [
                'required',
                'uuid',
                Rule::unique('seat_timerboard_sync_peers', 'instance_uuid')->ignore($peerId),
            ],
            'base_url' => 'required|url|max:255',
            'api_token' => ($requireToken ? 'required' : 'nullable') . '|string|max:255',
            'sync_tag_ids' => 'nullable|array',
            'sync_tag_ids.*' => 'integer|exists:seat_timerboard_tags,id',
            'incoming_role_id' => 'nullable|integer|exists:roles,id',
            'allow_remote_delete' => 'nullable|boolean',
            'is_enabled' => 'nullable|boolean',
        ];
    }

    private function payloadFromRequest(array $data, bool $isCreate): array
    {
        $payload = [
            'name' => $data['name'],
            'instance_uuid' => $data['instance_uuid'],
            'base_url' => $data['base_url'],
            'sync_tag_ids' => $data['sync_tag_ids'] ?? [],
            'incoming_role_id' => $data['incoming_role_id'] ?? null,
            'allow_remote_delete' => ! empty($data['allow_remote_delete']),
            'is_enabled' => ! empty($data['is_enabled']),
        ];

        if ($isCreate || filled($data['api_token'] ?? null)) {
            $payload['api_token'] = $data['api_token'];
        }

        return $payload;
    }

    private function normalizeUrl(?string $url): string
    {
        return rtrim(trim((string) $url), '/');
    }
}
