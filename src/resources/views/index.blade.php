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
                            <i class="fas fa-plus"></i> Add Timer
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

    <!-- Timer Modal -->
    <div class="modal fade" id="timerModal" tabindex="-1" role="dialog" aria-labelledby="timerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="timerModalLabel">Timer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="timerForm" action="" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="timer_id" id="timer_id" value="">
                    
                    <div class="modal-body">
                        
                        <div class="form-group">
                            <label for="system">System / Location <span class="text-danger">*</span></label>
                            <select name="system" class="form-control select2-system" id="system" required style="width: 100%;">
                                @if(old('system'))
                                    <option value="{{ old('system') }}" selected>{{ old('system') }}</option>
                                @endif
                                <!-- Suggestion via AJAX -->
                            </select>
                            <small class="text-muted">Search for a solar system or celestial (e.g. Moon, Planet)</small>
                        </div>

                        <div class="form-group">
                            <label for="structure_type">Structure Type <span class="text-danger">*</span></label>
                            <select name="structure_type" class="form-control select2-structure-type" id="structure_type" required style="width: 100%;">
                                <option value="">Select Type</option>
                                <option value="Ansiblex">Ansiblex Jump Gate</option>
                                <option value="Astrahus">Astrahus</option>
                                <option value="Athanor">Athanor</option>
                                <option value="Azbel">Azbel</option>
                                <option value="POCO">Customs Office</option>
                                <option value="Fortizar">Fortizar</option>
                                <option value="Keepstar">Keepstar</option>
                                <option value="Metenox">Metenox Moon Drill</option>                        
                                <option value="Pharolux">Pharolux Cyno Beacon</option>
                                <option value="POS">POS</option>
                                <option value="Raitaru">Raitaru</option>
                                <option value="Skyhook">Skyhook</option>
                                <option value="Sotiyo">Sotiyo</option>
                                <option value="Tatara">Tatara</option>
                                <option value="Tenebrex">Tenebrex Jammer</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="structure_name">Structure Name</label>
                            <input type="text" name="structure_name" class="form-control" id="structure_name" placeholder="Structure Name" value="{{ old('structure_name') }}">
                        </div>

                        <div class="form-group">
                            <label for="owner_corporation">Owner <span class="text-danger">*</span></label>
                            <select name="owner_corporation" class="form-control select2-corporation" id="owner_corporation" required style="width: 100%;">
                                @if(old('owner_corporation'))
                                    <option value="{{ old('owner_corporation') }}" selected>{{ old('owner_corporation') }}</option>
                                @endif
                                <!-- Suggestion via AJAX -->
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="attacker_corporation">Attacker (Optional)</label>
                            <select name="attacker_corporation" class="form-control select2-attacker-corporation" id="attacker_corporation" style="width: 100%;">
                                @if(old('attacker_corporation'))
                                    <option value="{{ old('attacker_corporation') }}" selected>{{ old('attacker_corporation') }}</option>
                                @endif
                                <!-- Suggestion via AJAX -->
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="time_input">Time <span class="text-danger">*</span></label>
                            <input type="text" name="time_input" class="form-control" id="time_input" placeholder="YYYY.MM.DD HH:MM:SS or '2 days 4 hours'" value="{{ old('time_input') }}" required>
                            <small class="form-text text-muted">Enter absolute EVE time (UTC) or relative time like '1d 4h 30m'.</small>
                        </div>

                        <div class="form-group">
                            <label>Tags</label>
                            <div class="d-flex flex-wrap">
                                @foreach($tags as $tag)
                                    <div class="m-1">
                                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" id="tag_{{ $tag->id }}" class="d-none tag-checkbox">
                                        <label class="badge p-2 tag-badge" for="tag_{{ $tag->id }}" 
                                               style="background-color: {{ $tag->color }}; color: #fff; cursor: pointer; opacity: 0.5; border: 2px solid transparent;"
                                               data-color="{{ $tag->color }}">
                                            {{ $tag->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="role_id">Access Role</label>
                            <select name="role_id" class="form-control" id="role_id" style="width: 100%;">
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

@push('javascript')
<script>
    $(document).ready(function() {
        
        // Initialize Select2
        function initSelect2() {
            $('.select2-structure-type').select2({
                theme: 'bootstrap4',
                dropdownParent: $('#timerModal'),
                placeholder: 'Select Structure Type',
                allowClear: true
            });

            $('.select2-system').select2({
                theme: 'bootstrap4',
                dropdownParent: $('#timerModal'),
                placeholder: 'Search for a system or celestial...',
                minimumInputLength: 3,
                ajax: {
                    url: '{{ route("timerboard.search.systems") }}',
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
            });

            $('.select2-corporation').select2({
                theme: 'bootstrap4',
                dropdownParent: $('#timerModal'),
                placeholder: 'Search for corporation or alliance...',
                minimumInputLength: 3,
                ajax: {
                    url: '{{ route("timerboard.search.corporations") }}',
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
            });

             $('.select2-attacker-corporation').select2({
                theme: 'bootstrap4',
                dropdownParent: $('#timerModal'),
                placeholder: 'Search for attacker (corp/alliance)...',
                minimumInputLength: 3,
                allowClear: true,
                ajax: {
                    url: '{{ route("timerboard.search.corporations") }}',
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
            });
        }

        initSelect2();

        // Handle "Add Timer" click
        $('#create-timer-btn').click(function() {
            $('#timerModalLabel').text('Add Timer');
            $('#timerForm').attr('action', '{{ route("timerboard.store") }}');
            $('#formMethod').val('POST');
            $('#timerForm')[0].reset();
            $('#timer_id').val('');
            
            // Clear Select2s
            $('.select2-system').val(null).trigger('change');
            $('.select2-structure-type').val(null).trigger('change');
            $('.select2-corporation').val(null).trigger('change');
            $('.select2-attacker-corporation').val(null).trigger('change');
            
            // Uncheck all tags and reset visuals
            $('.tag-checkbox').prop('checked', false).trigger('change');

            // Reset Role to default
            $('#role_id').val('{{ $defaultRoleId }}');

            $('#timerModal').modal('show');
        });
        
        // Tag visual toggle
        $(document).on('change', '.tag-checkbox', function() {
            var label = $('label[for="' + $(this).attr('id') + '"]');
            if($(this).is(':checked')) {
                label.css('opacity', '1');
                label.css('box-shadow', '0 0 5px rgba(0,0,0,0.5)');
            } else {
                label.css('opacity', '0.5');
                label.css('box-shadow', 'none');
            }
        });

        // Handle "Edit Timer" click
        $('.edit-timer-btn').click(function() {
            var timer = $(this).data('timer');
            var tags = $(this).data('tags'); // Array of tag IDs

            $('#timerModalLabel').text('Edit Timer');
            
            var url = '{{ route("timerboard.update", ":id") }}';
            url = url.replace(':id', timer.id);
            
            $('#timerForm').attr('action', url);
            $('#formMethod').val('PUT');
            $('#timer_id').val(timer.id);

            // Populate fields
            $('#structure_name').val(timer.structure_name);
            
            // Format time YYYY.MM.DD HH:MM:SS
            var date = new Date(timer.eve_time);
            var formattedTime = date.getUTCFullYear() + "." +
                                ("0" + (date.getUTCMonth() + 1)).slice(-2) + "." +
                                ("0" + date.getUTCDate()).slice(-2) + " " +
                                ("0" + date.getUTCHours()).slice(-2) + ":" +
                                ("0" + date.getUTCMinutes()).slice(-2) + ":" +
                                ("0" + date.getUTCSeconds()).slice(-2);
            $('#time_input').val(formattedTime); 

            // Populate Select2s (System)
            var systemOption = new Option(timer.system, timer.system, true, true);
            $('.select2-system').append(systemOption).trigger('change');

            // Structure Type
            $('.select2-structure-type').val(timer.structure_type).trigger('change');

            // Owner Corp
            var ownerOption = new Option(timer.owner_corporation, timer.owner_corporation, true, true);
            $('.select2-corporation').append(ownerOption).trigger('change');

            // Attacker Corp
            if (timer.attacker_corporation) {
                var attackerOption = new Option(timer.attacker_corporation, timer.attacker_corporation, true, true);
                $('.select2-attacker-corporation').append(attackerOption).trigger('change');
            } else {
                 $('.select2-attacker-corporation').val(null).trigger('change');
            }

            $('.tag-checkbox').prop('checked', false).trigger('change');
            if (tags) {
                tags.forEach(function(tagId) {
                    $('#tag_' + tagId).prop('checked', true).trigger('change');
                });
            }

            // Role
            $('#role_id').val(timer.role_id || '');

            $('#timerModal').modal('show');
        });

        @if($errors->any())
            $('#timerModal').modal('show');
            var oldMethod = '{{ old("_method") }}';
            if (oldMethod === 'PUT') {
                var timerId = '{{ old("timer_id") }}';
                var url = '{{ route("timerboard.update", ":id") }}';
                url = url.replace(':id', timerId);
                $('#timerForm').attr('action', url);
                $('#timerModalLabel').text('Edit Timer');
                $('#formMethod').val('PUT');
                $('#timer_id').val(timerId); // Restore ID
            } else {
                 $('#timerForm').attr('action', '{{ route("timerboard.store") }}');
                 $('#timerModalLabel').text('Add Timer');
                 $('#formMethod').val('POST');
            }
            
            // Restore Structure Type Select2
            var oldType = '{{ old("structure_type") }}';
            if(oldType) {
                 $('.select2-structure-type').val(oldType).trigger('change');
            }

            // Restore Tags
            var oldTags = @json(old('tags', []));
            if(oldTags && oldTags.length > 0) {
                 oldTags.forEach(function(tagId) {
                      $('#tag_' + tagId).prop('checked', true).trigger('change');
                 });
            }

            // Restore Role
            var oldRole = '{{ old("role_id") }}';
            if(oldRole !== '') {
                $('#role_id').val(oldRole);
            }
        @endif

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
    });
</script>
@endpush
