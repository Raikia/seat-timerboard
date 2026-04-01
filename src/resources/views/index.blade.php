@extends('web::layouts.grids.12')

@section('title', 'Timerboard')
@section('page_header', 'Timerboard')

@section('content')
    <div class="card">
        <div class="card-header p-2">
            <ul class="nav nav-pills">
                <li class="nav-item"><a class="nav-link active" href="#current" data-toggle="tab">Current</a></li>
                <li class="nav-item"><a class="nav-link" href="#elapsed" data-toggle="tab">Elapsed</a></li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content">
                <div class="tab-pane active" id="current">
                    <div class="mb-2 d-flex justify-content-between align-items-center">
                        @can('seat-timerboard.create')
                        <button type="button" class="btn btn-primary btn-sm" id="create-timer-btn">
                            <i class="fas fa-plus"></i> Add Timers
                        </button>
                        @endcan
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> Timers remain in "Current" for 2 hours after elapsing.
                        </small>
                    </div>
                    <table class="table table-hover table-striped timers-table" id="current-timers-table">
                        <thead>
                            <tr>
                                <th>System</th>
                                <th>Type</th>
                                <th>Name</th>
                                <th>Owner</th>
                                <th>Attacker</th>
                                <th>Eve Time (UTC)</th>
                                <th>Local Time</th>
                                <th>Countdown</th>
                                <th>Tags</th>
                                <th>Created By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($currentTimers as $timer)
                                <tr class="timer-row active-timer" data-time="{{ $timer->eve_time->toIso8601String() }}">
                                    <td>
                                        <a href="https://evemaps.dotlan.net/map/{{ str_replace(' ', '_', $timer->mapDenormalize->region->itemName) }}/{{ str_replace(' ', '_', $timer->mapDenormalize->solarSystemID==null?$timer->mapDenormalize->itemName:$timer->mapDenormalize->system->itemName) }}" target="_blank">
                                            {{ $timer->system }}
                                        </a>
                                        <br>
                                        <span class="text-muted small">
                                            {{ $timer->mapDenormalize->region->itemName ?? '' }}
                                        </span>
                                    </td>
                                    <td>
                                        <img src="{{ $timer->getStructureImage() }}" alt="{{ $timer->structure_type }}" class="img-circle" style="width: 24px; height: 24px; margin-right: 5px;">
                                        {{ $timer->structure_type }}
                                    </td>
                                    <td>{{ $timer->structure_name }}</td>
                                    <td>{{ $timer->owner_corporation }}</td>
                                    <td>{{ $timer->attacker_corporation }}</td>
                                    <td>{{ $timer->eve_time->format('Y-m-d H:i:s') }}</td>
                                    <td class="local-time" data-order="{{ $timer->eve_time->timestamp }}">Calculating...</td>
                                    <td class="countdown font-weight-bold">Calculating...</td>
                                    <td>
                                        @foreach($timer->tags as $tag)
                                            <span class="badge" style="background-color: {{ $tag->color }}; color: #fff;">{{ $tag->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        {{ $timer->user->name ?? 'Unknown' }}
                                        <br>
                                        <span class="text-muted small">
                                            {{ $timer->role ? $timer->role->title : 'Public' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            @can('seat-timerboard.edit')
                                                <button type="button" class="btn btn-warning edit-timer-btn" title="Edit"
                                                    data-timer='@json($timer)'
                                                    data-tags='@json($timer->tags->pluck("id"))'>
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>
                                            @endcan
                                            @can('seat-timerboard.delete')
                                                <form action="{{ route('timerboard.destroy', $timer->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this timer?');">
                                                    {{ csrf_field() }}
                                                    {{ method_field('DELETE') }}
                                                    <button type="submit" class="btn btn-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.tab-pane -->
                <div class="tab-pane" id="elapsed">
                    <table class="table table-hover table-striped timers-table" id="elapsed-timers-table">
                        <thead>
                            <tr>
                                <th>System</th>
                                <th>Type</th>
                                <th>Name</th>
                                <th>Owner</th>
                                <th>Attacker</th>
                                <th>Eve Time (UTC)</th>
                                <th>Local Time</th>
                                <th>Countdown</th>
                                <th>Tags</th>
                                <th>Created By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($elapsedTimers as $timer)
                                <tr class="timer-row static-timer" data-time="{{ $timer->eve_time->toIso8601String() }}">
                                    <td>
                                        <a href="https://evemaps.dotlan.net/map/{{ str_replace(' ', '_', $timer->mapDenormalize->region->itemName) }}/{{ str_replace(' ', '_', $timer->mapDenormalize->solarSystemID==null?$timer->mapDenormalize->itemName:$timer->mapDenormalize->system->itemName) }}" target="_blank">
                                            {{ $timer->system }}
                                        </a>
                                        <br>
                                        <span class="text-muted small">
                                            {{ $timer->mapDenormalize->region->itemName ?? '' }}
                                        </span>
                                    </td>
                                    <td>
                                        <img src="{{ $timer->getStructureImage() }}" alt="{{ $timer->structure_type }}" class="img-circle" style="width: 24px; height: 24px; margin-right: 5px;">
                                        {{ $timer->structure_type }}
                                    </td>
                                    <td>{{ $timer->structure_name }}</td>
                                    <td>{{ $timer->owner_corporation }}</td>
                                    <td>{{ $timer->attacker_corporation }}</td>
                                    <td>{{ $timer->eve_time->format('Y-m-d H:i:s') }}</td>
                                    <td class="local-time" data-order="{{ $timer->eve_time->timestamp }}">Calculating...</td>
                                    <td class="countdown font-weight-bold text-danger">ELAPSED</td>
                                    <td>
                                        @foreach($timer->tags as $tag)
                                            <span class="badge" style="background-color: {{ $tag->color }}; color: #fff;">{{ $tag->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        {{ $timer->user->name ?? 'Unknown' }}
                                        <br>
                                        <span class="text-muted small">
                                            {{ $timer->role ? $timer->role->title : 'Public' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            @can('seat-timerboard.edit')
                                                <button type="button" class="btn btn-warning edit-timer-btn" title="Edit"
                                                    data-timer='@json($timer)'
                                                    data-tags='@json($timer->tags->pluck("id"))'>
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>
                                            @endcan
                                            @can('seat-timerboard.delete')
                                                <form action="{{ route('timerboard.destroy', $timer->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this timer?');">
                                                    {{ csrf_field() }}
                                                    {{ method_field('DELETE') }}
                                                    <button type="submit" class="btn btn-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
        </div>
    </div>

    @can('seat-timerboard.create')
        <div class="modal fade" id="batchTimerModal" tabindex="-1" role="dialog" aria-labelledby="batchTimerModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title" id="batchTimerModalLabel">Add Timers</h5>
                            <small class="text-muted">Queue up as many timers as you need and submit them together.</small>
                        </div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="batchTimerForm" action="{{ route('timerboard.storeMany') }}" method="POST">
                        {{ csrf_field() }}
                        <input type="hidden" name="form_context" value="batch_create">

                        <div class="modal-body">
                            @if($errors->any() && old('form_context') === 'batch_create')
                                <div class="alert alert-danger">
                                    <strong>We couldn't save that batch yet.</strong>
                                    <ul class="mb-0 mt-2 pl-3">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                                <div class="text-muted mb-2 mb-md-0">
                                    Each timer keeps its own system, structure, tags, and access role.
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="add-timer-row-btn">
                                    <i class="fas fa-plus"></i> Add Another Timer
                                </button>
                            </div>

                            <div id="batch-timer-rows"></div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-outline-primary" id="add-timer-row-footer-btn">
                                <i class="fas fa-plus"></i> Add Another Timer
                            </button>
                            <button type="submit" class="btn btn-primary" id="saveBatchTimersBtn">Save Timers</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    <div class="modal fade" id="editTimerModal" tabindex="-1" role="dialog" aria-labelledby="editTimerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editTimerModalLabel">Edit Timer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editTimerForm" action="" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" id="editFormMethod" value="PUT">
                    <input type="hidden" name="timer_id" id="edit_timer_id" value="">
                    <input type="hidden" name="form_context" value="edit">

                    <div class="modal-body">
                        @if($errors->any() && old('form_context') === 'edit')
                            <div class="alert alert-danger">
                                <strong>We couldn't save those timer changes yet.</strong>
                                <ul class="mb-0 mt-2 pl-3">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="edit_system">System / Location <span class="text-danger">*</span></label>
                            <select name="system" class="form-control edit-system-select" id="edit_system" required style="width: 100%;"></select>
                            <small class="text-muted">Search for a solar system or celestial (e.g. Moon, Planet)</small>
                        </div>

                        <div class="form-group">
                            <label for="edit_structure_type">Structure Type <span class="text-danger">*</span></label>
                            <select name="structure_type" class="form-control edit-structure-type-select" id="edit_structure_type" required style="width: 100%;">
                                <option value="">Select Type</option>
                                @foreach($structureTypes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="edit_structure_name">Structure Name</label>
                            <input type="text" name="structure_name" class="form-control" id="edit_structure_name" placeholder="Structure Name" value="{{ old('form_context') === 'edit' ? old('structure_name') : '' }}">
                        </div>

                        <div class="form-group">
                            <label for="edit_owner_corporation">Owner <span class="text-danger">*</span></label>
                            <select name="owner_corporation" class="form-control edit-owner-corporation-select" id="edit_owner_corporation" required style="width: 100%;"></select>
                        </div>

                        <div class="form-group">
                            <label for="edit_attacker_corporation">Attacker (Optional)</label>
                            <select name="attacker_corporation" class="form-control edit-attacker-corporation-select" id="edit_attacker_corporation" style="width: 100%;"></select>
                        </div>

                        <div class="form-group">
                            <label for="edit_time_input">Time <span class="text-danger">*</span></label>
                            <input type="text" name="time_input" class="form-control" id="edit_time_input" placeholder="YYYY.MM.DD HH:MM[:SS] or '2 days 4 hours'" value="{{ old('form_context') === 'edit' ? old('time_input') : '' }}" required>
                            <small class="form-text text-muted">Enter absolute EVE time (UTC) with optional seconds, or relative time like '1d 4h 30m'.</small>
                        </div>

                        <div class="form-group">
                            <label>Tags</label>
                            <div class="d-flex flex-wrap">
                                @foreach($tags as $tag)
                                    <div class="m-1">
                                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" id="edit_tag_{{ $tag->id }}" class="d-none tag-checkbox">
                                        <label class="badge p-2 tag-badge" for="edit_tag_{{ $tag->id }}"
                                               style="background-color: {{ $tag->color }}; color: #fff; cursor: pointer; opacity: 0.5; border: 2px solid transparent;"
                                               data-color="{{ $tag->color }}">
                                            {{ $tag->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="edit_role_id">Access Role</label>
                            <select name="role_id" class="form-control" id="edit_role_id" style="width: 100%;">
                                <option value="">Public (Everyone)</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->title }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Restrict visibility to a specific role.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="saveTimerBtn">Save Timer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@php
    $timerTagOptions = $tags->map(function ($tag) {
        return [
            'id' => $tag->id,
            'name' => $tag->name,
            'color' => $tag->color,
        ];
    })->values();

    $timerRoleOptions = $roles->map(function ($role) {
        return [
            'id' => $role->id,
            'title' => $role->title,
        ];
    })->values();

    $batchOldTimers = array_values((array) old('timers', []));
    $batchHadErrors = $errors->any() && old('form_context') === 'batch_create';
    $editHadErrors = $errors->any() && old('form_context') === 'edit';
    $oldEditValues = [
        'structure_name' => old('form_context') === 'edit' ? old('structure_name') : '',
        'time_input' => old('form_context') === 'edit' ? old('time_input') : '',
        'system' => old('form_context') === 'edit' ? old('system') : '',
        'structure_type' => old('form_context') === 'edit' ? old('structure_type') : '',
        'owner_corporation' => old('form_context') === 'edit' ? old('owner_corporation') : '',
        'attacker_corporation' => old('form_context') === 'edit' ? old('attacker_corporation') : '',
        'role_id' => old('form_context') === 'edit' ? old('role_id') : '',
        'tags' => old('tags', []),
    ];
@endphp

@push('head')
<style>
    #batchTimerModal .batch-timer-row .form-group,
    #editTimerModal .form-group {
        position: relative;
    }

    #batchTimerModal .select2-container,
    #editTimerModal .select2-container {
        width: 100% !important;
    }

    #batchTimerModal .select2-dropdown,
    #editTimerModal .select2-dropdown {
        z-index: 2055;
    }
</style>
@endpush

@push('javascript')
<script>
    $(document).ready(function() {
        var structureTypes = @json($structureTypes);
        var availableTags = @json($timerTagOptions);
        var availableRoles = @json($timerRoleOptions);
        var defaultRoleId = @json($defaultRoleId);
        var batchOldTimers = @json($batchOldTimers);
        var batchHadErrors = @json($batchHadErrors);
        var editHadErrors = @json($editHadErrors);
        var oldEditValues = @json($oldEditValues);
        var batchRowCounter = 0;

        function escapeHtml(value) {
            return $('<div>').text(value || '').html();
        }

        function buildAjaxConfig(url, placeholder, allowClear) {
            return {
                theme: 'bootstrap4',
                placeholder: placeholder,
                minimumInputLength: 3,
                allowClear: !!allowClear,
                ajax: {
                    url: url,
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return { q: params.term };
                    },
                    processResults: function (data) {
                        return { results: data.results };
                    },
                    cache: true
                }
            };
        }

        function initStructureTypeSelect($elements, $fallbackParent) {
            $elements.each(function() {
                var $element = $(this);
                var $dropdownParent = $element.closest('.form-group');

                if (!$dropdownParent.length) {
                    $dropdownParent = $fallbackParent;
                }

                $element.select2({
                    theme: 'bootstrap4',
                    dropdownParent: $dropdownParent,
                    placeholder: 'Select Structure Type',
                    allowClear: true,
                    width: '100%'
                });
            });
        }

        function initRemoteSelect($elements, $fallbackParent, url, placeholder, allowClear) {
            $elements.each(function() {
                var $element = $(this);
                var $dropdownParent = $element.closest('.form-group');

                if (!$dropdownParent.length) {
                    $dropdownParent = $fallbackParent;
                }

                $element.select2($.extend({}, buildAjaxConfig(url, placeholder, allowClear), {
                    dropdownParent: $dropdownParent,
                    width: '100%'
                }));
            });
        }

        function setSelectValue($select, value) {
            if (value === null || value === undefined || value === '') {
                $select.val(null).trigger('change');
                return;
            }

            var hasOption = false;
            $select.find('option').each(function() {
                if ($(this).val() == value) {
                    hasOption = true;
                }
            });

            if (!hasOption) {
                $select.append(new Option(value, value, true, true));
            }

            $select.val(value).trigger('change');
        }

        function formatUtcTimestamp(timeString) {
            var date = new Date(timeString);

            return date.getUTCFullYear() + '.' +
                ('0' + (date.getUTCMonth() + 1)).slice(-2) + '.' +
                ('0' + date.getUTCDate()).slice(-2) + ' ' +
                ('0' + date.getUTCHours()).slice(-2) + ':' +
                ('0' + date.getUTCMinutes()).slice(-2) + ':' +
                ('0' + date.getUTCSeconds()).slice(-2);
        }

        function buildStructureTypeOptions(selectedValue) {
            var options = '<option value="">Select Type</option>';

            $.each(structureTypes, function(value, label) {
                var selected = String(selectedValue || '') === String(value) ? ' selected' : '';
                options += '<option value="' + escapeHtml(value) + '"' + selected + '>' + escapeHtml(label) + '</option>';
            });

            return options;
        }

        function buildRoleOptions(selectedValue) {
            var normalizedValue = selectedValue === null || selectedValue === undefined ? '' : String(selectedValue);
            var options = '<option value="">Public (Everyone)</option>';

            availableRoles.forEach(function(role) {
                var selected = normalizedValue === String(role.id) ? ' selected' : '';
                options += '<option value="' + role.id + '"' + selected + '>' + escapeHtml(role.title) + '</option>';
            });

            return options;
        }

        function buildTagMarkup(rowKey, selectedTags) {
            var normalizedTags = (selectedTags || []).map(function(tagId) {
                return String(tagId);
            });

            return availableTags.map(function(tag) {
                var checkboxId = 'batch_tag_' + rowKey + '_' + tag.id;
                var checked = normalizedTags.indexOf(String(tag.id)) !== -1 ? ' checked' : '';

                return '' +
                    '<div class="m-1">' +
                        '<input type="checkbox" name="timers[' + rowKey + '][tags][]" value="' + tag.id + '" id="' + checkboxId + '" class="d-none tag-checkbox"' + checked + '>' +
                        '<label class="badge p-2 tag-badge" for="' + checkboxId + '" style="background-color: ' + escapeHtml(tag.color) + '; color: #fff; cursor: pointer; opacity: 0.5; border: 2px solid transparent;" data-color="' + escapeHtml(tag.color) + '">' + escapeHtml(tag.name) + '</label>' +
                    '</div>';
            }).join('');
        }

        function buildBatchRow(rowKey, timerData) {
            var data = timerData || {};
            var selectedRole = data.role_id !== undefined && data.role_id !== null && data.role_id !== ''
                ? data.role_id
                : (defaultRoleId || '');

            return '' +
                '<div class="card mb-3 batch-timer-row" data-row-key="' + rowKey + '">' +
                    '<div class="card-header d-flex flex-wrap justify-content-between align-items-center">' +
                        '<strong class="batch-row-title">Timer</strong>' +
                        '<button type="button" class="btn btn-outline-danger btn-sm remove-batch-row-btn">' +
                            '<i class="fas fa-times"></i> Remove' +
                        '</button>' +
                    '</div>' +
                    '<div class="card-body">' +
                        '<div class="form-row">' +
                            '<div class="form-group col-lg-6">' +
                                '<label>System / Location <span class="text-danger">*</span></label>' +
                                '<select name="timers[' + rowKey + '][system]" class="form-control batch-system-select" required style="width: 100%;"></select>' +
                                '<small class="text-muted">Search for a solar system or celestial.</small>' +
                            '</div>' +
                            '<div class="form-group col-lg-6">' +
                                '<label>Structure Type <span class="text-danger">*</span></label>' +
                                '<select name="timers[' + rowKey + '][structure_type]" class="form-control batch-structure-type-select" required style="width: 100%;">' +
                                    buildStructureTypeOptions(data.structure_type) +
                                '</select>' +
                            '</div>' +
                        '</div>' +
                        '<div class="form-row">' +
                            '<div class="form-group col-lg-6">' +
                                '<label>Structure Name</label>' +
                                '<input type="text" name="timers[' + rowKey + '][structure_name]" class="form-control" placeholder="Structure Name" value="' + escapeHtml(data.structure_name) + '">' +
                            '</div>' +
                            '<div class="form-group col-lg-6">' +
                                '<label>Time <span class="text-danger">*</span></label>' +
                                '<input type="text" name="timers[' + rowKey + '][time_input]" class="form-control" placeholder="YYYY.MM.DD HH:MM[:SS] or 2 days 4 hours" value="' + escapeHtml(data.time_input) + '" required>' +
                                '<small class="text-muted">Absolute EVE time (UTC) or relative time like 1d 4h 30m.</small>' +
                            '</div>' +
                        '</div>' +
                        '<div class="form-row">' +
                            '<div class="form-group col-lg-6">' +
                                '<label>Owner <span class="text-danger">*</span></label>' +
                                '<select name="timers[' + rowKey + '][owner_corporation]" class="form-control batch-owner-corporation-select" required style="width: 100%;"></select>' +
                            '</div>' +
                            '<div class="form-group col-lg-6">' +
                                '<label>Attacker (Optional)</label>' +
                                '<select name="timers[' + rowKey + '][attacker_corporation]" class="form-control batch-attacker-corporation-select" style="width: 100%;"></select>' +
                            '</div>' +
                        '</div>' +
                        '<div class="form-row">' +
                            '<div class="form-group col-lg-8">' +
                                '<label>Tags</label>' +
                                '<div class="d-flex flex-wrap">' + buildTagMarkup(rowKey, data.tags || []) + '</div>' +
                            '</div>' +
                            '<div class="form-group col-lg-4">' +
                                '<label>Access Role</label>' +
                                '<select name="timers[' + rowKey + '][role_id]" class="form-control batch-role-select" style="width: 100%;">' +
                                    buildRoleOptions(selectedRole) +
                                '</select>' +
                                '<small class="text-muted">Restrict visibility to a specific role.</small>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>';
        }

        function refreshBatchRowTitles() {
            $('#batch-timer-rows .batch-timer-row').each(function(index) {
                $(this).find('.batch-row-title').text('Timer ' + (index + 1));
            });

            var disableRemove = $('#batch-timer-rows .batch-timer-row').length === 1;
            $('.remove-batch-row-btn').prop('disabled', disableRemove);
        }

        function initializeBatchRow($row, timerData) {
            var data = timerData || {};
            var $modal = $('#batchTimerModal');

            initStructureTypeSelect($row.find('.batch-structure-type-select'), $modal);
            initRemoteSelect($row.find('.batch-system-select'), $modal, '{{ route("timerboard.search.systems") }}', 'Search for a system or celestial...', false);
            initRemoteSelect($row.find('.batch-owner-corporation-select'), $modal, '{{ route("timerboard.search.corporations") }}', 'Search for corporation or alliance...', false);
            initRemoteSelect($row.find('.batch-attacker-corporation-select'), $modal, '{{ route("timerboard.search.corporations") }}', 'Search for attacker (corp/alliance)...', true);

            setSelectValue($row.find('.batch-system-select'), data.system || '');
            $row.find('.batch-structure-type-select').val(data.structure_type || null).trigger('change');
            setSelectValue($row.find('.batch-owner-corporation-select'), data.owner_corporation || '');
            setSelectValue($row.find('.batch-attacker-corporation-select'), data.attacker_corporation || '');
            $row.find('.batch-role-select').val(data.role_id !== undefined && data.role_id !== null ? data.role_id : (defaultRoleId || '')).trigger('change');
            $row.find('.tag-checkbox').trigger('change');
        }

        function addBatchTimerRow(timerData) {
            var rowKey = batchRowCounter++;
            var $row = $(buildBatchRow(rowKey, timerData));

            $('#batch-timer-rows').append($row);
            initializeBatchRow($row, timerData);
            refreshBatchRowTitles();
        }

        function resetBatchRows(timerData) {
            $('#batch-timer-rows').empty();
            batchRowCounter = 0;

            var timers = timerData && timerData.length ? timerData : [{}];
            timers.forEach(function(timer) {
                addBatchTimerRow(timer);
            });
        }

        function resetEditForm() {
            $('#editTimerForm')[0].reset();
            $('#editTimerForm').attr('action', '');
            $('#edit_timer_id').val('');
            setSelectValue($('#edit_system'), '');
            $('#edit_structure_type').val(null).trigger('change');
            setSelectValue($('#edit_owner_corporation'), '');
            setSelectValue($('#edit_attacker_corporation'), '');
            $('#edit_role_id').val('').trigger('change');
            $('#editTimerForm .tag-checkbox').prop('checked', false).trigger('change');
        }

        initStructureTypeSelect($('#edit_structure_type'), $('#editTimerModal'));
        initRemoteSelect($('#edit_system'), $('#editTimerModal'), '{{ route("timerboard.search.systems") }}', 'Search for a system or celestial...', false);
        initRemoteSelect($('#edit_owner_corporation'), $('#editTimerModal'), '{{ route("timerboard.search.corporations") }}', 'Search for corporation or alliance...', false);
        initRemoteSelect($('#edit_attacker_corporation'), $('#editTimerModal'), '{{ route("timerboard.search.corporations") }}', 'Search for attacker (corp/alliance)...', true);

        @can('seat-timerboard.create')
            $('#create-timer-btn').click(function() {
                resetBatchRows([{}]);
                $('#batchTimerModal').modal('show');
            });

            $('#add-timer-row-btn, #add-timer-row-footer-btn').click(function() {
                addBatchTimerRow({});
            });

            $(document).on('click', '.remove-batch-row-btn', function() {
                if ($('#batch-timer-rows .batch-timer-row').length === 1) {
                    return;
                }

                $(this).closest('.batch-timer-row').remove();
                refreshBatchRowTitles();
            });
        @endcan

        $(document).on('change', '.tag-checkbox', function() {
            var label = $('label[for="' + $(this).attr('id') + '"]');

            if ($(this).is(':checked')) {
                label.css('opacity', '1');
                label.css('box-shadow', '0 0 5px rgba(0,0,0,0.5)');
            } else {
                label.css('opacity', '0.5');
                label.css('box-shadow', 'none');
            }
        });

        $('.edit-timer-btn').click(function() {
            var timer = $(this).data('timer');
            var tags = $(this).data('tags') || [];
            var url = '{{ route("timerboard.update", ":id") }}'.replace(':id', timer.id);

            resetEditForm();
            $('#editTimerForm').attr('action', url);
            $('#edit_timer_id').val(timer.id);
            $('#edit_structure_name').val(timer.structure_name || '');
            $('#edit_time_input').val(formatUtcTimestamp(timer.eve_time));
            setSelectValue($('#edit_system'), timer.system || '');
            $('#edit_structure_type').val(timer.structure_type || null).trigger('change');
            setSelectValue($('#edit_owner_corporation'), timer.owner_corporation || '');
            setSelectValue($('#edit_attacker_corporation'), timer.attacker_corporation || '');
            $('#edit_role_id').val(timer.role_id || '').trigger('change');

            $('#editTimerForm .tag-checkbox').prop('checked', false);
            tags.forEach(function(tagId) {
                $('#edit_tag_' + tagId).prop('checked', true);
            });
            $('#editTimerForm .tag-checkbox').trigger('change');

            $('#editTimerModal').modal('show');
        });

        if (batchHadErrors) {
            resetBatchRows(batchOldTimers);
            $('#batchTimerModal').modal('show');
        }

        if (editHadErrors) {
            var timerId = '{{ old("timer_id") }}';
            var editUrl = '{{ route("timerboard.update", ":id") }}'.replace(':id', timerId);

            resetEditForm();
            $('#editTimerForm').attr('action', editUrl);
            $('#edit_timer_id').val(timerId);
            $('#edit_structure_name').val(oldEditValues.structure_name);
            $('#edit_time_input').val(oldEditValues.time_input);
            setSelectValue($('#edit_system'), oldEditValues.system);
            $('#edit_structure_type').val(oldEditValues.structure_type).trigger('change');
            setSelectValue($('#edit_owner_corporation'), oldEditValues.owner_corporation);
            setSelectValue($('#edit_attacker_corporation'), oldEditValues.attacker_corporation);
            $('#edit_role_id').val(oldEditValues.role_id).trigger('change');

            $('#editTimerForm .tag-checkbox').prop('checked', false);
            if (oldEditValues.tags && oldEditValues.tags.length) {
                oldEditValues.tags.forEach(function(tagId) {
                    $('#edit_tag_' + tagId).prop('checked', true);
                });
            }
            $('#editTimerForm .tag-checkbox').trigger('change');

            $('#editTimerModal').modal('show');
        }

        // Initialize DataTables
        $('.timers-table').DataTable({
            "order": [[ 5, "asc" ]], // Sort by Eve Time (6th column, index 5)
            "columnDefs": [
                { "orderable": false, "targets": [8, 10] } 
            ],
            "stateSave": true, 
            "paging": true,
            "pageLength": 25,
            "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ]
        });

        function updateTimers() {
            const now = new Date();
            // Only update active timers (Current tab)
            // DataTable usually keeps elements in DOM but hides them if paged? 
            // Actually, for precise countdowns, we should update all found rows in the DOM that are active.
            // If rows are paginated out by DataTables, they might not be in the DOM depending on DataTables version/config (usually removed).
            // However, we only care about what the user sees or what's physically there.
            
            const rows = document.querySelectorAll('.timer-row.active-timer');

            rows.forEach(row => {
                const timeStr = row.getAttribute('data-time');
                const eveTime = new Date(timeStr);
                const diff = eveTime - now;

                // Local Time Update 
                const localTimeCell = row.querySelector('.local-time');
                if (localTimeCell.textContent === 'Calculating...') {
                    localTimeCell.textContent = eveTime.toLocaleString();
                }

                // Countdown Update
                const countdownCell = row.querySelector('.countdown');
                if (diff <= 0) {
                    countdownCell.textContent = 'ELAPSED';
                    countdownCell.classList.remove('text-warning');
                    countdownCell.classList.add('text-danger');
                    // Optional: could change class to static-timer to stop updating it, 
                    // but user wanted it to stay in tab until refresh.
                    // We can validly stop updating text if we know it says ELAPSED.
                    row.classList.remove('active-timer');
                    row.classList.add('static-timer');
                } else {
                    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                    let countdownStr = '';
                    if (days > 0) countdownStr += days + 'd ';
                    if (hours > 0) countdownStr += hours + 'h ';
                    countdownStr += minutes + 'm ' + seconds + 's';

                    countdownCell.textContent = countdownStr;
                    
                    if (days == 0 && hours < 4) {
                         countdownCell.classList.add('text-warning');
                    }
                }
            });
        }

        // Initialize static timers local time (Elapsed tab)
        function initStaticTimers() {
             const staticRows = document.querySelectorAll('.timer-row.static-timer');
             staticRows.forEach(row => {
                const timeStr = row.getAttribute('data-time');
                const eveTime = new Date(timeStr);
                const localTimeCell = row.querySelector('.local-time');
                if (localTimeCell && localTimeCell.textContent === 'Calculating...') {
                    localTimeCell.textContent = eveTime.toLocaleString();
                }
             });
        }

        initStaticTimers();
        setInterval(updateTimers, 1000);
        updateTimers();

        if (!batchHadErrors) {
            $('.tag-checkbox').trigger('change');
        }
    });
</script>
@endpush
