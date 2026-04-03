<style>
    .timerboard-toolbar {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 0.85rem;
        margin-bottom: 1rem;
    }

    .timerboard-toolbar-copy h5 {
        color: #183247;
        font-weight: 700;
    }

    .timerboard-toolbar-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        align-items: center;
    }

    .timerboard-primary-action {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.55rem 0.9rem;
        border-radius: 999px;
        box-shadow: 0 10px 20px rgba(0, 123, 255, 0.16);
    }

    .timerboard-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 0.85rem;
        margin-bottom: 1rem;
    }

    .timerboard-tabs {
        margin-bottom: 1rem;
        padding-bottom: 0.15rem;
        border-bottom: 1px solid rgba(24, 50, 71, 0.08);
    }

    .timerboard-tabs .nav-pills {
        gap: 0.5rem;
    }

    .timerboard-tabs .nav-link {
        border-radius: 999px;
        padding: 0.42rem 0.9rem;
        color: #5f6f82;
        font-weight: 600;
    }

    .timerboard-tabs .nav-link.active {
        box-shadow: 0 8px 16px rgba(24, 50, 71, 0.12);
    }

    .timerboard-stat-card {
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
        padding: 1rem 1.05rem;
        border: 1px solid rgba(24, 50, 71, 0.09);
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 10px 22px rgba(24, 50, 71, 0.05);
    }

    .timerboard-stat-card.is-urgent {
        background: linear-gradient(180deg, #fff7ef 0%, #fffdf9 100%);
        border-color: rgba(198, 112, 36, 0.2);
    }

    .timerboard-stat-label {
        color: #708294;
        font-size: 0.76rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .timerboard-stat-value {
        color: #183247;
        font-size: 1.65rem;
        font-weight: 700;
        line-height: 1.1;
    }

    .timerboard-stat-meta {
        color: #6f8194;
        font-size: 0.84rem;
    }

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

    .timerboard-filters {
        padding: 1rem 1rem 0.85rem;
        border: 1px solid rgba(31, 73, 103, 0.1);
        border-radius: 12px;
        background: linear-gradient(180deg, #fbfcfe 0%, #f4f7fb 100%);
        box-shadow: 0 8px 20px rgba(24, 50, 71, 0.05);
    }

    .timerboard-filters-header {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: flex-start;
        gap: 0.75rem;
        margin-bottom: 0.85rem;
    }

    .timerboard-filters-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: flex-end;
        gap: 0.5rem;
    }

    .timer-filter-summary-header {
        min-height: 1rem;
    }

    .timer-filter-chip-row {
        margin-bottom: 0.8rem;
    }

    .timer-filter-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 0.45rem;
    }

    .timer-filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.28rem 0.65rem;
        border-radius: 999px;
        background: rgba(33, 76, 108, 0.08);
        color: #214c6c;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .timerboard-filters .form-group label {
        font-size: 0.78rem;
        font-weight: 600;
        letter-spacing: 0.01em;
        color: #54657a;
        margin-bottom: 0.35rem;
    }

    .timerboard-filters .form-control-sm {
        min-height: calc(1.8125rem + 2px);
    }

    .timerboard-section-header {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 0.85rem;
    }

    .timerboard-section-header h5 {
        color: #183247;
        font-weight: 700;
    }

    .timerboard-section-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.3rem 0.7rem;
        border-radius: 999px;
        background: rgba(33, 76, 108, 0.08);
        color: #214c6c;
        font-size: 0.82rem;
        font-weight: 600;
    }

    .timerboard-section-pill.is-muted {
        background: rgba(108, 117, 125, 0.1);
        color: #5f6f82;
    }

    .timerboard-table-shell {
        border: 1px solid rgba(24, 50, 71, 0.08);
        border-radius: 14px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 14px 28px rgba(24, 50, 71, 0.05);
    }

    .timerboard-table-shell.is-muted {
        box-shadow: 0 10px 22px rgba(24, 50, 71, 0.035);
    }

    .timerboard-table-shell .dataTables_wrapper {
        padding: 0.9rem 0.95rem 0.25rem;
    }

    .timerboard-table-shell table.dataTable {
        margin-top: 0 !important;
        margin-bottom: 0.75rem !important;
        border-collapse: separate !important;
        border-spacing: 0;
    }

    .timerboard-table-shell .table thead th {
        border-top: 0;
        border-bottom: 1px solid rgba(24, 50, 71, 0.08);
        background: #f7f9fc;
        color: #56687b;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .timerboard-table-shell .table td {
        border-top: 1px solid rgba(24, 50, 71, 0.06);
        padding-top: 0.9rem;
        padding-bottom: 0.9rem;
        vertical-align: middle;
    }

    .timer-row {
        transition: background-color 0.16s ease, box-shadow 0.16s ease;
    }

    .timer-row.is-soon {
        background: rgba(255, 248, 235, 0.55);
    }

    .timer-row.is-imminent {
        background: rgba(255, 241, 226, 0.92);
        box-shadow: inset 4px 0 0 #d9822b;
    }

    .timer-row.static-timer {
        background: rgba(248, 250, 252, 0.82);
    }

    .timer-primary-link,
    .timer-system-cell .font-weight-semibold {
        color: #183247;
        font-weight: 700;
    }

    .timer-primary-link:hover,
    .timer-primary-link:focus {
        color: #214c6c;
    }

    .timer-structure-icon {
        width: 24px;
        height: 24px;
        margin-right: 0.45rem;
        box-shadow: 0 4px 10px rgba(24, 50, 71, 0.12);
    }

    .timer-type-cell {
        color: #314557;
        font-weight: 600;
        white-space: nowrap;
    }

    .timer-time-cell {
        color: #2f4355;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    .timer-time-primary {
        color: #21384c;
        font-weight: 600;
        line-height: 1.2;
    }

    .timer-time-secondary {
        margin-top: 0.15rem;
        color: #7a8897;
        font-size: 0.78rem;
        line-height: 1.15;
    }

    .timer-created-by-cell {
        min-width: 9rem;
    }

    .timer-actions .btn {
        border-radius: 8px;
    }

    .timer-countdown-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 7.75rem;
        padding: 0.4rem 0.7rem;
        border-radius: 999px;
        background: #eef3f8;
        color: #22435b;
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: 0.01em;
        line-height: 1.1;
        font-variant-numeric: tabular-nums;
    }

    .timer-countdown-pill.is-soon {
        background: #fff1db;
        color: #9f5c00;
    }

    .timer-countdown-pill.is-imminent {
        background: #ffe3c0;
        color: #8b3c00;
        box-shadow: inset 0 0 0 1px rgba(139, 60, 0, 0.12);
    }

    .timer-countdown-pill.is-elapsed {
        background: #f7dfe2;
        color: #9f2d3d;
    }

    .timerboard-table-shell .dataTables_length,
    .timerboard-table-shell .dataTables_filter {
        margin-bottom: 0.75rem;
    }

    .timerboard-table-shell .dataTables_info,
    .timerboard-table-shell .dataTables_paginate {
        padding-bottom: 0.5rem;
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

    @media (max-width: 767.98px) {
        .timerboard-filters {
            padding-left: 1rem;
            padding-right: 1rem;
        }

        .timerboard-table-shell .dataTables_wrapper {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        .timer-countdown-pill {
            min-width: auto;
        }
    }
</style>
