@extends('web::layouts.grids.12')

@section('title', 'Timerboard Settings')
@section('page_header', 'Timerboard Settings')

@section('content')
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">General Settings</h3>
        </div>
        <div class="card-body">
            
            <form action="{{ route('timerboard.settings.notifications') }}" method="POST" class="mb-4">
                {{ csrf_field() }}
                
                <h5 class="mb-3">Notifications</h5>
                
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="notification_enabled" name="notification_enabled" {{ $notificationEnabled ? 'checked' : '' }}>
                        <label class="custom-control-label" for="notification_enabled">Enable Notifications</label>
                    </div>
                    <small class="text-muted">Send notifications (Discord/Slack) when a new timer is created.</small>
                </div>

                <div class="form-group">
                    <label for="notification_role_ids">Filter by Access Role</label>
                    <select name="notification_role_ids[]" id="notification_role_ids" class="form-control select2" multiple style="width: 100%;">
                        <option value="public" {{ in_array('public', $notificationRoleIds) ? 'selected' : '' }}>Public (Everyone)</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ in_array((string)$role->id, $notificationRoleIds) ? 'selected' : '' }}>
                                {{ $role->title }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Only send notifications if the timer is restricted to one of these roles. Select "Public" to include public timers.</small>
                </div>

                <div class="form-group">
                    <label class="d-block">Per-Group Tag Filters</label>
                    <small class="text-muted d-block mb-3">Use this when different Discord or Slack groups should receive different timer tags. If Allowed Tags is empty, that group receives all timer tags unless Blocked Tags matches. Blocked Tags always wins.</small>

                    @if($notificationGroups->isEmpty())
                        <div class="alert alert-secondary mb-0">
                            No SeAT notification groups are currently subscribed to the Timerboard "New Timer" alert.
                        </div>
                    @else
                        <div id="notification-group-filter-accordion">
                            @foreach($notificationGroups as $group)
                                @php($groupFilter = $notificationGroupTagFilters->get($group->id))
                                @php($integrationSummary = $group->integrations->pluck('name')->filter()->implode(', '))
                                @php($allowedCount = count($groupFilter->allowed_tag_ids ?? []))
                                @php($blockedCount = count($groupFilter->blocked_tag_ids ?? []))
                                <div class="card mb-3">
                                    <div class="card-header p-0" id="notification-group-filter-heading-{{ $group->id }}">
                                        <button
                                            class="btn btn-link btn-block text-left text-decoration-none px-3 py-3"
                                            type="button"
                                            data-toggle="collapse"
                                            data-target="#notification-group-filter-collapse-{{ $group->id }}"
                                            aria-expanded="false"
                                            aria-controls="notification-group-filter-collapse-{{ $group->id }}">
                                            <input type="hidden" name="notification_group_filters[{{ $loop->index }}][notification_group_id]" value="{{ $group->id }}">

                                            <div class="d-flex justify-content-between align-items-start flex-wrap">
                                                <div class="pr-3">
                                                    <strong>{{ $group->name }}</strong>
                                                    <div class="text-muted small">
                                                        Integrations: {{ $integrationSummary ?: 'None configured' }}
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <span class="badge badge-secondary">{{ $group->alerts->count() }} alert{{ $group->alerts->count() === 1 ? '' : 's' }}</span>
                                                    <div class="text-muted small mt-1">
                                                        {{ $allowedCount }} allowed, {{ $blockedCount }} blocked
                                                    </div>
                                                </div>
                                            </div>
                                        </button>
                                    </div>

                                    <div
                                        id="notification-group-filter-collapse-{{ $group->id }}"
                                        class="collapse"
                                        aria-labelledby="notification-group-filter-heading-{{ $group->id }}"
                                        data-parent="#notification-group-filter-accordion">
                                        <div class="card-body">
                                            <div class="form-group mb-3">
                                                <label for="notification_group_filters_{{ $group->id }}_allowed">Allowed Tags</label>
                                                <select
                                                    name="notification_group_filters[{{ $loop->index }}][allowed_tag_ids][]"
                                                    id="notification_group_filters_{{ $group->id }}_allowed"
                                                    class="form-control"
                                                    multiple
                                                    size="{{ min(max($tags->count(), 4), 10) }}">
                                                    @foreach($tags as $tag)
                                                        <option value="{{ $tag->id }}" {{ in_array($tag->id, $groupFilter->allowed_tag_ids ?? [], true) ? 'selected' : '' }}>
                                                            {{ $tag->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted">Leave empty to allow all timer tags for this group.</small>
                                            </div>

                                            <div class="form-group mb-0">
                                                <label for="notification_group_filters_{{ $group->id }}_blocked">Blocked Tags</label>
                                                <select
                                                    name="notification_group_filters[{{ $loop->index }}][blocked_tag_ids][]"
                                                    id="notification_group_filters_{{ $group->id }}_blocked"
                                                    class="form-control"
                                                    multiple
                                                    size="{{ min(max($tags->count(), 4), 10) }}">
                                                    @foreach($tags as $tag)
                                                        <option value="{{ $tag->id }}" {{ in_array($tag->id, $groupFilter->blocked_tag_ids ?? [], true) ? 'selected' : '' }}>
                                                            {{ $tag->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <small class="text-muted">Any blocked match prevents a notification for this group, even if the same timer also matches Allowed Tags.</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <button type="submit" class="btn btn-primary btn-sm">Save Notification Settings</button>
            </form>

            <hr>

            <h5 class="mb-3">Defaults</h5>
            <form action="{{ route('timerboard.settings.default-role') }}" method="POST" class="form-inline">
                {{ csrf_field() }}
                <div class="form-group mb-2">
                    <label for="default_timer_role" class="mr-2">Default Access Role:</label>
                    <select name="default_timer_role" id="default_timer_role" class="form-control mr-2">
                        <option value="">Public (Everyone)</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ $defaultRoleId == $role->id ? 'selected' : '' }}>
                                {{ $role->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mb-2">Save Default Role</button>
            </form>
            <small class="text-muted">Timers created will default to this role restricted visibility.</small>

            <hr>

            <h5 class="mb-3">Display</h5>
            <form action="{{ route('timerboard.settings.display') }}" method="POST" class="form-inline">
                {{ csrf_field() }}
                <div class="form-group mb-2">
                    <label for="local_time_format" class="mr-2">Local Time Format:</label>
                    <select name="local_time_format" id="local_time_format" class="form-control mr-2">
                        <option value="24h" {{ $localTimeFormat === '24h' ? 'selected' : '' }}>24-hour</option>
                        <option value="ampm" {{ $localTimeFormat === 'ampm' ? 'selected' : '' }}>AM/PM</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mb-2">Save Display Settings</button>
            </form>
            <small class="text-muted">This only affects the dashboard's Local Time column. Eve Time stays in UTC 24-hour format.</small>

            <hr>

            <h5 class="mb-2">Automatic Timer Import</h5>
            <p class="text-muted mb-3">
                Timerboard can watch new SeAT structure notifications for selected corporations and alliances, then automatically create timers when those notifications include a parseable reinforcement or anchoring date. Use this to keep friendly structure timers flowing into the board without manual entry.
            </p>
            <form action="{{ route('timerboard.settings.auto-import') }}" method="POST">
                {{ csrf_field() }}

                <div class="form-group">
                    <label for="tracked_corporation_ids">Tracked Corporations</label>
                    <select name="tracked_corporation_ids[]" id="tracked_corporation_ids" class="form-control" multiple style="width: 100%;">
                        @foreach($trackedCorporations as $corporation)
                            <option value="{{ $corporation['id'] }}" selected>{{ $corporation['text'] }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">New notifications received for members of these corporations can create timers automatically when the notification includes a parseable timer.</small>
                </div>

                <div class="form-group">
                    <label for="tracked_alliance_ids">Tracked Alliances</label>
                    <select name="tracked_alliance_ids[]" id="tracked_alliance_ids" class="form-control" multiple style="width: 100%;">
                        @foreach($trackedAlliances as $alliance)
                            <option value="{{ $alliance['id'] }}" selected>{{ $alliance['text'] }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Alliance selections automatically expand to their current member corporations, so future corp joins and leaves are picked up automatically.</small>
                </div>

                <button type="submit" class="btn btn-primary btn-sm">Save Auto-Import Settings</button>
            </form>
            <small class="text-muted d-block mt-2">This only applies to new SeAT notifications going forward. Imported timers will reuse your default access role and add tags like Auto Imported, Friendly, and event-specific tags when appropriate.</small>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Manage Tags</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Color</th>
                        <th>Type</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tags as $tag)
                        <tr>
                            <td>
                                <span class="badge" style="background-color: {{ $tag->color }}; color: #fff;">{{ $tag->name }}</span>
                            </td>
                            <td>{{ $tag->color }}</td>
                            <td>
                                @if($tag->isProtectedSystemTag())
                                    <span class="badge badge-secondary">Required for auto-import</span>
                                @else
                                    <span class="text-muted">Custom</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="btn btn-warning btn-xs" data-toggle="modal" data-target="#editTagModal-{{ $tag->id }}">
                                    {{ $tag->isProtectedSystemTag() ? 'Edit Color' : 'Edit' }}
                                </button>
                                @unless($tag->isProtectedSystemTag())
                                    <form action="{{ route('timerboard.settings.tags.destroy', $tag->id) }}" method="POST" onsubmit="return confirm('Are you sure?');" style="display:inline-block;">
                                        {{ csrf_field() }}
                                        {{ method_field('DELETE') }}
                                        <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                                    </form>
                                @else
                                    <small class="text-muted d-inline-block ml-2">Name and deletion are locked.</small>
                                @endunless

                                <!-- Edit Tag Modal -->
                                <div class="modal fade" id="editTagModal-{{ $tag->id }}" tabindex="-1" role="dialog" aria-labelledby="editTagModalLabel-{{ $tag->id }}" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editTagModalLabel-{{ $tag->id }}">Edit Tag</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form action="{{ route('timerboard.settings.tags.update', $tag->id) }}" method="POST">
                                                {{ csrf_field() }}
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="edit-tag-name-{{ $tag->id }}">Tag Name</label>
                                                        <input
                                                            type="text"
                                                            class="form-control"
                                                            name="name"
                                                            id="edit-tag-name-{{ $tag->id }}"
                                                            value="{{ $tag->name }}"
                                                            {{ $tag->isProtectedSystemTag() ? 'readonly' : 'required' }}>
                                                        @if($tag->isProtectedSystemTag())
                                                            <small class="text-muted">This tag name is reserved by Timerboard auto-import and cannot be changed.</small>
                                                        @endif
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="edit-tag-color-{{ $tag->id }}">Color</label>
                                                        <input type="color" class="form-control" name="color" id="edit-tag-color-{{ $tag->id }}" value="{{ $tag->color }}" style="width: 50px;" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <hr>

            <h4>Add New Tag</h4>
            <form action="{{ route('timerboard.settings.tags.store') }}" method="POST" class="form-inline">
                {{ csrf_field() }}
                <div class="form-group mb-2">
                    <label for="new-tag-name" class="sr-only">Name</label>
                    <input type="text" class="form-control" name="name" id="new-tag-name" placeholder="Tag Name" required>
                </div>
                <div class="form-group mx-sm-3 mb-2">
                    <label for="new-tag-color" class="sr-only">Color</label>
                    <input type="color" class="form-control" name="color" id="new-tag-color" value="#007bff" style="width: 50px;" required>
                </div>
                <button type="submit" class="btn btn-primary mb-2">Add Tag</button>
            </form>
        </div>
    </div>

    </div>

    @can('seat-timerboard.delete')
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Maintenance</h3>
        </div>
        <div class="card-body">
            <p>Use this button to remove all timers that have already elapsed. This action cannot be undone.</p>
            <form action="{{ route('timerboard.destroy.elapsed') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete ALL elapsed timers? This action cannot be undone.');">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}
                <button type="submit" class="btn btn-danger">Delete All Elapsed Timers</button>
            </form>
        </div>
    </div>
    @endcan

    @can('seat-timerboard.delete-all')
    <div class="card card-danger mt-4">
        <div class="card-header">
            <h3 class="card-title">Danger Zone</h3>
        </div>
        <div class="card-body">
            <p>Delete all timers from the database. This action cannot be undone.</p>
            <form action="{{ route('timerboard.truncate') }}" method="POST" onsubmit="return confirm('Are you ABSOLUTELY sure? This will delete ALL timers defined in the system.');">
                {{ csrf_field() }}
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Delete All Timers
                </button>
            </form>
        </div>
    </div>
    @endcan
@endsection

@push('javascript')
    <script>
        $(function () {
            function initializeTrackedEntitySelect(selector, url, placeholder) {
                $(selector).select2({
                    ajax: {
                        url: url,
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                q: params.term || ''
                            };
                        },
                        processResults: function (data) {
                            return data;
                        }
                    },
                    minimumInputLength: 2,
                    placeholder: placeholder,
                    width: '100%',
                    allowClear: false
                });
            }

            initializeTrackedEntitySelect(
                '#tracked_corporation_ids',
                @json(route('timerboard.settings.search.corporations')),
                'Search corporations...'
            );

            initializeTrackedEntitySelect(
                '#tracked_alliance_ids',
                @json(route('timerboard.settings.search.alliances')),
                'Search alliances...'
            );
        });
    </script>
@endpush
