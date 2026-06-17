@extends('web::layouts.grids.12')

@section('title', 'Timerboard Sync')
@section('page_header', 'Timerboard Sync')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">What This Does</h3>
        </div>
        <div class="card-body">
            <p class="mb-2">
                Timerboard Sync lets this SeAT instance exchange timers directly with other SeAT instances.
            </p>
            <p class="mb-2">
                This is useful when you work with friendly groups, coalition partners, or alts on separate SeAT installs and want everyone to see the same relevant structure timers for joint operations without manually re-entering them.
            </p>
            <p class="mb-2">
                Sync is peer-to-peer, not a mesh. This SeAT only sends timers to the remote instances you explicitly configure here, and remote timers do not get forwarded onward to other peers.
            </p>
            <p class="mb-2">
                Outbound sync is tag-controlled per peer. A local timer will only be sent to a given peer if it has at least one of that peer's selected sync tags. New timers, updates, and optionally deletes can all propagate.
            </p>
            <p class="mb-0 text-muted">
                Timers received from another SeAT are marked locally as remote synced, keep their original tags, and use the receiving role you choose for that peer.
            </p>

            <hr>

            <h5 class="mb-2">How To Create The API Token</h5>
            <ol class="mb-0 pl-3">
                <li>On the remote SeAT instance, open the SeAT API admin page.</li>
                <li>Create a new API token for timer sync.</li>
                <li>Set the allowed source IP to the public IP of the SeAT instance that will be calling in, or use `0.0.0.0` if you intentionally want it open to any source.</li>
                <li>Copy that token value and paste it here as the Peer API Token.</li>
                <li>Repeat the process in the opposite direction if you want both SeAT instances to send timers to each other.</li>
            </ol>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">Local Instance</h3>
        </div>
        <div class="card-body">
            <p class="mb-2"><strong>Name:</strong> {{ $localInstanceName }}</p>
            <p class="mb-2"><strong>Base URL:</strong> <code>{{ $localBaseUrl }}</code></p>
            <p class="mb-2"><strong>Instance UUID:</strong> <code>{{ $localInstanceUuid }}</code></p>
            <p class="mb-0 text-muted">
                Use this UUID on other SeAT instances when they configure this server as a sync peer. Remote access should use a SeAT API token with a strict source IP allowlist.
            </p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">Add Sync Peer</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('timerboard.sync.store') }}" method="POST">
                {{ csrf_field() }}

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="peer_name">Peer Name</label>
                        <input type="text" class="form-control" id="peer_name" name="name" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="peer_base_url">Peer Base URL</label>
                        <input type="url" class="form-control" id="peer_base_url" name="base_url" placeholder="https://seat.example.com" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="peer_instance_uuid">Peer Instance UUID</label>
                        <input type="text" class="form-control" id="peer_instance_uuid" name="instance_uuid" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="peer_api_token">Peer API Token</label>
                        <input type="text" class="form-control" id="peer_api_token" name="api_token" required>
                        <small class="text-muted">This is the remote SeAT token this instance will use when pushing timers outward.</small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="incoming_role_id">Default Receiving Role</label>
                        <select name="incoming_role_id" id="incoming_role_id" class="form-control">
                            <option value="">Public (Everyone)</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->title }}</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Timers received from this peer will use this local role.</small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="sync_tag_ids">Sync When Timer Has Any Of These Tags</label>
                    <select name="sync_tag_ids[]" id="sync_tag_ids" class="form-control select2" multiple style="width: 100%;">
                        @foreach($tags as $tag)
                            <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Only local timers with at least one selected tag will be pushed to this peer.</small>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <div class="custom-control custom-switch mt-2">
                            <input type="checkbox" class="custom-control-input" id="allow_remote_delete" name="allow_remote_delete" value="1" checked>
                            <label class="custom-control-label" for="allow_remote_delete">Remote instance decides delete</label>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <div class="custom-control custom-switch mt-2">
                            <input type="checkbox" class="custom-control-input" id="is_enabled" name="is_enabled" value="1" checked>
                            <label class="custom-control-label" for="is_enabled">Peer enabled</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Add Peer</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Configured Peers</h3>
        </div>
        <div class="card-body">
            @if($peers->isEmpty())
                <p class="text-muted mb-0">No sync peers configured yet.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Peer URL</th>
                                <th>Instance UUID</th>
                                <th>Sync Tags</th>
                                <th>Receiving Role</th>
                                <th>Delete Control</th>
                                <th>Enabled</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($peers as $peer)
                                @php($peerTagIds = collect($peer->sync_tag_ids)->map(fn ($id) => (int) $id)->all())
                                <tr>
                                    <td>{{ $peer->name }}</td>
                                    <td><code>{{ $peer->base_url }}</code></td>
                                    <td><code>{{ $peer->instance_uuid }}</code></td>
                                    <td>
                                        @if(empty($peerTagIds))
                                            <span class="text-muted">None</span>
                                        @else
                                            {{ $tags->whereIn('id', $peerTagIds)->pluck('name')->implode(', ') }}
                                        @endif
                                    </td>
                                    <td>{{ optional($peer->incomingRole)->title ?? 'Public' }}</td>
                                    <td>{{ $peer->allow_remote_delete ? 'Remote can delete' : 'Local keeps copies' }}</td>
                                    <td>{{ $peer->is_enabled ? 'Yes' : 'No' }}</td>
                                    <td class="text-nowrap">
                                        <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#editPeerModal-{{ $peer->id }}">Edit</button>
                                        <form action="{{ route('timerboard.sync.destroy', $peer->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete this sync peer?');">
                                            {{ csrf_field() }}
                                            {{ method_field('DELETE') }}
                                            <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    @foreach($peers as $peer)
        @php($peerTagIds = collect($peer->sync_tag_ids)->map(fn ($id) => (int) $id)->all())
        <div class="modal fade" id="editPeerModal-{{ $peer->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit {{ $peer->name }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('timerboard.sync.update', $peer->id) }}" method="POST">
                        {{ csrf_field() }}
                        {{ method_field('POST') }}

                        <div class="modal-body">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label>Name</label>
                                    <input type="text" class="form-control" name="name" value="{{ $peer->name }}" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Peer Base URL</label>
                                    <input type="url" class="form-control" name="base_url" value="{{ $peer->base_url }}" required>
                                </div>
                                <div class="form-group col-md-4">
                                    <label>Peer Instance UUID</label>
                                    <input type="text" class="form-control" name="instance_uuid" value="{{ $peer->instance_uuid }}" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Peer API Token</label>
                                    <input type="text" class="form-control" name="api_token" placeholder="Leave blank to keep current token">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Default Receiving Role</label>
                                    <select name="incoming_role_id" class="form-control">
                                        <option value="">Public (Everyone)</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->id }}" {{ (int) $peer->incoming_role_id === (int) $role->id ? 'selected' : '' }}>
                                                {{ $role->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Sync Tags</label>
                                <select name="sync_tag_ids[]" class="form-control select2" multiple style="width: 100%;">
                                    @foreach($tags as $tag)
                                        <option value="{{ $tag->id }}" {{ in_array((int) $tag->id, $peerTagIds, true) ? 'selected' : '' }}>
                                            {{ $tag->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="custom-control custom-switch mb-2">
                                <input type="checkbox" class="custom-control-input" id="allow_remote_delete_{{ $peer->id }}" name="allow_remote_delete" value="1" {{ $peer->allow_remote_delete ? 'checked' : '' }}>
                                <label class="custom-control-label" for="allow_remote_delete_{{ $peer->id }}">Remote instance decides delete</label>
                            </div>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_enabled_{{ $peer->id }}" name="is_enabled" value="1" {{ $peer->is_enabled ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_enabled_{{ $peer->id }}">Peer enabled</label>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@push('javascript')
    <script>
        $(function () {
            $('.select2').select2({
                width: '100%'
            });
        });
    </script>
@endpush
