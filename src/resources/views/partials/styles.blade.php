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

    .timerboard-batch-modal .batch-timer-row .form-group,
    .timerboard-edit-modal .form-group,
    .timerboard-note-modal .form-group {
        position: relative;
    }

    .timerboard-modal .modal-content {
        border: 0;
        border-radius: 14px;
        overflow: visible;
        box-shadow: 0 18px 45px rgba(26, 35, 52, 0.22);
    }

    .timerboard-modal .modal-header {
        border-bottom: 0;
        background: linear-gradient(135deg, #183247 0%, #214c6c 100%);
        color: #fff;
    }

    .timerboard-modal .modal-header .close {
        color: #fff;
        opacity: 0.85;
        text-shadow: none;
    }

    .timerboard-batch-modal .modal-body {
        background: linear-gradient(180deg, #f5f7fb 0%, #eef2f7 100%);
        max-height: 72vh;
        overflow-y: auto;
    }

    .timerboard-edit-modal .modal-body {
        background: #f7f9fc;
    }

    .timerboard-note-modal .modal-body {
        background: linear-gradient(180deg, #f8fafd 0%, #f1f5f9 100%);
    }

    .timerboard-note-modal {
        z-index: 1060;
    }

    .modal-backdrop.timer-note-backdrop {
        z-index: 1059;
    }

    .timerboard-edit-modal .modal-dialog,
    .timerboard-note-modal .modal-dialog {
        min-height: calc(100vh - 3.5rem);
        display: flex;
        align-items: center;
        margin-left: auto;
        margin-right: auto;
    }

    .timerboard-batch-modal .batch-modal-toolbar {
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

    .timerboard-batch-modal .batch-modal-description {
        opacity: 0.85;
    }

    .timerboard-batch-modal .batch-toolbar-copy {
        font-size: 0.88rem;
    }

    .timerboard-batch-modal .batch-toolbar-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.75rem;
    }

    .timerboard-batch-modal .batch-count-pill {
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

    .timerboard-batch-modal .batch-toolbar-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .timerboard-batch-modal .batch-timer-row {
        border: 1px solid rgba(31, 73, 103, 0.12);
        border-radius: 14px;
        overflow: visible;
        box-shadow: 0 10px 24px rgba(23, 43, 77, 0.08);
        transition: box-shadow 0.18s ease, border-color 0.18s ease, transform 0.18s ease;
    }

    .timerboard-batch-modal .batch-timer-row.is-active {
        border-color: rgba(33, 76, 108, 0.28);
        box-shadow: 0 16px 32px rgba(23, 43, 77, 0.12);
        transform: translateY(-1px);
    }

    .timerboard-batch-modal .batch-timer-row + .batch-timer-row {
        margin-top: 1rem;
    }

    .timerboard-batch-modal .batch-timer-row .card-header {
        background: linear-gradient(180deg, #fbfcfe 0%, #f1f5f9 100%);
        border-bottom: 1px solid rgba(31, 73, 103, 0.08);
        padding: 0.8rem 1rem;
    }

    .timerboard-batch-modal .batch-timer-row.is-collapsed .card-header {
        border-bottom-color: transparent;
    }

    .timerboard-batch-modal .batch-timer-row .card-body {
        background: rgba(255, 255, 255, 0.95);
        padding: 1rem 1rem 0.5rem;
    }

    .timerboard-batch-modal .batch-row-heading {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        min-width: 0;
        cursor: pointer;
    }

    .timerboard-batch-modal .batch-row-index {
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

    .timerboard-batch-modal .batch-row-subtitle {
        color: #5f6f82;
        font-size: 0.8rem;
    }

    .timerboard-batch-modal .card-body label,
    .timerboard-edit-modal .modal-body label,
    .timerboard-note-modal .modal-body label {
        color: #314557;
        font-weight: 600;
    }

    .timerboard-batch-modal .card-body .text-muted,
    .timerboard-edit-modal .modal-body .text-muted,
    .timerboard-note-modal .modal-body .text-muted {
        color: #6c7b8d !important;
    }

    .timerboard-batch-modal .batch-row-title-wrap {
        min-width: 0;
        flex: 1 1 auto;
    }

    .timerboard-batch-modal .batch-row-summary {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
        margin-top: 0.35rem;
    }

    .timerboard-batch-modal .batch-summary-pill {
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

    .timerboard-batch-modal .batch-summary-pill.is-placeholder {
        color: #7a8897;
        background: rgba(122, 136, 151, 0.12);
        font-weight: 500;
    }

    .timerboard-batch-modal .batch-row-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        align-items: center;
    }

    .timerboard-batch-modal .toggle-batch-row-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.2rem;
        height: 2.2rem;
        padding: 0;
        border-radius: 999px;
    }

    .timerboard-batch-modal .toggle-batch-row-btn i {
        transition: transform 0.18s ease;
    }

    .timerboard-batch-modal .batch-timer-row.is-collapsed .toggle-batch-row-btn i {
        transform: rotate(-90deg);
    }

    .timerboard-batch-modal .batch-footer-summary {
        margin-right: auto;
        color: #5f6f82;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .timerboard-form-modal .select2-container {
        width: 100% !important;
    }

    .timerboard-form-modal .select2-container--bootstrap4 .select2-selection {
        min-height: calc(2.25rem + 2px);
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        background: #fff;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.03);
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .timerboard-form-modal .select2-container--bootstrap4.select2-container--focus .select2-selection,
    .timerboard-form-modal .select2-container--bootstrap4.select2-container--open .select2-selection {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.14);
    }

    .timerboard-form-modal .select2-container--bootstrap4 .select2-selection--single {
        padding: 0.375rem 0.75rem;
    }

    .timerboard-form-modal .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        padding: 0;
        color: #495057;
        line-height: 1.5;
        display: block;
    }

    .timerboard-form-modal .select2-container--bootstrap4 .select2-selection__placeholder {
        color: #6c757d;
    }

    .timerboard-form-modal .select2-container--bootstrap4 .select2-selection__arrow {
        display: none;
    }

    .timerboard-form-modal .select2-container--bootstrap4 .select2-selection__clear {
        color: #6c757d;
        margin-right: 0;
    }

    .timerboard-form-modal .select2-container--bootstrap4 .select2-dropdown {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        box-shadow: 0 10px 24px rgba(23, 43, 77, 0.14);
        overflow: hidden;
        background: #fff;
    }

    .timerboard-form-modal .select2-container--bootstrap4 .select2-search--dropdown {
        padding: 0.45rem;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
    }

    .timerboard-form-modal .select2-container--bootstrap4 .select2-search__field {
        height: calc(2rem + 2px);
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        padding: 0.25rem 0.6rem;
        font-size: 0.95rem;
        background: #fff;
    }

    .timerboard-form-modal .select2-container--bootstrap4 .select2-results__option {
        padding: 0.5rem 0.75rem;
        font-size: 0.95rem;
        color: #495057;
    }

    .timerboard-form-modal .select2-container--bootstrap4 .select2-results__option--highlighted[aria-selected] {
        background: #e9f2ff;
        color: #1f3b57;
    }

    .timerboard-form-modal .select2-container--bootstrap4 .select2-results__option[aria-selected=true] {
        background: #f2f4f7;
        color: #1f3b57;
        font-weight: 600;
    }

    .timerboard-form-modal .select2-dropdown {
        z-index: 2055;
    }

    .timerboard-shell.timerboard-dark-skin,
    .timerboard-modal.timerboard-dark-skin {
        --timerboard-dark-text: var(--color-text-primary, #e6edf5);
        --timerboard-dark-text-secondary: var(--color-text-secondary, #f5f9fd);
        --timerboard-dark-text-muted: var(--color-text-tertiary, #9fb0c3);
        --timerboard-dark-text-soft: var(--color-text-tertiary, #b9c8d6);
        --timerboard-dark-summary-bg: rgba(122, 167, 211, 0.18);
        --timerboard-dark-summary-text: var(--color-text-secondary, #f5f9fd);
        --timerboard-dark-summary-placeholder-bg: rgba(174, 192, 210, 0.14);
        --timerboard-dark-summary-placeholder-text: var(--color-text-primary, #d6e3f0);
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
        --timerboard-dark-accent: #d9822b;
        --timerboard-dark-urgent-bg: linear-gradient(180deg, rgba(217, 130, 43, 0.18) 0%, rgba(110, 74, 33, 0.22) 100%);
        --timerboard-dark-urgent-border: rgba(217, 130, 43, 0.32);
        --timerboard-dark-modal-header: linear-gradient(180deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%);
    }

    .timerboard-shell.timerboard-dark-skin .timerboard-toolbar-copy h5,
    .timerboard-shell.timerboard-dark-skin .timerboard-section-header h5,
    .timerboard-shell.timerboard-dark-skin .timerboard-stat-value,
    .timerboard-shell.timerboard-dark-skin .timer-primary-link,
    .timerboard-shell.timerboard-dark-skin .timer-system-cell .font-weight-semibold,
    .timerboard-shell.timerboard-dark-skin .timer-type-cell,
    .timerboard-shell.timerboard-dark-skin .timer-time-cell,
    .timerboard-shell.timerboard-dark-skin .timer-time-primary {
        color: var(--timerboard-dark-text);
    }

    .timerboard-shell.timerboard-dark-skin .timerboard-stat-meta,
    .timerboard-shell.timerboard-dark-skin .timer-time-secondary,
    .timerboard-shell.timerboard-dark-skin .timerboard-toolbar-copy .text-muted,
    .timerboard-shell.timerboard-dark-skin .timerboard-section-header .text-muted,
    .timerboard-shell.timerboard-dark-skin .timer-created-by-cell .text-muted,
    .timerboard-batch-modal.timerboard-dark-skin .batch-footer-summary,
    .timerboard-batch-modal.timerboard-dark-skin .batch-row-subtitle,
    .timerboard-shell.timerboard-dark-skin .timerboard-filters .text-muted {
        color: var(--timerboard-dark-text-muted) !important;
    }

    .timerboard-shell.timerboard-dark-skin .timerboard-stat-card,
    .timerboard-shell.timerboard-dark-skin .timerboard-filters,
    .timerboard-shell.timerboard-dark-skin .timerboard-table-shell {
        background: var(--timerboard-dark-surface);
        border-color: var(--timerboard-dark-border);
        box-shadow: 0 14px 28px rgba(0, 0, 0, 0.24);
    }

    .timerboard-shell.timerboard-dark-skin .timerboard-stat-card.is-urgent {
        background: var(--timerboard-dark-urgent-bg);
        border-color: var(--timerboard-dark-urgent-border);
    }

    .timerboard-shell.timerboard-dark-skin .timerboard-tabs {
        border-bottom-color: var(--timerboard-dark-border-soft);
    }

    .timerboard-shell.timerboard-dark-skin .timerboard-tabs .nav-link {
        color: var(--timerboard-dark-text-muted);
    }

    .timerboard-shell.timerboard-dark-skin .timerboard-tabs .nav-link.active {
        color: var(--timerboard-dark-text-secondary);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.28);
    }

    .timerboard-shell.timerboard-dark-skin .timer-filter-chip,
    .timerboard-shell.timerboard-dark-skin .timerboard-section-pill,
    .timerboard-shell.timerboard-dark-skin .edit-note-trigger,
    .timerboard-modal.timerboard-dark-skin .edit-note-trigger,
    .timerboard-modal.timerboard-dark-skin .timer-note-launch {
        background: var(--timerboard-dark-chip-bg);
        color: var(--timerboard-dark-text);
        border-color: var(--timerboard-dark-chip-border);
    }

    .timerboard-batch-modal.timerboard-dark-skin .batch-summary-pill {
        background: var(--timerboard-dark-summary-bg);
        color: var(--timerboard-dark-summary-text);
        border-color: rgba(122, 167, 211, 0.28);
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.04);
    }

    .timerboard-shell.timerboard-dark-skin .timerboard-section-pill.is-muted {
        background: rgba(174, 192, 210, 0.1);
        color: var(--timerboard-dark-text-muted);
    }

    .timerboard-batch-modal.timerboard-dark-skin .batch-summary-pill.is-placeholder {
        background: var(--timerboard-dark-summary-placeholder-bg);
        color: var(--timerboard-dark-summary-placeholder-text);
        border-color: rgba(174, 192, 210, 0.2);
    }

    .timerboard-shell.timerboard-dark-skin .timer-note-trigger {
        color: var(--timerboard-dark-text-muted);
    }

    .timerboard-shell.timerboard-dark-skin .timer-note-trigger:hover,
    .timerboard-shell.timerboard-dark-skin .timer-note-trigger:focus,
    .timerboard-shell.timerboard-dark-skin .timer-primary-link:hover,
    .timerboard-shell.timerboard-dark-skin .timer-primary-link:focus,
    .timerboard-shell.timerboard-dark-skin .edit-note-trigger:hover,
    .timerboard-shell.timerboard-dark-skin .edit-note-trigger:focus,
    .timerboard-modal.timerboard-dark-skin .edit-note-trigger:hover,
    .timerboard-modal.timerboard-dark-skin .edit-note-trigger:focus,
    .timerboard-modal.timerboard-dark-skin .timer-note-launch:hover,
    .timerboard-modal.timerboard-dark-skin .timer-note-launch:focus {
        color: var(--timerboard-dark-text-secondary);
    }

    .timerboard-shell.timerboard-dark-skin .edit-note-trigger:hover,
    .timerboard-shell.timerboard-dark-skin .edit-note-trigger:focus,
    .timerboard-modal.timerboard-dark-skin .edit-note-trigger:hover,
    .timerboard-modal.timerboard-dark-skin .edit-note-trigger:focus,
    .timerboard-modal.timerboard-dark-skin .timer-note-launch:hover,
    .timerboard-modal.timerboard-dark-skin .timer-note-launch:focus {
        background: var(--timerboard-dark-chip-bg-hover);
        border-color: var(--timerboard-dark-chip-border-hover);
    }

    .timerboard-shell.timerboard-dark-skin .edit-note-trigger.has-note,
    .timerboard-shell.timerboard-dark-skin .timer-note-launch.has-note,
    .timerboard-modal.timerboard-dark-skin .edit-note-trigger.has-note,
    .timerboard-modal.timerboard-dark-skin .timer-note-launch.has-note {
        background: var(--timerboard-dark-active-bg);
        border-color: var(--timerboard-dark-active-border);
        color: var(--timerboard-dark-text-secondary);
    }

    .timerboard-shell.timerboard-dark-skin .timerboard-table-shell .table thead th {
        background: var(--timerboard-dark-surface-alt);
        color: var(--timerboard-dark-text-muted);
        border-bottom-color: var(--timerboard-dark-border-soft);
    }

    .timerboard-shell.timerboard-dark-skin .timerboard-table-shell .table td {
        color: var(--timerboard-dark-text);
        border-top-color: var(--timerboard-dark-border-subtle);
    }

    .timerboard-shell.timerboard-dark-skin .timer-countdown-pill {
        background: linear-gradient(180deg, #3b4857 0%, #33404f 100%);
        color: #e5eef7;
        box-shadow: inset 0 0 0 1px rgba(220, 231, 242, 0.1);
    }

    .timerboard-shell.timerboard-dark-skin .timer-countdown-pill.is-soon {
        background: linear-gradient(180deg, rgba(215, 153, 33, 0.52) 0%, rgba(181, 118, 20, 0.58) 100%);
        color: #fff0cc;
        box-shadow: inset 0 0 0 1px rgba(255, 240, 204, 0.12);
    }

    .timerboard-shell.timerboard-dark-skin .timer-countdown-pill.is-imminent {
        background: linear-gradient(180deg, rgba(204, 94, 32, 0.6) 0%, rgba(166, 64, 8, 0.68) 100%);
        color: #fff0d9;
        box-shadow: inset 0 0 0 1px rgba(255, 240, 217, 0.12);
    }

    .timerboard-shell.timerboard-dark-skin .timer-countdown-pill.is-elapsed {
        background: linear-gradient(180deg, rgba(176, 71, 89, 0.56) 0%, rgba(135, 42, 58, 0.64) 100%);
        color: #ffd9df;
        box-shadow: inset 0 0 0 1px rgba(255, 217, 223, 0.12);
    }

    .timerboard-shell.timerboard-dark-skin .timerboard-filters .form-group label,
    .timerboard-shell.timerboard-dark-skin .timerboard-stat-label,
    .timerboard-shell.timerboard-dark-skin .timerboard-table-shell .dataTables_info,
    .timerboard-shell.timerboard-dark-skin .timerboard-table-shell .dataTables_filter label,
    .timerboard-shell.timerboard-dark-skin .timerboard-table-shell .dataTables_length label,
    .timerboard-shell.timerboard-dark-skin .timerboard-table-shell .paginate_button {
        color: var(--timerboard-dark-text-soft) !important;
    }

    .timerboard-shell.timerboard-dark-skin .timerboard-table-shell .dataTables_filter input,
    .timerboard-shell.timerboard-dark-skin .timerboard-table-shell .dataTables_length select,
    .timerboard-shell.timerboard-dark-skin .timerboard-filters .form-control {
        background: var(--timerboard-dark-surface-alt);
        border-color: rgba(174, 192, 210, 0.18);
        color: var(--timerboard-dark-text);
    }

    .timerboard-shell.timerboard-dark-skin .timerboard-filters select.form-control option,
    .timerboard-shell.timerboard-dark-skin .timerboard-table-shell .dataTables_length select option {
        background: var(--timerboard-dark-surface);
        color: var(--timerboard-dark-text);
    }

    .timerboard-shell.timerboard-dark-skin .timerboard-filters .form-control::placeholder {
        color: var(--timerboard-dark-text-soft) !important;
        opacity: 0.88;
    }

    .timerboard-modal.timerboard-dark-skin .modal-content,
    .timerboard-batch-modal.timerboard-dark-skin .batch-timer-row,
    .timerboard-batch-modal.timerboard-dark-skin .batch-timer-row .card-header,
    .timerboard-note-modal.timerboard-dark-skin .timer-note-readonly {
        background: var(--timerboard-dark-surface);
        border-color: rgba(174, 192, 210, 0.14);
        color: var(--timerboard-dark-text);
    }

    .timerboard-batch-modal.timerboard-dark-skin .batch-timer-row .card-header {
        background: var(--timerboard-dark-modal-header);
        border-bottom-color: var(--timerboard-dark-border-soft);
    }

    .timerboard-batch-modal.timerboard-dark-skin .batch-modal-toolbar,
    .timerboard-batch-modal.timerboard-dark-skin .batch-timer-row .card-body {
        background: var(--timerboard-dark-surface);
        border-color: var(--timerboard-dark-border);
        box-shadow: 0 14px 28px rgba(0, 0, 0, 0.24);
    }

    .timerboard-modal.timerboard-dark-skin .modal-body {
        color: var(--timerboard-dark-text);
        background: linear-gradient(180deg, var(--timerboard-dark-surface-soft) 0%, var(--timerboard-dark-surface-deep) 100%);
    }

    .timerboard-batch-modal.timerboard-dark-skin .batch-row-index,
    .timerboard-batch-modal.timerboard-dark-skin .batch-count-pill {
        background: var(--timerboard-dark-index-bg);
        color: var(--timerboard-dark-text-secondary);
    }

    .timerboard-modal.timerboard-dark-skin .modal-body label {
        color: var(--timerboard-dark-text);
    }

    .timerboard-modal.timerboard-dark-skin .modal-body .text-muted {
        color: var(--timerboard-dark-text-soft) !important;
    }

    .timerboard-form-modal.timerboard-dark-skin .select2-container--bootstrap4 .select2-selection,
    .timerboard-form-modal.timerboard-dark-skin .select2-container--bootstrap4 .select2-search__field,
    .timerboard-form-modal.timerboard-dark-skin input.form-control,
    .timerboard-form-modal.timerboard-dark-skin select.form-control,
    .timerboard-form-modal.timerboard-dark-skin textarea.form-control,
    .timerboard-note-modal.timerboard-dark-skin textarea.form-control {
        background: var(--timerboard-dark-surface-alt);
        border-color: rgba(174, 192, 210, 0.18);
        color: var(--timerboard-dark-text);
        box-shadow: none;
    }

    .timerboard-form-modal.timerboard-dark-skin select.form-control option {
        background: var(--timerboard-dark-surface);
        color: var(--timerboard-dark-text);
    }

    .timerboard-form-modal.timerboard-dark-skin .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        color: var(--timerboard-dark-text) !important;
    }

    .timerboard-form-modal.timerboard-dark-skin .select2-container--bootstrap4 .select2-selection__placeholder,
    .timerboard-form-modal.timerboard-dark-skin .select2-container--bootstrap4 .select2-search__field::placeholder,
    .timerboard-form-modal.timerboard-dark-skin input.form-control::placeholder,
    .timerboard-form-modal.timerboard-dark-skin textarea.form-control::placeholder,
    .timerboard-note-modal.timerboard-dark-skin textarea.form-control::placeholder {
        color: var(--timerboard-dark-text-soft) !important;
        opacity: 0.88;
    }

    .timerboard-form-modal.timerboard-dark-skin .select2-container--bootstrap4 .select2-selection__clear,
    .timerboard-form-modal.timerboard-dark-skin .select2-container--bootstrap4 .select2-selection__arrow {
        color: var(--timerboard-dark-text-soft);
    }

    .timerboard-form-modal.timerboard-dark-skin .select2-container--bootstrap4 .select2-dropdown,
    .timerboard-form-modal.timerboard-dark-skin .select2-container--bootstrap4 .select2-search--dropdown {
        background: var(--timerboard-dark-surface);
        border-color: var(--timerboard-dark-border);
    }

    .timerboard-form-modal.timerboard-dark-skin .select2-container--bootstrap4 .select2-results__option {
        color: var(--timerboard-dark-text);
    }

    .timerboard-form-modal.timerboard-dark-skin .select2-container--bootstrap4 .select2-results__option--highlighted[aria-selected] {
        background: var(--timerboard-dark-dropdown-highlight);
        color: var(--timerboard-dark-text-secondary);
    }

    .timerboard-form-modal.timerboard-dark-skin .select2-container--bootstrap4 .select2-results__option[aria-selected=true] {
        background: var(--timerboard-dark-dropdown-selected);
        color: var(--timerboard-dark-text-secondary);
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
