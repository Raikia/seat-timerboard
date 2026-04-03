<div class="timer-name-cell">
    <span>{{ $timer->structure_name ?: '—' }}</span>
    @if(filled($timer->notes))
        <button
            type="button"
            class="btn btn-link btn-sm p-0 timer-note-trigger"
            title="View saved note"
            data-note-title="{{ $timer->structure_name ?: $timer->system }}"
            data-note-system="{{ $timer->system }}">
            <i class="fas fa-sticky-note"></i>
        </button>
        <textarea class="d-none timer-note-content" aria-hidden="true" tabindex="-1">{{ $timer->notes }}</textarea>
    @endif
</div>
