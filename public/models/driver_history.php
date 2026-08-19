<?php
require_once __DIR__ . '/../../inc/app.php';
workshop_require_login();

$workshopUser = $_SESSION['user'];
$workshopBase = workshop_base_path();
$activePage   = 'drivers_history';
?>
<!doctype html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Driver History – SHENGCHI AUTO LTD</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <link rel="stylesheet" href="../layout.css.php?v=<?= time() ?>">

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

  <script>
    var _base_url_ = <?= json_encode($workshopBase) ?>;
    function start_loader() {}
    function end_loader() {}
  </script>

  <style>
    .toast-success { background-color: #28a745 !important; color: white !important; }
    .toast-error   { background-color: #dc3545 !important; color: white !important; }

    .history-header {
      background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
      color: white; padding: 1.5rem 0; margin-bottom: 2rem; border-radius: 12px;
    }

    .timeline-item {
      background: #fff; border-radius: 12px; padding: 1.2rem 1.5rem;
      margin-bottom: 1rem; border-left: 4px solid #e2e8f0;
      box-shadow: 0 2px 12px rgba(0,0,0,0.04); transition: transform 0.2s ease;
    }
    .timeline-item:hover { transform: translateX(4px); }

    .timeline-item.type-assignment { border-left-color: #3b82f6; }
    .timeline-item.type-trip       { border-left-color: #22c55e; }
    .timeline-item.type-fuel       { border-left-color: #f97316; }

    .badge-assignment { background: #dbeafe; color: #1e40af; padding: .3em .7em; border-radius: 6px; font-size: .72rem; font-weight: 700; }
    .badge-trip       { background: #dcfce7; color: #166534; padding: .3em .7em; border-radius: 6px; font-size: .72rem; font-weight: 700; }
    .badge-fuel       { background: #fff7ed; color: #9a3412; padding: .3em .7em; border-radius: 6px; font-size: .72rem; font-weight: 700; }

    .timeline-date { font-size: .78rem; color: #94a3b8; font-weight: 600; }
    .timeline-detail { font-size: .88rem; color: #334155; line-height: 1.5; }
    .timeline-driver { font-weight: 700; color: #0f172a; }
    .timeline-vehicle { font-size: .82rem; color: #6366f1; font-weight: 600; }

    .filter-bar { background: #fff; border-radius: 12px; padding: 1rem 1.5rem; box-shadow: 0 2px 12px rgba(0,0,0,0.04); }

    .loading-placeholder { text-align: center; padding: 3rem; color: #94a3b8; }
    .empty-state { text-align: center; padding: 3rem; color: #94a3b8; }
    .empty-state i { font-size: 3rem; margin-bottom: 1rem; display: block; }
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
          <h4 class="fw-bold mb-0" data-i18n="driversHistory">Driver History</h4>
          <p class="text-muted small mb-0">Complete activity timeline for all drivers</p>
        </div>
        <ol class="breadcrumb mb-0" style="--bs-breadcrumb-divider:'›';">
          <li class="breadcrumb-item"><a href="../index.php" class="text-primary">Home</a></li>
          <li class="breadcrumb-item active" data-i18n="driversHistory">Driver History</li>
        </ol>
      </div>
    </div>

    <div class="app-content p-4">
      <!-- Filter Bar -->
      <div class="filter-bar mb-4">
        <div class="d-flex align-items-center gap-3 flex-wrap">
          <div class="flex-grow-1">
            <label class="form-label small fw-semibold text-muted mb-1">Select Driver</label>
            <select class="form-select" id="selectDriver">
              <option value="">All Drivers</option>
            </select>
          </div>
          <div>
            <label class="form-label small fw-semibold text-muted mb-1">&nbsp;</label>
            <button class="btn btn-primary" id="btnLoad"><i class="bi bi-arrow-clockwise me-1"></i>Load History</button>
          </div>
        </div>
      </div>

      <!-- Timeline Container -->
      <div id="timelineContainer">
        <div class="loading-placeholder">
          <div class="spinner-border text-primary mb-3"></div>
          <p>Loading driver history...</p>
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

</div>

<script>
const API = '../../classes/Drivers.php';
const fmt = n => 'UGX ' + Number(n).toLocaleString('en-UG');

function loadDrivers() {
  $.getJSON(API + '?f=drivers', function(rows) {
    const sel = $('#selectDriver');
    sel.empty().append('<option value="">All Drivers</option>');
    if (Array.isArray(rows)) {
      rows.forEach(function(r) {
        sel.append(`<option value="${r.driver_id}">${r.driver_name}</option>`);
      });
    }
  });
}

function loadHistory() {
  const driverId = $('#selectDriver').val();
  let url = API + '?f=history_all';
  if (driverId) url = API + '?f=history&driver_id=' + driverId;

  $('#timelineContainer').html('<div class="loading-placeholder"><div class="spinner-border text-primary mb-3"></div><p>Loading driver history...</p></div>');

  $.getJSON(url, function(resp) {
    let entries = [];
    if (Array.isArray(resp)) {
      entries = resp;
    } else if (resp && resp.status === 'success' && Array.isArray(resp.data)) {
      entries = resp.data;
    } else {
      $('#timelineContainer').html('<div class="empty-state"><i class="bi bi-inbox"></i><p>No history data found.</p></div>');
      return;
    }

    if (entries.length === 0) {
      $('#timelineContainer').html('<div class="empty-state"><i class="bi bi-inbox"></i><p>No history entries found.</p></div>');
      return;
    }

    let html = '';
    entries.forEach(function(e) {
      const type = (e.type || '').toLowerCase();
      let typeClass = '';
      let badgeClass = '';
      let icon = '';
      let typeName = e.type || 'Unknown';

      if (type === 'assignment') {
        typeClass = 'type-assignment';
        badgeClass = 'badge-assignment';
        icon = 'bi-link-45deg';
      } else if (type === 'trip') {
        typeClass = 'type-trip';
        badgeClass = 'badge-trip';
        icon = 'bi-map';
      } else if (type === 'fuel') {
        typeClass = 'type-fuel';
        badgeClass = 'badge-fuel';
        icon = 'bi-fuel-pump';
      }

      const driverName = e.driver_name || 'Unknown Driver';
      const vehicle = e.vehicle || e.plate_number || '';
      const description = e.description || e.details || '';
      const date = e.date || e.created_at || '';

      html += `
        <div class="timeline-item ${typeClass}">
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div class="flex-grow-1">
              <div class="d-flex align-items-center gap-2 mb-1">
                <span class="${badgeClass}"><i class="bi ${icon} me-1"></i>${typeName}</span>
                <span class="timeline-date">${date}</span>
              </div>
              <div class="timeline-detail">
                <span class="timeline-driver">${driverName}</span>
                ${vehicle ? `<span class="timeline-vehicle ms-2"><i class="bi bi-car-front me-1"></i>${vehicle}</span>` : ''}
                ${description ? `<div class="mt-1 text-muted small">${description}</div>` : ''}
              </div>
            </div>
          </div>
        </div>
      `;
    });

    $('#timelineContainer').html(html);
  }).fail(function() {
    $('#timelineContainer').html('<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><p>Failed to load history data.</p></div>');
    toastr.error('Could not load driver history.');
  });
}

$(document).ready(function() {
  loadDrivers();
  loadHistory();

  $('#btnLoad').click(function() { loadHistory(); });
  $('#selectDriver').change(function() { loadHistory(); });
});
</script>
</body>
</html>
