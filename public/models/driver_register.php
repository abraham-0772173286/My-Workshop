<?php
require_once __DIR__ . '/../../inc/app.php';
workshop_require_login();

$workshopUser = $_SESSION['user'];
$workshopBase = workshop_base_path();
$activePage   = 'drivers_register';
?>
<!doctype html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Register Driver – SHENGCHI AUTO LTD</title>
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
    #driversTable tbody tr.selected td { background: #eef2ff; }
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
    .register-form-card { border-radius: 16px; }
    .register-form-card .form-label { color: #475569; }
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
          <h4 class="fw-bold mb-0" data-i18n="driversRegister">Register Driver</h4>
          <p class="text-muted small mb-0">Add a new driver to the system</p>
        </div>
        <ol class="breadcrumb mb-0" style="--bs-breadcrumb-divider:'›';">
          <li class="breadcrumb-item"><a href="../index.php" class="text-primary">Home</a></li>
          <li class="breadcrumb-item active" data-i18n="driversRegister">Register Driver</li>
        </ol>
      </div>
    </div>

    <div class="app-content p-4">
      <!-- Registration Form -->
      <div class="card border-0 shadow-sm register-form-card mb-4">
        <div class="card-header bg-white py-3" style="border-radius:16px 16px 0 0;">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="bi bi-person-plus me-2 text-primary"></i>Driver Details</h5>
            <div class="d-flex gap-2">
              <button class="btn btn-sm btn-light border" id="btnNew" title="New"><i class="fa fa-file me-1"></i>New</button>
              <button class="btn btn-sm btn-danger" id="btnDeleteDriver" title="Delete"><i class="fa fa-trash me-1"></i>Delete</button>
            </div>
          </div>
        </div>
        <div class="card-body p-4">
          <form id="driverForm">
            <input type="hidden" name="driver_id" id="driverId" value="0">
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label small fw-semibold">Driver Name <span class="text-danger">*</span></label>
                <input class="form-control" type="text" name="driver_name" id="inputName" placeholder="Enter full name" required>
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-semibold">Mobile Number</label>
                <input class="form-control" type="text" name="mobile" id="inputMobile" placeholder="e.g. 0700123456">
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-semibold">License Number</label>
                <input class="form-control" type="text" name="license_no" id="inputLicense" placeholder="e.g. U1234567">
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-semibold">ID Number</label>
                <input class="form-control" type="text" name="id_number" id="inputIdNumber" placeholder="National ID / Passport">
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-semibold">Address</label>
                <input class="form-control" type="text" name="address" id="inputAddress" placeholder="Physical address">
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-semibold">Emergency Contact</label>
                <input class="form-control" type="text" name="emergency_contact" id="inputEmergency" placeholder="Name & phone">
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-semibold">Status</label>
                <select class="form-select" name="status" id="inputStatus">
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                </select>
              </div>
            </div>
            <div class="mt-4">
              <button type="submit" class="btn btn-primary px-4" id="btnSaveDriver"><i class="bi bi-save me-1"></i>Save Driver</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Drivers list -->
      <div class="card border-0 shadow-sm" style="border-radius:16px;">
        <div class="card-header bg-white py-3" style="border-radius:16px 16px 0 0;">
          <h5 class="fw-bold mb-0"><i class="bi bi-list-ul me-2 text-primary"></i>Existing Drivers</h5>
        </div>
        <div class="card-body p-4">
          <div class="table-responsive">
            <table id="driversTable" class="table align-middle w-100">
              <thead>
                <tr>
                  <th>Driver Name</th>
                  <th>Mobile</th>
                  <th>License No</th>
                  <th>ID Number</th>
                  <th>Status</th>
                  <th>Trips</th>
                  <th>Assignments</th>
                  <th>Total Fuel</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </main>

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

const fmt = n => 'UGX ' + Number(n).toLocaleString('en-UG');

function loadTable() {
  if (table) { table.ajax.reload(null, false); return; }
  table = $('#driversTable').DataTable({
    scrollX: true,
    scrollCollapse: true,
    processing: true,
    pageLength: 25,
    dom: 'lfrtip',
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
      { data:'total_fuel', render: d => Number(d) > 0 ? `<span class="fw-semibold">${fmt(d)}</span>` : '<span class="text-muted">—</span>' }
    ]
  });

  $('#driversTable tbody').on('click', 'tr', function () {
    const rowData = table.row(this).data();
    if (!rowData) return;
    $('#driversTable tbody tr').removeClass('selected');
    $(this).addClass('selected');
    loadDriver(rowData.driver_id);
  });
}

function loadDriver(id) {
  $.getJSON(API + '?f=get&id=' + id, function(r) {
    $('#driverId').val(r.driver_id);
    $('#inputName').val(r.driver_name);
    $('#inputMobile').val(r.mobile);
    $('#inputLicense').val(r.license_no);
    $('#inputIdNumber').val(r.id_number);
    $('#inputAddress').val(r.address);
    $('#inputEmergency').val(r.emergency_contact);
    $('#inputStatus').val(r.status);
    toastr.info('Loaded: ' + r.driver_name);
  }).fail(() => toastr.error('Could not load driver details.'));
}

function clearForm() {
  $('#driverForm')[0].reset();
  $('#driverId').val(0);
  $('#driversTable tbody tr').removeClass('selected');
}

$(document).ready(function () {
  loadTable();

  $('#btnNew').click(function() { clearForm(); toastr.info('Form cleared.'); });

  $('#driverForm').on('submit', function(e) {
    e.preventDefault();
    const btn = $('#btnSaveDriver').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');
    $.ajax({
      url: API + '?f=save', method: 'POST', dataType: 'json', data: $(this).serialize(),
      success: function(r) {
        if (r.status === 'success') {
          toastr.success(r.msg);
          clearForm();
          loadTable();
        } else {
          toastr.error(r.msg);
        }
      },
      error: xhr => toastr.error(xhr.responseJSON?.msg || 'Could not save the driver.'),
      complete: () => $('#btnSaveDriver').prop('disabled', false).html('<i class="bi bi-save me-1"></i>Save Driver')
    });
  });

  $('#btnDeleteDriver').click(function() {
    const id = $('#driverId').val();
    if (!id || id === '0') { toastr.warning('Select a driver to delete.'); return; }
    const name = $('#inputName').val();
    $('#confirmBody').text('Delete driver "' + name + '"? This cannot be undone.');
    const m = new bootstrap.Modal('#confirmModal');
    $('#btnConfirm').off('click').on('click', function() {
      $.post(API + '?f=delete', { id: id }, function(r) {
        if (r.status === 'success') {
          toastr.success(r.msg);
          clearForm();
          loadTable();
        } else {
          toastr.error(r.msg);
        }
        bootstrap.Modal.getInstance('#confirmModal').hide();
      }, 'json').fail(() => toastr.error('Delete failed.'));
    });
    m.show();
  });

  $(window).on('resize', function () { if (table) { table.columns.adjust(); } });
});
</script>
</body>
</html>
