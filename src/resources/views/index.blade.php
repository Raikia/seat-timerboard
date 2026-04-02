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
                                        @if($timer->getDotlanMapUrl())
                                            <a href="{{ $timer->getDotlanMapUrl() }}" target="_blank">
                                                {{ $timer->system }}
                                            </a>
                                        @else
                                            {{ $timer->system }}
                                        @endif
                                        <br>
                                        <span class="text-muted small">
                                            {{ $timer->getRegionName() }}
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
                                        @if($timer->getDotlanMapUrl())
                                            <a href="{{ $timer->getDotlanMapUrl() }}" target="_blank">
                                                {{ $timer->system }}
                                            </a>
                                        @else
                                            {{ $timer->system }}
                                        @endif
                                        <br>
                                        <span class="text-muted small">
                                            {{ $timer->getRegionName() }}
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
                            <small class="d-block mt-1" style="opacity: 0.85;">Queue up as many timers as you need and submit them together.</small>
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

                            <div class="batch-modal-toolbar">
                                <div class="batch-toolbar-meta">
                                    <div class="batch-count-pill">
                                        <i class="fas fa-layer-group"></i>
                                        <span id="batch-timer-count">1 timer</span>
                                    </div>
                                    <div class="text-muted">
                                        Each timer keeps its own system, structure, tags, and access role.
                                    </div>
                                </div>
                                <div class="batch-toolbar-actions">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="duplicate-last-row-btn">
                                        <i class="far fa-clone"></i> Duplicate Last
                                    </button>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="add-timer-row-btn">
                                        <i class="fas fa-plus"></i> Add Blank Timer
                                    </button>
                                </div>
                            </div>

                            <div id="batch-timer-rows"></div>
                        </div>

                        <div class="modal-footer">
                            <div class="batch-footer-summary" id="batch-footer-summary">1 timer ready to save</div>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-outline-primary" id="add-timer-row-footer-btn">
                                <i class="fas fa-plus"></i> Add Blank Timer
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

    #batchTimerModal .modal-content,
    #editTimerModal .modal-content {
        border: 0;
        border-radius: 14px;
        overflow: visible;
        box-shadow: 0 18px 45px rgba(26, 35, 52, 0.22);
    }

    #batchTimerModal .modal-header,
    #editTimerModal .modal-header {
        border-bottom: 0;
        background: linear-gradient(135deg, #183247 0%, #214c6c 100%);
        color: #fff;
    }

    #batchTimerModal .modal-header .close,
    #editTimerModal .modal-header .close {
        color: #fff;
        opacity: 0.85;
        text-shadow: none;
    }

    #batchTimerModal .modal-body {
        background: linear-gradient(180deg, #f5f7fb 0%, #eef2f7 100%);
        max-height: 72vh;
        overflow-y: auto;
    }

    #editTimerModal .modal-body {
        background: #f7f9fc;
    }

    #batchTimerModal .batch-modal-toolbar {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
        padding: 0.85rem 1rem;
        border-radius: 12px;
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(33, 76, 108, 0.1);
        box-shadow: 0 8px 18px rgba(24, 50, 71, 0.06);
    }

    #batchTimerModal .batch-toolbar-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.75rem;
    }

    #batchTimerModal .batch-count-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.35rem 0.75rem;
        border-radius: 999px;
        background: #183247;
        color: #fff;
        font-size: 0.85rem;
        font-weight: 600;
    }

    #batchTimerModal .batch-toolbar-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    #batchTimerModal .batch-timer-row {
        border: 1px solid rgba(31, 73, 103, 0.12);
        border-radius: 14px;
        overflow: visible;
        box-shadow: 0 10px 24px rgba(23, 43, 77, 0.08);
        transition: box-shadow 0.18s ease, border-color 0.18s ease, transform 0.18s ease;
    }

    #batchTimerModal .batch-timer-row.is-active {
        border-color: rgba(33, 76, 108, 0.28);
        box-shadow: 0 16px 32px rgba(23, 43, 77, 0.12);
        transform: translateY(-1px);
    }

    #batchTimerModal .batch-timer-row + .batch-timer-row {
        margin-top: 1rem;
    }

    #batchTimerModal .batch-timer-row .card-header {
        background: linear-gradient(180deg, #fbfcfe 0%, #f1f5f9 100%);
        border-bottom: 1px solid rgba(31, 73, 103, 0.08);
        padding: 0.8rem 1rem;
    }

    #batchTimerModal .batch-timer-row.is-collapsed .card-header {
        border-bottom-color: transparent;
    }

    #batchTimerModal .batch-timer-row .card-body {
        background: rgba(255, 255, 255, 0.95);
        padding: 1rem 1rem 0.5rem;
    }

    #batchTimerModal .batch-row-heading {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        min-width: 0;
        cursor: pointer;
    }

    #batchTimerModal .batch-row-index {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2rem;
        height: 2rem;
        padding: 0 0.55rem;
        border-radius: 999px;
        background: #214c6c;
        color: #fff;
        font-size: 0.85rem;
        font-weight: 700;
    }

    #batchTimerModal .batch-row-subtitle {
        color: #5f6f82;
        font-size: 0.83rem;
    }

    #batchTimerModal .batch-row-title-wrap {
        min-width: 0;
        flex: 1 1 auto;
    }

    #batchTimerModal .batch-row-summary {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        margin-top: 0.35rem;
    }

    #batchTimerModal .batch-summary-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.22rem 0.55rem;
        border-radius: 999px;
        background: rgba(24, 50, 71, 0.08);
        color: #2c4358;
        font-size: 0.77rem;
        font-weight: 600;
        line-height: 1.2;
    }

    #batchTimerModal .batch-summary-pill.is-placeholder {
        color: #7a8897;
        background: rgba(122, 136, 151, 0.12);
        font-weight: 500;
    }

    #batchTimerModal .batch-row-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        align-items: center;
    }

    #batchTimerModal .toggle-batch-row-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.2rem;
        height: 2.2rem;
        padding: 0;
        border-radius: 999px;
    }

    #batchTimerModal .toggle-batch-row-btn i {
        transition: transform 0.18s ease;
    }

    #batchTimerModal .batch-timer-row.is-collapsed .toggle-batch-row-btn i {
        transform: rotate(-90deg);
    }

    #batchTimerModal .batch-footer-summary {
        margin-right: auto;
        color: #5f6f82;
        font-size: 0.9rem;
        font-weight: 500;
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
        var activeSelect2Instance = null;

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

                $element.select2({
                    theme: 'bootstrap4',
                    dropdownParent: $fallbackParent,
                    placeholder: 'Select Structure Type',
                    allowClear: true,
                    width: '100%'
                });
            });
        }

        function initRemoteSelect($elements, $fallbackParent, url, placeholder, allowClear) {
            $elements.each(function() {
                var $element = $(this);

                $element.select2($.extend({}, buildAjaxConfig(url, placeholder, allowClear), {
                    dropdownParent: $fallbackParent,
                    width: '100%'
                }));
            });
        }

        function repositionSelect2Dropdown(instance) {
            if (!instance || !instance.$dropdown || !instance.$container) {
                return;
            }

            var dropdownParent = instance.options.get('dropdownParent');
            var $dropdownParent = dropdownParent && dropdownParent.jquery ? dropdownParent : $(dropdownParent);

            if (!$dropdownParent.length) {
                return;
            }

            var parentOffset = $dropdownParent.offset();
            var containerOffset = instance.$container.offset();

            if (!parentOffset || !containerOffset) {
                return;
            }

            instance.$dropdown.css({
                top: containerOffset.top - parentOffset.top + instance.$container.outerHeight(false),
                left: containerOffset.left - parentOffset.left,
                width: instance.$container.outerWidth(false)
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
                        '<div class="batch-row-heading" role="button" tabindex="0" aria-expanded="true">' +
                            '<span class="batch-row-index">1</span>' +
                            '<div class="batch-row-title-wrap">' +
                                '<strong class="batch-row-title d-block">Timer</strong>' +
                                '<span class="batch-row-subtitle">Fill this out or duplicate it to build the next timer faster.</span>' +
                                '<div class="batch-row-summary"></div>' +
                            '</div>' +
                        '</div>' +
                        '<div class="batch-row-actions">' +
                            '<button type="button" class="btn btn-light btn-sm toggle-batch-row-btn" title="Collapse or expand timer">' +
                                '<i class="fas fa-chevron-down"></i>' +
                            '</button>' +
                            '<button type="button" class="btn btn-outline-secondary btn-sm duplicate-batch-row-btn">' +
                                '<i class="far fa-clone"></i> Duplicate' +
                            '</button>' +
                            '<button type="button" class="btn btn-outline-danger btn-sm remove-batch-row-btn">' +
                                '<i class="fas fa-times"></i> Remove' +
                            '</button>' +
                        '</div>' +
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
            var rowCount = $('#batch-timer-rows .batch-timer-row').length;

            $('#batch-timer-rows .batch-timer-row').each(function(index) {
                $(this).find('.batch-row-index').text(index + 1);
                $(this).find('.batch-row-title').text('Timer ' + (index + 1));
            });

            var disableRemove = rowCount === 1;
            $('.remove-batch-row-btn').prop('disabled', disableRemove);

            var countLabel = rowCount === 1 ? '1 timer' : rowCount + ' timers';
            $('#batch-timer-count').text(countLabel);
            $('#batch-footer-summary').text(rowCount === 1 ? '1 timer ready to save' : rowCount + ' timers ready to save');
        }

        function roleTitleForValue(roleId) {
            if (roleId === null || roleId === undefined || roleId === '') {
                return 'Public';
            }

            var normalizedRoleId = String(roleId);
            var matchedRole = availableRoles.find(function(role) {
                return String(role.id) === normalizedRoleId;
            });

            return matchedRole ? matchedRole.title : 'Restricted';
        }

        function createSummaryPill(iconClass, text, isPlaceholder) {
            var pillClass = 'batch-summary-pill' + (isPlaceholder ? ' is-placeholder' : '');

            return '<span class="' + pillClass + '"><i class="' + iconClass + '"></i>' + escapeHtml(text) + '</span>';
        }

        function updateBatchRowSummary($row) {
            var data = collectBatchRowData($row);
            var summaryBits = [];

            summaryBits.push(createSummaryPill('fas fa-map-marker-alt', data.system || 'System pending', !data.system));
            summaryBits.push(createSummaryPill('fas fa-building', data.structure_type || 'Type pending', !data.structure_type));
            summaryBits.push(createSummaryPill('far fa-clock', data.time_input || 'Time pending', !data.time_input));
            summaryBits.push(createSummaryPill('fas fa-flag', data.owner_corporation || 'Owner pending', !data.owner_corporation));

            if (data.structure_name) {
                summaryBits.push(createSummaryPill('fas fa-signature', data.structure_name, false));
            }

            if (data.attacker_corporation) {
                summaryBits.push(createSummaryPill('fas fa-crosshairs', data.attacker_corporation, false));
            }

            if (data.tags.length) {
                summaryBits.push(createSummaryPill('fas fa-tags', data.tags.length + (data.tags.length === 1 ? ' tag' : ' tags'), false));
            }

            summaryBits.push(createSummaryPill('fas fa-user-shield', roleTitleForValue(data.role_id), false));

            $row.find('.batch-row-summary').html(summaryBits.join(''));
        }

        function setBatchRowExpanded($row, expanded, immediate) {
            var $body = $row.find('.card-body');
            var $heading = $row.find('.batch-row-heading');
            var $toggle = $row.find('.toggle-batch-row-btn');

            $row.toggleClass('is-collapsed', !expanded);
            $row.toggleClass('is-active', expanded);
            $heading.attr('aria-expanded', expanded ? 'true' : 'false');
            $toggle.attr('aria-expanded', expanded ? 'true' : 'false');

            if (immediate) {
                $body.toggle(expanded);
            } else {
                $body.stop(true, true)[expanded ? 'slideDown' : 'slideUp'](140);
            }
        }

        function activateBatchRow($row, immediate) {
            $('#batch-timer-rows .batch-timer-row').not($row).each(function() {
                setBatchRowExpanded($(this), false, immediate);
            });

            setBatchRowExpanded($row, true, immediate);
            updateBatchRowSummary($row);
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
            updateBatchRowSummary($row);
        }

        function collectBatchRowData($row) {
            var tags = [];

            $row.find('.tag-checkbox:checked').each(function() {
                tags.push($(this).val());
            });

            return {
                system: $row.find('.batch-system-select').val() || '',
                structure_type: $row.find('.batch-structure-type-select').val() || '',
                structure_name: $row.find('input[name$="[structure_name]"]').val() || '',
                owner_corporation: $row.find('.batch-owner-corporation-select').val() || '',
                attacker_corporation: $row.find('.batch-attacker-corporation-select').val() || '',
                time_input: $row.find('input[name$="[time_input]"]').val() || '',
                role_id: $row.find('.batch-role-select').val() || '',
                tags: tags
            };
        }

        function focusBatchRow($row) {
            var $firstInput = $row.find('.batch-system-select').first();

            if ($firstInput.length) {
                $firstInput.select2('open');
            }

            $row[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function addBatchTimerRow(timerData, shouldFocus) {
            var rowKey = batchRowCounter++;
            var $row = $(buildBatchRow(rowKey, timerData));

            $('#batch-timer-rows').append($row);
            initializeBatchRow($row, timerData);
            refreshBatchRowTitles();
            activateBatchRow($row, true);

            if (shouldFocus) {
                focusBatchRow($row);
            }

            return $row;
        }

        function resetBatchRows(timerData) {
            $('#batch-timer-rows').empty();
            batchRowCounter = 0;

            var timers = timerData && timerData.length ? timerData : [{}];
            timers.forEach(function(timer) {
                addBatchTimerRow(timer, false);
            });

            var $lastRow = $('#batch-timer-rows .batch-timer-row').last();
            if ($lastRow.length) {
                activateBatchRow($lastRow, true);
            }
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
                addBatchTimerRow({}, true);
            });

            $('#duplicate-last-row-btn').click(function() {
                var $lastRow = $('#batch-timer-rows .batch-timer-row').last();

                if ($lastRow.length) {
                    addBatchTimerRow(collectBatchRowData($lastRow), true);
                } else {
                    addBatchTimerRow({}, true);
                }
            });

            $(document).on('click', '.duplicate-batch-row-btn', function() {
                var $row = $(this).closest('.batch-timer-row');
                addBatchTimerRow(collectBatchRowData($row), true);
            });

            $(document).on('click', '.remove-batch-row-btn', function() {
                var $row = $(this).closest('.batch-timer-row');
                var wasActive = $row.hasClass('is-active');

                if ($('#batch-timer-rows .batch-timer-row').length === 1) {
                    return;
                }

                var $nextRow = $row.next('.batch-timer-row');
                var $prevRow = $row.prev('.batch-timer-row');

                $row.remove();
                refreshBatchRowTitles();

                if (wasActive) {
                    activateBatchRow($nextRow.length ? $nextRow : $prevRow, true);
                }
            });
        @endcan

        $(document).on('click', '.batch-row-heading, .toggle-batch-row-btn', function(event) {
            event.preventDefault();

            var $row = $(this).closest('.batch-timer-row');
            activateBatchRow($row, false);
        });

        $(document).on('keydown', '.batch-row-heading', function(event) {
            if (event.key !== 'Enter' && event.key !== ' ') {
                return;
            }

            event.preventDefault();
            activateBatchRow($(this).closest('.batch-timer-row'), false);
        });

        $(document).on('input change', '#batch-timer-rows input, #batch-timer-rows select', function() {
            var $row = $(this).closest('.batch-timer-row');
            updateBatchRowSummary($row);
        });

        $(document).on('focus', '#batch-timer-rows input, #batch-timer-rows select, #batch-timer-rows .select2-selection', function() {
            var $row = $(this).closest('.batch-timer-row');

            if ($row.length && !$row.hasClass('is-active')) {
                activateBatchRow($row, false);
            }
        });

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

        $(document).on('select2:open', function(event) {
            activeSelect2Instance = $(event.target).data('select2') || null;

            requestAnimationFrame(function() {
                repositionSelect2Dropdown(activeSelect2Instance);
            });
        });

        $(document).on('select2:close', function() {
            activeSelect2Instance = null;
        });

        $('#batchTimerModal .modal-body, #editTimerModal .modal-body').on('scroll', function() {
            repositionSelect2Dropdown(activeSelect2Instance);
        });

        $(window).on('resize', function() {
            repositionSelect2Dropdown(activeSelect2Instance);
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
