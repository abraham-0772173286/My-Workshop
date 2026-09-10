<?php
require_once __DIR__ . '/../inc/app.php';
workshop_require_login();
$workshopUser = $_SESSION['user'];
$workshopBase = workshop_base_path();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Dashboard – SHENGCHI AUTO LTD</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
  <script>
    var _base_url_ = <?= json_encode($workshopBase) ?>;
    function start_loader(){}
    function end_loader(){}
  </script>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300..800;1,9..40,300..800&display=swap');
    *{font-family:'DM Sans',sans-serif;}

    /* ── sidebar ── */
    .garage-sidebar{
      position:fixed;z-index:1045;top:0;left:0;
      width:260px;height:100vh;overflow:hidden;
      display:flex;flex-direction:column;
      background:linear-gradient(180deg,#4015bf 0%,#29106f 100%);
      transition:transform .28s ease;
      box-shadow:5px 0 24px rgba(21,5,64,.16);
    }
    .sidebar-backdrop{
      position:fixed;inset:0;z-index:1040;
      background:rgba(15,10,40,.45);
      opacity:0;visibility:hidden;
      transition:opacity .28s ease,visibility .28s ease;
    }
    .garage-sidebar.is-collapsed{transform:translateX(-100%);}
    .app-header{margin-left:260px;transition:margin-left .28s ease;}
    .app-main  {margin-left:260px!important;transition:margin-left .28s ease;}
    body.garage-sidebar-collapsed .app-header,
    body.garage-sidebar-collapsed .app-main{margin-left:0!important;}

    .garage-sidebar .sidebar-brand{display:flex;align-items:center;gap:10px;padding:19px 18px;color:#fff;text-decoration:none;background:rgba(0,0,0,.15);border-bottom:1px solid rgba(255,255,255,.1);flex-shrink:0;}
    .garage-sidebar .brand-mark{width:39px;height:39px;border-radius:10px;display:grid;place-items:center;background:#f5ad2b;color:#301078;font-size:20px;flex-shrink:0;}
    .garage-sidebar .brand-name{font-size:15px;line-height:1.1;font-weight:800;}
    .garage-sidebar .brand-name small{display:block;margin-top:4px;color:#cfbefd;font-size:9px;letter-spacing:1px;}
    .garage-sidebar .nav-sidebar{padding:14px 7px;overflow-y:auto;flex:1;scrollbar-width:none;}
    .garage-sidebar .nav-sidebar::-webkit-scrollbar{display:none;}
    .garage-sidebar .nav-item{margin:2px 0;}
    .garage-sidebar .nav-link{display:flex;align-items:center;min-height:46px;gap:12px;padding:10px 18px;color:rgba(255,255,255,.88)!important;font-size:14.5px;transition:background .18s;}
    .garage-sidebar .nav-link:hover,.garage-sidebar .nav-link.active{background:rgba(154,109,255,.35);color:#fff!important;}
    .garage-sidebar .nav-link i:first-child{width:26px;font-size:20px;text-align:center;flex-shrink:0;}
    .garage-sidebar .nav-link .nav-arrow{margin-left:auto;font-size:13px;opacity:.6;}
    .garage-sidebar .nav-header{font-size:.65rem!important;text-transform:uppercase;letter-spacing:1.8px;font-weight:700;color:rgba(189,170,255,.7)!important;padding:18px 18px 5px!important;}
    .garage-sidebar .sidebar-footer{padding:13px 18px;border-top:1px solid rgba(255,255,255,.12);color:#d2c7f1;font-size:11px;flex-shrink:0;}
    .garage-sidebar .sidebar-footer i{color:#f5ad2b;}

    @media(max-width:991px){
      .app-header,.app-main{margin-left:0!important;}
      .garage-sidebar{transform:translateX(-100%);}
      .garage-sidebar.is-open{transform:translateX(0);}
      .sidebar-backdrop.show{opacity:1;visibility:visible;}
    }

    /* ── KPI cards ── */
    .kpi-card{
      border:none;border-radius:20px;background:#fff;padding:22px 24px;
      display:flex;align-items:center;gap:18px;
      box-shadow:0 4px 20px rgba(0,0,0,.06);
      transition:transform .2s,box-shadow .2s;
    }
    .kpi-card:hover{transform:translateY(-3px);box-shadow:0 10px 28px rgba(0,0,0,.1);}
    .kpi-icon{width:56px;height:56px;border-radius:14px;display:grid;place-items:center;font-size:24px;flex-shrink:0;}
    .kpi-val{font-size:1.9rem;font-weight:800;line-height:1;color:#0f172a;}
    .kpi-label{font-size:.78rem;color:#6c757d;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-top:4px;}
    .kpi-sub{font-size:.75rem;margin-top:6px;color:#94a3b8;}

    /* card colours */
    .kpi-blue  {background:#fff;} .kpi-blue  .kpi-icon{background:#4f46e5;color:#fff;}
    .kpi-green {background:#fff;} .kpi-green .kpi-icon{background:#0d9488;color:#fff;}
    .kpi-amber {background:#fff;} .kpi-amber .kpi-icon{background:#d97706;color:#fff;}
    .kpi-red   {background:#fff;} .kpi-red   .kpi-icon{background:#f97316;color:#fff;}
    .kpi-sky   {background:#fff;} .kpi-sky   .kpi-icon{background:#0284c7;color:#fff;}
    .kpi-violet{background:#fff;} .kpi-violet .kpi-icon{background:#7c3aed;color:#fff;}

    /* ── section cards ── */
    .dash-card{border:none;border-radius:20px;box-shadow:0 4px 20px rgba(0,0,0,.06);}
    .dash-card .card-header{background:#fff;border-bottom:1px solid #f1f5f9;border-radius:20px 20px 0 0!important;padding:16px 20px;}
    .dash-card .card-header h6{font-weight:800;font-size:.9rem;margin:0;color:#0f172a;}

    /* ── status pills ── */
    .pill{padding:.28em .75em;font-size:.68rem;font-weight:700;text-transform:uppercase;border-radius:50px;}
    .pill-done   {background:#dcfce7;color:#166534;border:1px solid #bbf7d0;}
    .pill-pending{background:#fff4d8;color:#9a5a00;border:1px solid #ffe3a2;}

    /* ── table tweaks ── */
    .dash-table{font-size:.83rem;}
    .dash-table thead th{font-size:.7rem;text-transform:uppercase;letter-spacing:.5px;color:#94a3b8;font-weight:700;border-bottom:2px solid #f1f5f9;padding:10px 12px;}
    .dash-table tbody td{padding:10px 12px;vertical-align:middle;border-bottom:1px solid #f8fafc;}
    .dash-table tbody tr:last-child td{border-bottom:none;}
    .dash-table tbody tr:hover td{background:#f8fafc;}

    /* misc */
    .text-primary{color:#dc3545!important;}
    .bg-primary{background-color:#dc3545!important;color:#fff!important;}
    .app-footer{background:#fff;border-top:1px solid #e9ecef;padding:18px 0;}
    .footer-content{display:flex;flex-direction:column;align-items:center;gap:8px;}
    @media(min-width:768px){.footer-content{flex-direction:row;justify-content:space-between;padding:0 40px;}}
    .footer-contacts a{font-size:14px;color:#6c757d;text-decoration:none;margin-left:12px;}
    .footer-contacts a:hover{color:#25d366;}
    .skeleton{background:linear-gradient(90deg,#f1f5f9 25%,#e2e8f0 50%,#f1f5f9 75%);background-size:200% 100%;animation:shimmer 1.4s infinite;border-radius:8px;height:32px;}
    @keyframes shimmer{0%{background-position:200% 0}100%{background-position:-200% 0}}
  </style>
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<div class="app-wrapper">

<?php include 'navbar.php'; ?>

<?php $activePage = 'dashboard'; include 'sidebar.php'; ?>

<main class="app-main">
    <div class="app-content-header px-4 pt-3 pb-0">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h4 class="fw-bold mb-0" data-i18n="dashboard">Dashboard</h4>
        <p class="text-muted small mb-0"><span data-i18n="welcomeBack">Welcome back</span>, <?= htmlspecialchars($workshopUser['name'], ENT_QUOTES) ?> — <span data-i18n="dashboardWelcome">here's what's happening today.</span></p>
      </div>
      <ol class="breadcrumb mb-0" style="--bs-breadcrumb-divider:'›';">
        <li class="breadcrumb-item active" data-i18n="home">Home</li>
      </ol>
    </div>
  </div>

  <div class="app-content p-4">

    <!-- ── KPI CARDS ────────────────────────────────────────── -->
    <div class="row g-3 mb-4" id="kpiRow">
      <!-- skeletons while loading -->
      <?php for($i=0;$i<6;$i++): ?>
      <div class="col-12 col-sm-6 col-xl-4"><div class="skeleton" style="height:100px;"></div></div>
      <?php endfor; ?>
    </div>

    <!-- ── CHARTS ROW ────────────────────────────────────────── -->
    <div class="row g-3 mb-4">
      <div class="col-12 col-lg-8">
        <div class="card dash-card h-100">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h6><i class="bi bi-bar-chart-line me-2 text-primary"></i><span data-i18n="revenueLast6Months">Revenue — Last 6 Months</span></h6>
            <span class="badge bg-light text-muted small fw-normal" id="chartSubtitle">UGX</span>
          </div>
          <div class="card-body p-3"><canvas id="revenueChart" height="110"></canvas></div>
        </div>
      </div>
      <div class="col-12 col-lg-4">
        <div class="card dash-card h-100">
          <div class="card-header">
            <h6><i class="bi bi-pie-chart me-2 text-primary"></i><span data-i18n="repairTypesChart">Repair Types</span></h6>
          </div>
          <div class="card-body p-3 d-flex align-items-center justify-content-center">
            <canvas id="typesChart" height="190"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- ── TABLES ROW ────────────────────────────────────────── -->
    <div class="row g-3">
      <!-- recent jobs -->
      <div class="col-12 col-xl-7">
        <div class="card dash-card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h6><i class="bi bi-clock-history me-2 text-primary"></i><span data-i18n="recentRepairJobs">Recent Repair Jobs</span></h6>
            <a href="repair_jobs.php" class="btn btn-sm btn-outline-secondary" style="font-size:.75rem;" data-i18n="viewAll">View all</a>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table dash-table mb-0" id="recentTable">
                <thead><tr>
                  <th data-i18n="jobNo">Job No.</th><th data-i18n="customer">Customer</th><th data-i18n="vehicle">Vehicle</th>
                  <th data-i18n="totalUGX">Total (UGX)</th><th data-i18n="status">Status</th><th data-i18n="date">Date</th>
                </tr></thead>
                <tbody id="recentBody"><tr><td colspan="6" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2"></div><span data-i18n="loading">Loading…</span></td></tr></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- pending jobs -->
      <div class="col-12 col-xl-5">
        <div class="card dash-card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h6><i class="bi bi-exclamation-circle me-2 text-warning"></i><span data-i18n="pendingJobs">Pending Jobs</span></h6>
            <span class="badge bg-warning text-dark" id="pendingBadge">—</span>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive" style="max-height:340px;overflow-y:auto;">
              <table class="table dash-table mb-0">
                <thead><tr><th data-i18n="jobNo">Job No.</th><th data-i18n="customer">Customer</th><th data-i18n="plate">Plate</th><th data-i18n="amount">Amount</th></tr></thead>
                <tbody id="pendingBody"><tr><td colspan="4" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2"></div><span data-i18n="loading">Loading…</span></td></tr></tbody>
              </table>
            </div>
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
const API = '../classes/Dashboard.php';
const fmt = n => 'UGX ' + Number(n).toLocaleString('en-UG', {minimumFractionDigits:0, maximumFractionDigits:0});

function t(key, fallback) {
  if (typeof window._getTranslation === 'function') return window._getTranslation(key) || fallback || key;
  if (typeof _translations !== 'undefined' && _translations[key]) return _translations[key];
  return fallback || key;
}

// ── KPI cards ────────────────────────────────────────────────────────────────
function loadStats() {
  $.getJSON(API + '?f=stats', function(s) {
    const cards = [
      { labelKey:'totalJobs',     fallback:'Total Jobs',       val: s.total_jobs,        subKey:'thisMonth',          subFallback:'this month',          icon:'bi-clipboard2-check', cls:'kpi-blue'  },
      { labelKey:'revenueEarned', fallback:'Revenue Earned',   val: fmt(s.total_revenue), subVal: fmt(s.revenue_this_month), subKey:'thisMonth', subFallback:'this month', icon:'bi-cash-coin', cls:'kpi-green' },
      { labelKey:'pendingRepairs',fallback:'Pending Repairs',  val: s.pending_jobs,      subKey:'awaitingCompletion', subFallback:'awaiting completion',  icon:'bi-hourglass-split',  cls:'kpi-amber' },
      { labelKey:'completedJobs', fallback:'Completed Jobs',   val: s.done_jobs,         subKey:'repairsDone',        subFallback:'repairs done',         icon:'bi-check-circle',     cls:'kpi-sky'   },
      { labelKey:'customers',     fallback:'Customers',        val: s.total_customers,   subKey:'registeredOwners',   subFallback:'registered owners',    icon:'bi-people',           cls:'kpi-violet'},
      { labelKey:'vehicles',      fallback:'Vehicles',         val: s.total_vehicles,    subKey:'onRecord',           subFallback:'on record',            icon:'bi-car-front',        cls:'kpi-red'   },
    ];
    let html = '';
    cards.forEach(c => {
      var label = t(c.labelKey, c.fallback);
      var sub = c.subVal ? c.subVal + ' ' + t(c.subKey, c.subFallback) : t(c.subKey, c.subFallback);
      html += '<div class="col-12 col-sm-6 col-xl-4">' +
        '<div class="kpi-card ' + c.cls + '">' +
          '<div class="kpi-icon"><i class="bi ' + c.icon + '"></i></div>' +
          '<div>' +
            '<div class="kpi-val">' + c.val + '</div>' +
            '<div class="kpi-label">' + label + '</div>' +
            '<div class="kpi-sub text-muted">' + sub + '</div>' +
          '</div>' +
        '</div>' +
      '</div>';
    });
    $('#kpiRow').html(html);
  }).fail(() => toastr.error(t('couldNotLoadStats', 'Could not load stats.')));
}

// ── Revenue bar chart ─────────────────────────────────────────────────────────
let revenueChart = null;
function loadRevenueChart() {
  $.getJSON(API + '?f=monthly_revenue', function(rows) {
    const labels  = rows.map(r => r.month);
    const revenue = rows.map(r => parseFloat(r.revenue));
    const jobs    = rows.map(r => parseInt(r.jobs));

    if (revenueChart) revenueChart.destroy();
    revenueChart = new Chart(document.getElementById('revenueChart'), {
      type: 'bar',
      data: {
        labels,
        datasets: [
          {
            label: t('revenueUGX', 'Revenue (UGX)'),
            data: revenue,
            backgroundColor: 'rgba(99,102,241,.15)',
            borderColor: '#6366f1',
            borderWidth: 2,
            borderRadius: { topLeft: 10, topRight: 10 },
            yAxisID: 'y',
          },
          {
            label: t('jobs', 'Jobs'),
            data: jobs,
            type: 'line',
            borderColor: '#14b8a6',
            backgroundColor: 'rgba(20,184,166,.12)',
            pointBackgroundColor: '#14b8a6',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            tension: .4,
            yAxisID: 'y1',
          }
        ]
      },
      options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: {
          legend: { labels: { font: { family: 'DM Sans', size: 12 }, color: '#94a3b8', usePointStyle: true } },
          tooltip: {
            backgroundColor: '#0f172a',
            titleFont: { family: 'DM Sans' },
            bodyFont: { family: 'DM Sans' }
          }
        },
        scales: {
          x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { family: 'DM Sans' } } },
          y:  { position: 'left',  grid: { color: '#eef2f7' }, ticks: { color: '#94a3b8', font: { family: 'DM Sans' }, callback: v => 'UGX ' + v.toLocaleString() } },
          y1: { position: 'right', grid: { drawOnChartArea: false }, ticks: { stepSize: 1, color: '#94a3b8', font: { family: 'DM Sans' } } }
        }
      }
    });
    const total = revenue.reduce((a,b) => a+b, 0);
    $('#chartSubtitle').text(t('total', 'Total') + ': UGX ' + total.toLocaleString());
  }).fail(() => {
    toastr.error(t('couldNotLoadRevenueChart', 'Could not load revenue chart.'));
    $('#chartSubtitle').text(t('failedToLoad', 'Failed to load'));
  });
}

// ── Repair types doughnut ─────────────────────────────────────────────────────
let typesChart = null;
function loadTypesChart() {
  $.getJSON(API + '?f=repair_types', function(rows) {
    if (!rows.length) { $('#typesChart').closest('.card-body').html('<p class="text-muted text-center py-4 small">' + t('noDataYet', 'No data yet.') + '</p>'); return; }
    const palette = ['#6366f1','#14b8a6','#f59e0b','#f97316','#10b981','#7c3aed','#ff7f50','#0ea5e9'];
    if (typesChart) typesChart.destroy();
    typesChart = new Chart(document.getElementById('typesChart'), {
      type: 'doughnut',
      data: {
        labels: rows.map(r => r.label),
        datasets: [{ data: rows.map(r => parseInt(r.value)), backgroundColor: palette, borderColor: '#fff', borderWidth: 4, hoverOffset: 6 }]
      },
      options: {
        cutout: '60%',
        plugins: {
          legend: { position: 'bottom', labels: { font: { family: 'DM Sans', size: 11 }, boxWidth: 12, padding: 10, color: '#94a3b8', usePointStyle: true } },
          tooltip: { backgroundColor: '#0f172a', titleFont: { family: 'DM Sans' }, bodyFont: { family: 'DM Sans' } }
        }
      }
    });
  }).fail(() => {
    toastr.error(t('couldNotLoadTypesChart', 'Could not load repair types chart.'));
  });
}

// ── Recent jobs table ─────────────────────────────────────────────────────────
function loadRecentJobs() {
  $.getJSON(API + '?f=recent_jobs', function(rows) {
    if (!rows.length) { $('#recentBody').html('<tr><td colspan="6" class="text-center py-4 text-muted">' + t('noJobsYet', 'No jobs recorded yet.') + '</td></tr>'); return; }
    let html = '';
    rows.forEach(r => {
      const pill = r.status === 'REPAIR DONE'
        ? '<span class="pill pill-done">' + t('done', 'Done') + '</span>'
        : '<span class="pill pill-pending">' + t('pending', 'Pending') + '</span>';
      html += `<tr>
        <td><code class="fw-bold text-primary">${r.job_no}</code></td>
        <td class="fw-semibold">${r.customer}</td>
        <td><span class="small text-muted">${r.plate}</span><br><span class="small">${r.model}</span></td>
        <td class="fw-semibold">${fmt(r.total)}</td>
        <td>${pill}</td>
        <td class="text-muted small">${r.date}</td>
      </tr>`;
    });
    $('#recentBody').html(html);
  }).fail(() => $('#recentBody').html('<tr><td colspan="6" class="text-center text-danger py-3">' + t('failedToLoad', 'Failed to load.') + '</td></tr>'));
}

// ── Pending jobs table ────────────────────────────────────────────────────────
function loadPendingJobs() {
  $.getJSON(API + '?f=pending_jobs', function(rows) {
    $('#pendingBadge').text(rows.length);
    if (!rows.length) { $('#pendingBody').html('<tr><td colspan="4" class="text-center py-4 text-muted"><i class="bi bi-check2-circle text-success me-1"></i>' + t('allClear', 'All clear!') + '</td></tr>'); return; }
    let html = '';
    rows.forEach(r => {
      html += `<tr>
        <td><code class="fw-bold text-primary">${r.job_no}</code></td>
        <td>
          <span class="fw-semibold d-block">${r.customer}</span>
          <span class="text-muted small">${r.contact}</span>
        </td>
        <td><span class="badge bg-light text-dark border">${r.plate}</span></td>
        <td class="fw-semibold small">${fmt(r.total)}</td>
      </tr>`;
    });
    $('#pendingBody').html(html);
  }).fail(() => $('#pendingBody').html('<tr><td colspan="4" class="text-center text-danger py-3">' + t('failedToLoad', 'Failed to load.') + '</td></tr>'));
}

function renderDashboard() {
  loadStats();
  loadRevenueChart();
  loadTypesChart();
  loadRecentJobs();
  loadPendingJobs();
}

$(document).ready(function() {
  var booted = false;

  function onLanguageChanged() {
    renderDashboard();
    booted = true;
  }

  // If a non-English language is saved, wait for translations to arrive
  // before first render (avoids flashing English), with a safety timeout.
  var savedLang = localStorage.getItem('appLanguage') || 'en';
  if (savedLang !== 'en' && !window._i18nLoaded) {
    document.addEventListener('languageChanged', onLanguageChanged);
    setTimeout(function () {
      if (!booted) { renderDashboard(); booted = true; }
    }, 1500);
  } else {
    renderDashboard();
    booted = true;
    document.addEventListener('languageChanged', onLanguageChanged);
  }
});
</script>
</body>
</html>
