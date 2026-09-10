<?php
require_once __DIR__ . '/../../inc/app.php';
workshop_require_login();

$workshopUser = $_SESSION['user'];
$workshopBase = workshop_base_path();
$activePage   = 'drivers_fuel';
?>
<!doctype html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Fuel Records – SHENGCHI AUTO LTD</title>
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
    .toast-success { background-color: #28a745 !important; color: white !important; }
    .toast-error   { background-color: #dc3545 !important; color: white !important; }

    #fuelTable { font-size: .85rem; border-collapse: separate; border-spacing: 0; }
    #fuelTable thead th {
      background: #f8fafc; color: #64748b; font-size: .72rem; font-weight: 700;
      text-transform: uppercase; letter-spacing: .5px; border-bottom: 2px solid #eef2f7;
      border-top: 0; white-space: nowrap; padding: .8rem .9rem;
    }
    #fuelTable tbody td {
      padding: .7rem .9rem; vertical-align: middle; border-bottom: 1px solid #f8fafc; white-space: nowrap;
    }
    #fuelTable tbody tr:hover td { background: #f8fafc; }
    #fuelTable .dataTables_empty { text-align: center; padding: 2.5rem !important; color: #94a3b8; font-size: .9rem; }
    #fuelTable_wrapper .dataTables_scrollBody::-webkit-scrollbar { height: 8px; }
    #fuelTable_wrapper .dataTables_scrollBody::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    #fuelTable_wrapper .dataTables_length,
    #fuelTable_wrapper .dataTables_filter { margin-bottom: .9rem; }
    #fuelTable_wrapper .dataTables_length select,
    #fuelTable_wrapper .dataTables_filter input {
      border: 1px solid #e2e8f0; border-radius: 8px; padding: .35rem .6rem; font-size: .85rem; outline: none;
    }
    #fuelTable_wrapper .dataTables_filter input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.12); }
    #fuelTable_wrapper .dataTables_info { color: #94a3b8; font-size: .8rem; padding-top: 1rem; }
    #fuelTable_wrapper .dataTables_paginate { padding-top: .9rem; }
    #fuelTable_wrapper .dataTables_paginate .paginate_button {
      border: 1px solid #e2e8f0 !important; border-radius: 8px !important; background: #fff !important;
      color: #64748b !important; font-size: .8rem; margin: 0 2px; padding: .3rem .75rem !important;
    }
    #fuelTable_wrapper .dataTables_paginate .paginate_button:hover {
      background: #f8fafc !important; color: #6366f1 !important; border-color: #c7d2fe !important;
    }
    #fuelTable_wrapper .dataTables_paginate .paginate_button.current,
    #fuelTable_wrapper .dataTables_paginate .paginate_button.current:hover {
      background: #6366f1 !important; border-color: #6366f1 !important; color: #fff !important;
    }
    .fuel-badge-diesel { background: #dbeafe; color: #1e40af; padding: .25em .6em; border-radius: 6px; font-size: .72rem; font-weight: 700; }
    .fuel-badge-petrol { background: #dcfce7; color: #166534; padding: .25em .6em; border-radius: 6px; font-size: .72rem; font-weight: 700; }
    .fuel-badge-other  { background: #fef3c7; color: #92400e; padding: .25em .6em; border-radius: 6px; font-size: .72rem; font-weight: 700; }
    #fuelTable td code { font-size: .8rem; background: #eef2ff; padding: .2rem .5rem; border-radius: 6px; }
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
          <h4 class="fw-bold mb-0" data-i18n="driversFuel">Fuel Records</h4>
          <p class="text-muted small mb-0" data-i18n="fuelSubtitle">Track fuel purchases and consumption</p>
        </div>
        <ol class="breadcrumb mb-0" style="--bs-breadcrumb-divider:'›';">
          <li class="breadcrumb-item"><a href="../index.php" class="text-primary" data-i18n="home">Home</a></li>
          <li class="breadcrumb-item active" data-i18n="driversFuel">Fuel Records</li>
        </ol>
      </div>
    </div>

    <div class="app-content p-4">
      <div class="card border-0 shadow-sm" style="border-radius:16px;">
        <div class="card-header bg-white py-3" style="border-radius:16px 16px 0 0;">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="bi bi-fuel-pump me-2 text-primary"></i><span data-i18n="fuelRegister">Fuel Register</span></h5>
            <div class="d-flex gap-2">
              <button class="btn btn-primary btn-sm" id="btnAdd">
                <i class="fa fa-plus me-1"></i><span data-i18n="addFuelRecord">Add Fuel Record</span>
              </button>
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
            <table id="fuelTable" class="table align-middle w-100">
              <thead>
                <tr>
                  <th style="width:30px;"></th>
                  <th data-i18n="driver">Driver</th>
                  <th data-i18n="vehicle">Vehicle</th>
                  <th data-i18n="date">Date</th>
                  <th data-i18n="liters">Liters</th>
                  <th data-i18n="costPerLiter">Cost/Liter</th>
                  <th data-i18n="totalUGX">Total (UGX)</th>
                  <th data-i18n="type">Type</th>
                  <th data-i18n="station">Station</th>
                  <th data-i18n="receiptNo">Receipt No</th>
                  <th class="text-center" data-i18n="action">Action</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Add / Edit Fuel Modal -->
  <div class="modal fade" id="entryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content rounded-3">
        <div class="modal-header bg-primary text-white">
          <div>
            <h5 class="modal-title fw-bold" id="entryModalTitle"><span data-i18n="addFuelRecord">Add Fuel Record</span></h5>
            <small class="opacity-75"><span data-i18n="fuelModalSubtitle">Record fuel purchase details</span></small>
          </div>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="entryForm">
          <input type="hidden" name="fuel_id" id="entryId" value="0">
          <div class="modal-body p-4">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label small fw-semibold"><span data-i18n="driverLabel">Driver</span> <span class="text-danger">*</span></label>
                <select class="form-select" name="driver_id" id="selectDriver" required>
                  <option value="" data-i18n="selectDriverOption">— Select driver —</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold"><span data-i18n="vehicleLabel2">Vehicle</span> <span class="text-danger">*</span></label>
                <select class="form-select" name="vehicle_id" id="selectVehicle" required>
                  <option value="" data-i18n="selectVehicleOption">— Select vehicle —</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold"><span data-i18n="fuelDate">Fuel Date</span> <span class="text-danger">*</span></label>
                <input class="form-control" type="date" name="fuel_date" id="inputFuelDate" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold"><span data-i18n="fuelType">Fuel Type</span> <span class="text-danger">*</span></label>
                <select class="form-select" name="fuel_type" id="inputFuelType" required>
                  <option value="DIESEL" data-i18n="diesel">DIESEL</option>
                  <option value="PETROL" data-i18n="petrol">PETROL</option>
                  <option value="OTHER" data-i18n="fuelOther">OTHER</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-semibold"><span data-i18n="litersLabel">Liters</span> <span class="text-danger">*</span></label>
                <input class="form-control" type="number" name="liters" id="inputLiters" step="0.1" min="0" placeholder="0" required>
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-semibold"><span data-i18n="costPerLiterLabel">Cost/Liter (UGX)</span> <span class="text-danger">*</span></label>
                <input class="form-control" type="number" name="cost_per_liter" id="inputCostPerLiter" step="1" min="0" placeholder="0" required>
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-semibold"><span data-i18n="totalCostLabel2">Total Cost (UGX)</span></label>
                <input class="form-control" type="number" name="total_cost" id="inputTotalCost" step="1" min="0" placeholder="Auto-calculated" readonly style="background:#f8fafc;">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold"><span data-i18n="stationLabel">Station</span></label>
                <input class="form-control" type="text" name="station" id="inputStation" placeholder="e.g. Shell Total">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold"><span data-i18n="receiptNoLabel">Receipt No</span></label>
                <input class="form-control" type="text" name="receipt_no" id="inputReceiptNo" placeholder="Receipt reference">
              </div>
              <div class="col-12">
                <label class="form-label small fw-semibold"><span data-i18n="notesLabel">Notes</span></label>
                <textarea class="form-control" name="notes" id="inputNotes" rows="2" placeholder="Additional notes..."></textarea>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary" id="btnSave"><i class="bi bi-save me-1"></i>Save Record</button>
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
          <h6 class="modal-title text-danger fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Are you sure?</h6>
        </div>
        <div class="modal-body text-center py-4" id="confirmBody">This action cannot be undone.</div>
        <div class="modal-footer border-0 justify-content-center pb-4">
          <button class="btn btn-danger px-4 fw-bold" id="btnConfirm">YES, DELETE</button>
          <button class="btn btn-light px-4" data-bs-dismiss="modal">CANCEL</button>
        </div>
      </div>
    </div>
  </div>

  <footer class="app-footer">
    <div class="footer-content">
      <div class="text-muted small order-2 order-md-1"><strong>Copyright &copy; 2026</strong> <span class="d-none d-sm-inline">| All Rights Reserved.</span></div>
      <div class="order-1 order-md-2 text-center">
        <div style="font-size:11px;text-transform:uppercase;letter-spacing:2px;color:#adb5bd;font-weight:600;" class="mb-1">Think of it, We Develop it.</div>
        <a href="https://pearl-host.com/" target="_blank" class="text-decoration-none text-primary text-uppercase fw-bold"><i class="bi bi-gem me-1"></i> AB Solutions</a>
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

function calcTotal() {
  const liters = parseFloat($('#inputLiters').val()) || 0;
  const cost = parseFloat($('#inputCostPerLiter').val()) || 0;
  const total = liters * cost;
  $('#inputTotalCost').val(total > 0 ? Math.round(total) : '');
}

$(document).ready(function () {
  table = $('#fuelTable').DataTable({
    scrollX: true,
    scrollCollapse: true,
    processing: true,
    pageLength: 50,
    dom: 'lfrtip',
    buttons: [
      { extend:'copy',  exportOptions:{ columns:[1,2,3,4,5,6,7,8,9] } },
      { extend:'csv',   exportOptions:{ columns:[1,2,3,4,5,6,7,8,9] } },
      { extend:'excel', exportOptions:{ columns:[1,2,3,4,5,6,7,8,9] } },
      { extend:'pdf',   exportOptions:{ columns:[1,2,3,4,5,6,7,8,9] } },
      { extend:'print', exportOptions:{ columns:[1,2,3,4,5,6,7,8,9] } }
    ],
    ajax: {
      url: API + '?f=fuel_viewall',
      dataSrc: function (json) {
        if (!Array.isArray(json)) { toastr.error('Unexpected response from server.'); return []; }
        return json;
      },
      error: function (xhr, error, thrown) {
        toastr.error('Could not load fuel records: ' + (thrown || error));
      }
    },
    columns: [
      { data:'fuel_id', orderable:false, className:'text-center',
        render: d => `<div class="form-check"><input class="form-check-input row-check" type="checkbox" value="${d}"></div>` },
      { data:'driver_name', render: d => `<span class="fw-bold text-dark">${d || '—'}</span>` },
      { data:'vehicle', render: d => `<span class="small"><i class="bi bi-car-front me-1 text-muted"></i>${d || '—'}</span>` },
      { data:'fuel_date', className:'text-muted small' },
      { data:'liters', className:'text-center', render: d => d ? `${Number(d).toFixed(1)} L` : '<span class="text-muted">—</span>' },
      { data:'cost_per_liter', render: d => Number(d) > 0 ? fmt(d) : '<span class="text-muted">—</span>' },
      { data:'total_cost', render: d => Number(d) > 0 ? `<span class="fw-semibold">${fmt(d)}</span>` : '<span class="text-muted">—</span>' },
      { data:'fuel_type', className:'text-center',
        render: d => {
          if (d === 'DIESEL') return '<span class="fuel-badge-diesel">DIESEL</span>';
          if (d === 'PETROL') return '<span class="fuel-badge-petrol">PETROL</span>';
          return '<span class="fuel-badge-other">' + (d || 'OTHER') + '</span>';
        }
      },
      { data:'station', render: d => d || '<span class="text-muted">—</span>' },
      { data:'receipt_no', render: d => d ? `<code>${d}</code>` : '<span class="text-muted">—</span>' },
      { data:'fuel_id', orderable:false, className:'text-center',
        render: function(d, t, r) {
          let btns = '';
          btns += `<button class="btn btn-sm btn-outline-primary btn-edit me-1" data-id="${d}" title="Edit" style="border-radius:8px;"><i class="fa fa-edit"></i></button>`;
          btns += `<button class="btn btn-sm btn-outline-danger btn-delete" data-id="${d}" title="Delete" style="border-radius:8px;"><i class="fa fa-trash"></i></button>`;
          return btns;
        }
      }
    ]
  });

  $('#fuelTable tbody').on('click', 'tr', function () {
    $(this).toggleClass('selected').siblings().removeClass('selected');
    const sel = $(this).hasClass('selected');
    table.$('.row-check').prop('checked', false);
    $(this).find('.row-check').prop('checked', sel);
    selectedId = sel ? table.row(this).data().fuel_id : null;
  });

  $(window).on('resize', function () { if (table) { table.columns.adjust(); } });

  $('#inputLiters').on('input', calcTotal);
  $('#inputCostPerLiter').on('input', calcTotal);

  loadDropdowns();
});

function loadDropdowns() {
  $.getJSON(API + '?f=drivers', function(rows) {
    const sel = $('#selectDriver');
    sel.empty().append('<option value="">— Select driver —</option>');
    if (Array.isArray(rows)) {
      rows.forEach(function(r) {
        sel.append(`<option value="${r.driver_id}">${r.driver_name}</option>`);
      });
    }
  });
  $.getJSON(API + '?f=vehicles', function(rows) {
    const sel = $('#selectVehicle');
    sel.empty().append('<option value="">— Select vehicle —</option>');
    if (Array.isArray(rows)) {
      rows.forEach(function(r) {
        sel.append(`<option value="${r.vehicle_id}">${r.plate_number} — ${r.owner || r.model || ''}</option>`);
      });
    }
  });
}

$('#btnAdd').click(function() {
  $('#entryModalTitle').text('Add Fuel Record');
  $('#entryForm')[0].reset();
  $('#entryId').val(0);
  loadDropdowns();
  new bootstrap.Modal('#entryModal').show();
});

$(document).on('click', '.btn-edit', function(e) {
  e.stopPropagation();
  const id = $(this).data('id');
  $.getJSON(API + '?f=fuel_get&id=' + id, function(r) {
    $('#entryModalTitle').text('Edit Fuel Record');
    $('#entryId').val(r.fuel_id);
    loadDropdowns();
    setTimeout(function() {
      $('#selectDriver').val(r.driver_id);
      $('#selectVehicle').val(r.vehicle_id);
    }, 300);
    $('#inputFuelDate').val(r.fuel_date);
    $('#inputLiters').val(r.liters);
    $('#inputCostPerLiter').val(r.cost_per_liter);
    $('#inputTotalCost').val(r.total_cost);
    $('#inputFuelType').val(r.fuel_type);
    $('#inputStation').val(r.station);
    $('#inputReceiptNo').val(r.receipt_no);
    $('#inputNotes').val(r.notes);
    new bootstrap.Modal('#entryModal').show();
  }).fail(() => toastr.error('Could not load fuel record details.'));
});

$('#entryForm').on('submit', function(e) {
  e.preventDefault();
  const btn = $('#btnSave').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');
  $.ajax({
    url: API + '?f=fuel_save', method: 'POST', dataType: 'json', data: $(this).serialize(),
    success: function(r) {
      if (r.status === 'success') {
        toastr.success(r.msg);
        bootstrap.Modal.getInstance('#entryModal').hide();
        table.ajax.reload(null, false);
      } else {
        toastr.error(r.msg);
      }
    },
    error: xhr => toastr.error(xhr.responseJSON?.msg || 'Could not save the fuel record.'),
    complete: () => $('#btnSave').prop('disabled', false).html('<i class="bi bi-save me-1"></i>Save Record')
  });
});

$(document).on('click', '.btn-delete', function(e) {
  e.stopPropagation();
  selectedId = $(this).data('id');
  $('#confirmBody').text('Delete this fuel record? This cannot be undone.');
  const m = new bootstrap.Modal('#confirmModal');
  $('#btnConfirm').off('click').on('click', function() {
    $.post(API + '?f=fuel_delete', { id: selectedId }, function(r) {
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
