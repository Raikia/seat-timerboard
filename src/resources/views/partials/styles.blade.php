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

    .timerboard-action-row {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 1rem;
        justify-content: flex-end;
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
        background: transparent;
    }

    .timer-row.is-imminent {
        background: transparent;
        box-shadow: none;
    }

    .timer-row.static-timer {
        background: transparent;
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
        background: linear-gradient(180deg, #eff5fb 0%, #e3edf7 100%);
        color: #21425a;
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: 0.01em;
        line-height: 1.1;
        font-variant-numeric: tabular-nums;
        box-shadow: inset 0 0 0 1px rgba(34, 67, 91, 0.08), 0 2px 6px rgba(24, 50, 71, 0.06);
    }

    .timer-countdown-pill.is-soon {
        background: linear-gradient(180deg, #ffebb9 0%, #ffd796 100%);
        color: #8f4800;
        box-shadow: inset 0 0 0 1px rgba(143, 72, 0, 0.18), 0 2px 6px rgba(143, 72, 0, 0.08);
    }

    .timer-countdown-pill.is-imminent {
        background: linear-gradient(180deg, #ffc777 0%, #ffab5a 100%);
        color: #6d2300;
        box-shadow: inset 0 0 0 1px rgba(109, 35, 0, 0.2), 0 2px 6px rgba(109, 35, 0, 0.1);
    }

    .timer-countdown-pill.is-elapsed {
        background: linear-gradient(180deg, #f3c9d2 0%, #e8aab7 100%);
        color: #842436;
        box-shadow: inset 0 0 0 1px rgba(132, 36, 54, 0.18), 0 2px 6px rgba(132, 36, 54, 0.08);
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

    #batchTimerModal .batch-modal-description {
        opacity: 0.85;
    }

    #batchTimerModal .batch-toolbar-copy {
        font-size: 0.88rem;
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
        font-size: 0.8rem;
    }

    #batchTimerModal .card-body label,
    #editTimerModal .modal-body label,
    #timerNoteModal .modal-body label {
        color: #314557;
        font-weight: 600;
    }

    #batchTimerModal .card-body .text-muted,
    #editTimerModal .modal-body .text-muted,
    #timerNoteModal .modal-body .text-muted {
        color: #6c7b8d !important;
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

    body.dark-mode {
        --timerboard-dark-text: #e6edf5;
        --timerboard-dark-text-secondary: #f5f9fd;
        --timerboard-dark-text-muted: #9fb0c3;
        --timerboard-dark-text-soft: #b9c8d6;
        --timerboard-dark-surface: #2a3441;
        --timerboard-dark-surface-alt: #313d4b;
        --timerboard-dark-surface-soft: #252f3a;
        --timerboard-dark-surface-deep: #222a35;
        --timerboard-dark-border: rgba(174, 192, 210, 0.16);
        --timerboard-dark-border-soft: rgba(174, 192, 210, 0.12);
        --timerboard-dark-border-subtle: rgba(174, 192, 210, 0.08);
        --timerboard-dark-chip-bg: rgba(122, 167, 211, 0.12);
        --timerboard-dark-chip-bg-hover: rgba(122, 167, 211, 0.18);
        --timerboard-dark-chip-border: rgba(122, 167, 211, 0.24);
        --timerboard-dark-chip-border-hover: rgba(122, 167, 211, 0.34);
        --timerboard-dark-active-bg: rgba(80, 145, 209, 0.22);
        --timerboard-dark-active-border: rgba(122, 167, 211, 0.4);
        --timerboard-dark-index-bg: #335b81;
        --timerboard-dark-dropdown-highlight: #3a4b5d;
        --timerboard-dark-dropdown-selected: #324152;
        --timerboard-dark-row-alt: rgba(255, 255, 255, 0.04);
        --timerboard-dark-row-soon: rgba(125, 86, 32, 0.22);
        --timerboard-dark-row-imminent: rgba(149, 82, 23, 0.32);
        --timerboard-dark-row-static: rgba(255, 255, 255, 0.02);
        --timerboard-dark-accent: #d9822b;
        --timerboard-dark-urgent-bg: linear-gradient(180deg, #3a3327 0%, #312c24 100%);
        --timerboard-dark-urgent-border: rgba(217, 130, 43, 0.32);
        --timerboard-dark-modal-header: linear-gradient(180deg, #303b48 0%, #293340 100%);
    }

    .timerboard-dark-skin {
        --timerboard-dark-text: var(--color-text-primary, #e6edf5);
        --timerboard-dark-text-secondary: var(--color-text-secondary, #f5f9fd);
        --timerboard-dark-text-muted: var(--color-text-tertiary, #9fb0c3);
        --timerboard-dark-text-soft: var(--color-text-tertiary, #b9c8d6);
        --timerboard-dark-surface: var(--color-background-secondary, #2a3441);
        --timerboard-dark-surface-alt: var(--color-background-tertiary, #313d4b);
        --timerboard-dark-surface-soft: var(--color-background-secondary, #252f3a);
        --timerboard-dark-surface-deep: var(--color-background-primary, #222a35);
        --timerboard-dark-border: rgba(174, 192, 210, 0.16);
        --timerboard-dark-border-soft: rgba(174, 192, 210, 0.12);
        --timerboard-dark-border-subtle: rgba(174, 192, 210, 0.08);
        --timerboard-dark-chip-bg: rgba(122, 167, 211, 0.12);
        --timerboard-dark-chip-bg-hover: rgba(122, 167, 211, 0.18);
        --timerboard-dark-chip-border: rgba(122, 167, 211, 0.24);
        --timerboard-dark-chip-border-hover: rgba(122, 167, 211, 0.34);
        --timerboard-dark-active-bg: rgba(80, 145, 209, 0.22);
        --timerboard-dark-active-border: rgba(122, 167, 211, 0.4);
        --timerboard-dark-index-bg: var(--color-accent-secondary, #335b81);
        --timerboard-dark-dropdown-highlight: var(--color-accent-secondary, #3a4b5d);
        --timerboard-dark-dropdown-selected: rgba(255, 255, 255, 0.08);
        --timerboard-dark-row-alt: rgba(255, 255, 255, 0.04);
        --timerboard-dark-row-soon: rgba(125, 86, 32, 0.22);
        --timerboard-dark-row-imminent: rgba(149, 82, 23, 0.32);
        --timerboard-dark-row-static: rgba(255, 255, 255, 0.02);
        --timerboard-dark-accent: #d9822b;
        --timerboard-dark-urgent-bg: linear-gradient(180deg, rgba(217, 130, 43, 0.18) 0%, rgba(110, 74, 33, 0.22) 100%);
        --timerboard-dark-urgent-border: rgba(217, 130, 43, 0.32);
        --timerboard-dark-modal-header: linear-gradient(180deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%);
    }

    body.dark-mode .timerboard-toolbar-copy h5,
    body.dark-mode .timerboard-section-header h5,
    body.dark-mode .timerboard-stat-value,
    body.dark-mode .timer-primary-link,
    body.dark-mode .timer-system-cell .font-weight-semibold,
    body.dark-mode .timer-type-cell,
    body.dark-mode .timer-time-cell,
    body.dark-mode .timer-time-primary,
    .timerboard-dark-skin .timerboard-toolbar-copy h5,
    .timerboard-dark-skin .timerboard-section-header h5,
    .timerboard-dark-skin .timerboard-stat-value,
    .timerboard-dark-skin .timer-primary-link,
    .timerboard-dark-skin .timer-system-cell .font-weight-semibold,
    .timerboard-dark-skin .timer-type-cell,
    .timerboard-dark-skin .timer-time-cell,
    .timerboard-dark-skin .timer-time-primary {
        color: var(--timerboard-dark-text);
    }

    body.dark-mode .timerboard-stat-meta,
    body.dark-mode .timer-time-secondary,
    body.dark-mode .timerboard-toolbar-copy .text-muted,
    body.dark-mode .timerboard-section-header .text-muted,
    body.dark-mode .timer-created-by-cell .text-muted,
    body.dark-mode .batch-footer-summary,
    body.dark-mode #batchTimerModal .batch-row-subtitle,
    .timerboard-dark-skin .timerboard-stat-meta,
    .timerboard-dark-skin .timer-time-secondary,
    .timerboard-dark-skin .timerboard-toolbar-copy .text-muted,
    .timerboard-dark-skin .timerboard-section-header .text-muted,
    .timerboard-dark-skin .timer-created-by-cell .text-muted,
    .timerboard-dark-skin .batch-footer-summary,
    .timerboard-dark-skin .batch-row-subtitle {
        color: var(--timerboard-dark-text-muted) !important;
    }

    body.dark-mode .timerboard-stat-card,
    body.dark-mode .timerboard-filters,
    body.dark-mode .timerboard-table-shell,
    .timerboard-dark-skin .timerboard-stat-card,
    .timerboard-dark-skin .timerboard-filters,
    .timerboard-dark-skin .timerboard-table-shell {
        background: var(--timerboard-dark-surface);
        border-color: var(--timerboard-dark-border);
        box-shadow: 0 14px 28px rgba(0, 0, 0, 0.24);
    }

    body.dark-mode .timerboard-stat-card.is-urgent,
    .timerboard-dark-skin .timerboard-stat-card.is-urgent {
        background: var(--timerboard-dark-urgent-bg);
        border-color: var(--timerboard-dark-urgent-border);
    }

    body.dark-mode .timerboard-tabs,
    .timerboard-dark-skin .timerboard-tabs {
        border-bottom-color: var(--timerboard-dark-border-soft);
    }

    body.dark-mode .timerboard-tabs .nav-link,
    .timerboard-dark-skin .timerboard-tabs .nav-link {
        color: var(--timerboard-dark-text-muted);
    }

    body.dark-mode .timerboard-tabs .nav-link.active,
    .timerboard-dark-skin .timerboard-tabs .nav-link.active {
        color: var(--timerboard-dark-text-secondary);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.28);
    }

    body.dark-mode .timer-filter-chip,
    body.dark-mode .timerboard-section-pill,
    body.dark-mode .batch-summary-pill,
    body.dark-mode .edit-note-trigger,
    .timerboard-dark-skin .timer-filter-chip,
    .timerboard-dark-skin .timerboard-section-pill,
    .timerboard-dark-skin .batch-summary-pill,
    .timerboard-dark-skin .edit-note-trigger {
        background: var(--timerboard-dark-chip-bg);
        color: var(--timerboard-dark-text);
        border-color: var(--timerboard-dark-chip-border);
    }

    body.dark-mode .timerboard-section-pill.is-muted,
    body.dark-mode .batch-summary-pill.is-placeholder,
    .timerboard-dark-skin .timerboard-section-pill.is-muted,
    .timerboard-dark-skin .batch-summary-pill.is-placeholder {
        background: rgba(174, 192, 210, 0.1);
        color: var(--timerboard-dark-text-muted);
    }

    body.dark-mode .timer-note-trigger,
    .timerboard-dark-skin .timer-note-trigger {
        color: var(--timerboard-dark-text-muted);
    }

    body.dark-mode .timer-note-trigger:hover,
    body.dark-mode .timer-note-trigger:focus,
    body.dark-mode .timer-primary-link:hover,
    body.dark-mode .timer-primary-link:focus,
    body.dark-mode .edit-note-trigger:hover,
    body.dark-mode .edit-note-trigger:focus,
    .timerboard-dark-skin .timer-note-trigger:hover,
    .timerboard-dark-skin .timer-note-trigger:focus,
    .timerboard-dark-skin .timer-primary-link:hover,
    .timerboard-dark-skin .timer-primary-link:focus,
    .timerboard-dark-skin .edit-note-trigger:hover,
    .timerboard-dark-skin .edit-note-trigger:focus {
        color: var(--timerboard-dark-text-secondary);
    }

    body.dark-mode .edit-note-trigger:hover,
    body.dark-mode .edit-note-trigger:focus,
    .timerboard-dark-skin .edit-note-trigger:hover,
    .timerboard-dark-skin .edit-note-trigger:focus {
        background: var(--timerboard-dark-chip-bg-hover);
        border-color: var(--timerboard-dark-chip-border-hover);
    }

    body.dark-mode .edit-note-trigger.has-note,
    body.dark-mode .timer-note-launch.has-note,
    .timerboard-dark-skin .edit-note-trigger.has-note,
    .timerboard-dark-skin .timer-note-launch.has-note {
        background: var(--timerboard-dark-active-bg);
        border-color: var(--timerboard-dark-active-border);
        color: var(--timerboard-dark-text-secondary);
    }

    body.dark-mode .timerboard-table-shell .table thead th,
    .timerboard-dark-skin .timerboard-table-shell .table thead th {
        background: var(--timerboard-dark-surface-alt);
        color: var(--timerboard-dark-text-muted);
        border-bottom-color: var(--timerboard-dark-border-soft);
    }

    body.dark-mode .timerboard-table-shell .table td,
    .timerboard-dark-skin .timerboard-table-shell .table td {
        color: var(--timerboard-dark-text);
        border-top-color: var(--timerboard-dark-border-subtle);
    }

    body.dark-mode .timer-row.is-soon,
    .timerboard-dark-skin .timer-row.is-soon {
        background: transparent;
    }

    body.dark-mode .timer-row.is-imminent,
    .timerboard-dark-skin .timer-row.is-imminent {
        background: transparent;
        box-shadow: none;
    }

    body.dark-mode .timer-row.static-timer,
    .timerboard-dark-skin .timer-row.static-timer {
        background: transparent;
    }

    body.dark-mode .timer-countdown-pill,
    .timerboard-dark-skin .timer-countdown-pill {
        background: linear-gradient(180deg, #3b4857 0%, #33404f 100%);
        color: #e5eef7;
        box-shadow: inset 0 0 0 1px rgba(220, 231, 242, 0.1);
    }

    body.dark-mode .timer-countdown-pill.is-soon,
    .timerboard-dark-skin .timer-countdown-pill.is-soon {
        background: linear-gradient(180deg, rgba(215, 153, 33, 0.52) 0%, rgba(181, 118, 20, 0.58) 100%);
        color: #fff0cc;
        box-shadow: inset 0 0 0 1px rgba(255, 240, 204, 0.12);
    }

    body.dark-mode .timer-countdown-pill.is-imminent,
    .timerboard-dark-skin .timer-countdown-pill.is-imminent {
        background: linear-gradient(180deg, rgba(204, 94, 32, 0.6) 0%, rgba(166, 64, 8, 0.68) 100%);
        color: #fff0d9;
        box-shadow: inset 0 0 0 1px rgba(255, 240, 217, 0.12);
    }

    body.dark-mode .timer-countdown-pill.is-elapsed,
    .timerboard-dark-skin .timer-countdown-pill.is-elapsed {
        background: linear-gradient(180deg, rgba(176, 71, 89, 0.56) 0%, rgba(135, 42, 58, 0.64) 100%);
        color: #ffd9df;
        box-shadow: inset 0 0 0 1px rgba(255, 217, 223, 0.12);
    }

    body.dark-mode .timerboard-filters .form-group label,
    body.dark-mode .timerboard-stat-label,
    body.dark-mode .timerboard-table-shell .dataTables_info,
    body.dark-mode .timerboard-table-shell .dataTables_filter label,
    body.dark-mode .timerboard-table-shell .dataTables_length label,
    body.dark-mode .timerboard-table-shell .paginate_button,
    .timerboard-dark-skin .timerboard-filters .form-group label,
    .timerboard-dark-skin .timerboard-stat-label,
    .timerboard-dark-skin .timerboard-table-shell .dataTables_info,
    .timerboard-dark-skin .timerboard-table-shell .dataTables_filter label,
    .timerboard-dark-skin .timerboard-table-shell .dataTables_length label,
    .timerboard-dark-skin .timerboard-table-shell .paginate_button {
        color: var(--timerboard-dark-text-soft) !important;
    }

    body.dark-mode .timerboard-table-shell .dataTables_filter input,
    body.dark-mode .timerboard-table-shell .dataTables_length select,
    .timerboard-dark-skin .timerboard-table-shell .dataTables_filter input,
    .timerboard-dark-skin .timerboard-table-shell .dataTables_length select {
        background: var(--timerboard-dark-surface-alt);
        border-color: rgba(174, 192, 210, 0.18);
        color: var(--timerboard-dark-text);
    }

    body.dark-mode #batchTimerModal .modal-content,
    body.dark-mode #editTimerModal .modal-content,
    body.dark-mode #timerNoteModal .modal-content,
    body.dark-mode #batchTimerModal .batch-timer-row,
    body.dark-mode #batchTimerModal .batch-timer-row .card-header,
    body.dark-mode #timerNoteModal .timer-note-readonly,
    #batchTimerModal.timerboard-dark-skin .modal-content,
    #editTimerModal.timerboard-dark-skin .modal-content,
    #timerNoteModal.timerboard-dark-skin .modal-content,
    #batchTimerModal.timerboard-dark-skin .batch-timer-row,
    #batchTimerModal.timerboard-dark-skin .batch-timer-row .card-header,
    #timerNoteModal.timerboard-dark-skin .timer-note-readonly {
        background: var(--timerboard-dark-surface);
        border-color: rgba(174, 192, 210, 0.14);
        color: var(--timerboard-dark-text);
    }

    body.dark-mode #batchTimerModal .batch-timer-row .card-header,
    #batchTimerModal.timerboard-dark-skin .batch-timer-row .card-header {
        background: var(--timerboard-dark-modal-header);
        border-bottom-color: var(--timerboard-dark-border-soft);
    }

    body.dark-mode #batchTimerModal .batch-modal-toolbar,
    body.dark-mode #batchTimerModal .batch-timer-row .card-body,
    #batchTimerModal.timerboard-dark-skin .batch-modal-toolbar,
    #batchTimerModal.timerboard-dark-skin .batch-timer-row .card-body {
        background: var(--timerboard-dark-surface);
        border-color: var(--timerboard-dark-border);
        box-shadow: 0 14px 28px rgba(0, 0, 0, 0.24);
    }

    body.dark-mode #batchTimerModal .modal-body,
    body.dark-mode #editTimerModal .modal-body,
    body.dark-mode #timerNoteModal .modal-body,
    #batchTimerModal.timerboard-dark-skin .modal-body,
    #editTimerModal.timerboard-dark-skin .modal-body,
    #timerNoteModal.timerboard-dark-skin .modal-body {
        color: var(--timerboard-dark-text);
        background: linear-gradient(180deg, var(--timerboard-dark-surface-soft) 0%, var(--timerboard-dark-surface-deep) 100%);
    }

    body.dark-mode #batchTimerModal .batch-row-index,
    body.dark-mode #batchTimerModal .batch-count-pill,
    #batchTimerModal.timerboard-dark-skin .batch-row-index,
    #batchTimerModal.timerboard-dark-skin .batch-count-pill {
        background: var(--timerboard-dark-index-bg);
        color: var(--timerboard-dark-text-secondary);
    }

    body.dark-mode #batchTimerModal .card-body label,
    body.dark-mode #editTimerModal .modal-body label,
    body.dark-mode #timerNoteModal .modal-body label,
    #batchTimerModal.timerboard-dark-skin .card-body label,
    #editTimerModal.timerboard-dark-skin .modal-body label,
    #timerNoteModal.timerboard-dark-skin .modal-body label {
        color: var(--timerboard-dark-text);
    }

    body.dark-mode #batchTimerModal .card-body .text-muted,
    body.dark-mode #editTimerModal .modal-body .text-muted,
    body.dark-mode #timerNoteModal .modal-body .text-muted,
    body.dark-mode .timerboard-filters .text-muted,
    #batchTimerModal.timerboard-dark-skin .card-body .text-muted,
    #editTimerModal.timerboard-dark-skin .modal-body .text-muted,
    #timerNoteModal.timerboard-dark-skin .modal-body .text-muted,
    .timerboard-dark-skin .timerboard-filters .text-muted {
        color: var(--timerboard-dark-text-soft) !important;
    }

    body.dark-mode #batchTimerModal .select2-container--bootstrap4 .select2-selection,
    body.dark-mode #editTimerModal .select2-container--bootstrap4 .select2-selection,
    body.dark-mode #batchTimerModal .select2-container--bootstrap4 .select2-search__field,
    body.dark-mode #editTimerModal .select2-container--bootstrap4 .select2-search__field,
    body.dark-mode #timerNoteModal textarea.form-control,
    body.dark-mode #editTimerModal textarea.form-control,
    body.dark-mode #editTimerModal input.form-control,
    body.dark-mode #editTimerModal select.form-control,
    body.dark-mode #batchTimerModal input.form-control,
    body.dark-mode #batchTimerModal select.form-control,
    body.dark-mode .timerboard-filters .form-control,
    #batchTimerModal.timerboard-dark-skin .select2-container--bootstrap4 .select2-selection,
    #editTimerModal.timerboard-dark-skin .select2-container--bootstrap4 .select2-selection,
    #batchTimerModal.timerboard-dark-skin .select2-container--bootstrap4 .select2-search__field,
    #editTimerModal.timerboard-dark-skin .select2-container--bootstrap4 .select2-search__field,
    #timerNoteModal.timerboard-dark-skin textarea.form-control,
    #editTimerModal.timerboard-dark-skin textarea.form-control,
    #editTimerModal.timerboard-dark-skin input.form-control,
    #editTimerModal.timerboard-dark-skin select.form-control,
    #batchTimerModal.timerboard-dark-skin input.form-control,
    #batchTimerModal.timerboard-dark-skin select.form-control,
    .timerboard-dark-skin .timerboard-filters .form-control {
        background: var(--timerboard-dark-surface-alt);
        border-color: rgba(174, 192, 210, 0.18);
        color: var(--timerboard-dark-text);
        box-shadow: none;
    }

    body.dark-mode #batchTimerModal select.form-control option,
    body.dark-mode #editTimerModal select.form-control option,
    body.dark-mode .timerboard-filters select.form-control option,
    body.dark-mode .timerboard-table-shell .dataTables_length select option,
    #batchTimerModal.timerboard-dark-skin select.form-control option,
    #editTimerModal.timerboard-dark-skin select.form-control option,
    .timerboard-dark-skin .timerboard-filters select.form-control option,
    .timerboard-dark-skin .timerboard-table-shell .dataTables_length select option {
        background: var(--timerboard-dark-surface);
        color: var(--timerboard-dark-text);
    }

    body.dark-mode #batchTimerModal .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered,
    body.dark-mode #editTimerModal .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered,
    #batchTimerModal.timerboard-dark-skin .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered,
    #editTimerModal.timerboard-dark-skin .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        color: var(--timerboard-dark-text) !important;
    }

    body.dark-mode #batchTimerModal .select2-container--bootstrap4 .select2-selection__placeholder,
    body.dark-mode #editTimerModal .select2-container--bootstrap4 .select2-selection__placeholder,
    body.dark-mode #batchTimerModal .select2-container--bootstrap4 .select2-search__field::placeholder,
    body.dark-mode #editTimerModal .select2-container--bootstrap4 .select2-search__field::placeholder,
    body.dark-mode .timerboard-filters .form-control::placeholder,
    body.dark-mode #editTimerModal input.form-control::placeholder,
    body.dark-mode #batchTimerModal input.form-control::placeholder,
    body.dark-mode #timerNoteModal textarea.form-control::placeholder,
    #batchTimerModal.timerboard-dark-skin .select2-container--bootstrap4 .select2-selection__placeholder,
    #editTimerModal.timerboard-dark-skin .select2-container--bootstrap4 .select2-selection__placeholder,
    #batchTimerModal.timerboard-dark-skin .select2-container--bootstrap4 .select2-search__field::placeholder,
    #editTimerModal.timerboard-dark-skin .select2-container--bootstrap4 .select2-search__field::placeholder,
    .timerboard-dark-skin .timerboard-filters .form-control::placeholder,
    #editTimerModal.timerboard-dark-skin input.form-control::placeholder,
    #batchTimerModal.timerboard-dark-skin input.form-control::placeholder,
    #timerNoteModal.timerboard-dark-skin textarea.form-control::placeholder {
        color: var(--timerboard-dark-text-soft) !important;
        opacity: 0.88;
    }

    body.dark-mode #batchTimerModal .select2-container--bootstrap4 .select2-selection__clear,
    body.dark-mode #editTimerModal .select2-container--bootstrap4 .select2-selection__clear,
    body.dark-mode #batchTimerModal .select2-container--bootstrap4 .select2-selection__arrow,
    body.dark-mode #editTimerModal .select2-container--bootstrap4 .select2-selection__arrow,
    #batchTimerModal.timerboard-dark-skin .select2-container--bootstrap4 .select2-selection__clear,
    #editTimerModal.timerboard-dark-skin .select2-container--bootstrap4 .select2-selection__clear,
    #batchTimerModal.timerboard-dark-skin .select2-container--bootstrap4 .select2-selection__arrow,
    #editTimerModal.timerboard-dark-skin .select2-container--bootstrap4 .select2-selection__arrow {
        color: var(--timerboard-dark-text-soft);
    }

    body.dark-mode #batchTimerModal .select2-container--bootstrap4 .select2-dropdown,
    body.dark-mode #editTimerModal .select2-container--bootstrap4 .select2-dropdown,
    body.dark-mode #batchTimerModal .select2-container--bootstrap4 .select2-search--dropdown,
    body.dark-mode #editTimerModal .select2-container--bootstrap4 .select2-search--dropdown,
    #batchTimerModal.timerboard-dark-skin .select2-container--bootstrap4 .select2-dropdown,
    #editTimerModal.timerboard-dark-skin .select2-container--bootstrap4 .select2-dropdown,
    #batchTimerModal.timerboard-dark-skin .select2-container--bootstrap4 .select2-search--dropdown,
    #editTimerModal.timerboard-dark-skin .select2-container--bootstrap4 .select2-search--dropdown {
        background: var(--timerboard-dark-surface);
        border-color: var(--timerboard-dark-border);
    }

    body.dark-mode #batchTimerModal .select2-container--bootstrap4 .select2-results__option,
    body.dark-mode #editTimerModal .select2-container--bootstrap4 .select2-results__option,
    #batchTimerModal.timerboard-dark-skin .select2-container--bootstrap4 .select2-results__option,
    #editTimerModal.timerboard-dark-skin .select2-container--bootstrap4 .select2-results__option {
        color: var(--timerboard-dark-text);
    }

    body.dark-mode #batchTimerModal .select2-container--bootstrap4 .select2-results__option--highlighted[aria-selected],
    body.dark-mode #editTimerModal .select2-container--bootstrap4 .select2-results__option--highlighted[aria-selected],
    #batchTimerModal.timerboard-dark-skin .select2-container--bootstrap4 .select2-results__option--highlighted[aria-selected],
    #editTimerModal.timerboard-dark-skin .select2-container--bootstrap4 .select2-results__option--highlighted[aria-selected] {
        background: var(--timerboard-dark-dropdown-highlight);
        color: var(--timerboard-dark-text-secondary);
    }

    body.dark-mode #batchTimerModal .select2-container--bootstrap4 .select2-results__option[aria-selected=true],
    body.dark-mode #editTimerModal .select2-container--bootstrap4 .select2-results__option[aria-selected=true],
    #batchTimerModal.timerboard-dark-skin .select2-container--bootstrap4 .select2-results__option[aria-selected=true],
    #editTimerModal.timerboard-dark-skin .select2-container--bootstrap4 .select2-results__option[aria-selected=true] {
        background: var(--timerboard-dark-dropdown-selected);
        color: var(--timerboard-dark-text-secondary);
    }

    .timerboard-skin-jet .timerboard-toolbar-copy h5,
    .timerboard-skin-jet .timerboard-section-header h5,
    .timerboard-skin-jet .timerboard-stat-value,
    .timerboard-skin-jet .timer-primary-link,
    .timerboard-skin-jet .timer-system-cell .font-weight-semibold,
    .timerboard-skin-jet .timer-type-cell,
    .timerboard-skin-jet .timer-time-cell,
    .timerboard-skin-jet .timer-time-primary {
        color: #ebdbb2;
    }

    .timerboard-skin-jet .timerboard-toolbar-copy .text-muted,
    .timerboard-skin-jet .timerboard-stat-meta,
    .timerboard-skin-jet .timer-time-secondary,
    .timerboard-skin-jet .timerboard-section-header .text-muted,
    .timerboard-skin-jet .timer-created-by-cell .text-muted,
    .timerboard-skin-jet .timerboard-filters .text-muted,
    .timerboard-skin-jet .batch-footer-summary,
    #batchTimerModal.timerboard-skin-jet .batch-row-subtitle {
        color: #bdae93 !important;
    }

    .timerboard-skin-jet .timerboard-stat-card,
    .timerboard-skin-jet .timerboard-filters,
    .timerboard-skin-jet .timerboard-table-shell,
    .timerboard-skin-jet#batchTimerModal .batch-modal-toolbar,
    .timerboard-skin-jet#batchTimerModal .batch-timer-row .card-body {
        background: #3c3836;
        border-color: rgba(168, 153, 132, 0.18);
        box-shadow: 0 14px 28px rgba(0, 0, 0, 0.22);
    }

    .timerboard-skin-jet .timerboard-stat-card.is-urgent {
        background: linear-gradient(180deg, #4f4330 0%, #443827 100%);
        border-color: rgba(215, 153, 33, 0.34);
    }

    .timerboard-skin-jet .timerboard-tabs {
        border-bottom-color: rgba(168, 153, 132, 0.16);
    }

    .timerboard-skin-jet .timerboard-tabs .nav-link {
        color: #fe8019;
    }

    .timerboard-skin-jet .timerboard-tabs .nav-link.active {
        color: #32302f;
        box-shadow: none;
    }

    .timerboard-skin-jet .timer-filter-chip,
    .timerboard-skin-jet .timerboard-section-pill,
    .timerboard-skin-jet .batch-summary-pill,
    .timerboard-skin-jet .edit-note-trigger {
        background: rgba(168, 153, 132, 0.16);
        color: #ebdbb2;
        border-color: rgba(168, 153, 132, 0.24);
    }

    .timerboard-skin-jet .timerboard-section-pill.is-muted,
    .timerboard-skin-jet .batch-summary-pill.is-placeholder {
        background: rgba(146, 131, 116, 0.18);
        color: #d5c4a1;
    }

    .timerboard-skin-jet .timer-note-trigger {
        color: #d5c4a1;
    }

    .timerboard-skin-jet .timer-note-trigger:hover,
    .timerboard-skin-jet .timer-note-trigger:focus,
    .timerboard-skin-jet .timer-primary-link:hover,
    .timerboard-skin-jet .timer-primary-link:focus,
    .timerboard-skin-jet .edit-note-trigger:hover,
    .timerboard-skin-jet .edit-note-trigger:focus {
        color: #fe8019;
    }

    .timerboard-skin-jet .edit-note-trigger:hover,
    .timerboard-skin-jet .edit-note-trigger:focus {
        background: rgba(254, 128, 25, 0.14);
        border-color: rgba(254, 128, 25, 0.24);
    }

    .timerboard-skin-jet .edit-note-trigger.has-note,
    .timerboard-skin-jet .timer-note-launch.has-note {
        background: rgba(69, 133, 136, 0.28);
        border-color: rgba(69, 133, 136, 0.4);
        color: #ebdbb2;
    }

    .timerboard-skin-jet .timerboard-table-shell .table thead th {
        background: #32302f;
        color: #bdae93;
        border-bottom-color: #7c6f64;
    }

    .timerboard-skin-jet .timerboard-table-shell .table td {
        color: #ebdbb2;
        border-top-color: #7c6f64;
    }

    .timerboard-skin-jet .timer-row.is-soon {
        background: transparent;
    }

    .timerboard-skin-jet .timer-row.is-imminent {
        background: transparent;
        box-shadow: none;
    }

    .timerboard-skin-jet .timer-row.static-timer {
        background: transparent;
    }

    .timerboard-skin-jet .timer-countdown-pill {
        background: #504945;
        color: #ebdbb2;
        box-shadow: inset 0 0 0 1px rgba(235, 219, 178, 0.08);
    }

    .timerboard-skin-jet .timer-countdown-pill.is-soon {
        background: linear-gradient(180deg, #d79921 0%, #b57614 100%);
        color: #fbf1c7;
        box-shadow: inset 0 0 0 1px rgba(251, 241, 199, 0.12), 0 2px 6px rgba(0, 0, 0, 0.12);
    }

    .timerboard-skin-jet .timer-countdown-pill.is-imminent {
        background: linear-gradient(180deg, #fe8019 0%, #d65d0e 100%);
        color: #fbf1c7;
        box-shadow: inset 0 0 0 1px rgba(251, 241, 199, 0.12), 0 2px 6px rgba(0, 0, 0, 0.14);
    }

    .timerboard-skin-jet .timer-countdown-pill.is-elapsed {
        background: linear-gradient(180deg, #cc241d 0%, #9d0006 100%);
        color: #f2d6cf;
        box-shadow: inset 0 0 0 1px rgba(242, 214, 207, 0.12), 0 2px 6px rgba(0, 0, 0, 0.14);
    }

    #batchTimerModal.timerboard-skin-jet .modal-content,
    #editTimerModal.timerboard-skin-jet .modal-content,
    #timerNoteModal.timerboard-skin-jet .modal-content,
    #batchTimerModal.timerboard-skin-jet .batch-timer-row,
    #batchTimerModal.timerboard-skin-jet .batch-timer-row .card-header,
    #timerNoteModal.timerboard-skin-jet .timer-note-readonly {
        background: #3c3836;
        border-color: rgba(168, 153, 132, 0.16);
        color: #ebdbb2;
    }

    #batchTimerModal.timerboard-skin-jet .batch-timer-row .card-header {
        background: linear-gradient(180deg, #45403d 0%, #3c3836 100%);
        border-bottom-color: rgba(168, 153, 132, 0.14);
    }

    #batchTimerModal.timerboard-skin-jet .modal-body,
    #editTimerModal.timerboard-skin-jet .modal-body,
    #timerNoteModal.timerboard-skin-jet .modal-body {
        background: linear-gradient(180deg, #32302f 0%, #2b2928 100%);
        color: #ebdbb2;
    }

    #batchTimerModal.timerboard-skin-jet .card-body label,
    #editTimerModal.timerboard-skin-jet .modal-body label,
    #timerNoteModal.timerboard-skin-jet .modal-body label {
        color: #ebdbb2;
    }

    #batchTimerModal.timerboard-skin-jet .card-body .text-muted,
    #editTimerModal.timerboard-skin-jet .modal-body .text-muted,
    #timerNoteModal.timerboard-skin-jet .modal-body .text-muted {
        color: #bdae93 !important;
    }

    #batchTimerModal.timerboard-skin-jet .batch-row-index,
    #batchTimerModal.timerboard-skin-jet .batch-count-pill {
        background: #458588;
        color: #ebdbb2;
    }

    #batchTimerModal.timerboard-skin-jet .select2-container--bootstrap4 .select2-selection,
    #editTimerModal.timerboard-skin-jet .select2-container--bootstrap4 .select2-selection,
    #batchTimerModal.timerboard-skin-jet .select2-container--bootstrap4 .select2-search__field,
    #editTimerModal.timerboard-skin-jet .select2-container--bootstrap4 .select2-search__field,
    #timerNoteModal.timerboard-skin-jet textarea.form-control,
    #editTimerModal.timerboard-skin-jet textarea.form-control,
    #editTimerModal.timerboard-skin-jet input.form-control,
    #editTimerModal.timerboard-skin-jet select.form-control,
    #batchTimerModal.timerboard-skin-jet input.form-control,
    #batchTimerModal.timerboard-skin-jet select.form-control,
    .timerboard-skin-jet .timerboard-filters .form-control,
    .timerboard-skin-jet .timerboard-table-shell .dataTables_filter input,
    .timerboard-skin-jet .timerboard-table-shell .dataTables_length select {
        background: #736d6a;
        border: none;
        color: #f2e5bc;
        box-shadow: none;
    }

    #batchTimerModal.timerboard-skin-jet .select2-container--bootstrap4 .select2-selection__rendered,
    #editTimerModal.timerboard-skin-jet .select2-container--bootstrap4 .select2-selection__rendered,
    #batchTimerModal.timerboard-skin-jet .select2-container--bootstrap4 .select2-results__option,
    #editTimerModal.timerboard-skin-jet .select2-container--bootstrap4 .select2-results__option,
    #batchTimerModal.timerboard-skin-jet .select2-container--bootstrap4 .select2-selection__placeholder,
    #editTimerModal.timerboard-skin-jet .select2-container--bootstrap4 .select2-selection__placeholder,
    .timerboard-skin-jet .timerboard-filters .form-control::placeholder,
    #timerNoteModal.timerboard-skin-jet textarea.form-control::placeholder,
    #editTimerModal.timerboard-skin-jet input.form-control::placeholder,
    #batchTimerModal.timerboard-skin-jet input.form-control::placeholder {
        color: #f2e5bc !important;
    }

    #batchTimerModal.timerboard-skin-jet .select2-container--bootstrap4 .select2-search__field::placeholder,
    #editTimerModal.timerboard-skin-jet .select2-container--bootstrap4 .select2-search__field::placeholder {
        color: #d5c4a1 !important;
        opacity: 0.9;
    }

    #batchTimerModal.timerboard-skin-jet select.form-control option,
    #editTimerModal.timerboard-skin-jet select.form-control option,
    .timerboard-skin-jet .timerboard-filters select.form-control option,
    .timerboard-skin-jet .timerboard-table-shell .dataTables_length select option {
        background: #3c3836;
        color: #f2e5bc;
    }

    #batchTimerModal.timerboard-skin-jet .select2-container--bootstrap4 .select2-dropdown,
    #editTimerModal.timerboard-skin-jet .select2-container--bootstrap4 .select2-dropdown,
    #batchTimerModal.timerboard-skin-jet .select2-container--bootstrap4 .select2-search--dropdown,
    #editTimerModal.timerboard-skin-jet .select2-container--bootstrap4 .select2-search--dropdown {
        background: #736d6a;
        border-color: #7c6f64;
    }

    #batchTimerModal.timerboard-skin-jet .select2-container--bootstrap4 .select2-results__option--highlighted[aria-selected],
    #editTimerModal.timerboard-skin-jet .select2-container--bootstrap4 .select2-results__option--highlighted[aria-selected] {
        background: #a89984;
        color: #282828;
    }

    #batchTimerModal.timerboard-skin-jet .select2-container--bootstrap4 .select2-results__option[aria-selected=true],
    #editTimerModal.timerboard-skin-jet .select2-container--bootstrap4 .select2-results__option[aria-selected=true] {
        background: #fe8019;
        color: #32302f;
    }

    .timerboard-skin-jet .timerboard-filters .form-group label,
    .timerboard-skin-jet .timerboard-stat-label {
        color: #d5c4a1;
    }

    .timerboard-skin-jet .timerboard-table-shell .dataTables_info,
    .timerboard-skin-jet .timerboard-table-shell .dataTables_filter label,
    .timerboard-skin-jet .timerboard-table-shell .dataTables_length label,
    .timerboard-skin-jet .timerboard-table-shell .paginate_button {
        color: #d5c4a1 !important;
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
