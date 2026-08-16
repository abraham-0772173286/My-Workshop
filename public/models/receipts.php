<?php
require_once __DIR__ . '/../../inc/app.php';
workshop_require_login();
workshop_require_permission('view_receipts');

$workshopUser = $_SESSION['user'];
$workshopBase = workshop_base_path();
$activePage   = 'receipts';
?>
<!doctype html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Receipts – SHENGCHI AUTO LTD</title>
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

    /* ---------- Receipts table (same clean style as Repair Jobs) ---------- */
    #receiptsTable { font-size: .85rem; border-collapse: separate; border-spacing: 0; }
    #receiptsTable thead th {
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
    #receiptsTable tbody td {
      padding: .7rem .9rem;
      vertical-align: middle;
      border-bottom: 1px solid #f8fafc;
      white-space: nowrap;
    }
    #receiptsTable tbody tr:hover td { background: #f8fafc; }
    #receiptsTable .dataTables_empty { text-align: center; padding: 2.5rem !important; color: #94a3b8; font-size: .9rem; }
    #receiptsTable_wrapper .dataTables_scrollBody::-webkit-scrollbar { height: 8px; }
    #receiptsTable_wrapper .dataTables_scrollBody::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    #receiptsTable_wrapper .dataTables_length,
    #receiptsTable_wrapper .dataTables_filter { margin-bottom: .9rem; }
    #receiptsTable_wrapper .dataTables_length select,
    #receiptsTable_wrapper .dataTables_filter input {
      border: 1px solid #e2e8f0; border-radius: 8px; padding: .35rem .6rem; font-size: .85rem; outline: none;
    }
    #receiptsTable_wrapper .dataTables_filter input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.12); }
    #receiptsTable_wrapper .dataTables_info { color: #94a3b8; font-size: .8rem; padding-top: 1rem; }
    #receiptsTable_wrapper .dataTables_paginate { padding-top: .9rem; }
    #receiptsTable_wrapper .dataTables_paginate .paginate_button {
      border: 1px solid #e2e8f0 !important; border-radius: 8px !important; background: #fff !important;
      color: #64748b !important; font-size: .8rem; margin: 0 2px; padding: .3rem .75rem !important;
    }
    #receiptsTable_wrapper .dataTables_paginate .paginate_button:hover {
      background: #f8fafc !important; color: #6366f1 !important; border-color: #c7d2fe !important;
    }
    #receiptsTable_wrapper .dataTables_paginate .paginate_button.current,
    #receiptsTable_wrapper .dataTables_paginate .paginate_button.current:hover {
      background: #6366f1 !important; border-color: #6366f1 !important; color: #fff !important;
    }
    /* receipt number pill + method badge */
    .receipt-pill {
      font-size: .78rem; font-weight: 700; color: #4338ca; background: #eef2ff;
      padding: .2rem .6rem; border-radius: 6px; letter-spacing: .3px;
    }
    .method-badge {
      font-size: .72rem; font-weight: 700; color: #0f766e; background: #f0fdfa;
      padding: .25rem .6rem; border-radius: 20px; letter-spacing: .4px;
    }
    .not-issued { font-size: .72rem; color: #94a3b8; font-style: italic; }
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
          <h4 class="fw-bold mb-0" data-i18n="receipts">Receipts</h4>
          <p class="text-muted small mb-0">Payment records with printable receipts</p>
        </div>
        <ol class="breadcrumb mb-0" style="--bs-breadcrumb-divider:'›';">
          <li class="breadcrumb-item"><a href="../index.php" class="text-primary">Home</a></li>
          <li class="breadcrumb-item active" data-i18n="receipts">Receipts</li>
        </ol>
      </div>
    </div>

    <div class="app-content p-4">
      <div class="card border-0 shadow-sm" style="border-radius:16px;">
        <div class="card-header bg-white py-3" style="border-radius:16px 16px 0 0;">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="bi bi-receipt me-2 text-primary"></i>Receipts Register</h5>
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
        </div>

        <div class="card-body p-4">
          <!-- Every payment appears here. Payments without a receipt yet show
               "Not issued" — clicking Print issues the receipt first (RCP-xxxxx),
               then opens the printable receipt. -->
          <div class="table-responsive">
            <table id="receiptsTable" class="table align-middle w-100">
              <thead>
                <tr>
                  <th>Receipt No</th>
                  <th>Job No</th>
                  <th>Customer</th>
                  <th>Vehicle</th>
                  <th>Repair Type</th>
                  <th>Amount Paid</th>
                  <th>Method</th>
                  <th>Reference</th>
                  <th>Issued On</th>
                  <th class="text-center">Action</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
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
const API = '../classes/Receipts.php';
let table = null;

$(document).ready(function () {
  table = $('#receiptsTable').DataTable({
    scrollX: true,
    scrollCollapse: true,
    processing: true,
    pageLength: 50,
    dom: 'lfrtip',
    buttons: [
      { extend:'copy',  exportOptions:{ columns:[0,1,2,3,4,5,6,7,8] } },
      { extend:'csv',   exportOptions:{ columns:[0,1,2,3,4,5,6,7,8] } },
      { extend:'excel', exportOptions:{ columns:[0,1,2,3,4,5,6,7,8] } },
      { extend:'pdf',   exportOptions:{ columns:[0,1,2,3,4,5,6,7,8] } },
      { extend:'print', exportOptions:{ columns:[0,1,2,3,4,5,6,7,8] } }
    ],
    ajax: {
      url: API + '?f=viewall',
      dataSrc: function (json) {
        if (!Array.isArray(json)) { toastr.error('Unexpected response from server.'); return []; }
        return json;
      },
      error: function (xhr, error, thrown) {
        toastr.error('Could not load receipts: ' + (thrown || error));
      }
    },
    columns: [
      { data:'receipt_no',
        render: d => d ? `<span class="receipt-pill">${d}</span>` : `<span class="not-issued">Not issued</span>` },
      { data:'job_no', render: d => `<code class="text-primary fw-bold" style="font-size:.8rem">${d}</code>` },
      { data:'customer', render: d => `<span class="fw-bold text-dark">${d}</span>` },
      { data:'vehicle', render: d => `<span class="small"><i class="bi bi-car-front me-1 text-muted"></i>${d}</span>` },
      { data:'repair_type' },
      { data:'amount_paid', render: d => `<span class="fw-semibold">UGX ${Number(d).toLocaleString()}</span>` },
      { data:'payment_method',
        render: d => `<span class="method-badge">${d}</span>` },
      { data:'reference' },
      { data:'issued_on', className:'text-muted small' },
      { data:'payment_id', orderable:false, className:'text-center',
        render: d => `<button class="btn btn-sm btn-outline-primary btn-print-receipt" data-payment="${d}" title="Print receipt"><i class="bi bi-printer me-1"></i>Print</button>` }
    ]
  });

  // Recalculate column widths when the layout changes (e.g. sidebar collapse)
  $(window).on('resize', function () {
    if (table) { table.columns.adjust(); }
  });
});

// Print handler — delegate so it works for rows loaded via AJAX
$(document).on('click', '.btn-print-receipt', function () {
  printReceipt($(this).data('payment'));
});

function exportTable(type) {
  if (!table) return;
  const map = { csv: '.buttons-csv', excel: '.buttons-excel', pdf: '.buttons-pdf', print: '.buttons-print' };
  if (map[type]) table.button(map[type]).trigger();
}

// ---------------------------------------------------------------
// printReceipt(paymentId)
//   1. Asks the API to make sure a receipt exists (issues RCP-xxxxx
//      on first print for that payment — safe to call repeatedly).
//   2. Loads the receipt details and opens them in a new window
//      styled as a real paper receipt, then triggers that window's
//      native print dialog.
// ---------------------------------------------------------------
function printReceipt(paymentId) {
  $.ajax({
    url: API + '?f=issue',
    method: 'POST',
    dataType: 'json',
    data: { payment_id: paymentId },
    success: function (resp) {
      if (resp.status !== 'success') return toastr.error(resp.msg || 'Could not issue receipt.');
      openReceiptWindow(paymentId);
    },
    error: xhr => toastr.error(xhr.responseJSON?.msg || 'Could not issue receipt.')
  });
}

function openReceiptWindow(paymentId) {
  $.getJSON(API + '?f=get&payment_id=' + paymentId, function (r) {
    if (r.status !== 'success') return toastr.error(r.msg);
    const d = r.data;
    const fmt = n => 'UGX ' + Number(n).toLocaleString('en-UG');
    const win = window.open('', '_blank', 'width=820,height=900');
    if (!win) return toastr.warning('Please allow pop-ups for this site.');

    win.document.write(`<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Receipt ${d.receipt_no} — SHENGCHI AUTO LTD</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Segoe UI', Arial, sans-serif; color: #0f172a; background: #f1f5f9; padding: 30px; }
  .receipt { max-width: 720px; margin: 0 auto; background: #fff; border-radius: 14px; overflow: hidden; box-shadow: 0 8px 30px rgba(2,9,16,.12); }
  .r-head { background: #0e3158; color: #fff; padding: 22px 30px; display: flex; justify-content: space-between; align-items: center; }
  .r-head .brand { font-size: 20px; font-weight: 800; letter-spacing: .5px; }
  .r-head .brand small { display: block; font-size: 10px; letter-spacing: 2px; color: #f5a623; font-weight: 600; margin-top: 3px; }
  .r-head .rc-no { text-align: right; }
  .r-head .rc-no b { font-size: 18px; color: #f5a623; letter-spacing: 1px; }
  .r-head .rc-no span { display: block; font-size: 11px; color: #c3d4e6; margin-top: 3px; }
  .r-title { text-align: center; padding: 18px 0 6px; font-size: 14px; letter-spacing: 4px; color: #94a3b8; font-weight: 700; text-transform: uppercase; }
  .r-body { padding: 10px 30px 26px; }
  .cols { display: flex; justify-content: space-between; gap: 20px; margin-bottom: 18px; }
  .cols .label { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 4px; font-weight: 700; }
  .cols .val { font-size: 14px; font-weight: 600; }
  .divider { border: 0; border-top: 2px dashed #e2e8f0; margin: 14px 0; }
  table { width: 100%; border-collapse: collapse; font-size: 13px; }
  table th { background: #f8fafc; color: #64748b; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; padding: 8px 10px; text-align: left; }
  table td { padding: 10px; border-bottom: 1px solid #f1f5f9; }
  table td.num, table th.num { text-align: right; }
  .totals { margin-top: 14px; }
  .totals .row { display: flex; justify-content: space-between; padding: 5px 0; font-size: 13px; }
  .totals .row.total { border-top: 2px solid #0e3158; margin-top: 6px; padding-top: 10px; font-size: 16px; font-weight: 800; }
  .totals .row.paid { color: #0f766e; font-weight: 700; }
  .r-foot { text-align: center; padding: 18px 30px; background: #f8fafc; font-size: 12px; color: #64748b; }
  .r-foot .thanks { color: #0e3158; font-weight: 700; letter-spacing: 1px; }
  @media print { body { background: #fff; padding: 0; } .receipt { box-shadow: none; border-radius: 0; } }
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
  <script>window.onload = function(){ window.focus(); window.print(); };</scr` + `ipt>
</body>
</html>`);

    win.document.close();
    win.focus();
  }).fail(() => toastr.error('Could not load receipt details.'));
}
</script>
</body>
</html>
