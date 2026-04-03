<style>
    .timer-name-cell {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        flex-wrap: wrap;
    }

    .timer-note-trigger {
        color: #617488;
        line-height: 1;
    }

    .timer-note-trigger:hover,
    .timer-note-trigger:focus {
        color: #214c6c;
        text-decoration: none;
    }

    .edit-note-trigger {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-weight: 500;
        border-radius: 999px;
        padding-left: 0.8rem;
        padding-right: 0.8rem;
        color: #5f6f82;
        border-color: rgba(95, 111, 130, 0.35);
        background: rgba(255, 255, 255, 0.92);
    }

    .edit-note-trigger:hover,
    .edit-note-trigger:focus {
        color: #214c6c;
        border-color: rgba(33, 76, 108, 0.4);
        background: #f7fbff;
        box-shadow: none;
    }

    .edit-note-trigger.has-note {
        color: #214c6c;
        border-color: rgba(33, 76, 108, 0.45);
        background: rgba(33, 76, 108, 0.08);
    }

    .timer-note-launch.has-note {
        border-color: #214c6c;
        background: #214c6c;
        color: #fff;
    }

    .timer-note-context {
        margin-bottom: 1rem;
    }

    .timer-note-readonly {
        min-height: 7rem;
        padding: 1rem 1.1rem;
        border: 1px solid rgba(31, 73, 103, 0.14);
        border-radius: 12px;
        background: #fff;
        color: #314557;
        line-height: 1.6;
        white-space: pre-line;
        box-shadow: inset 0 1px 1px rgba(15, 23, 42, 0.03);
    }

    #batchTimerModal .batch-timer-row .form-group,
    #editTimerModal .form-group,
    #timerNoteModal .form-group {
        position: relative;
    }

    #batchTimerModal .modal-content,
    #editTimerModal .modal-content,
    #timerNoteModal .modal-content {
        border: 0;
        border-radius: 14px;
        overflow: visible;
        box-shadow: 0 18px 45px rgba(26, 35, 52, 0.22);
    }

    #batchTimerModal .modal-header,
    #editTimerModal .modal-header,
    #timerNoteModal .modal-header {
        border-bottom: 0;
        background: linear-gradient(135deg, #183247 0%, #214c6c 100%);
        color: #fff;
    }

    #batchTimerModal .modal-header .close,
    #editTimerModal .modal-header .close,
    #timerNoteModal .modal-header .close {
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

    #timerNoteModal .modal-body {
        background: linear-gradient(180deg, #f8fafd 0%, #f1f5f9 100%);
    }

    #timerNoteModal {
        z-index: 1060;
    }

    .modal-backdrop.timer-note-backdrop {
        z-index: 1059;
    }

    #editTimerModal .modal-dialog,
    #timerNoteModal .modal-dialog {
        min-height: calc(100vh - 3.5rem);
        display: flex;
        align-items: center;
        margin-left: auto;
        margin-right: auto;
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

    #batchTimerModal .select2-container--bootstrap4 .select2-selection,
    #editTimerModal .select2-container--bootstrap4 .select2-selection {
        min-height: calc(2.25rem + 2px);
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        background: #fff;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.03);
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    #batchTimerModal .select2-container--bootstrap4.select2-container--focus .select2-selection,
    #batchTimerModal .select2-container--bootstrap4.select2-container--open .select2-selection,
    #editTimerModal .select2-container--bootstrap4.select2-container--focus .select2-selection,
    #editTimerModal .select2-container--bootstrap4.select2-container--open .select2-selection {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.14);
    }

    #batchTimerModal .select2-container--bootstrap4 .select2-selection--single,
    #editTimerModal .select2-container--bootstrap4 .select2-selection--single {
        padding: 0.375rem 0.75rem;
    }

    #batchTimerModal .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered,
    #editTimerModal .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        padding: 0;
        color: #495057;
        line-height: 1.5;
        display: block;
    }

    #batchTimerModal .select2-container--bootstrap4 .select2-selection__placeholder,
    #editTimerModal .select2-container--bootstrap4 .select2-selection__placeholder {
        color: #6c757d;
    }

    #batchTimerModal .select2-container--bootstrap4 .select2-selection__arrow,
    #editTimerModal .select2-container--bootstrap4 .select2-selection__arrow {
        display: none;
    }

    #batchTimerModal .select2-container--bootstrap4 .select2-selection__clear,
    #editTimerModal .select2-container--bootstrap4 .select2-selection__clear {
        color: #6c757d;
        margin-right: 0;
    }

    #batchTimerModal .select2-container--bootstrap4 .select2-dropdown,
    #editTimerModal .select2-container--bootstrap4 .select2-dropdown {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        box-shadow: 0 10px 24px rgba(23, 43, 77, 0.14);
        overflow: hidden;
        background: #fff;
    }

    #batchTimerModal .select2-container--bootstrap4 .select2-search--dropdown,
    #editTimerModal .select2-container--bootstrap4 .select2-search--dropdown {
        padding: 0.45rem;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
    }

    #batchTimerModal .select2-container--bootstrap4 .select2-search__field,
    #editTimerModal .select2-container--bootstrap4 .select2-search__field {
        height: calc(2rem + 2px);
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        padding: 0.25rem 0.6rem;
        font-size: 0.95rem;
        background: #fff;
    }

    #batchTimerModal .select2-container--bootstrap4 .select2-results__option,
    #editTimerModal .select2-container--bootstrap4 .select2-results__option {
        padding: 0.5rem 0.75rem;
        font-size: 0.95rem;
        color: #495057;
    }

    #batchTimerModal .select2-container--bootstrap4 .select2-results__option--highlighted[aria-selected],
    #editTimerModal .select2-container--bootstrap4 .select2-results__option--highlighted[aria-selected] {
        background: #e9f2ff;
        color: #1f3b57;
    }

    #batchTimerModal .select2-container--bootstrap4 .select2-results__option[aria-selected=true],
    #editTimerModal .select2-container--bootstrap4 .select2-results__option[aria-selected=true] {
        background: #f2f4f7;
        color: #1f3b57;
        font-weight: 600;
    }

    #batchTimerModal .select2-dropdown,
    #editTimerModal .select2-dropdown {
        z-index: 2055;
    }
</style>
