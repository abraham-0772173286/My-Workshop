<?php
require_once __DIR__ . '/../inc/app.php';
workshop_require_login();
$workshopUser = $_SESSION['user'];
$workshopBase = workshop_base_path();
$activePage   = 'customers';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Customers – SHENGCHI AUTO LTD</title>
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
    var _base_url_ = <?= json_encode($workshopBase) ?>;
    function start_loader(){}
    function end_loader(){}
  </script>
  <style>
    .toast-success{background:#28a745!important;color:#fff!important;}
    .toast-error  {background:#dc3545!important;color:#fff!important;}
    .avatar-circle{width:38px;height:38px;border-radius:50%;display:grid;place-items:center;font-weight:700;font-size:14px;flex-shrink:0;}
    .lifetime-badge{font-size:.7rem;font-weight:700;color:#7c3aed;background:#f5f3ff;padding:2px 8px;border-radius:20px;}
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
        <h4 class="fw-bold mb-0">Customers</h4>
        <p class="text-muted small mb-0">All registered vehicle owners</p>
      </div>
      <ol class="breadcrumb mb-0" style="--bs-breadcrumb-divider:'›';">
        <li class="breadcrumb-item"><a href="index.php" class="text-primary">Home</a></li>
        <li class="breadcrumb-item active">Customers</li>
      </ol>
    </div>
  </div>

  <div class="app-content p-4">
    <div class="card border-0 shadow-sm" style="border-radius:16px;">
      <div class="card-header bg-white py-3" style="border-radius:16px 16px 0 0;">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="fw-bold mb-0"><i class="bi bi-people me-2 text-primary"></i>Customer Register</h5>
          <button class="btn btn-primary btn-sm" id="btnAddCustomer">
            <i class="fa fa-plus me-1"></i> Add Customer
          </button>
        </div>
      </div>

      <div class="card-body p-4">
        <!-- toolbar -->
        <div class="mb-3 d-flex flex-wrap gap-2 align-items-center justify-content-between">
          <div class="d-flex flex-wrap gap-2">
            <div class="d-flex gap-2 border-end pe-3 me-1">
              <button class="btn btn-outline-success btn-sm btnEdit"><i class="fa fa-edit me-1"></i>Edit</button>
              <button class="btn btn-outline-danger  btn-sm btnDelete"><i class="fa fa-trash me-1"></i>Delete</button>
            </div>
            <a class="btn btn-outline-info btn-sm" id="btnViewVehicles" href="#"><i class="bi bi-car-front me-1"></i>Vehicles</a>
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
          <table id="customersTable" class="table align-middle w-100">
            <thead>
              <tr>
                <th style="width:30px;"></th>
                <th>Customer</th>
                <th>Contact</th>
                <th>Address</th>
                <th class="text-center">Vehicles</th>
                <th class="text-center">Jobs</th>
                <th>Lifetime Value</th>
                <th>Joined</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</main>

<!-- Add / Edit Customer Modal -->
<div class="modal fade" id="customerModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-3">
      <div class="modal-header bg-primary text-white">
        <div>
          <h5 class="modal-title fw-bold" id="customerModalTitle">Add Customer</h5>
          <small class="opacity-75">Customer details</small>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="customerForm">
        <input type="hidden" name="customer_id" id="customerId" value="0">
        <div class="modal-body p-4">
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label small fw-semibold">Full name <span class="text-danger">*</span></label>
              <input class="form-control" name="fullname" id="inputFullname" placeholder="e.g. John Kamau" required>
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold">Phone / contact <span class="text-danger">*</span></label>
              <input class="form-control" name="contact" id="inputContact" placeholder="e.g. 0712 345 678" required>
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold">Address</label>
              <input class="form-control" name="address" id="inputAddress" placeholder="e.g. Westlands, Nairobi">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="btnSaveCustomer">
            <i class="bi bi-save me-1"></i>Save Customer
          </button>
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
const API = '../classes/Customers.php';
const colors = ['#4f46e5','#16a34a','#d97706','#dc2626','#0284c7','#7c3aed','#db2777','#0891b2'];
const initials = name => name.split(' ').slice(0,2).map(w=>w[0]?.toUpperCase()||'').join('');
const colorFor  = name => colors[name.charCodeAt(0) % colors.length];

$(document).ready(function () {
  table = $('#customersTable').DataTable({
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
      dataSrc: function(json){ return Array.isArray(json) ? json : []; },
      error: () => toastr.error('Could not load customers.')
    },
    columns: [
      { data:'customer_id', orderable:false,
        render: d => `<div class="form-check"><input class="form-check-input row-check" type="checkbox" value="${d}"></div>` },
      { data:'fullname',
        render: (d,t,r) => {
          const bg = colorFor(d);
          return `<div class="d-flex align-items-center gap-2">
            <div class="avatar-circle" style="background:${bg}20;color:${bg}">${initials(d)}</div>
            <div><span class="fw-semibold">${d}</span></div>
          </div>`;
        }},
      { data:'contact',  render: d => `<a href="tel:${d}" class="text-decoration-none">${d}</a>` },
      { data:'address' },
      { data:'total_vehicles', className:'text-center',
        render: d => `<span class="badge bg-light text-dark border fw-bold">${d}</span>` },
      { data:'total_jobs', className:'text-center',
        render: d => `<span class="badge bg-light text-dark border fw-bold">${d}</span>` },
      { data:'lifetime_value',
        render: d => `<span class="lifetime-badge">KES ${Number(d).toLocaleString()}</span>` },
      { data:'joined', className:'text-muted small' }
    ]
  });

  // row selection
  $('#customersTable tbody').on('click','tr', function(){
    $(this).toggleClass('selected').siblings().removeClass('selected');
    const sel = $(this).hasClass('selected');
    table.$('.row-check').prop('checked',false);
    $(this).find('.row-check').prop('checked',sel);
    selectedId = sel ? table.row(this).data().customer_id : null;
  });
});

// ── Add ───────────────────────────────────────────────────────────────────────
$('#btnAddCustomer').click(function(){
  $('#customerModalTitle').text('Add Customer');
  $('#customerForm')[0].reset();
  $('#customerId').val(0);
  new bootstrap.Modal('#customerModal').show();
});

// ── Edit ──────────────────────────────────────────────────────────────────────
$('.btnEdit').click(function(){
  if (!selectedId) return toastr.error('Select a customer first.');
  $.getJSON(API + '?f=get&id=' + selectedId, function(r){
    $('#customerModalTitle').text('Edit Customer');
    $('#customerId').val(r.customer_id);
    $('#inputFullname').val(r.fullname);
    $('#inputContact').val(r.contact);
    $('#inputAddress').val(r.address);
    new bootstrap.Modal('#customerModal').show();
  }).fail(() => toastr.error('Could not load customer details.'));
});

// ── Save form ─────────────────────────────────────────────────────────────────
$('#customerForm').on('submit', function(e){
  e.preventDefault();
  const btn = $('#btnSaveCustomer').prop('disabled',true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving…');
  $.ajax({
    url: API + '?f=save', method:'POST', dataType:'json', data: $(this).serialize(),
    success: function(r){
      if(r.status==='success'){ toastr.success(r.msg); bootstrap.Modal.getInstance('#customerModal').hide(); table.ajax.reload(null,false); }
      else toastr.error(r.msg);
    },
    error: xhr => toastr.error(xhr.responseJSON?.msg || 'Save failed.'),
    complete: () => $('#btnSaveCustomer').prop('disabled',false).html('<i class="bi bi-save me-1"></i>Save Customer')
  });
});

// ── Delete ────────────────────────────────────────────────────────────────────
$('.btnDelete').click(function(){
  if (!selectedId) return toastr.error('Select a customer first.');
  $('#confirmBody').text('Delete this customer? This cannot be undone.');
  const m = new bootstrap.Modal('#confirmModal');
  $('#btnConfirm').off('click').on('click', function(){
    $.post(API + '?f=delete', {id: selectedId}, function(r){
      if(r.status==='success'){ toastr.success(r.msg); table.ajax.reload(); selectedId=null; }
      else toastr.error(r.msg);
      bootstrap.Modal.getInstance('#confirmModal').hide();
    }, 'json').fail(() => toastr.error('Delete failed.'));
  });
  m.show();
});

// ── View vehicles ─────────────────────────────────────────────────────────────
$('#btnViewVehicles').click(function(e){
  e.preventDefault();
  if (!selectedId) return toastr.error('Select a customer first.');
  window.location.href = 'vehicles.php?customer_id=' + selectedId;
});

function exportTable(type){
  if(!table) return;
  const map={csv:'.buttons-csv',excel:'.buttons-excel',pdf:'.buttons-pdf',print:'.buttons-print'};
  if(map[type]) table.button(map[type]).trigger();
}
</script>
</body>
</html>
