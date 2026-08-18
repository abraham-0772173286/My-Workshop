<?php
require_once __DIR__ . '/../../inc/app.php';
workshop_require_login();
workshop_require_permission('view_payments');

$workshopUser = $_SESSION['user'];
$workshopBase = workshop_base_path();
$activePage   = 'payments';
?>
<!doctype html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Payments – SHENGCHI AUTO LTD</title>
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

    /* ---------- Payments table ---------- */
    #paymentsTable { font-size: .85rem; border-collapse: separate; border-spacing: 0; }
    #paymentsTable thead th {
      background: #f8fafc;
      color: #64748b;
      font-size: .72rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .5px;
      border-bottom: 2px solid #eef2f7;
      border-top: 0;
      white-space: nowrap;
      padding: .8rem .9rem;
    }
    #paymentsTable tbody td {
      padding: .7rem .9rem;
      vertical-align: middle;
      border-bottom: 1px solid #f8fafc;
      white-space: nowrap;
    }
    #paymentsTable tbody tr:hover td { background: #f8fafc; }
    #paymentsTable .dataTables_empty { text-align: center; padding: 2.5rem !important; color: #94a3b8; font-size: .9rem; }
    #paymentsTable_wrapper .dataTables_scrollBody::-webkit-scrollbar { height: 8px; }
    #paymentsTable_wrapper .dataTables_scrollBody::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    #paymentsTable_wrapper .dataTables_length,
    #paymentsTable_wrapper .dataTables_filter { margin-bottom: .9rem; }
    #paymentsTable_wrapper .dataTables_length select,
    #paymentsTable_wrapper .dataTables_filter input {
      border: 1px solid #e2e8f0; border-radius: 8px; padding: .35rem .6rem; font-size: .85rem; outline: none;
    }
    #paymentsTable_wrapper .dataTables_filter input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.12); }
    #paymentsTable_wrapper .dataTables_info { color: #94a3b8; font-size: .8rem; padding-top: 1rem; }
    #paymentsTable_wrapper .dataTables_paginate { padding-top: .9rem; }
    #paymentsTable_wrapper .dataTables_paginate .paginate_button {
      border: 1px solid #e2e8f0 !important; border-radius: 8px !important; background: #fff !important;
      color: #64748b !important; font-size: .8rem; margin: 0 2px; padding: .3rem .75rem !important;
    }
    #paymentsTable_wrapper .dataTables_paginate .paginate_button:hover {
      background: #f8fafc !important; color: #6366f1 !important; border-color: #c7d2fe !important;
    }
    #paymentsTable_wrapper .dataTables_paginate .paginate_button.current,
    #paymentsTable_wrapper .dataTables_paginate .paginate_button.current:hover {
      background: #6366f1 !important; border-color: #6366f1 !important; color: #fff !important;
    }
    /* method badge + receipt pill */
    .method-badge {
      font-size: .72rem; font-weight: 700; color: #0f766e; background: #f0fdfa;
      padding: .25rem .6rem; border-radius: 20px; letter-spacing: .4px;
    }
    .receipt-pill {
      font-size: .78rem; font-weight: 700; color: #4338ca; background: #eef2ff;
      padding: .2rem .6rem; border-radius: 6px; letter-spacing: .3px;
    }
    .not-issued { font-size: .72rem; color: #94a3b8; font-style: italic; }
    #paymentsTable td code { font-size: .8rem; background: #eef2ff; padding: .2rem .5rem; border-radius: 6px; }
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
          <h4 class="fw-bold mb-0" data-i18n="payments">Payments</h4>
          <p class="text-muted small mb-0">Record and manage payments for repair jobs</p>
        </div>
        <ol class="breadcrumb mb-0" style="--bs-breadcrumb-divider:'›';">
          <li class="breadcrumb-item"><a href="../index.php" class="text-primary">Home</a></li>
          <li class="breadcrumb-item active" data-i18n="payments">Payments</li>
        </ol>
      </div>
    </div>

    <div class="app-content p-4">
      <div class="card border-0 shadow-sm" style="border-radius:16px;">
        <div class="card-header bg-white py-3" style="border-radius:16px 16px 0 0;">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="bi bi-cash-stack me-2 text-primary"></i>Payments Register</h5>
            <div class="d-flex gap-2">
              <button class="btn btn-primary btn-sm" id="btnAddPayment">
                <i class="fa fa-plus me-1"></i>Record Payment
              </button>
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
        </div>

        <div class="card-body p-4">
          <div class="table-responsive">
            <table id="paymentsTable" class="table align-middle w-100">
              <thead>
                <tr>
                  <th style="width:30px;"></th>
                  <th>Job No</th>
                  <th>Customer</th>
                  <th>Vehicle</th>
                  <th>Repair Type</th>
                  <th>Total Cost</th>
                  <th>Amount Paid</th>
                  <th>Method</th>
                  <th>Reference</th>
                  <th>Paid On</th>
                  <th>Receipt</th>
                  <th class="text-center">Action</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Add / Edit Payment Modal -->
  <div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content rounded-3">
        <div class="modal-header bg-primary text-white">
          <div>
            <h5 class="modal-title fw-bold" id="paymentModalTitle">Record Payment</h5>
            <small class="opacity-75">Select a repair job and enter payment details</small>
          </div>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="paymentForm">
          <input type="hidden" name="payment_id" id="paymentId" value="0">
          <div class="modal-body p-4">
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label small fw-semibold">Repair job <span class="text-danger">*</span></label>
                <select class="form-select" name="repair_job_id" id="selectJob" required>
                  <option value="">— Select a completed repair job —</option>
                </select>
              </div>
              <div class="col-12" id="jobInfoBox" style="display:none;">
                <div class="alert alert-light border mb-0 py-2 px-3">
                  <div class="d-flex justify-content-between flex-wrap gap-2">
                    <div><span class="text-muted small">Customer:</span> <strong id="infoCustomer"></strong></div>
                    <div><span class="text-muted small">Vehicle:</span> <strong id="infoVehicle"></strong></div>
                    <div><span class="text-muted small">Total Cost:</span> <strong id="infoTotal" class="text-primary"></strong></div>
                    <div><span class="text-muted small">Already Paid:</span> <strong id="infoPaid" class="text-success"></strong></div>
                    <div><span class="text-muted small">Balance:</span> <strong id="infoBalance" class="text-danger"></strong></div>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-semibold">Amount paid (UGX) <span class="text-danger">*</span></label>
                <input class="form-control" type="number" name="amount_paid" id="inputAmount" min="0" step="0.01" placeholder="0" required>
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-semibold">Payment method <span class="text-danger">*</span></label>
                <select class="form-select" name="payment_method" required>
                  <option value="CASH">CASH</option>
                  <option value="MPESA">M-PESA</option>
                  <option value="BANK TRANSFER">BANK TRANSFER</option>
                  <option value="OTHER">OTHER</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label small fw-semibold">Reference</label>
                <input class="form-control" name="reference" id="inputReference" placeholder="e.g. M-Pesa code, cheque no.">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary" id="btnSavePayment"><i class="bi bi-save me-1"></i>Save Payment</button>
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

</div><!-- /.app-wrapper -->

<script>
const API = '../classes/Payments.php';
const RCP_API = '../classes/Receipts.php';
let table = null;
let selectedId = null;
let jobsData = [];

const fmt = n => 'UGX ' + Number(n).toLocaleString('en-UG');

$(document).ready(function () {
  table = $('#paymentsTable').DataTable({
    scrollX: true,
    scrollCollapse: true,
    processing: true,
    pageLength: 50,
    dom: 'lfrtip',
    buttons: [
      { extend:'copy',  exportOptions:{ columns:[1,2,3,4,5,6,7,8,9,10] } },
      { extend:'csv',   exportOptions:{ columns:[1,2,3,4,5,6,7,8,9,10] } },
      { extend:'excel', exportOptions:{ columns:[1,2,3,4,5,6,7,8,9,10] } },
      { extend:'pdf',   exportOptions:{ columns:[1,2,3,4,5,6,7,8,9,10] } },
      { extend:'print', exportOptions:{ columns:[1,2,3,4,5,6,7,8,9,10] } }
    ],
    ajax: {
      url: API + '?f=viewall',
      dataSrc: function (json) {
        if (!Array.isArray(json)) { toastr.error('Unexpected response from server.'); return []; }
        return json;
      },
      error: function (xhr, error, thrown) {
        toastr.error('Could not load payments: ' + (thrown || error));
      }
    },
    columns: [
      { data:'payment_id', orderable:false, className:'text-center',
        render: d => `<div class="form-check"><input class="form-check-input row-check" type="checkbox" value="${d}"></div>` },
      { data:'job_no', render: d => `<code class="text-primary fw-bold">${d}</code>` },
      { data:'customer', render: d => `<span class="fw-bold text-dark">${d}</span>` },
      { data:'vehicle', render: d => `<span class="small"><i class="bi bi-car-front me-1 text-muted"></i>${d}</span>` },
      { data:'repair_type' },
      { data:'total_cost', render: d => `<span class="fw-semibold">${fmt(d)}</span>` },
      { data:'amount_paid', render: d => `<span class="fw-semibold text-success">${fmt(d)}</span>` },
      { data:'payment_method', render: d => `<span class="method-badge">${d}</span>` },
      { data:'reference', render: d => d || '<span class="text-muted">—</span>' },
      { data:'paid_on', className:'text-muted small' },
      { data:'receipt_no', render: d => d ? `<span class="receipt-pill">${d}</span>` : `<span class="not-issued">Not issued</span>` },
      { data:'payment_id', orderable:false, className:'text-center',
        render: function(d, t, r) {
          let btns = '';
          if (r.receipt_no) {
            btns += `<button class="btn btn-sm btn-outline-success btn-print-receipt me-1" data-payment="${d}" title="Print receipt"><i class="bi bi-printer"></i></button>`;
          }
          btns += `<button class="btn btn-sm btn-outline-primary btn-edit-payment me-1" data-id="${d}" title="Edit"><i class="fa fa-edit"></i></button>`;
          btns += `<button class="btn btn-sm btn-outline-danger btn-delete-payment" data-id="${d}" title="Delete"><i class="fa fa-trash"></i></button>`;
          return btns;
        }
    ]
  });

  // Row selection
  $('#paymentsTable tbody').on('click', 'tr', function () {
    $(this).toggleClass('selected').siblings().removeClass('selected');
    const sel = $(this).hasClass('selected');
    table.$('.row-check').prop('checked', false);
    $(this).find('.row-check').prop('checked', sel);
    selectedId = sel ? table.row(this).data().payment_id : null;
  });

  // Recalculate column widths when the layout changes
  $(window).on('resize', function () {
    if (table) { table.columns.adjust(); }
  });

  // Load repair jobs for the dropdown
  loadJobs();
});

function loadJobs() {
  $.getJSON(API + '?f=jobs', function(rows) {
    jobsData = rows;
    const sel = $('#selectJob');
    sel.empty().append('<option value="">— Select a completed repair job —</option>');
    rows.forEach(function(r) {
      const label = r.job_no + ' — ' + r.customer + ' (' + r.plate + ') | Balance: ' + fmt(r.balance);
      sel.append(`<option value="${r.repair_job_id}">${label}</option>`);
    });
  });
}

// Show job info when a job is selected
$('#selectJob').on('change', function() {
  const id = parseInt($(this).val());
  const job = jobsData.find(j => j.repair_job_id === id);
  if (job) {
    $('#jobInfoBox').show();
    $('#infoCustomer').text(job.customer);
    $('#infoVehicle').text(job.plate);
    $('#infoTotal').text(fmt(job.total_cost));
    $('#infoPaid').text(fmt(job.total_paid));
    $('#infoBalance').text(fmt(job.balance));
    $('#inputAmount').val(job.balance > 0 ? job.balance : '').attr('max', job.balance);
  } else {
    $('#jobInfoBox').hide();
  }
});

// ── Add ────────────────────────────────────────────────────────────────────
$('#btnAddPayment').click(function() {
  $('#paymentModalTitle').text('Record Payment');
  $('#paymentForm')[0].reset();
  $('#paymentId').val(0);
  $('#jobInfoBox').hide();
  loadJobs();
  new bootstrap.Modal('#paymentModal').show();
});

// ── Edit (via row button) ──────────────────────────────────────────────────
$(document).on('click', '.btn-edit-payment', function(e) {
  e.stopPropagation();
  const id = $(this).data('id');
  $.getJSON(API + '?f=get&id=' + id, function(r) {
    $('#paymentModalTitle').text('Edit Payment');
    $('#paymentId').val(r.payment_id);
    // Reload jobs then set value
    loadJobs();
    setTimeout(function() {
      $('#selectJob').val(r.repair_job_id).trigger('change');
    }, 300);
    $('#inputAmount').val(r.amount_paid);
    $('select[name="payment_method"]').val(r.payment_method);
    $('#inputReference').val(r.reference);
    new bootstrap.Modal('#paymentModal').show();
  }).fail(() => toastr.error('Could not load payment details.'));
});

// ── Save form ──────────────────────────────────────────────────────────────
$('#paymentForm').on('submit', function(e) {
  e.preventDefault();
  const btn = $('#btnSavePayment').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Saving...');
  $.ajax({
    url: API + '?f=save', method: 'POST', dataType: 'json', data: $(this).serialize(),
    success: function(r) {
      if (r.status === 'success') {
        toastr.success(r.msg);
        bootstrap.Modal.getInstance('#paymentModal').hide();
        table.ajax.reload(null, false);
      } else {
        toastr.error(r.msg);
      }
    },
    error: xhr => toastr.error(xhr.responseJSON?.msg || 'Could not save the payment.'),
    complete: () => $('#btnSavePayment').prop('disabled', false).html('<i class="bi bi-save me-1"></i>Save Payment')
  });
});

// ── Delete (via row button) ────────────────────────────────────────────────
$(document).on('click', '.btn-delete-payment', function(e) {
  e.stopPropagation();
  selectedId = $(this).data('id');
  $('#confirmBody').text('Delete this payment? This cannot be undone.');
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

// ── Print receipt (delegates to Receipts API) ──────────────────────────────
$(document).on('click', '.btn-print-receipt', function(e) {
  e.stopPropagation();
  const paymentId = $(this).data('payment');
  $.ajax({
    url: RCP_API + '?f=issue',
    method: 'POST',
    dataType: 'json',
    data: { payment_id: paymentId },
    success: function(resp) {
      if (resp.status !== 'success') return toastr.error(resp.msg || 'Could not issue receipt.');
      openReceiptWindow(paymentId);
    },
    error: xhr => toastr.error(xhr.responseJSON?.msg || 'Could not issue receipt.')
  });
});

function openReceiptWindow(paymentId) {
  $.getJSON(RCP_API + '?f=get&payment_id=' + paymentId, function(r) {
    if (r.status !== 'success') return toastr.error(r.msg);
    const d = r.data;
    const win = window.open('', '_blank', 'width=820,height=900');
    if (!win) return toastr.warning('Please allow pop-ups for this site.');
    win.document.write(`<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Receipt ${d.receipt_no} — SHENGCHI AUTO LTD</title>
<style>
  *{box-sizing:border-box;margin:0;padding:0;}
  body{font-family:'Segoe UI',Arial,sans-serif;color:#0f172a;background:#f1f5f9;padding:30px;}
  .receipt{max-width:720px;margin:0 auto;background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 8px 30px rgba(2,9,16,.12);}
  .r-head{background:#0e3158;color:#fff;padding:22px 30px;display:flex;justify-content:space-between;align-items:center;}
  .r-head .brand{font-size:20px;font-weight:800;letter-spacing:.5px;}
  .r-head .brand small{display:block;font-size:10px;letter-spacing:2px;color:#f5a623;font-weight:600;margin-top:3px;}
  .r-head .rc-no{text-align:right;}
  .r-head .rc-no b{font-size:18px;color:#f5a623;letter-spacing:1px;}
  .r-head .rc-no span{display:block;font-size:11px;color:#c3d4e6;margin-top:3px;}
  .r-title{text-align:center;padding:18px 0 6px;font-size:14px;letter-spacing:4px;color:#94a3b8;font-weight:700;text-transform:uppercase;}
  .r-body{padding:10px 30px 26px;}
  .cols{display:flex;justify-content:space-between;gap:20px;margin-bottom:18px;}
  .cols .label{font-size:10px;text-transform:uppercase;letter-spacing:1px;color:#94a3b8;margin-bottom:4px;font-weight:700;}
  .cols .val{font-size:14px;font-weight:600;}
  .divider{border:0;border-top:2px dashed #e2e8f0;margin:14px 0;}
  table{width:100%;border-collapse:collapse;font-size:13px;}
  table th{background:#f8fafc;color:#64748b;font-size:10px;text-transform:uppercase;letter-spacing:1px;padding:8px 10px;text-align:left;}
  table td{padding:10px;border-bottom:1px solid #f1f5f9;}
  table td.num,table th.num{text-align:right;}
  .totals{margin-top:14px;}
  .totals .row{display:flex;justify-content:space-between;padding:5px 0;font-size:13px;}
  .totals .row.total{border-top:2px solid #0e3158;margin-top:6px;padding-top:10px;font-size:16px;font-weight:800;}
  .totals .row.paid{color:#0f766e;font-weight:700;}
  .r-foot{text-align:center;padding:18px 30px;background:#f8fafc;font-size:12px;color:#64748b;}
  .r-foot .thanks{color:#0e3158;font-weight:700;letter-spacing:1px;}
  @media print{body{background:#fff;padding:0;}.receipt{box-shadow:none;border-radius:0;}}
</style>
</head>
<body>
  <div class="receipt">
    <div class="r-head">
      <div class="brand">SHENGCHI AUTO LTD<small>金龙汽车维修 · WORKSHOP OPERATIONS</small></div>
      <div class="rc-no"><b>${d.receipt_no}</b><span>Issued ${d.issued_on}</span></div>
    </div>
    <div class="r-title">Official Receipt</div>
    <div class="r-body">
      <div class="cols">
        <div>
          <div class="label">Billed To</div>
          <div class="val">${d.customer}</div>
          <div style="font-size:12px;color:#64748b">${d.contact}<br>${d.address}</div>
        </div>
        <div style="text-align:right">
          <div class="label">Job / Vehicle</div>
          <div class="val">${d.job_no} · ${d.plate}</div>
          <div style="font-size:12px;color:#64748b">${d.model}</div>
        </div>
      </div>
      <table>
        <thead><tr><th>Description</th><th class="num">Amount (UGX)</th></tr></thead>
        <tbody>
          <tr><td>Repair service — ${d.repair_type}</td><td class="num">${fmt(d.total_cost)}</td></tr>
          <tr><td style="color:#94a3b8">of which: parts</td><td class="num" style="color:#64748b">${fmt(d.parts_cost)}</td></tr>
          <tr><td style="color:#94a3b8">of which: labour</td><td class="num" style="color:#64748b">${fmt(d.labour_cost)}</td></tr>
        </tbody>
      </table>
      <div class="totals">
        <div class="row"><span>Job Total</span><span>${fmt(d.total_cost)}</span></div>
        <div class="row paid"><span>Amount Paid</span><span>${fmt(d.amount_paid)}</span></div>
        <div class="row"><span>Payment Method</span><span>${d.payment_method} ${d.reference !== '—' ? '· ' + d.reference : ''}</span></div>
        <div class="row"><span>Paid On</span><span>${d.paid_on}</span></div>
        <div class="row total"><span>Receipt Total</span><span>${fmt(d.amount_paid)}</span></div>
      </div>
    </div>
    <div class="r-foot">
      <div class="thanks">Thank you for your business!</div>
      <div>SHENGCHI AUTO LTD · P.O. Box 123, Kampala, Uganda · +256 763 808854 · support@pearl-host.com</div>
    </div>
  </div>
  <script>window.onload=function(){window.focus();window.print();};</scr` + `ipt>
</body>
</html>`);
    win.document.close();
    win.focus();
  }).fail(() => toastr.error('Could not load receipt details.'));
}

function exportTable(type) {
  if (!table) return;
  const map = { csv: '.buttons-csv', excel: '.buttons-excel', pdf: '.buttons-pdf', print: '.buttons-print' };
  if (map[type]) table.button(map[type]).trigger();
}
</script>
</body>
</html>
