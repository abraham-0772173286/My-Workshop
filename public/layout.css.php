<?php header('Content-Type: text/css; charset=utf-8'); ?>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300..800;1,9..40,300..800&display=swap');

* {
    font-family: 'DM Sans', sans-serif;
}

/* ── Sidebar Layout ──────────────────────────────────────────────────── */
.garage-sidebar {
    position: fixed;
    z-index: 1045;
    top: 0;
    left: 0;
    width: 260px;
    height: 100vh;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    background: linear-gradient(180deg, #4015bf 0%, #29106f 100%);
    transition: transform .28s ease;
    box-shadow: 5px 0 24px rgba(21, 5, 64, .16);
}

.garage-sidebar.is-collapsed {
    transform: translateX(-100%);
}

.app-header {
    margin-left: 260px;
    transition: margin-left .28s ease;
}

/* ── Page Layout (footer always at bottom) ───────────────────────────── */
.app-wrapper {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.app-main {
    margin-left: 260px !important;
    flex: 1 0 auto;
    transition: margin-left .28s ease;
}

body.garage-sidebar-collapsed .app-header,
body.garage-sidebar-collapsed .app-main {
    margin-left: 0 !important;
}

/* ── Sidebar Brand ───────────────────────────────────────────────────── */
.garage-sidebar .sidebar-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 19px 18px;
    color: #fff;
    text-decoration: none;
    background: rgba(0, 0, 0, .15);
    border-bottom: 1px solid rgba(255, 255, 255, .1);
    flex-shrink: 0;
}

.garage-sidebar .brand-mark {
    width: 39px;
    height: 39px;
    border-radius: 10px;
    display: grid;
    place-items: center;
    background: #f5ad2b;
    color: #301078;
    font-size: 20px;
    flex-shrink: 0;
}

.garage-sidebar .brand-name {
    font-size: 15px;
    line-height: 1.1;
    font-weight: 800;
}

.garage-sidebar .brand-name small {
    display: block;
    margin-top: 4px;
    color: #cfbefd;
    font-size: 9px;
    letter-spacing: 1px;
}

/* ── Sidebar Navigation ──────────────────────────────────────────────── */
.garage-sidebar .nav-sidebar {
    padding: 14px 7px;
    overflow-y: auto;
    flex: 1;
    scrollbar-width: none;
}

.garage-sidebar .nav-sidebar::-webkit-scrollbar {
    display: none;
}

.garage-sidebar .nav-item {
    margin: 2px 0;
}

.garage-sidebar .nav-link {
    display: flex;
    align-items: center;
    min-height: 46px;
    gap: 12px;
    padding: 10px 18px;
    color: rgba(255, 255, 255, .88) !important;
    font-size: 14.5px;
    transition: background .18s;
    text-decoration: none;
}

.garage-sidebar .nav-link:hover,
.garage-sidebar .nav-link.active {
    background: rgba(154, 109, 255, .35);
    color: #fff !important;
}

.garage-sidebar .nav-link i:first-child {
    width: 26px;
    font-size: 20px;
    text-align: center;
    flex-shrink: 0;
}

.garage-sidebar .nav-link .nav-arrow {
    margin-left: auto;
    font-size: 13px;
    opacity: .6;
}

.garage-sidebar .nav-header {
    font-size: .65rem !important;
    text-transform: uppercase;
    letter-spacing: 1.8px;
    font-weight: 700;
    color: rgba(189, 170, 255, .7) !important;
    padding: 18px 18px 5px !important;
}

.garage-sidebar .sidebar-footer {
    padding: 13px 18px;
    border-top: 1px solid rgba(255, 255, 255, .12);
    color: #d2c7f1;
    font-size: 11px;
    flex-shrink: 0;
}

.garage-sidebar .sidebar-footer i {
    color: #f5ad2b;
}

/* ── Sidebar Submenu (expandable/collapsible) ──────────────────────── */
.garage-sidebar .nav-parent {
    cursor: pointer;
    user-select: none;
}

.garage-sidebar .nav-chevron {
    margin-left: auto;
    font-size: 13px;
    opacity: .5;
    transition: transform .25s ease;
    flex-shrink: 0;
}

.garage-sidebar .nav-parent.open .nav-chevron {
    transform: rotate(180deg);
    opacity: .8;
}

.garage-sidebar .nav-submenu {
    padding: 0;
    margin: 0;
}

.garage-sidebar .nav-subitem {
    margin: 1px 0;
}

.garage-sidebar .nav-subitem .sub-link {
    display: flex;
    align-items: center;
    min-height: 40px;
    gap: 10px;
    padding: 8px 18px 8px 54px;
    color: rgba(255, 255, 255, .72) !important;
    font-size: 13px;
    font-weight: 500;
    transition: background .18s, color .18s;
    text-decoration: none;
    border-left: 2px solid transparent;
}

.garage-sidebar .nav-subitem .sub-link:hover {
    background: rgba(154, 109, 255, .2);
    color: #fff !important;
}

.garage-sidebar .nav-subitem .sub-link.active {
    background: rgba(154, 109, 255, .35);
    color: #fff !important;
    border-left-color: #f5ad2b;
    font-weight: 700;
}

.garage-sidebar .nav-subitem .sub-link i:first-child {
    width: 22px;
    font-size: 16px;
    text-align: center;
    flex-shrink: 0;
}

/* ── Mobile Sidebar Backdrop (tap outside to close) ──────────────────── */
.sidebar-backdrop {
    position: fixed;
    inset: 0;
    z-index: 1040;
    background: rgba(15, 10, 40, .45);
    opacity: 0;
    visibility: hidden;
    transition: opacity .28s ease, visibility .28s ease;
}

/* ── Responsive Sidebar ──────────────────────────────────────────────── */
@media (max-width: 991px) {
    .app-header,
    .app-main {
        margin-left: 0 !important;
    }

    .garage-sidebar {
        transform: translateX(-100%);
    }

    .garage-sidebar.is-open {
        transform: translateX(0);
    }

    .sidebar-backdrop.show {
        opacity: 1;
        visibility: visible;
    }
}

/* ── Navbar Dropdowns ────────────────────────────────────────────────── */
.navbar .dropdown-menu {
    display: none;
    position: absolute;
    z-index: 1051 !important;
    border: 1px solid #e9ecef;
    box-shadow: 0 4px 12px rgba(0, 0, 0, .1) !important;
    background: #fff;
    border-radius: 8px;
    padding: 8px 0;
    min-width: 200px;
}

.navbar .dropdown-menu.show {
    display: block;
}

.navbar .dropdown-item {
    padding: 8px 16px;
    color: #333;
    text-decoration: none;
    display: block;
    transition: background .2s;
}

.navbar .dropdown-item:hover {
    background: #f8f9fa;
}

.navbar .dropdown-toggle::after {
    display: inline-block;
    margin-left: 4px;
}

.navbar .dropdown-menu-end {
    right: 0;
    left: auto !important;
}

.navbar .dropdown-header {
    padding: 8px 16px;
    font-size: 0.875rem;
    font-weight: 600;
    color: #6c757d;
}

.navbar .dropdown-divider {
    margin: 4px 0;
    border-top: 1px solid #e9ecef;
}

/* ── Buttons (hover keeps each button's own colour) ─────────────────── */
.btn {
    transition: color .15s ease-in-out,
                background-color .15s ease-in-out,
                border-color .15s ease-in-out,
                box-shadow .15s ease-in-out,
                transform .15s ease,
                filter .15s ease;
}

.btn:not(.btn-light):not(.btn-link):hover,
.btn:not(.btn-light):not(.btn-link):focus-visible {
    filter: brightness(1.08) saturate(1.08);
    box-shadow: 0 .35rem .9rem rgba(15, 23, 42, .14);
    transform: translateY(-1px);
}

.btn:not(.btn-light):not(.btn-link):active {
    transform: translateY(0);
    filter: brightness(.98);
}

.btn-light:hover,
.btn-light:focus-visible {
    border-color: #cbd5e1;
    box-shadow: 0 .25rem .6rem rgba(15, 23, 42, .12);
}

/* ── Status Pills ────────────────────────────────────────────────────── */
.status-pill {
    padding: .28em .75em;
    font-size: .68rem;
    font-weight: 700;
    text-transform: uppercase;
    border-radius: 50px;
}

.status-done {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.status-pending {
    background: #fff4d8;
    color: #9a5a00;
    border: 1px solid #ffe3a2;
}

/* ── Utility Classes ─────────────────────────────────────────────────── */
.action-group {
    border-right: 1px solid #e2e8f0;
    padding-right: 1rem;
    margin-right: 1rem;
}

.form-check-input:checked {
    background-color: #4f46e5;
    border-color: #4f46e5;
}

.text-primary {
    color: #dc3545 !important;
}

.bg-primary {
    background-color: #dc3545 !important;
    color: #fff !important;
}

/* ── Footer ──────────────────────────────────────────────────────────── */
.app-footer {
    background: #fff;
    border-top: 1px solid #e9ecef;
    padding: 18px 0;
}

.footer-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
}

@media (min-width: 768px) {
    .footer-content {
        flex-direction: row;
        justify-content: space-between;
        padding: 0 40px;
    }
}

.footer-contacts a {
    font-size: 14px;
    color: #6c757d;
    text-decoration: none;
    margin-left: 12px;
}

.footer-contacts a:hover {
    color: #25d366;
}

/* ── Compact Footer (while maximized/fullscreen) ─────────────────────── */
body.is-fullscreen .app-footer,
:root:fullscreen .app-footer {
    padding: 6px 0;
}

body.is-fullscreen .footer-content,
:root:fullscreen .footer-content {
    gap: 3px;
}

body.is-fullscreen .footer-content > div,
:root:fullscreen .footer-content > div {
    font-size: 11px;
}

body.is-fullscreen .footer-content > div strong,
:root:fullscreen .footer-content > div strong {
    font-weight: 600;
}


/* ══════════════════════════════════════════════════════════════════════
   RESPONSIVE — Mobile & Tablet
   Breakpoints:
     ≤ 991px  — tablet / large phone  (sidebar off-canvas already handled)
     ≤ 767px  — phone landscape
     ≤ 575px  — phone portrait
   ══════════════════════════════════════════════════════════════════════ */

/* ── Shared tablet + mobile ─────────────────────────────────────────── */
@media (max-width: 991px) {

    /* Content fills full width (sidebar is off-canvas) */
    .app-header,
    .app-main {
        margin-left: 0 !important;
    }

    /* Footer not fixed on mobile — let it flow naturally */
    .app-footer {
        position: static !important;
        margin-left: 0 !important;
    }

    /* Tighter content padding */
    .app-content {
        padding: 16px !important;
    }

    .app-content-header {
        padding: 12px 16px 0 !important;
    }

    /* Page headings scale down */
    .app-content-header h4 {
        font-size: 1.1rem !important;
    }

    /* Cards: remove heavy shadows, round corners less aggressively */
    .card {
        border-radius: 10px !important;
    }

    .card-header {
        padding: 12px 16px !important;
    }

    .card-body {
        padding: 14px !important;
    }

    /* Breadcrumb hides on small screens */
    .breadcrumb {
        display: none !important;
    }

    /* DataTables — prevent controls from overflowing */
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        float: none !important;
        text-align: left !important;
        margin-bottom: 10px;
    }

    .dataTables_wrapper .dataTables_filter input {
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box;
    }

    .dataTables_wrapper .dataTables_info,
    .dataTables_wrapper .dataTables_paginate {
        float: none !important;
        text-align: center !important;
        margin-top: 10px;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: .25rem .55rem !important;
        font-size: .75rem !important;
        margin: 0 1px !important;
    }

    /* Tables — horizontal scroll */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* Stat / summary cards in dashboard */
    .row.g-3 > [class*="col-md"],
    .row.g-4 > [class*="col-md"] {
        flex: 0 0 100%;
        max-width: 100%;
    }

    /* Action button groups in card headers: wrap & shrink */
    .card-header .d-flex.gap-2,
    .card-header .d-flex.gap-3 {
        flex-wrap: wrap;
        gap: 6px !important;
    }

    /* Toolbar badges / info strips */
    .badge {
        font-size: .68rem;
        white-space: normal !important;
        word-break: break-word;
    }

    /* Form modals: full width */
    .modal-dialog {
        margin: 8px !important;
        max-width: calc(100vw - 16px) !important;
    }

    .modal-body {
        padding: 14px !important;
    }

    /* Offcanvas drawers: full width on mobile */
    .offcanvas-end {
        width: 100% !important;
    }
}

/* ── Phone landscape & small tablets (≤ 767px) ──────────────────────── */
@media (max-width: 767px) {

    .app-content {
        padding: 12px !important;
    }

    /* Nav bar: shrink user name */
    .navbar .d-none.d-md-inline {
        display: none !important;
    }

    /* Card header toolbar: stack vertically */
    .card-header .d-flex.justify-content-between {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 10px;
    }

    /* Tables: smaller font */
    table.dataTable,
    table.table {
        font-size: .78rem !important;
    }

    table.dataTable thead th,
    table.table thead th {
        font-size: .68rem !important;
        padding: .5rem .55rem !important;
        white-space: nowrap;
    }

    table.dataTable tbody td,
    table.table tbody td {
        padding: .5rem .55rem !important;
        vertical-align: middle;
    }

    /* Action button pairs in table rows: stack icons */
    .d-flex.gap-1 {
        flex-wrap: wrap;
        gap: 4px !important;
    }

    /* Footer: single column */
    .footer-content {
        flex-direction: column !important;
        text-align: center;
        gap: 6px !important;
        padding: 0 16px !important;
    }

    .footer-contacts {
        margin-top: 4px;
    }

    .footer-contacts a {
        margin: 0 6px;
        font-size: 16px;
    }

    /* Modal forms: single column */
    .modal .row.g-3 > .col-md-6 {
        flex: 0 0 100%;
        max-width: 100%;
    }

    /* Section headings */
    h3.card-title, h4 {
        font-size: 1rem !important;
    }

    h5 {
        font-size: .95rem !important;
    }

    /* Chart containers */
    canvas {
        max-height: 220px !important;
    }
}

/* ── Phone portrait (≤ 575px) ───────────────────────────────────────── */
@media (max-width: 575px) {

    body {
        font-size: 13px;
    }

    .app-content {
        padding: 10px !important;
    }

    .app-content-header {
        padding: 10px !important;
    }

    .app-content-header h4 {
        font-size: 1rem !important;
    }

    .app-content-header p.small {
        display: none;
    }

    /* Card: no shadow, straight edges on very small screens */
    .card {
        border-radius: 8px !important;
        box-shadow: 0 2px 8px rgba(0,0,0,.07) !important;
    }

    .card-header {
        padding: 10px 12px !important;
    }

    .card-body {
        padding: 10px !important;
    }

    /* Buttons: slightly smaller */
    .btn-sm {
        padding: .3rem .65rem !important;
        font-size: .75rem !important;
    }

    /* Export dropdown: full width */
    .dropdown-menu {
        min-width: 160px !important;
    }

    /* Status pills */
    .status-pill {
        font-size: .62rem !important;
        padding: .22em .55em !important;
    }

    /* Badge pills in tables */
    .badge {
        font-size: .62rem !important;
    }

    /* Paginate: fewer buttons */
    .dataTables_paginate .paginate_button.previous,
    .dataTables_paginate .paginate_button.next {
        display: inline-block !important;
    }

    /* Avatar circles (customers table) */
    .cust-avatar {
        width: 32px !important;
        height: 32px !important;
        font-size: 13px !important;
        border-radius: 7px !important;
    }

    /* Sidebar brand compact */
    .garage-sidebar .sidebar-brand {
        padding: 14px 14px !important;
    }

    .garage-sidebar .brand-name {
        font-size: 13px !important;
    }

    /* Stat cards on dashboard: two columns */
    .row.g-3 > [class*="col-"] {
        flex: 0 0 50%;
        max-width: 50%;
    }

    /* But 1-column for very narrow */
    @media (max-width: 380px) {
        .row.g-3 > [class*="col-"] {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
}

/* ── Sidebar width on tablet (give a bit more room) ─────────────────── */
@media (min-width: 992px) and (max-width: 1199px) {
    .garage-sidebar {
        width: 230px;
    }

    .app-header,
    .app-main {
        margin-left: 230px !important;
    }

    .garage-sidebar .nav-link {
        font-size: 13.5px;
        padding: 9px 14px;
    }
}

/* ── Touch-friendly: bigger tap targets on mobile ───────────────────── */
@media (hover: none) and (pointer: coarse) {

    .garage-sidebar .nav-link {
        min-height: 52px !important;
    }

    .garage-sidebar .nav-subitem .sub-link {
        min-height: 46px !important;
    }

    .btn {
        min-height: 40px;
    }

    .btn-sm {
        min-height: 34px !important;
    }

    .form-control,
    .form-select {
        min-height: 42px;
        font-size: 15px !important;
    }

    /* DataTables pagination buttons: bigger */
    .dataTables_paginate .paginate_button {
        padding: .4rem .8rem !important;
        font-size: .82rem !important;
    }
}
