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
    .toast-success { background-color: #28a745 !important; color: white !important; }
    .toast-error   { background-color: #dc3545 !important; color: white !important; }

    #driversTable { font-size: .85rem; border-collapse: separate; border-spacing: 0; }
    #driversTable thead th {
      background: #f8fafc; color: #64748b; font-size: .72rem; font-weight: 700;
      text-transform: uppercase; letter-spacing: .5px; border-bottom: 2px solid #eef2f7;
      border-top: 0; white-space: nowrap; padding: .8rem .9rem;
    }
    #driversTable tbody td {
      padding: .7rem .9rem; vertical-align: middle; border-bottom: 1px solid #f8fafc; white-space: nowrap;
    }
    #driversTable tbody tr:hover td { background: #f8fafc; }
    #driversTable .dataTables_empty { text-align: center; padding: 2.5rem !important; color: #94a3b8; font-size: .9rem; }
    #driversTable_wrapper .dataTables_scrollBody::-webkit-scrollbar { height: 8px; }
    #driversTable_wrapper .dataTables_scrollBody::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    #driversTable_wrapper .dataTables_length,
    #driversTable_wrapper .dataTables_filter { margin-bottom: .9rem; }
    #driversTable_wrapper .dataTables_length select,
    #driversTable_wrapper .dataTables_filter input {
      border: 1px solid #e2e8f0; border-radius: 8px; padding: .35rem .6rem; font-size: .85rem; outline: none;
    }
    #driversTable_wrapper .dataTables_filter input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.12); }
    #driversTable_wrapper .dataTables_info { color: #94a3b8; font-size: .8rem; padding-top: 1rem; }
    #driversTable_wrapper .dataTables_paginate { padding-top: .9rem; }
    #driversTable_wrapper .dataTables_paginate .paginate_button {
      border: 1px solid #e2e8f0 !important; border-radius: 8px !important; background: #fff !important;
      color: #64748b !important; font-size: .8rem; margin: 0 2px; padding: .3rem .75rem !important;
    }
    #driversTable_wrapper .dataTables_paginate .paginate_button:hover {
      background: #f8fafc !important; color: #6366f1 !important; border-color: #c7d2fe !important;
    }
    #driversTable_wrapper .dataTables_paginate .paginate_button.current,
    #driversTable_wrapper .dataTables_paginate .paginate_button.current:hover {
      background: #6366f1 !important; border-color: #6366f1 !important; color: #fff !important;
    }
    .status-pill {
      padding: .28em .75em; font-size: .68rem; font-weight: 700; text-transform: uppercase; border-radius: 50px;
    }
    .status-active { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .status-inactive { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    #driversTable td code { font-size: .8rem; background: #eef2ff; padding: .2rem .5rem; border-radius: 6px; }
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
            <table id="driversTable" class="table align-middle w-100">
              <thead>
                <tr>
                  <th style="width:30px;"></th>
                  <th data-i18n="driverName">Driver Name</th>
                  <th data-i18n="mobile">Mobile</th>
                  <th data-i18n="licenseNo">License No</th>
                  <th data-i18n="idNumber">ID Number</th>
                  <th data-i18n="status">Status</th>
                  <th data-i18n="trips">Trips</th>
                  <th data-i18n="assignments">Assignments</th>
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
                <input class="form-control" type="text" name="driver_name" id="inputName" required>
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold"><span data-i18n="mobileNumber">Mobile Number</span></label>
                <input class="form-control" type="text" name="mobile" id="inputMobile">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold"><span data-i18n="licenseNumber">License Number</span></label>
                <input class="form-control" type="text" name="license_no" id="inputLicense">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold"><span data-i18n="idNumber">ID Number</span></label>
                <input class="form-control" type="text" name="id_number" id="inputIdNumber">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold"><span data-i18n="address">Address</span></label>
                <input class="form-control" type="text" name="address" id="inputAddress">
              </div>
              <div class="col-md-6">
                <label class="form-label small fw-semibold"><span data-i18n="emergencyContact">Emergency Contact</span></label>
                <input class="form-control" type="text" name="emergency_contact" id="inputEmergency">
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

$(document).ready(function () {
  table = $('#driversTable').DataTable({
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
      { data:'driver_name', render: d => `<span class="fw-bold text-dark">${d}</span>` },
      { data:'mobile', render: d => d || '<span class="text-muted">—</span>' },
      { data:'license_no', render: d => d ? `<code class="text-primary fw-bold">${d}</code>` : '<span class="text-muted">—</span>' },
      { data:'id_number', render: d => d || '<span class="text-muted">—</span>' },
      { data:'status', className:'text-center',
        render: d => {
          if (d === 'active') return '<span class="status-pill status-active"><i class="bi bi-check-circle-fill me-1"></i>ACTIVE</span>';
          return '<span class="status-pill status-inactive"><i class="bi bi-x-circle me-1"></i>INACTIVE</span>';
        }
      },
      { data:'total_trips', className:'text-center', render: d => d || 0 },
      { data:'total_assignments', className:'text-center', render: d => d || 0 },
      { data:'total_fuel', render: d => Number(d) > 0 ? `<span class="fw-semibold">${fmt(d)}</span>` : '<span class="text-muted">—</span>' },
      { data:'driver_id', orderable:false, className:'text-center',
        render: function(d, t, r) {
          let btns = '';
          btns += `<button class="btn btn-sm btn-outline-primary btn-edit-driver me-1" data-id="${d}" title="Edit" style="border-radius:8px;"><i class="fa fa-edit"></i></button>`;
          btns += `<button class="btn btn-sm btn-outline-danger btn-delete-driver" data-id="${d}" title="Delete" style="border-radius:8px;"><i class="fa fa-trash"></i></button>`;
          return btns;
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
