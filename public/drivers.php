<?php
require_once __DIR__ . '/../inc/app.php';
workshop_require_login();
$workshopUser = $_SESSION['user'];
$workshopBase = workshop_base_path();
$activePage   = 'drivers';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Drivers – SHENGCHI AUTO LTD</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <link rel="stylesheet" href="layout.css.php?v=<?= time() ?>">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <script>
    var _base_url_ = <?= json_encode($workshopBase) ?>;
    function start_loader(){}
    function end_loader(){}
  </script>
  <style>
    .toast-success{background:#28a745!important;color:#fff!important;}
    .toast-error{background:#dc3545!important;color:#fff!important;}
    .plate-badge{font-family:monospace;font-weight:800;font-size:.85rem;letter-spacing:1px;background:#1e293b;color:#f8fafc;padding:3px 10px;border-radius:6px;}
    .driver-badge{font-size:.75rem;font-weight:600;color:#fff;background:#6366f1;padding:3px 10px;border-radius:20px;}
    .btn-new{background:#2563eb;color:#fff;border:none;}
    .btn-new:hover{background:#1d4ed8;color:#fff;}
    .btn-save{background:#16a34a;color:#fff;border:none;}
    .btn-save:hover{background:#15803d;color:#fff;}
    .btn-delete{background:#dc2626;color:#fff;border:none;}
    .btn-delete:hover{background:#b91c1c;color:#fff;}
    .btn-print{background:#d97706;color:#fff;border:none;}
    .btn-print:hover{background:#b45309;color:#fff;}
    .time-badge{font-size:.7rem;font-weight:600;padding:3px 8px;border-radius:6px;}
    .time-in{background:#dcfce7;color:#166534;}
    .time-out{background:#fee2e2;color:#991b1b;}
  </style>
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<div class="app-wrapper">

<?php include 'navbar.php'; ?>
<?php include 'sidebar.php'; ?>

<main class="app-main">
  <div class="app-content-header px-4 pt-3 pb-0">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-person-workspace me-2 text-primary"></i>Drivers</h4>
        <p class="text-muted small mb-0">Track drivers, vehicles and schedule</p>
      </div>
      <ol class="breadcrumb mb-0" style="--bs-breadcrumb-divider:'›';">
        <li class="breadcrumb-item"><a href="index.php" class="text-primary">Home</a></li>
        <li class="breadcrumb-item"><a href="vehicles.php" class="text-primary">Vehicles</a></li>
        <li class="breadcrumb-item active">Drivers</li>
      </ol>
    </div>
  </div>

  <div class="app-content p-4">
    <!-- Action Buttons Row -->
    <div class="d-flex flex-wrap gap-2 mb-3">
      <button class="btn btn-new btn-sm fw-semibold" id="btnNew">
        <i class="fa fa-plus me-1"></i>New
      </button>
      <button class="btn btn-save btn-sm fw-semibold" id="btnSaveTop" disabled>
        <i class="fa fa-floppy-disk me-1"></i>Save
      </button>
      <button class="btn btn-delete btn-sm fw-semibold" id="btnDeleteTop" disabled>
        <i class="fa fa-trash me-1"></i>Delete
      </button>
      <button class="btn btn-print btn-sm fw-semibold" id="btnPrint">
        <i class="fa fa-print me-1"></i>Print
      </button>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius:16px;">
      <div class="card-header bg-white py-3" style="border-radius:16px 16px 0 0;">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="fw-bold mb-0"><i class="bi bi-journal-text me-2 text-primary"></i>Driver Log</h5>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" onclick="$('#driversTable').DataTable().ajax.reload(null,false);">
              <i class="fa fa-sync-alt me-1"></i>Refresh
            </button>
          </div>
        </div>
      </div>

      <div class="card-body p-4">
        <div class="table-responsive">
          <table id="driversTable" class="table align-middle w-100">
            <thead>
              <tr>
                <th style="width:30px;"></th>
                <th>Driver</th>
                <th>Vehicle</th>
                <th>Plate</th>
                <th>Type</th>
                <th>Model/Year</th>
                <th>Mobile</th>
                <th>Time In</th>
                <th>Time Out</th>
                <th>Description</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</main>

<!-- Driver Modal -->
<div class="modal fade" id="driverModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-3">
      <div class="modal-header" style="background:linear-gradient(135deg,#6366f1,#4f46e5);color:#fff;">
        <div>
          <h5 class="modal-title fw-bold" id="driverModalTitle">New Driver Record</h5>
          <small class="opacity-75">Vehicle, driver and schedule details</small>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="driverForm">
        <input type="hidden" name="driver_id" id="driverId" value="0">
        <div class="modal-body p-4">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label small fw-semibold">Vehicle (plate & owner) <span class="text-danger">*</span></label>
              <select class="form-select" name="vehicle_id" id="selectVehicle" required>
                <option value="">— select vehicle —</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Driver Name <span class="text-danger">*</span></label>
              <input class="form-control" name="driver_name" id="inputDriverName" placeholder="e.g. James Okello" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Driver Mobile</label>
              <input class="form-control" name="driver_mobile" id="inputDriverMobile" placeholder="0772 123 456">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Vehicle Type</label>
              <select class="form-select" name="vehicle_type" id="inputVehicleType">
                <option value="">— select type —</option>
                <option value="Sedan">Sedan</option>
                <option value="SUV">SUV</option>
                <option value="Truck">Truck</option>
                <option value="Pickup">Pickup</option>
                <option value="Van">Van</option>
                <option value="Bus">Bus</option>
                <option value="Motorcycle">Motorcycle</option>
                <option value="Other">Other</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Model / Year</label>
              <input class="form-control" name="model_year" id="inputModelYear" placeholder="e.g. Toyota Prado 2022">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Time In</label>
              <input class="form-control" type="datetime-local" name="time_in" id="inputTimeIn">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Time Out</label>
              <input class="form-control" type="datetime-local" name="time_out" id="inputTimeOut">
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold">Description</label>
              <textarea class="form-control" name="description" id="inputDescription" rows="3" placeholder="Additional notes..."></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-save fw-semibold" id="btnSaveDriver">
            <i class="bi bi-save me-1"></i>Save Record
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Confirm Delete Modal -->
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
    <div class="text-muted small order-2 order-md-1"><strong>Copyright &copy; 2026</strong><span class="d-none d-sm-inline"> | All Rights Reserved.</span></div>
    <div class="order-1 order-md-2 text-center">
      <div style="font-size:11px;text-transform:uppercase;letter-spacing:2px;color:#adb5bd;font-weight:600;" class="mb-1">Think of it, We Develop it.</div>
      <a href="https://pearl-host.com/" target="_blank" class="text-decoration-none text-primary text-uppercase fw-bold"><i class="bi bi-gem me-1"></i> AB Solutions</a>
    </div>
    <div class="footer-contacts order-3">
      <a href="https://wa.me/256772173286" target="_blank"><i class="bi bi-whatsapp"></i></a>
      <a href="tel:+256763808854"><i class="bi bi-telephone-outbound"></i></a>
      <a href="mailto:support@pearl-host.com"><i class="bi bi-envelope-at"></i></a>
    </div>
  </div>
</footer>

</div>

<script>
let table = null;
let selectedId = null;
const API = '../classes/Drivers.php';

$(document).ready(function(){
  table = $('#driversTable').DataTable({
    responsive: true,
    pageLength: 25,
    dom: 'lfrtip',
    ajax: {
      url: API + '?f=viewall',
      dataSrc: function(json){ return Array.isArray(json) ? json : []; },
      error: () => toastr.error('Could not load driver records.')
    },
    columns: [
      { data:'driver_id', orderable:false,
        render: d => `<div class="form-check"><input class="form-check-input row-check" type="checkbox" value="${d}"></div>` },
      { data:'driver_name', render: d => `<span class="driver-badge">${d}</span>` },
      { data:'car_owner', render: d => `<span class="fw-semibold">${d}</span>` },
      { data:'plate_number', render: d => `<span class="plate-badge">${d}</span>` },
      { data:'vehicle_type', render: d => d ? `<span class="small">${d}</span>` : '<span class="text-muted">—</span>' },
      { data:'model_year', render: d => d ? `<span class="small">${d}</span>` : '<span class="text-muted">—</span>' },
      { data:'driver_mobile', render: d => d ? `<a href="tel:${d}" class="text-decoration-none small">${d}</a>` : '<span class="text-muted">—</span>' },
      { data:'time_in_display', render: d => d ? `<span class="time-badge time-in"><i class="fa fa-sign-in-alt me-1"></i>${d}</span>` : '<span class="text-muted">—</span>' },
      { data:'time_out_display', render: d => d ? `<span class="time-badge time-out"><i class="fa fa-sign-out-alt me-1"></i>${d}</span>` : '<span class="text-muted">—</span>' },
      { data:'description', render: d => d ? `<span class="small text-muted" title="${d.replace(/"/g,'&quot;')}">${d.length > 40 ? d.substring(0,40) + '…' : d}</span>` : '<span class="text-muted">—</span>' }
    ]
  });

  // Row selection
  $('#driversTable tbody').on('click','tr',function(){
    $(this).toggleClass('selected').siblings().removeClass('selected');
    const sel = $(this).hasClass('selected');
    table.$('.row-check').prop('checked',false);
    $(this).find('.row-check').prop('checked',sel);
    selectedId = sel ? table.row(this).data().driver_id : null;
    $('#btnSaveTop').prop('disabled', !sel);
    $('#btnDeleteTop').prop('disabled', !sel);
  });
});

// ── Load vehicles into dropdown ──────────────────────────────────────────────
function loadVehicleDropdown(selectedVehicleId){
  $.getJSON(API + '?f=vehicles', function(rows){
    const sel = $('#selectVehicle').empty().append('<option value="">— select vehicle —</option>');
    rows.forEach(r => {
      const label = r.plate_number + ' — ' + r.car_owner + (r.model ? ' (' + r.model + ')' : '');
      const opt = $('<option>').val(r.id).text(label);
      if(parseInt(r.id) === parseInt(selectedVehicleId)) opt.prop('selected',true);
      sel.append(opt);
    });
  });
}

// ── New ──────────────────────────────────────────────────────────────────────
$('#btnNew').click(function(){
  $('#driverModalTitle').text('New Driver Record');
  $('#driverForm')[0].reset();
  $('#driverId').val(0);
  loadVehicleDropdown(0);
  new bootstrap.Modal('#driverModal').show();
});

// ── Edit (double-click row) ──────────────────────────────────────────────────
$('#driversTable tbody').on('dblclick','tr', function(){
  const data = table.row(this).data();
  if(!data) return;
  selectedId = data.driver_id;
  $.getJSON(API + '?f=get&id=' + selectedId, function(r){
    $('#driverModalTitle').text('Edit Driver Record');
    $('#driverId').val(r.driver_id);
    $('#inputDriverName').val(r.driver_name);
    $('#inputDriverMobile').val(r.driver_mobile);
    $('#inputVehicleType').val(r.vehicle_type);
    $('#inputModelYear').val(r.model_year);
    $('#inputTimeIn').val(r.time_in);
    $('#inputTimeOut').val(r.time_out);
    $('#inputDescription').val(r.description);
    loadVehicleDropdown(r.vehicle_id);
    new bootstrap.Modal('#driverModal').show();
  }).fail(() => toastr.error('Could not load driver record.'));
});

// ── Save ─────────────────────────────────────────────────────────────────────
$('#driverForm').on('submit', function(e){
  e.preventDefault();
  const btn = $('#btnSaveDriver').prop('disabled',true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving…');
  $.ajax({
    url: API + '?f=save', method:'POST', dataType:'json', data: $(this).serialize(),
    success: function(r){
      if(r.status==='success'){
        toastr.success(r.msg);
        bootstrap.Modal.getInstance('#driverModal').hide();
        table.ajax.reload(null,false);
      } else {
        toastr.error(r.msg);
      }
    },
    error: xhr => toastr.error(xhr.responseJSON?.msg || 'Save failed.'),
    complete: () => $('#btnSaveDriver').prop('disabled',false).html('<i class="bi bi-save me-1"></i>Save Record')
  });
});

// ── Delete ───────────────────────────────────────────────────────────────────
$('#btnDeleteTop').click(function(){
  if(!selectedId) return toastr.error('Select a driver record first.');
  $('#confirmBody').text('Delete this driver record? This cannot be undone.');
  const m = new bootstrap.Modal('#confirmModal');
  $('#btnConfirm').off('click').on('click',function(){
    $.post(API + '?f=delete', {id: selectedId}, function(r){
      if(r.status==='success'){
        toastr.success(r.msg);
        table.ajax.reload();
        selectedId = null;
        $('#btnSaveTop').prop('disabled',true);
        $('#btnDeleteTop').prop('disabled',true);
      } else {
        toastr.error(r.msg);
      }
      bootstrap.Modal.getInstance('#confirmModal').hide();
    },'json').fail(()=>toastr.error('Delete failed.'));
  });
  m.show();
});

// ── Print ────────────────────────────────────────────────────────────────────
$('#btnPrint').click(function(){
  const data = table.rows().data().toArray();
  if(!data.length) return toastr.error('No records to print.');

  let printContent = `
    <html><head><title>Driver Log - SHENGCHI AUTO LTD</title>
    <style>
      body{font-family:Arial,sans-serif;padding:20px;}
      h2{text-align:center;color:#4f46e5;}
      table{width:100%;border-collapse:collapse;margin-top:15px;font-size:12px;}
      th{background:#4f46e5;color:#fff;padding:8px;text-align:left;}
      td{padding:6px 8px;border-bottom:1px solid #e5e7eb;}
      tr:nth-child(even){background:#f9fafb;}
      .footer{margin-top:20px;text-align:center;font-size:11px;color:#888;}
    </style></head><body>
    <h2><i class="bi bi-person-workspace"></i> Driver Log</h2>
    <p style="text-align:center;color:#666;">SHENGCHI AUTO LTD (金龙汽车维修) — Generated: ${new Date().toLocaleString()}</p>
    <table>
      <thead><tr>
        <th>#</th><th>Driver</th><th>Plate</th><th>Type</th><th>Model/Year</th>
        <th>Mobile</th><th>Time In</th><th>Time Out</th><th>Description</th>
      </tr></thead><tbody>`;

  data.forEach((r, i) => {
    printContent += `<tr>
      <td>${i+1}</td><td>${r.driver_name}</td><td>${r.plate_number}</td>
      <td>${r.vehicle_type || '—'}</td><td>${r.model_year || '—'}</td>
      <td>${r.driver_mobile || '—'}</td><td>${r.time_in_display || '—'}</td>
      <td>${r.time_out_display || '—'}</td><td>${r.description || '—'}</td>
    </tr>`;
  });

  printContent += `</tbody></table>
    <div class="footer">SHENGCHI AUTO LTD — Driver Log Report</div>
    </body></html>`;

  const w = window.open('','','width=900,height=700');
  w.document.write(printContent);
  w.document.close();
  w.print();
});
</script>
</body>
</html>
