<?php
require_once __DIR__ . '/../../inc/app.php';
workshop_require_login();

$workshopUser = $_SESSION['user'];
$workshopBase = workshop_base_path();
$activePage   = 'drivers_all';
?>
<!doctype html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>All Drivers – SHENGCHI AUTO LTD</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <link rel="stylesheet" href="../layout.css.php?v=<?= time() ?>">

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

  <script>
    var _base_url_ = <?= json_encode($workshopBase) ?>;
    function start_loader() {}
    function end_loader() {}
  </script>

  <style>
    :root {
      --tbl-header-bg: #1e293b;
      --tbl-header-color: #e2e8f0;
      --tbl-header-accent: #818cf8;
      --tbl-row-odd: #ffffff;
      --tbl-row-even: #f8fafc;
      --tbl-row-hover: #eef2ff;
      --tbl-row-selected: #e0e7ff;
      --tbl-border: #e5e7eb;
      --tbl-border-light: #f1f5f9;
      --tbl-text-primary: #1e293b;
      --tbl-text-secondary: #64748b;
      --tbl-text-muted: #94a3b8;
      --tbl-accent: #6366f1;
      --tbl-accent-light: #eef2ff;
      --tbl-success-bg: #dcfce7;
      --tbl-success-text: #166534;
      --tbl-success-border: #bbf7d0;
      --tbl-danger-bg: #fee2e2;
      --tbl-danger-text: #991b1b;
      --tbl-danger-border: #fecaca;
      --tbl-radius: 12px;
      --tbl-radius-sm: 8px;
    }

    .toast-success { background-color: #28a745 !important; color: white !important; }
    .toast-error   { background-color: #dc3545 !important; color: white !important; }

    #driversTable {
      font-size: .875rem;
      border-collapse: separate;
      border-spacing: 0;
      color: var(--tbl-text-primary);
      width: 100%;
    }

    /* ── Header ────────────────────────────────────────────── */
    #driversTable thead th {
      background: var(--tbl-header-bg);
      color: var(--tbl-header-color);
      font-size: .7rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .08em;
      border: none;
      border-bottom: 3px solid var(--tbl-accent);
      white-space: nowrap;
      padding: 1rem 1.1rem;
      position: sticky;
      top: 0;
      z-index: 2;
    }
    #driversTable thead th:first-child { border-radius: var(--tbl-radius) 0 0 0; }
    #driversTable thead th:last-child  { border-radius: 0 var(--tbl-radius) 0 0; }
    #driversTable thead th.text-center { text-align: center; }

    /* ── Body cells ────────────────────────────────────────── */
    #driversTable tbody td {
      padding: .85rem 1.1rem;
      vertical-align: middle;
      border: none;
      border-bottom: 1px solid var(--tbl-border-light);
      white-space: nowrap;
      transition: background .15s ease;
    }

    /* ── Zebra striping ────────────────────────────────────── */
    #driversTable tbody tr:nth-child(odd)  td { background: var(--tbl-row-odd); }
    #driversTable tbody tr:nth-child(even) td { background: var(--tbl-row-even); }

    /* ── Hover & selection ─────────────────────────────────── */
    #driversTable tbody tr:hover td {
      background: var(--tbl-row-hover);
    }
    #driversTable tbody tr.selected td,
    #driversTable tbody tr.dt-rowSelected td {
      background: var(--tbl-row-selected) !important;
    }

    /* ── Last row no border ────────────────────────────────── */
    #driversTable tbody tr:last-child td { border-bottom: none; }

    /* ── Empty state ───────────────────────────────────────── */
    #driversTable .dataTables_empty {
      text-align: center;
      padding: 3rem !important;
      color: var(--tbl-text-muted);
      font-size: .95rem;
    }

    /* ── Scrollbar ─────────────────────────────────────────── */
    #driversTable_wrapper .dataTables_scrollBody::-webkit-scrollbar { height: 6px; }
    #driversTable_wrapper .dataTables_scrollBody::-webkit-scrollbar-track { background: transparent; }
    #driversTable_wrapper .dataTables_scrollBody::-webkit-scrollbar-thumb {
      background: #cbd5e1; border-radius: 3px;
    }

    /* ── Controls: length, filter ──────────────────────────── */
    #driversTable_wrapper .dataTables_length,
    #driversTable_wrapper .dataTables_filter {
      margin-bottom: 1rem;
    }
    #driversTable_wrapper .dataTables_length label,
    #driversTable_wrapper .dataTables_filter label {
      font-size: .82rem;
      font-weight: 600;
      color: var(--tbl-text-secondary);
    }
    #driversTable_wrapper .dataTables_length select,
    #driversTable_wrapper .dataTables_filter input {
      border: 1px solid var(--tbl-border);
      border-radius: var(--tbl-radius-sm);
      padding: .4rem .75rem;
      font-size: .85rem;
      outline: none;
      transition: border-color .2s, box-shadow .2s;
      background: #fff;
      color: var(--tbl-text-primary);
    }
    #driversTable_wrapper .dataTables_filter input:focus {
      border-color: var(--tbl-accent);
      box-shadow: 0 0 0 3px rgba(99,102,241,.1);
    }

    /* ── Info & pagination ─────────────────────────────────── */
    #driversTable_wrapper .dataTables_info {
      color: var(--tbl-text-muted);
      font-size: .8rem;
      padding-top: 1rem;
    }
    #driversTable_wrapper .dataTables_paginate { padding-top: 1rem; }
    #driversTable_wrapper .dataTables_paginate .paginate_button {
      border: 1px solid var(--tbl-border) !important;
      border-radius: var(--tbl-radius-sm) !important;
      background: #fff !important;
      color: var(--tbl-text-secondary) !important;
      font-size: .8rem;
      font-weight: 500;
      margin: 0 2px;
      padding: .35rem .85rem !important;
      transition: all .15s ease;
    }
    #driversTable_wrapper .dataTables_paginate .paginate_button:hover {
      background: var(--tbl-accent-light) !important;
      color: var(--tbl-accent) !important;
      border-color: #c7d2fe !important;
    }
    #driversTable_wrapper .dataTables_paginate .paginate_button.current,
    #driversTable_wrapper .dataTables_paginate .paginate_button.current:hover {
      background: var(--tbl-accent) !important;
      border-color: var(--tbl-accent) !important;
      color: #fff !important;
      font-weight: 600;
    }

    /* ── Status badges ─────────────────────────────────────── */
    .badge-status {
      display: inline-flex;
      align-items: center;
      gap: .35em;
      padding: .3em .85em;
      font-size: .7rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .04em;
      border-radius: 50px;
      line-height: 1.4;
      white-space: nowrap;
    }
    .badge-active {
      background: var(--tbl-success-bg);
      color: var(--tbl-success-text);
      border: 1px solid var(--tbl-success-border);
    }
    .badge-inactive {
      background: var(--tbl-danger-bg);
      color: var(--tbl-danger-text);
      border: 1px solid var(--tbl-danger-border);
    }

    /* ── Driver name cell ──────────────────────────────────── */
    .driver-cell {
      display: flex;
      align-items: center;
      gap: .65rem;
    }
    .driver-avatar {
      width: 34px;
      height: 34px;
      border-radius: 50%;
      background: var(--tbl-accent-light);
      color: var(--tbl-accent);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: .75rem;
      font-weight: 700;
      flex-shrink: 0;
      text-transform: uppercase;
    }
    .driver-name { font-weight: 600; color: var(--tbl-text-primary); }

    /* ── License tag ───────────────────────────────────────── */
    .license-tag {
      font-size: .78rem;
      font-weight: 600;
      background: var(--tbl-accent-light);
      color: var(--tbl-accent);
      padding: .25em .65em;
      border-radius: 6px;
      letter-spacing: .02em;
    }

    /* ── Numeric cells ─────────────────────────────────────── */
    .num-cell {
      font-variant-numeric: tabular-nums;
      font-weight: 600;
      color: var(--tbl-text-primary);
    }
    .num-cell.zero { color: var(--tbl-text-muted); font-weight: 400; }

    /* ── Fuel cost ─────────────────────────────────────────── */
    .fuel-amount {
      font-weight: 600;
      font-variant-numeric: tabular-nums;
      color: var(--tbl-text-primary);
    }

    /* ── Action buttons ────────────────────────────────────── */
    .action-btn {
      width: 32px;
      height: 32px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      border-radius: var(--tbl-radius-sm);
      font-size: .8rem;
      border: 1px solid var(--tbl-border);
      background: #fff;
      transition: all .15s ease;
    }
    .action-btn.btn-edit {
      color: var(--tbl-accent);
    }
    .action-btn.btn-edit:hover {
      background: var(--tbl-accent);
      color: #fff;
      border-color: var(--tbl-accent);
    }
    .action-btn.btn-delete {
      color: var(--tbl-danger-text);
    }
    .action-btn.btn-delete:hover {
      background: var(--tbl-danger-text);
      color: #fff;
      border-color: var(--tbl-danger-text);
    }

    /* ── Phone & ID cells ──────────────────────────────────── */
    .phone-text {
      font-variant-numeric: tabular-nums;
      color: var(--tbl-text-primary);
      font-size: .84rem;
    }
    .id-text {
      font-size: .84rem;
      font-weight: 500;
      color: var(--tbl-text-secondary);
      font-variant-numeric: tabular-nums;
    }
  </style>
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<div class="app-wrapper">

  <?php include '../navbar.php'; ?>
  <?php include '../sidebar.php'; ?>

  <main class="app-main">
    <div class="app-content-header px-4 pt-3 pb-0">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h4 class="fw-bold mb-0" data-i18n="driversAll">All Drivers</h4>
          <p class="text-muted small mb-0" data-i18n="driversSubtitle">View and manage all registered drivers</p>
        </div>
        <ol class="breadcrumb mb-0" style="--bs-breadcrumb-divider:'›';">
          <li class="breadcrumb-item"><a href="../index.php" class="text-primary" data-i18n="home">Home</a></li>
          <li class="breadcrumb-item active" data-i18n="driversAll">All Drivers</li>
        </ol>
      </div>
    </div>

    <div class="app-content p-4">
      <div class="card border-0 shadow-sm" style="border-radius:16px;">
        <div class="card-header bg-white py-3" style="border-radius:16px 16px 0 0;">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="bi bi-person-workspace me-2 text-primary"></i><span data-i18n="allDriversCard">All Drivers</span></h5>
            <div class="d-flex gap-2">
              <button class="btn btn-sm btn-light border" id="btnRefresh" title="Refresh"><i class="fa fa-sync"></i></button>
              <button class="btn btn-sm btn-light border" onclick="exportTable('print')"><i class="fa fa-print"></i></button>
              <div class="dropdown">
                <button class="btn btn-sm btn-light border dropdown-toggle" data-bs-toggle="dropdown">
                  <i class="bi bi-download me-1"></i><span data-i18n="export">Export</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                  <li><a class="dropdown-item small" href="#" onclick="exportTable('pdf')"><i class="fa fa-file-pdf text-danger me-2"></i>PDF</a></li>
                  <li><a class="dropdown-item small" href="#" onclick="exportTable('excel')"><i class="fa fa-file-excel text-success me-2"></i>Excel</a></li>
                  <li><a class="dropdown-item small" href="#" onclick="exportTable('csv')"><i class="fa fa-file-csv text-info me-2"></i>CSV</a></li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <div class="card-body p-4">
          <div class="table-responsive">
            <table id="driversTable" class="align-middle w-100">
              <thead>
                <tr>
                  <th style="width:42px;"></th>
                  <th data-i18n="driverName">Driver</th>
                  <th data-i18n="mobile">Mobile</th>
                  <th data-i18n="licenseNo">License No</th>
                  <th data-i18n="idNumber">ID Number</th>
                  <th data-i18n="status">Status</th>
                  <th class="text-center" data-i18n="trips">Trips</th>
                  <th class="text-center" data-i18n="assignments">Assignments</th>
                  <th data-i18n="totalFuel">Total Fuel</th>
                  <th class="text-center" data-i18n="action">Action</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Edit Driver Modal -->
  <div class="modal fade" id="driverModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content rounded-3">
        <div class="modal-header bg-primary text-white">
          <div>
            <h5 class="modal-title fw-bold" id="driverModalTitle"><span data-i18n="editDriver">Edit Driver</span></h5>
            <small class="opacity-75"><span data-i18n="updateSubtitle">Update driver details</span></small>
          </div>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="driverForm">
          <input type="hidden" name="driver_id" id="driverId" value="0">
          <div class="modal-body p-4">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label small fw-semibold"><span data-i18n="driverName">Driver Name</span> <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="driver_name" id="inputName" required maxlength="50">
                <div class="invalid-feedback">Driver name is required (max 50 characters).</div>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold"><span data-i18n="mobileNumber">Mobile Number</span></label>
                <input class="form-control" type="text" name="mobile" id="inputMobile" pattern="[0-9]*" inputmode="numeric">
                <div class="invalid-feedback">Phone number must contain only digits.</div>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold"><span data-i18n="licenseNumber">License Number</span></label>
                <input class="form-control" type="text" name="license_no" id="inputLicense">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold"><span data-i18n="idNumber">ID Number</span> <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="id_number" id="inputIdNumber" required maxlength="30">
                <div class="invalid-feedback">ID number is required (max 30 characters).</div>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold"><span data-i18n="address">Address</span></label>
                <input class="form-control" type="text" name="address" id="inputAddress" pattern="[A-Za-z0-9\s]*" inputmode="text">
                <div class="invalid-feedback">Address may only contain letters and numbers.</div>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold"><span data-i18n="emergencyContact">Emergency Contact</span> <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="emergency_contact" id="inputEmergency" required pattern="[0-9]*" inputmode="numeric">
                <div class="invalid-feedback">Emergency contact must contain only digits.</div>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold"><span data-i18n="status">Status</span></label>
                <select class="form-select" name="status" id="inputStatus">
                  <option value="active" data-i18n="active">Active</option>
                  <option value="inactive" data-i18n="inactive">Inactive</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal"><span data-i18n="cancel">Cancel</span></button>
            <button type="submit" class="btn btn-primary" id="btnSaveDriver"><i class="bi bi-save me-1"></i><span data-i18n="saveDriver">Save Driver</span></button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Confirm delete modal -->
  <div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
      <div class="modal-content border-top border-danger border-4">
        <div class="modal-header bg-white">
          <h6 class="modal-title text-danger fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i><span data-i18n="areYouSure">Are you sure?</span></h6>
        </div>
        <div class="modal-body text-center py-4" id="confirmBody"><span data-i18n="actionCannotUndone">This action cannot be undone.</span></div>
        <div class="modal-footer border-0 justify-content-center pb-4">
          <button class="btn btn-danger px-4 fw-bold" id="btnConfirm"><span data-i18n="yesDelete">YES, DELETE</span></button>
          <button class="btn btn-light px-4" data-bs-dismiss="modal"><span data-i18n="cancel">CANCEL</span></button>
        </div>
      </div>
    </div>
  </div>

  <footer class="app-footer">
    <div class="footer-content">
      <div class="text-muted small order-2 order-md-1"><strong>Copyright &copy; 2026</strong> <span class="d-none d-sm-inline">| <span data-i18n="allRightsReserved">All Rights Reserved.</span></span></div>
      <div class="order-1 order-md-2 text-center">
        <div style="font-size:11px;text-transform:uppercase;letter-spacing:2px;color:#adb5bd;font-weight:600;" class="mb-1"><span data-i18n="tagline">Think of it, We Develop it.</span></div>
        <a href="https://pearl-host.com/" target="_blank" class="text-decoration-none text-primary text-uppercase fw-bold"><i class="bi bi-gem me-1"></i> <span data-i18n="abSolutions">AB Solutions</span></a>
      </div>
      <div class="footer-contacts order-3">
        <a href="https://wa.me/256772173286" target="_blank" title="WhatsApp"><i class="bi bi-whatsapp"></i></a>
        <a href="tel:+256763808854" title="Call"><i class="bi bi-telephone-outbound"></i></a>
        <a href="mailto:support@pearl-host.com" title="Email"><i class="bi bi-envelope-at"></i></a>
      </div>
    </div>
  </footer>

</div>

<script>
const API = '../../classes/Drivers.php';
let table = null;
let selectedId = null;

const fmt = n => 'UGX ' + Number(n).toLocaleString('en-UG');

function formatPhone(val) {
  if (!val) return '<span class="text-muted">\u2014</span>';
  const digits = String(val).replace(/\D/g, '');
  if (digits.startsWith('254')) return '+' + digits;
  if (digits.startsWith('0')) return '+254' + digits.substring(1);
  if (digits.length >= 9) return '+254' + digits;
  return '+' + digits;
}

$(document).ready(function () {
  table = $('#driversTable').DataTable({
    scrollX: true,
    scrollCollapse: true,
    processing: true,
    pageLength: 25,
    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
    dom: '<"row align-items-center mb-3"<"col-sm-6"l><"col-sm-6"f>>rtip',
    language: {
      info: 'Showing _START_ to _END_ of _TOTAL_ drivers',
      infoEmpty: 'No drivers found',
      infoFiltered: '(filtered from _MAX_ total)',
      emptyTable: '<div style="padding:2rem;"><i class="bi bi-inbox" style="font-size:2rem;color:var(--tbl-text-muted);display:block;margin-bottom:.5rem;"></i>No drivers registered yet.</div>'
    },
    buttons: [
      { extend:'copy',  exportOptions:{ columns:[1,2,3,4,5,6,7,8,9] } },
      { extend:'csv',   exportOptions:{ columns:[1,2,3,4,5,6,7,8,9] } },
      { extend:'excel', exportOptions:{ columns:[1,2,3,4,5,6,7,8,9] } },
      { extend:'pdf',   exportOptions:{ columns:[1,2,3,4,5,6,7,8,9] } },
      { extend:'print', exportOptions:{ columns:[1,2,3,4,5,6,7,8,9] } }
    ],
    ajax: {
      url: API + '?f=viewall',
      dataSrc: function (json) {
        if (!Array.isArray(json)) { toastr.error('Unexpected response from server.'); return []; }
        return json;
      },
      error: function (xhr, error, thrown) {
        toastr.error('Could not load drivers: ' + (thrown || error));
      }
    },
    columns: [
      { data:'driver_id', orderable:false, className:'text-center',
        render: d => `<div class="form-check"><input class="form-check-input row-check" type="checkbox" value="${d}"></div>` },
      { data:'driver_name',
        render: function(d) {
          const initials = d.split(' ').map(w => w.charAt(0)).join('').substring(0, 2);
          return `<div class="driver-cell"><div class="driver-avatar">${initials}</div><span class="driver-name">${d}</span></div>`;
        }
      },
      { data:'mobile', render: d => `<span class="phone-text">${formatPhone(d)}</span>` },
      { data:'license_no',
        render: d => d ? `<span class="license-tag">${d}</span>` : '<span style="color:var(--tbl-text-muted)">—</span>'
      },
      { data:'id_number', render: d => d ? `<span class="id-text">${d}</span>` : '<span style="color:var(--tbl-text-muted)">—</span>' },
      { data:'status', className:'text-center',
        render: d => {
          if (d === 'active') return '<span class="badge-status badge-active"><i class="bi bi-check-circle-fill"></i>Active</span>';
          return '<span class="badge-status badge-inactive"><i class="bi bi-x-circle-fill"></i>Inactive</span>';
        }
      },
      { data:'total_trips', className:'text-center',
        render: d => { const v = d || 0; return `<span class="num-cell${v === 0 ? ' zero' : ''}">${v}</span>`; }
      },
      { data:'total_assignments', className:'text-center',
        render: d => { const v = d || 0; return `<span class="num-cell${v === 0 ? ' zero' : ''}">${v}</span>`; }
      },
      { data:'total_fuel',
        render: d => Number(d) > 0
          ? `<span class="fuel-amount">${fmt(d)}</span>`
          : '<span style="color:var(--tbl-text-muted)">—</span>'
      },
      { data:'driver_id', orderable:false, className:'text-center',
        render: function(d) {
          return `<div class="d-flex gap-1 justify-content-center">
            <button class="action-btn btn-edit btn-edit-driver" data-id="${d}" title="Edit"><i class="fa fa-pen"></i></button>
            <button class="action-btn btn-delete btn-delete-driver" data-id="${d}" title="Delete"><i class="fa fa-trash-can"></i></button>
          </div>`;
        }
      }
    ]
  });

  $('#driversTable tbody').on('click', 'tr', function () {
    $(this).toggleClass('selected').siblings().removeClass('selected');
    const sel = $(this).hasClass('selected');
    table.$('.row-check').prop('checked', false);
    $(this).find('.row-check').prop('checked', sel);
    selectedId = sel ? table.row(this).data().driver_id : null;
  });

  $(window).on('resize', function () {
    if (table) { table.columns.adjust(); }
  });

  $('#btnRefresh').click(function() { table.ajax.reload(null, false); toastr.info('Table refreshed.'); });
});

$(document).on('click', '.btn-edit-driver', function(e) {
  e.stopPropagation();
  const id = $(this).data('id');
  $.getJSON(API + '?f=get&id=' + id, function(r) {
    $('#driverModalTitle').text('Edit Driver');
    $('#driverId').val(r.driver_id);
    $('#inputName').val(r.driver_name);
    $('#inputMobile').val(r.mobile);
    $('#inputLicense').val(r.license_no);
    $('#inputIdNumber').val(r.id_number);
    $('#inputAddress').val(r.address);
    $('#inputEmergency').val(r.emergency_contact);
    $('#inputStatus').val(r.status);
    new bootstrap.Modal('#driverModal').show();
  }).fail(() => toastr.error('Could not load driver details.'));
});

$('#driverForm').on('submit', function(e) {
  e.preventDefault();
  const fields = $(this)[0].querySelectorAll('.form-control, .form-select');
  fields.forEach(f => f.classList.remove('is-invalid'));

  const name = $('#inputName').val().trim();
  const idNum = $('#inputIdNumber').val().trim();
  const mobile = $('#inputMobile').val().trim();
  const address = $('#inputAddress').val().trim();
  const emergency = $('#inputEmergency').val().trim();
  let valid = true;

  if (!name || name.length > 50) { $('#inputName').addClass('is-invalid'); valid = false; }
  if (!idNum || idNum.length > 30) { $('#inputIdNumber').addClass('is-invalid'); valid = false; }
  if (mobile && !/^[0-9]+$/.test(mobile)) { $('#inputMobile').addClass('is-invalid'); valid = false; }
  if (address && !/^[A-Za-z0-9\s]+$/.test(address)) { $('#inputAddress').addClass('is-invalid'); valid = false; }
  if (!emergency || !/^[0-9]+$/.test(emergency)) { $('#inputEmergency').addClass('is-invalid'); valid = false; }
  if (!valid) { toastr.warning('Please fix the highlighted fields.'); return; }

  const btn = $('#btnSaveDriver').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');
  $.ajax({
    url: API + '?f=save', method: 'POST', dataType: 'json', data: $(this).serialize(),
    success: function(r) {
      if (r.status === 'success') {
        toastr.success(r.msg);
        bootstrap.Modal.getInstance('#driverModal').hide();
        table.ajax.reload(null, false);
      } else {
        toastr.error(r.msg);
      }
    },
    error: xhr => toastr.error(xhr.responseJSON?.msg || 'Could not save the driver.'),
    complete: () => $('#btnSaveDriver').prop('disabled', false).html('<i class="bi bi-save me-1"></i>Save Driver')
  });
});

$(document).on('click', '.btn-delete-driver', function(e) {
  e.stopPropagation();
  selectedId = $(this).data('id');
  $('#confirmBody').text('Delete this driver? This cannot be undone.');
  const m = new bootstrap.Modal('#confirmModal');
  $('#btnConfirm').off('click').on('click', function() {
    $.post(API + '?f=delete', { id: selectedId }, function(r) {
      if (r.status === 'success') {
        toastr.success(r.msg);
        table.ajax.reload();
        selectedId = null;
      } else {
        toastr.error(r.msg);
      }
      bootstrap.Modal.getInstance('#confirmModal').hide();
    }, 'json').fail(() => toastr.error('Delete failed.'));
  });
  m.show();
});

function exportTable(type) {
  if (!table) return;
  const map = { csv: '.buttons-csv', excel: '.buttons-excel', pdf: '.buttons-pdf', print: '.buttons-print' };
  if (map[type]) table.button(map[type]).trigger();
}
</script>
</body>
</html>
