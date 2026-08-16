<?php
require_once __DIR__ . '/../inc/app.php';
workshop_require_login();
$workshopUser   = $_SESSION['user'];
$workshopBase   = workshop_base_path();
$activePage     = 'vehicles';
$filterCustomer = (int) ($_GET['customer_id'] ?? 0);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Vehicles – SHENGCHI AUTO LTD</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
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
  <script>
    var _base_url_      = <?= json_encode($workshopBase) ?>;
    var filterCustomer  = <?= $filterCustomer ?>;
    function start_loader(){}
    function end_loader(){}
  </script>
  <style>
    .toast-success{background:#28a745!important;color:#fff!important;}
    .toast-error  {background:#dc3545!important;color:#fff!important;}
    .plate-badge{font-family:monospace;font-weight:800;font-size:.85rem;letter-spacing:1px;background:#1e293b;color:#f8fafc;padding:3px 10px;border-radius:6px;}
    .jobs-badge{font-size:.7rem;font-weight:700;color:#4f46e5;background:#eef2ff;padding:2px 8px;border-radius:20px;}
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
        <h4 class="fw-bold mb-0">Vehicles</h4>
        <p class="text-muted small mb-0" id="pageSubtitle">All registered vehicles</p>
      </div>
      <ol class="breadcrumb mb-0" style="--bs-breadcrumb-divider:'›';">
        <li class="breadcrumb-item"><a href="index.php" class="text-primary">Home</a></li>
        <?php if($filterCustomer): ?>
        <li class="breadcrumb-item"><a href="customers.php" class="text-primary">Customers</a></li>
        <?php endif; ?>
        <li class="breadcrumb-item active">Vehicles</li>
      </ol>
    </div>
  </div>

  <div class="app-content p-4">
    <!-- filter banner shown when coming from a customer -->
    <?php if($filterCustomer): ?>
    <div class="alert alert-info d-flex align-items-center gap-2 mb-3 py-2" style="border-radius:10px;">
      <i class="bi bi-funnel-fill"></i>
      <span>Showing vehicles for one customer only.</span>
      <a href="vehicles.php" class="ms-auto btn btn-sm btn-outline-secondary">Clear filter</a>
    </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm" style="border-radius:16px;">
      <div class="card-header bg-white py-3" style="border-radius:16px 16px 0 0;">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="fw-bold mb-0"><i class="bi bi-car-front me-2 text-primary"></i>Vehicle Register</h5>
          <button class="btn btn-primary btn-sm" id="btnAddVehicle">
            <i class="fa fa-plus me-1"></i> Register Vehicle
          </button>
        </div>
      </div>

      <div class="card-body p-4">
        <div class="mb-3 d-flex flex-wrap gap-2 align-items-center justify-content-between">
          <div class="d-flex flex-wrap gap-2">
            <div class="d-flex gap-2 border-end pe-3 me-1">
              <button class="btn btn-outline-success btn-sm btnEdit"><i class="fa fa-edit me-1"></i>Edit</button>
              <button class="btn btn-outline-danger  btn-sm btnDelete"><i class="fa fa-trash me-1"></i>Delete</button>
            </div>
            <button class="btn btn-outline-info btn-sm btnJobs"><i class="bi bi-clipboard2-check me-1"></i>View Jobs</button>
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-sm btn-light border" onclick="exportTable('print')"><i class="fa fa-print"></i></button>
            <div class="dropdown">
              <button class="btn btn-sm btn-light border dropdown-toggle" data-bs-toggle="dropdown">
                <i class="bi bi-download me-1"></i>Export
              </button>
              <ul class="dropdown-menu dropdown-menu-end shadow">
                <li><a class="dropdown-item small" href="#" onclick="exportTable('pdf')"><i class="fa fa-file-pdf text-danger me-2"></i>PDF</a></li>
                <li><a class="dropdown-item small" href="#" onclick="exportTable('excel')"><i class="fa fa-file-excel text-success me-2"></i>Excel</a></li>
                <li><a class="dropdown-item small" href="#" onclick="exportTable('csv')"><i class="fa fa-file-csv text-info me-2"></i>CSV</a></li>
              </ul>
            </div>
          </div>
        </div>

        <div class="table-responsive">
          <table id="vehiclesTable" class="table align-middle w-100">
            <thead>
              <tr>
                <th style="width:30px;"></th>
                <th>Plate</th>
                <th>Model</th>
                <th>Owner</th>
                <th>Contact</th>
                <th class="text-center">Jobs</th>
                <th>Total Spent</th>
                <th>Date Received</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</main>

<!-- Add / Edit Vehicle Modal -->
<div class="modal fade" id="vehicleModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-3">
      <div class="modal-header bg-primary text-white">
        <div>
          <h5 class="modal-title fw-bold" id="vehicleModalTitle">Register Vehicle</h5>
          <small class="opacity-75">Plate number, owner and model details</small>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="vehicleForm">
        <input type="hidden" name="vehicle_id" id="vehicleId" value="0">
        <div class="modal-body p-4">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label small fw-semibold">Customer (owner) <span class="text-danger">*</span></label>
              <select class="form-select" name="customer_id" id="selectCustomer" required>
                <option value="">— select customer —</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Owner name (on vehicle) <span class="text-danger">*</span></label>
              <input class="form-control" name="car_owner" id="inputCarOwner" placeholder="e.g. John Kamau" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Plate number <span class="text-danger">*</span></label>
              <input class="form-control text-uppercase" name="plate_number" id="inputPlate" placeholder="KDD 821T" required>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Make / Model</label>
              <input class="form-control" name="model" id="inputModel" placeholder="Toyota Prado">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Date received <span class="text-danger">*</span></label>
              <input class="form-control" type="date" name="date_received" id="inputDateReceived">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="btnSaveVehicle">
            <i class="bi bi-save me-1"></i>Save Vehicle
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Confirm modal -->
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

</div><!-- /.app-wrapper -->

<script>
let table = null;
let selectedId = null;
const API     = '../classes/Vehicles.php';
const CUST_API= '../classes/Customers.php';

$(document).ready(function(){
  // set today as default date
  $('#inputDateReceived').val(new Date().toISOString().slice(0,10));

  table = $('#vehiclesTable').DataTable({
    responsive: true,
    pageLength: 25,
    dom: 'lfrtip',
    buttons: [
      {extend:'copy', exportOptions:{columns:':not(:first-child)'}},
      {extend:'csv',  exportOptions:{columns:':not(:first-child)'}},
      {extend:'excel',exportOptions:{columns:':not(:first-child)'}},
      {extend:'pdf',  exportOptions:{columns:':not(:first-child)'}},
      {extend:'print',exportOptions:{columns:':not(:first-child)'}}
    ],
    ajax: {
      url: API + '?f=viewall',
      dataSrc: function(json){
        let rows = Array.isArray(json) ? json : [];
        // filter by customer if coming from customers page
        if (filterCustomer) {
          rows = rows.filter(r => parseInt(r.customer_id) === filterCustomer);
          if (rows.length) {
            $('#pageSubtitle').text('Showing vehicles for: ' + rows[0].car_owner);
          }
        }
        return rows;
      },
      error: () => toastr.error('Could not load vehicles.')
    },
    columns: [
      { data:'vehicle_id', orderable:false,
        render: d => `<div class="form-check"><input class="form-check-input row-check" type="checkbox" value="${d}"></div>` },
      { data:'plate_number', render: d => `<span class="plate-badge">${d}</span>` },
      { data:'model', render: d => `<span class="text-dark">${d}</span>` },
      { data:'car_owner', render: d => `<span class="fw-semibold">${d}</span>` },
      { data:'contact', render: d => `<a href="tel:${d}" class="text-decoration-none small">${d}</a>` },
      { data:'total_jobs', className:'text-center',
        render: d => `<span class="jobs-badge">${d} job${d!=1?'s':''}</span>` },
      { data:'total_spent',
        render: d => `<span class="fw-semibold small">UGX ${Number(d).toLocaleString()}</span>` },
      { data:'date_received', className:'text-muted small' }
    ]
  });

  $('#vehiclesTable tbody').on('click','tr',function(){
    $(this).toggleClass('selected').siblings().removeClass('selected');
    const sel = $(this).hasClass('selected');
    table.$('.row-check').prop('checked',false);
    $(this).find('.row-check').prop('checked',sel);
    selectedId = sel ? table.row(this).data().vehicle_id : null;
  });
});

// ── Load customers into the dropdown ─────────────────────────────────────────
function loadCustomerDropdown(selectedCustId){
  $.getJSON(CUST_API + '?f=viewall', function(rows){
    const sel = $('#selectCustomer').empty().append('<option value="">— select customer —</option>');
    rows.forEach(r => {
      const opt = $('<option>').val(r.customer_id).text(r.fullname + ' — ' + r.contact);
      if(parseInt(r.customer_id) === parseInt(selectedCustId)) opt.prop('selected',true);
      sel.append(opt);
    });
  });
}

// ── Add ───────────────────────────────────────────────────────────────────────
$('#btnAddVehicle').click(function(){
  $('#vehicleModalTitle').text('Register Vehicle');
  $('#vehicleForm')[0].reset();
  $('#vehicleId').val(0);
  $('#inputDateReceived').val(new Date().toISOString().slice(0,10));
  loadCustomerDropdown(filterCustomer || 0);
  new bootstrap.Modal('#vehicleModal').show();
});

// ── Edit ──────────────────────────────────────────────────────────────────────
$('.btnEdit').click(function(){
  if(!selectedId) return toastr.error('Select a vehicle first.');
  $.getJSON(API + '?f=get&id=' + selectedId, function(r){
    $('#vehicleModalTitle').text('Edit Vehicle');
    $('#vehicleId').val(r.vehicle_id);
    $('#inputCarOwner').val(r.car_owner);
    $('#inputPlate').val(r.plate_number);
    $('#inputModel').val(r.model);
    $('#inputDateReceived').val(r.date_received);
    loadCustomerDropdown(r.customer_id);
    new bootstrap.Modal('#vehicleModal').show();
  }).fail(() => toastr.error('Could not load vehicle details.'));
});

// ── Save ──────────────────────────────────────────────────────────────────────
$('#vehicleForm').on('submit', function(e){
  e.preventDefault();
  const btn = $('#btnSaveVehicle').prop('disabled',true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving…');
  $.ajax({
    url: API + '?f=save', method:'POST', dataType:'json', data: $(this).serialize(),
    success: function(r){
      if(r.status==='success'){ toastr.success(r.msg); bootstrap.Modal.getInstance('#vehicleModal').hide(); table.ajax.reload(null,false); }
      else toastr.error(r.msg);
    },
    error: xhr => toastr.error(xhr.responseJSON?.msg || 'Save failed.'),
    complete: () => $('#btnSaveVehicle').prop('disabled',false).html('<i class="bi bi-save me-1"></i>Save Vehicle')
  });
});

// ── Delete ────────────────────────────────────────────────────────────────────
$('.btnDelete').click(function(){
  if(!selectedId) return toastr.error('Select a vehicle first.');
  $('#confirmBody').text('Delete this vehicle? This cannot be undone.');
  const m = new bootstrap.Modal('#confirmModal');
  $('#btnConfirm').off('click').on('click',function(){
    $.post(API + '?f=delete', {id: selectedId}, function(r){
      if(r.status==='success'){ toastr.success(r.msg); table.ajax.reload(); selectedId=null; }
      else toastr.error(r.msg);
      bootstrap.Modal.getInstance('#confirmModal').hide();
    },'json').fail(()=>toastr.error('Delete failed.'));
  });
  m.show();
});

// ── View Jobs for this vehicle ────────────────────────────────────────────────
$('.btnJobs').click(function(){
  if(!selectedId) return toastr.error('Select a vehicle first.');
  const data = table.row($('#vehiclesTable tbody tr.selected')).data();
  window.location.href = 'repair_jobs.php?plate=' + encodeURIComponent(data.plate_number);
});

function exportTable(type){
  if(!table) return;
  const map={csv:'.buttons-csv',excel:'.buttons-excel',pdf:'.buttons-pdf',print:'.buttons-print'};
  if(map[type]) table.button(map[type]).trigger();
}
</script>
</body>
</html>
