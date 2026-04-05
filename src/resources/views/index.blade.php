@extends('web::layouts.grids.12')

@section('title', 'Timerboard')
@section('page_header', 'Timerboard')

@section('content')
    @php
        $activeSkin = setting('skin') ?: 'default';
        $timerboardThemeClasses = in_array($activeSkin, ['jet', 'iuligigi', 'gigigraphite'], true)
            ? 'timerboard-dark-skin'
            : '';
        $currentCount = $currentTimers->count();
        $elapsedCount = $elapsedTimers->count();
        $noteCount = $currentTimers->merge($elapsedTimers)->filter(function ($timer) {
            return filled($timer->notes);
        })->count();
        $urgentCount = $currentTimers->filter(function ($timer) {
            return $timer->eve_time->isFuture() && $timer->eve_time->lte(now()->copy()->addDay());
        })->count();
    @endphp

    <div class="card timerboard-shell {{ $timerboardThemeClasses }}">
        <div class="card-body">
            <div class="timerboard-toolbar">
                <div class="timerboard-toolbar-copy">
                    <h5 class="mb-1">Timer Overview</h5>
                    <small class="text-muted">Current timers, urgent windows, saved notes, and elapsed references in one place.</small>
                </div>
            </div>

            <div class="timerboard-stats">
                <div class="timerboard-stat-card">
                    <span class="timerboard-stat-label">Current</span>
                    <strong class="timerboard-stat-value">{{ $currentCount }}</strong>
                    <span class="timerboard-stat-meta">Timers still in play</span>
                </div>
                <div class="timerboard-stat-card is-urgent">
                    <span class="timerboard-stat-label">Next 24h</span>
                    <strong class="timerboard-stat-value">{{ $urgentCount }}</strong>
                    <span class="timerboard-stat-meta">High-attention windows</span>
                </div>
                <div class="timerboard-stat-card">
                    <span class="timerboard-stat-label">Saved Notes</span>
                    <strong class="timerboard-stat-value">{{ $noteCount }}</strong>
                    <span class="timerboard-stat-meta">Timers with context attached</span>
                </div>
                <div class="timerboard-stat-card">
                    <span class="timerboard-stat-label">Elapsed</span>
                    <strong class="timerboard-stat-value">{{ $elapsedCount }}</strong>
                    <span class="timerboard-stat-meta">Past the 2-hour buffer</span>
                </div>
            </div>

            @can('seat-timerboard.create')
                <div class="timerboard-action-row">
                    <button type="button" class="btn btn-primary timerboard-primary-action" id="create-timer-btn">
                        <i class="fas fa-plus"></i> Add Timers
                    </button>
                </div>
            @endcan

            <div class="timerboard-filters mb-3" id="timerboard-filters">
                <div class="timerboard-filters-header">
                    <div>
                        <h6 class="mb-1">Filters</h6>
                        <small class="text-muted">Narrow timers by type, tag, region, owner, attacker, visibility, or whether a note exists.</small>
                    </div>
                    <div class="timerboard-filters-actions">
                        <small class="text-muted timer-filter-summary-header" id="timer-filter-summary">No filters applied.</small>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="toggle-timer-filters-btn" data-toggle="collapse" data-target="#timerboard-filters-body" aria-expanded="false" aria-controls="timerboard-filters-body">
                            <i class="fas fa-sliders-h"></i> Show Filters
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="clear-timer-filters-btn">
                            <i class="fas fa-undo"></i> Clear
                        </button>
                    </div>
                </div>
                <div class="timer-filter-chip-row d-none" id="timer-filter-chip-row">
                    <div class="timer-filter-chips" id="timer-filter-chips"></div>
                </div>
                <div class="collapse" id="timerboard-filters-body">
                    <div class="form-row">
                        <div class="form-group col-lg-2 col-md-4">
                            <label for="filter_structure_type">Structure Type</label>
                            <select class="form-control form-control-sm" id="filter_structure_type">
                                <option value="">Any</option>
                                @foreach($structureTypes as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-lg-2 col-md-4">
                            <label for="filter_tag">Tag</label>
                            <select class="form-control form-control-sm" id="filter_tag">
                                <option value="">Any</option>
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-lg-2 col-md-4">
                            <label for="filter_region">Region</label>
                            <select class="form-control form-control-sm" id="filter_region">
                                <option value="">Any</option>
                                @foreach($filterRegions as $region)
                                    <option value="{{ $region }}">{{ $region }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-lg-2 col-md-4">
                            <label for="filter_role">Visibility</label>
                            <select class="form-control form-control-sm" id="filter_role">
                                <option value="">Any</option>
                                <option value="public">Public</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-lg-2 col-md-4">
                            <label for="filter_notes">Notes</label>
                            <select class="form-control form-control-sm" id="filter_notes">
                                <option value="">Any</option>
                                <option value="with">Has note</option>
                                <option value="without">No note</option>
                            </select>
                        </div>
                        <div class="form-group col-lg-2 col-md-4">
                            <label for="filter_owner">Owner</label>
                            <input type="text" class="form-control form-control-sm" id="filter_owner" placeholder="Contains...">
                        </div>
                    </div>
                    <div class="form-row mb-0">
                        <div class="form-group col-lg-3 col-md-4 mb-0">
                            <label for="filter_attacker">Attacker</label>
                            <input type="text" class="form-control form-control-sm" id="filter_attacker" placeholder="Contains...">
                        </div>
                        <div class="form-group col-lg-3 col-md-4 mb-0">
                            <label for="filter_search">Quick Search</label>
                            <input type="text" class="form-control form-control-sm" id="filter_search" placeholder="System, structure, corp...">
                        </div>
                    </div>
                </div>
            </div>

            <div class="timerboard-tabs">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active" href="#current" data-toggle="tab">Current</a></li>
                    <li class="nav-item"><a class="nav-link" href="#elapsed" data-toggle="tab">Elapsed</a></li>
                </ul>
            </div>

            <div class="tab-content">
                <div class="tab-pane active" id="current">
                    <div class="timerboard-section-header">
                        <div>
                            <h5 class="mb-1">Current Timers</h5>
                            <small class="text-muted">Upcoming windows plus anything that elapsed within the last 2 hours.</small>
                        </div>
                        <span class="timerboard-section-pill">
                            <i class="fas fa-info-circle"></i> Grace window: 2 hours
                        </span>
                    </div>
                    <div class="timerboard-table-shell">
                        <table class="table table-hover timers-table" id="current-timers-table">
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
                                    <tr class="timer-row active-timer"
                                        data-time="{{ $timer->eve_time->toIso8601String() }}"
                                        data-structure-type="{{ $timer->structure_type }}"
                                        data-region="{{ $timer->getRegionName() }}"
                                        data-owner="{{ $timer->owner_corporation }}"
                                        data-attacker="{{ $timer->attacker_corporation }}"
                                        data-role-id="{{ $timer->role ? $timer->role->id : 'public' }}"
                                        data-tag-ids="{{ $timer->tags->pluck('id')->implode(',') }}"
                                        data-has-notes="{{ filled($timer->notes) ? '1' : '0' }}">
                                        <td class="timer-system-cell">
                                            @if($timer->getDotlanMapUrl())
                                                <a href="{{ $timer->getDotlanMapUrl() }}" target="_blank" class="timer-primary-link">
                                                    {{ $timer->system }}
                                                </a>
                                            @else
                                                <span class="font-weight-semibold">{{ $timer->system }}</span>
                                            @endif
                                            <br>
                                            <span class="text-muted small">
                                                {{ $timer->getRegionName() }}
                                            </span>
                                        </td>
                                        <td class="timer-type-cell">
                                            <img src="{{ $timer->getStructureImage() }}" alt="{{ $timer->structure_type }}" class="img-circle timer-structure-icon">
                                            <span>{{ $timer->structure_type }}</span>
                                        </td>
                                        <td>@include('seat-timerboard::partials.timer_name', ['timer' => $timer])</td>
                                        <td>{{ $timer->owner_corporation }}</td>
                                        <td>{{ $timer->attacker_corporation }}</td>
                                        <td class="timer-time-cell" data-order="{{ $timer->eve_time->timestamp }}">
                                            <div class="timer-time-primary">{{ $timer->eve_time->copy()->timezone('UTC')->format('l \\@ H:i') }}</div>
                                            <div class="timer-time-secondary">{{ $timer->eve_time->copy()->timezone('UTC')->format('n/j/Y') }}</div>
                                        </td>
                                        <td class="local-time timer-time-cell" data-order="{{ $timer->eve_time->timestamp }}">
                                            <div class="timer-time-primary">Calculating...</div>
                                            <div class="timer-time-secondary">&nbsp;</div>
                                        </td>
                                        <td><span class="countdown timer-countdown-pill">Calculating...</span></td>
                                        <td>
                                            @foreach($timer->tags as $tag)
                                                <span class="badge" style="background-color: {{ $tag->color }}; color: #fff;">{{ $tag->name }}</span>
                                            @endforeach
                                        </td>
                                        <td class="timer-created-by-cell">
                                            {{ $timer->user->name ?? 'Unknown' }}
                                            <br>
                                            <span class="text-muted small">
                                                {{ $timer->role ? $timer->role->title : 'Public' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm timer-actions">
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
                </div>
                <!-- /.tab-pane -->
                <div class="tab-pane" id="elapsed">
                    <div class="timerboard-section-header">
                        <div>
                            <h5 class="mb-1">Elapsed Timers</h5>
                            <small class="text-muted">Older timers kept around for reference once they are outside the grace window.</small>
                        </div>
                        <span class="timerboard-section-pill is-muted">
                            <i class="far fa-clock"></i> Reference queue
                        </span>
                    </div>
                    <div class="timerboard-table-shell is-muted">
                        <table class="table table-hover timers-table" id="elapsed-timers-table">
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
                                    <tr class="timer-row static-timer"
                                        data-time="{{ $timer->eve_time->toIso8601String() }}"
                                        data-structure-type="{{ $timer->structure_type }}"
                                        data-region="{{ $timer->getRegionName() }}"
                                        data-owner="{{ $timer->owner_corporation }}"
                                        data-attacker="{{ $timer->attacker_corporation }}"
                                        data-role-id="{{ $timer->role ? $timer->role->id : 'public' }}"
                                        data-tag-ids="{{ $timer->tags->pluck('id')->implode(',') }}"
                                        data-has-notes="{{ filled($timer->notes) ? '1' : '0' }}">
                                        <td class="timer-system-cell">
                                            @if($timer->getDotlanMapUrl())
                                                <a href="{{ $timer->getDotlanMapUrl() }}" target="_blank" class="timer-primary-link">
                                                    {{ $timer->system }}
                                                </a>
                                            @else
                                                <span class="font-weight-semibold">{{ $timer->system }}</span>
                                            @endif
                                            <br>
                                            <span class="text-muted small">
                                                {{ $timer->getRegionName() }}
                                            </span>
                                        </td>
                                        <td class="timer-type-cell">
                                            <img src="{{ $timer->getStructureImage() }}" alt="{{ $timer->structure_type }}" class="img-circle timer-structure-icon">
                                            <span>{{ $timer->structure_type }}</span>
                                        </td>
                                        <td>@include('seat-timerboard::partials.timer_name', ['timer' => $timer])</td>
                                        <td>{{ $timer->owner_corporation }}</td>
                                        <td>{{ $timer->attacker_corporation }}</td>
                                        <td class="timer-time-cell" data-order="{{ $timer->eve_time->timestamp }}">
                                            <div class="timer-time-primary">{{ $timer->eve_time->copy()->timezone('UTC')->format('l \\@ H:i') }}</div>
                                            <div class="timer-time-secondary">{{ $timer->eve_time->copy()->timezone('UTC')->format('n/j/Y') }}</div>
                                        </td>
                                        <td class="local-time timer-time-cell" data-order="{{ $timer->eve_time->timestamp }}">
                                            <div class="timer-time-primary">Calculating...</div>
                                            <div class="timer-time-secondary">&nbsp;</div>
                                        </td>
                                        <td><span class="countdown timer-countdown-pill is-elapsed">ELAPSED</span></td>
                                        <td>
                                            @foreach($timer->tags as $tag)
                                                <span class="badge" style="background-color: {{ $tag->color }}; color: #fff;">{{ $tag->name }}</span>
                                            @endforeach
                                        </td>
                                        <td class="timer-created-by-cell">
                                            {{ $timer->user->name ?? 'Unknown' }}
                                            <br>
                                            <span class="text-muted small">
                                                {{ $timer->role ? $timer->role->title : 'Public' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm timer-actions">
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
                </div>
                <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
        </div>
    </div>

    @can('seat-timerboard.create')
        <div class="modal fade timerboard-modal timerboard-form-modal timerboard-batch-modal {{ $timerboardThemeClasses }}" id="batchTimerModal" tabindex="-1" role="dialog" aria-labelledby="batchTimerModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title" id="batchTimerModalLabel">Add Timers</h5>
                            <small class="d-block mt-1 batch-modal-description">Build and save multiple timers at once.</small>
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
                                    <div class="text-muted batch-toolbar-copy">
                                        Each row saves as its own timer.
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

        <script type="text/template" id="batch-timer-row-template">
            <div class="card mb-3 batch-timer-row" data-row-key="__ROW_KEY__">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center">
                    <div class="batch-row-heading" role="button" tabindex="0" aria-expanded="true">
                        <span class="batch-row-index">1</span>
                        <div class="batch-row-title-wrap">
                            <strong class="batch-row-title d-block">Timer</strong>
                            <span class="batch-row-subtitle">Fill this in or duplicate it to move faster.</span>
                            <div class="batch-row-summary"></div>
                        </div>
                    </div>
                    <div class="batch-row-actions">
                        <button type="button" class="btn btn-outline-secondary btn-sm batch-note-btn timer-note-launch">
                            <i class="fas fa-sticky-note"></i> <span class="note-btn-label">Add note</span>
                        </button>
                        <button type="button" class="btn btn-light btn-sm toggle-batch-row-btn" title="Collapse or expand timer">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm duplicate-batch-row-btn">
                            <i class="far fa-clone"></i> Duplicate
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm remove-batch-row-btn">
                            <i class="fas fa-times"></i> Remove
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-lg-6">
                            <label>System / Location <span class="text-danger">*</span></label>
                            <select name="timers[__ROW_KEY__][system]" class="form-control batch-system-select" required style="width: 100%;"></select>
                            <small class="text-muted">Search for a solar system or celestial.</small>
                        </div>
                        <div class="form-group col-lg-6">
                            <label>Structure Type <span class="text-danger">*</span></label>
                            <select name="timers[__ROW_KEY__][structure_type]" class="form-control batch-structure-type-select" required style="width: 100%;">
                                __STRUCTURE_TYPE_OPTIONS__
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-lg-6">
                            <label>Structure Name</label>
                            <input type="text" name="timers[__ROW_KEY__][structure_name]" class="form-control" placeholder="Structure Name" value="__STRUCTURE_NAME__">
                            <textarea name="timers[__ROW_KEY__][notes]" class="form-control d-none batch-note-input">__NOTES__</textarea>
                        </div>
                        <div class="form-group col-lg-6">
                            <label>Time <span class="text-danger">*</span></label>
                            <input type="text" name="timers[__ROW_KEY__][time_input]" class="form-control" placeholder="YYYY.MM.DD HH:MM[:SS] or 2 days 4 hours" value="__TIME_INPUT__" required>
                            <small class="text-muted">Absolute EVE time (UTC) or relative time like 1d 4h 30m.</small>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-lg-6">
                            <label>Owner <span class="text-danger">*</span></label>
                            <select name="timers[__ROW_KEY__][owner_corporation]" class="form-control batch-owner-corporation-select" required style="width: 100%;"></select>
                        </div>
                        <div class="form-group col-lg-6">
                            <label>Attacker (Optional)</label>
                            <select name="timers[__ROW_KEY__][attacker_corporation]" class="form-control batch-attacker-corporation-select" style="width: 100%;"></select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-lg-8">
                            <label>Tags</label>
                            <div class="d-flex flex-wrap">__TAG_MARKUP__</div>
                        </div>
                        <div class="form-group col-lg-4">
                            <label>Access Role</label>
                            <select name="timers[__ROW_KEY__][role_id]" class="form-control batch-role-select" style="width: 100%;">
                                __ROLE_OPTIONS__
                            </select>
                            <small class="text-muted">Restrict visibility to a specific role.</small>
                        </div>
                    </div>
                </div>
            </div>
        </script>
    @endcan

    <div class="modal fade timerboard-modal timerboard-note-modal {{ $timerboardThemeClasses }}" id="timerNoteModal" tabindex="-1" role="dialog" aria-labelledby="timerNoteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="timerNoteModalLabel">Timer Notes</h5>
                        <small class="d-block mt-1" id="timerNoteModalSubtitle" style="opacity: 0.85;"></small>
                    </div>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="timer-note-context">
                        <div class="text-muted text-uppercase small font-weight-bold">Timer</div>
                        <div class="h5 mb-0" id="timerNoteModalTitle">Structure</div>
                    </div>

                    <div id="timerNoteReadonly" class="timer-note-readonly d-none"></div>

                    <div id="timerNoteEditor">
                        <div class="form-group mb-0">
                            <label for="timer_notes">Notes</label>
                            <textarea
                                name="notes"
                                id="timer_notes"
                                class="form-control"
                                rows="9"
                                placeholder="Add fittings, handoff details, or any other notes for this timer."></textarea>
                            <small class="d-flex justify-content-between text-muted mt-2">
                                <span>Optional context for fittings, access instructions, or quick handoffs.</span>
                                <span id="timer-note-char-count">0 characters</span>
                            </small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-outline-secondary" id="clearTimerNoteBtn">Clear Note</button>
                    <button type="button" class="btn btn-primary d-none" id="applyTimerNoteDraftBtn">Apply Note</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade timerboard-modal timerboard-form-modal timerboard-edit-modal {{ $timerboardThemeClasses }}" id="editTimerModal" tabindex="-1" role="dialog" aria-labelledby="editTimerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
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

                        <textarea name="notes" class="form-control d-none" id="edit_notes">{{ old('form_context') === 'edit' ? old('notes') : '' }}</textarea>

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
                        <button type="button" class="btn btn-outline-secondary btn-sm mr-auto edit-note-trigger" id="edit-note-btn">
                            <i class="fas fa-sticky-note"></i> <span class="note-btn-label">Add optional note</span>
                        </button>
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
        'notes' => old('form_context') === 'edit' ? old('notes') : '',
        'role_id' => old('form_context') === 'edit' ? old('role_id') : '',
        'tags' => old('tags', []),
    ];
@endphp

@push('head')
    @include('seat-timerboard::partials.styles')
@endpush

@push('javascript')
    @include('seat-timerboard::partials.scripts')
@endpush
