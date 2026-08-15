<?php
require_once __DIR__ . '/../inc/app.php';
workshop_require_login();

// Check if user has permission to view repair jobs
workshop_require_permission('manage_repair_jobs');

$workshopUser = $_SESSION['user'];
$workshopBase = workshop_base_path();
$userRole = $workshopUser['effective_role'] ?? $workshopUser['role'] ?? 'cashier';
$canDelete = workshop_has_permission('delete_records');
$canExport = workshop_has_permission('export_data');
?>
<!doctype html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Repair Jobs – SHENGCHI AUTO LTD</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css">
  <link rel="stylesheet" href="layout.css.php?v=<?= time() ?>">

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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    var _base_url_ = <?= json_encode($workshopBase) ?>;
    function start_loader() {}
    function end_loader() {}
    function pushNotification(title, msg, type) {
      toastr[type] ? toastr[type](msg, title) : toastr.info(msg, title);
    }
  </script>

  <style>
    .toast-success { background-color: #28a745 !important; color: white !important; }
    .toast-error   { background-color: #dc3545 !important; color: white !important; }
    .toast-warning { background-color: rgb(229,156,54); color: black; }
  </style>
</head>

<div id="notification-stack"></div>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<div class="app-wrapper">

  <?php include 'navbar.php'; ?>

<?php $activePage = 'repair_jobs'; include 'sidebar.php'; ?>

  <main class="app-main">
    <div class="app-content-header">
      <div class="row mt-0 mb-0">
        <div class="col-sm-6 mb-0 mt-0"><h3 class="mb-0"></h3></div>
        <div class="col-sm-6 mt-0">
          <ol class="breadcrumb float-sm-end" style="--bs-breadcrumb-divider: '›';">
            <li class="breadcrumb-item text-primary"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Repair Jobs</li>
          </ol>
        </div>
      </div>
    </div>

    <div class="app-content p-0 m-0">

      <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
          <div class="d-flex justify-content-between align-items-center">
            <h3 class="card-title fw-bold mb-0">Repair Jobs Register</h3>
            <button type="button" id="registerRepairJob" class="btn btn-primary btn-sm shadow-sm">
              <i class="fa fa-plus me-2"></i>Register Repair Job
            </button>
          </div>
        </div>

        <div class="card-body p-4">
          <div id="toolbar" class="mb-4 pb-3 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex flex-wrap gap-2 align-items-center">
              <div class="action-group d-flex gap-2">
                <button class="btn btn-outline-success btn-sm edit"><i class="fa fa-edit me-1"></i>Edit</button>
                <button class="btn btn-outline-warning btn-sm reset"><i class="fa fa-key me-1"></i>Password</button>
              </div>
              <div class="action-group d-flex gap-2">
                <button class="btn btn-outline-info btn-sm unlockuser"><i class="fa fa-unlock me-1"></i>Unlock</button>
                <button class="btn btn-outline-danger btn-sm lockuser"><i class="fa fa-lock me-1"></i>Lock</button>
              </div>
              <div class="d-flex gap-2">
                <button class="btn btn-outline-dark btn-sm assignrole"><i class="bi bi-shield-lock me-1"></i>Roles</button>
                <button class="btn btn-outline-secondary btn-sm assignreports"><i class="bi bi-file-earmark-text me-1"></i>Reports</button>
                <button class="btn btn-outline-danger btn-sm deleteuser ms-2"><i class="fa fa-trash"></i></button>
              </div>
            </div>
            <div class="d-flex gap-2">
              <button class="btn btn-sm btn-light border" onclick="exportTable('print', table)"><i class="fa fa-print"></i></button>
              <div class="dropdown">
                <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                  <i class="bi bi-download me-1"></i> Export
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                  <li><a class="dropdown-item small" href="#" onclick="exportTable('pdf', table)"><i class="fa fa-file-pdf text-danger me-2"></i>PDF Document</a></li>
                  <li><a class="dropdown-item small" href="#" onclick="exportTable('excel')"><i class="fa fa-file-excel text-success me-2"></i>Excel Spreadsheet</a></li>
                  <li><a class="dropdown-item small" href="#" onclick="exportTable('csv', table)"><i class="fa fa-file-csv text-info me-2"></i>CSV File</a></li>
                </ul>
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table id="repairjobstable" class="table align-middle w-100">
              <thead>
                <tr>
                  <th style="width:30px;"></th>
                  <th>Job No.</th>
                  <th>Customer</th>
                  <th>Vehicle</th>
                  <th>Repair Type</th>
                  <th>Parts Cost</th>
                  <th>Labour Cost</th>
                  <th>Status</th>
                  <th>Date</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>

      <!-- Register / Edit Repair Job Modal -->
      <div class="modal fade" id="repairJobModal" tabindex="-1" aria-labelledby="repairJobModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
          <div class="modal-content rounded-3">
            <div class="modal-header bg-primary text-white">
              <div>
                <h5 class="modal-title fw-bold" id="repairJobModalLabel">Register Repair Job</h5>
                <small class="opacity-75">Customer, vehicle and repair details</small>
              </div>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="repairJobForm">
              <div class="modal-body p-4">
                <div class="row g-3">
                  <div class="col-12"><h6 class="text-uppercase text-muted small fw-bold mb-0">Customer details</h6></div>
                  <div class="col-md-6"><label class="form-label small fw-semibold">Customer name <span class="text-danger">*</span></label><input class="form-control" name="customer_name" required></div>
                  <div class="col-md-6"><label class="form-label small fw-semibold">Phone / contact <span class="text-danger">*</span></label><input class="form-control" name="contact" required></div>
                  <div class="col-12"><label class="form-label small fw-semibold">Address</label><input class="form-control" name="address"></div>
                  <div class="col-12 pt-2"><h6 class="text-uppercase text-muted small fw-bold mb-0">Vehicle and repair</h6></div>
                  <div class="col-md-6"><label class="form-label small fw-semibold">Plate number <span class="text-danger">*</span></label><input class="form-control text-uppercase" name="plate_number" placeholder="KDD 821T" required></div>
                  <div class="col-md-6"><label class="form-label small fw-semibold">Vehicle make / model</label><input class="form-control" name="model" placeholder="Toyota Prado"></div>
                  <div class="col-12"><label class="form-label small fw-semibold">Repair type <span class="text-danger">*</span></label><input class="form-control" name="repair_type" placeholder="e.g. Brake pads replacement" required></div>
                  <div class="col-md-4"><label class="form-label small fw-semibold">Parts cost (KES) <span class="text-danger">*</span></label><input class="form-control" type="number" name="parts_cost" min="0" step="0.01" value="0" required></div>
                  <div class="col-md-4"><label class="form-label small fw-semibold">Labour cost (KES) <span class="text-danger">*</span></label><input class="form-control" type="number" name="labour_cost" min="0" step="0.01" value="0" required></div>
                  <div class="col-md-4">
                    <label class="form-label small fw-semibold">Repair status <span class="text-danger">*</span></label>
                    <select class="form-select" name="status" required>
                      <option value="REPAIR PENDING">REPAIR PENDING</option>
                      <option value="REPAIR DONE">REPAIR DONE</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="saveRepairJob"><i class="bi bi-save me-1"></i>Save Repair Job</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Confirm Modal -->
      <div class="modal fade" id="confirm_modal" role="dialog">
        <div class="modal-dialog modal-sm modal-dialog-centered">
          <div class="modal-content border-top border-danger border-4">
            <div class="modal-header bg-white">
              <h5 class="modal-title text-danger fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i>Are you sure?</h5>
            </div>
            <div class="modal-body py-4 text-center"><div id="delete_content" class="fs-6"></div></div>
            <div class="modal-footer border-0 justify-content-center pb-4">
              <button type="button" class="btn btn-danger px-4 fw-bold" id="confirm">YES, CONTINUE</button>
              <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">NO</button>
            </div>
          </div>
        </div>
      </div>

    </div><!-- /.app-content -->
  </main>

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

</div><!-- /.app-wrapper -->

<script>
  let table = null;
  let selectedJob = null;

  $(document).ready(function () {
    loadTable();
  });

  function loadTable() {
    if ($.fn.DataTable.isDataTable('#repairjobstable')) {
      table.destroy();
    }
    table = $('#repairjobstable').DataTable({
      responsive: true,
      processing: true,
      pageLength: 50,
      dom: 'lfrtip',
      buttons: [
        { extend: 'copy',  exportOptions: { columns: ':not(:first-child)' } },
        { extend: 'csv',   exportOptions: { columns: ':not(:first-child)' } },
        { extend: 'excel', exportOptions: { columns: ':not(:first-child)' } },
        { extend: 'pdf',   exportOptions: { columns: ':not(:first-child)' } },
        { extend: 'print', exportOptions: { columns: ':not(:first-child)' } }
      ],
      ajax: {
        url: '../classes/RepairJobs.php?f=viewall',
        dataSrc: function (json) {
          if (!Array.isArray(json)) {
            toastr.error('Unexpected response from server.');
            return [];
          }
          return json;
        },
        error: function (xhr, error, thrown) {
          toastr.error('Could not load repair jobs: ' + (thrown || error));
        }
      },
      columns: [
        { data: 'repair_job_id', orderable: false, render: d => `<div class="form-check"><input class="form-check-input row-check" type="checkbox" value="${d}"></div>` },
        { data: 'job_no',       render: d => `<code class="text-primary fw-bold">${d}</code>` },
        { data: 'customer',     render: d => `<span class="fw-bold text-dark">${d}</span>` },
        { data: 'vehicle',      render: d => `<span class="small"><i class="bi bi-car-front me-1 text-muted"></i>${d}</span>` },
        { data: 'repair_type' },
        { data: 'parts_cost',   render: d => `KES ${Number(d).toLocaleString()}` },
        { data: 'labour_cost',  render: d => `KES ${Number(d).toLocaleString()}` },
        { data: 'status', className: 'text-center', render: d => d === 'REPAIR DONE'
            ? '<span class="status-pill status-done"><i class="bi bi-check-circle-fill me-1"></i>REPAIR DONE</span>'
            : '<span class="status-pill status-pending"><i class="bi bi-clock-history me-1"></i>REPAIR PENDING</span>' },
        { data: 'date' }
      ]
    });

    $('#repairjobstable tbody').on('click', 'tr', function () {
      $(this).toggleClass('selected').siblings().removeClass('selected');
      const isSelected = $(this).hasClass('selected');
      table.$('.row-check').prop('checked', false);
      $(this).find('.row-check').prop('checked', isSelected);
      selectedJob = isSelected ? table.row(this).data().repair_job_id : null;
    });
  }

  const repairJobModal = new bootstrap.Modal(document.getElementById('repairJobModal'));
  $('#registerRepairJob').on('click', () => repairJobModal.show());

  $('#repairJobForm').on('submit', function (e) {
    e.preventDefault();
    const btn = $('#saveRepairJob');
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');
    $.ajax({
      url: '../classes/RepairJobs.php?f=save',
      method: 'POST',
      dataType: 'json',
      data: $(this).serialize(),
      success: function (resp) {
        if (resp.status === 'success') {
          toastr.success(resp.msg);
          $('#repairJobForm')[0].reset();
          repairJobModal.hide();
          table.ajax.reload(null, false);
        } else {
          toastr.error(resp.msg || 'Could not save the repair job.');
        }
      },
      error: xhr => toastr.error(xhr.responseJSON?.msg || 'Could not save the repair job.'),
      complete: () => btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i>Save Repair Job')
    });
  });

  $('.deleteuser').click(() => {
    if (!selectedJob) return toastr.error('Please select a repair job to delete.');
    _conf('Are you sure you want to delete this repair job?', 'delete_job');
  });
  $('.lockuser').click(() => { if (!selectedJob) return toastr.error('Select a record first.'); });
  $('.unlockuser').click(() => { if (!selectedJob) return toastr.error('Select a record first.'); });
  $('.reset').click(() => { if (!selectedJob) return toastr.error('Select a record first.'); });
  $('.edit').click(() => { if (!selectedJob) return toastr.error('Select a repair job to edit.'); });

  function _conf(msg, func, params = []) {
    $('#confirm_modal #confirm').attr('onclick', func + '(' + params.join(',') + ')');
    $('#confirm_modal #delete_content').text(msg);
    new bootstrap.Modal(document.getElementById('confirm_modal')).show();
  }

  function delete_job() {
    $.post('../classes/RepairJobs.php?f=delete', { id: selectedJob }, function (data) {
      const resp = typeof data === 'string' ? JSON.parse(data) : data;
      if (resp.status === 'success') {
        table.ajax.reload(); selectedJob = null;
        bootstrap.Modal.getInstance(document.getElementById('confirm_modal')).hide();
        toastr.success(resp.msg);
      } else { toastr.error(resp.msg); }
    });
  }

  function exportTable(type, mytable) {
    mytable = mytable || table;
    if (!mytable) return toastr.error('Table not ready.');
    const map = { csv: '.buttons-csv', excel: '.buttons-excel', pdf: '.buttons-pdf', print: '.buttons-print' };
    if (map[type]) mytable.button(map[type]).trigger();
  }
</script>
</body>
</html>
