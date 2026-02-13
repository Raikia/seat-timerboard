@extends('web::layouts.grids.12')

@section('title', 'Timerboard')
@section('page_header', 'Timerboard')

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Structure Timers</h3>
            <div class="card-tools">
                <a href="{{ route('timerboard.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add Timer
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <table class="table table-hover table-striped" id="timers-table">
                <thead>
                    <tr>
                        <th>System</th>
                        <th>Type</th>
                        <th>Name</th>
                        <th>Owner</th>
                        <th>Eve Time (UTC)</th>
                        <th>Local Time</th>
                        <th>Countdown</th>
                        <th>Tags</th>
                        <th>Created By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($timers as $timer)
                        <tr class="timer-row" data-time="{{ $timer->eve_time->toIso8601String() }}">
                            <td>{{ $timer->system }}</td>
                            <td>{{ $timer->structure_type }}</td>
                            <td>{{ $timer->structure_name }}</td>
                            <td>{{ $timer->owner_corporation }}</td>
                            <td>{{ $timer->eve_time->format('Y-m-d H:i:s') }}</td>
                            <td class="local-time">Calculating...</td>
                            <td class="countdown font-weight-bold">Calculating...</td>
                            <td>
                                @foreach($timer->tags as $tag)
                                    <span class="badge" style="background-color: {{ $tag->color }}; color: #fff;">{{ $tag->name }}</span>
                                @endforeach
                            </td>
                            <td>{{ $timer->user->name ?? 'Unknown' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('javascript')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function updateTimers() {
            const now = new Date();
            const rows = document.querySelectorAll('.timer-row');

            rows.forEach(row => {
                const timeStr = row.getAttribute('data-time');
                const eveTime = new Date(timeStr);
                const diff = eveTime - now;

                // Local Time Update (Once mainly, but keeping it simple)
                const localTimeCell = row.querySelector('.local-time');
                if (localTimeCell.textContent === 'Calculating...') {
                    localTimeCell.textContent = eveTime.toLocaleString();
                }

                // Countdown Update
                const countdownCell = row.querySelector('.countdown');
                if (diff <= 0) {
                    countdownCell.textContent = 'ELAPSED';
                    countdownCell.classList.add('text-danger');
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

        setInterval(updateTimers, 1000);
        updateTimers();
    });
</script>
@endpush
