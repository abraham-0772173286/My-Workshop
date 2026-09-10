<?php
require_once __DIR__ . '/../../inc/app.php';
workshop_require_login();

$workshopUser = $_SESSION['user'];
$workshopBase = workshop_base_path();
$activePage   = 'drivers_performance';
?>
<!doctype html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>Driver Performance – SHENGCHI AUTO LTD</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <link rel="stylesheet" href="../layout.css.php?v=<?= time() ?>">

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script>
    var _base_url_ = <?= json_encode($workshopBase) ?>;
    function start_loader() {}
    function end_loader() {}
  </script>

  <style>
    .toast-success { background-color: #28a745 !important; color: white !important; }
    .toast-error   { background-color: #dc3545 !important; color: white !important; }

    .stat-card {
      border-radius: 20px; border: 1px solid #eef2f7; background: #fff;
      padding: 1.5rem; height: 100%; box-shadow: 0 4px 20px rgba(0,0,0,0.06);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
    .stat-card.purple .stat-number { color: #6366f1; }
    .stat-card.green .stat-number  { color: #0d9488; }
    .stat-card.orange .stat-number { color: #d97706; }
    .stat-card.blue .stat-number   { color: #3b82f6; }

    .stat-number { font-size: 2.2rem; font-weight: 800; line-height: 1; margin: 0.5rem 0; }
    .stat-label  { font-size: 0.9rem; color: #6c757d; margin: 0; }

    .report-card { background: #fff; border: none; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); }
    .report-card .card-title { font-weight: 800; font-size: 1rem; color: #0f172a; }
    .chart-container { position: relative; height: 350px; width: 100%; }

    .perf-table { border-collapse: collapse; width: 100%; }
    .perf-table th {
      background: #f8fafc; color: #64748b; font-size: .72rem; font-weight: 700;
      text-transform: uppercase; letter-spacing: .5px; border-bottom: 2px solid #eef2f7;
      padding: .8rem .9rem; white-space: nowrap;
    }
    .perf-table td { padding: .7rem .9rem; vertical-align: middle; border-bottom: 1px solid #f8fafc; }
    .perf-table tbody tr:hover td { background: #f8fafc; }
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
          <h4 class="fw-bold mb-0" data-i18n="driversPerformance">Driver Performance</h4>
          <p class="text-muted small mb-0" data-i18n="performanceSubtitle">Analyze driver metrics and performance</p>
        </div>
        <ol class="breadcrumb mb-0" style="--bs-breadcrumb-divider:'›';">
          <li class="breadcrumb-item"><a href="../index.php" class="text-primary" data-i18n="home">Home</a></li>
          <li class="breadcrumb-item active" data-i18n="driversPerformance">Driver Performance</li>
        </ol>
      </div>
    </div>

    <div class="app-content p-4">

      <!-- Driver Selector -->
      <div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
        <div class="card-body d-flex flex-wrap align-items-center gap-3 py-3">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-person-vcard text-primary fs-5"></i>
            <span class="fw-semibold text-dark" data-i18n="selectDriverForPerformance">Driver Performance</span>
          </div>
          <div class="flex-grow-1" style="max-width:380px;">
            <select class="form-select form-select-sm" id="selectDriver">
              <option value="0" data-i18n="allDrivers">All Drivers</option>
            </select>
          </div>
          <button class="btn btn-sm btn-light border" id="btnRefresh"><i class="fa fa-sync"></i></button>
        </div>
      </div>

      <!-- Summary Statistics -->
      <div class="row g-4 mb-4">
        <div class="col-md-3">
          <div class="card stat-card purple">
            <div class="text-center">
              <div class="stat-number" id="totalTrips">0</div>
              <p class="stat-label" data-i18n="timesSentToTravel">Times Sent to Travel</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card stat-card green">
            <div class="text-center">
              <div class="stat-number" id="totalTripsCount">0</div>
              <p class="stat-label" data-i18n="totalTrips">Total Trips</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card stat-card blue">
            <div class="text-center">
              <div class="stat-number" id="totalDistance">0 km</div>
              <p class="stat-label" data-i18n="totalDistance">Total Distance</p>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card stat-card orange">
            <div class="text-center">
              <div class="stat-number" id="totalFuelCost">UGX 0</div>
              <p class="stat-label" data-i18n="totalFuelCost">Total Fuel Cost</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts Row -->
      <div class="row g-4 mb-4">
        <div class="col-lg-12">
          <div class="card report-card">
            <div class="card-body">
              <h6 class="card-title mb-3"><i class="bi bi-bar-chart me-2 text-primary"></i><span id="chartTitle">Trips Per Driver</span></h6>
              <div class="chart-container">
                <canvas id="tripsChart"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Performance Table -->
      <div class="card border-0 shadow-sm" style="border-radius:16px;">
        <div class="card-header bg-white py-3" style="border-radius:16px 16px 0 0;">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="bi bi-graph-up me-2 text-primary"></i><span data-i18n="performanceDetails">Driver Performance Details</span></h5>
            <span class="badge bg-primary-subtle text-primary" id="perfTitle">All Drivers</span>
          </div>
        </div>
        <div class="card-body p-4">
          <div class="table-responsive">
            <table class="table perf-table align-middle w-100" id="perfTable">
              <thead>
                <tr>
                  <th data-i18n="number">#</th>
                  <th data-i18n="driver">Driver</th>
                  <th data-i18n="timesSentToTravel">Times Sent to Travel</th>
                  <th data-i18n="totalTrips">Total Trips</th>
                  <th data-i18n="totalDistanceKm">Total Distance (km)</th>
                  <th data-i18n="totalFuelCostUGX">Total Fuel Cost (UGX)</th>
                  <th data-i18n="avgFuelTrip">Avg Fuel/Trip</th>
                  <th data-i18n="totalFareEarnedUGX">Total Fare Earned (UGX)</th>
                </tr>
              </thead>
              <tbody id="perfTableBody">
                <tr>
                  <td colspan="8" class="text-center py-4">
                    <div class="spinner-border spinner-border-sm me-2"></div><span data-i18n="loadingPerformanceData">Loading performance data...</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Selected Driver Trips Table -->
      <div class="card border-0 shadow-sm mt-4" id="tripsCard" style="border-radius:16px;display:none;">
        <div class="card-header bg-white py-3" style="border-radius:16px 16px 0 0;">
          <h5 class="fw-bold mb-0"><i class="bi bi-map me-2 text-primary"></i><span data-i18n="driverTripLog">Trip Log</span></h5>
        </div>
        <div class="card-body p-4">
          <div class="table-responsive">
            <table class="table perf-table align-middle w-100" id="tripsTable">
              <thead>
                <tr>
                  <th data-i18n="number">#</th>
                  <th data-i18n="vehicle">Vehicle</th>
                  <th data-i18n="tripDate">Trip Date</th>
                  <th data-i18n="origin">Origin</th>
                  <th data-i18n="destination">Destination</th>
                  <th data-i18n="distanceKm">Distance (km)</th>
                  <th data-i18n="fareEarned">Fare (UGX)</th>
                </tr>
              </thead>
              <tbody id="tripsTableBody"></tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </main>

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
const fmt = n => 'UGX ' + Number(n).toLocaleString('en-UG');
let tripsChart = null;
let selectedDriver = 0;

function loadDriverDropdown() {
  $.getJSON(API + '?f=drivers', function(rows) {
    const sel = $('#selectDriver');
    const keep = sel.val();
    sel.empty().append('<option value="0">All Drivers</option>');
    if (Array.isArray(rows)) {
      rows.forEach(function(r) {
        sel.append(`<option value="${r.id}">${r.driver_name}</option>`);
      });
    }
    sel.val(keep !== null ? keep : 0);
  });
}

function loadPerformance(driverId) {
  const url = driverId > 0 ? API + '?f=performance&driver_id=' + driverId : API + '?f=performance';
  $.getJSON(url, function(resp) {
    if (resp.status !== 'success' || !resp.data) {
      toastr.error('Failed to load performance data.');
      return;
    }

    const data = resp.data;
    const drivers = data.drivers || [];
    const summary = data.summary || {};
    const trips = data.trips || [];

    if (driverId > 0) {
      $('#totalTrips').text(summary.total_assignments || 0);
      $('#totalTripsCount').text(summary.total_trips || 0);
      $('#perfTitle').text(drivers[0] ? drivers[0].driver_name : 'Driver');
      $('#chartTitle').text('Trips Per Driver - ' + (drivers[0] ? drivers[0].driver_name : ''));
      $('#tripsCard').show();
      renderChartMonthly(drivers[0] ? drivers[0].monthly_trips : []);
      renderTripsTable(trips);
    } else {
      $('#totalTrips').text(summary.total_assignments || 0);
      $('#totalTripsCount').text(summary.total_trips || 0);
      $('#perfTitle').text('All Drivers');
      $('#chartTitle').text('Trips Per Driver');
      $('#tripsCard').hide();
      renderChart(drivers);
    }

    $('#totalDistance').text(Number(summary.total_distance || 0).toLocaleString('en-UG') + ' km');
    $('#totalFuelCost').text(fmt(summary.total_fuel_cost || 0));

    renderTable(drivers);
  }).fail(function() {
    toastr.error('Could not load performance data.');
  });
}

function renderChart(drivers) {
  const ctx = document.getElementById('tripsChart').getContext('2d');
  if (tripsChart) tripsChart.destroy();

  const labels = drivers.map(d => d.driver_name || 'Unknown');
  const trips = drivers.map(d => parseInt(d.total_trips) || 0);

  tripsChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
        label: 'Trips',
        data: trips,
        backgroundColor: 'rgba(99,102,241,0.15)',
        borderColor: '#6366f1',
        borderWidth: 2,
        minBarLength: 8,
        borderRadius: { topLeft: 10, topRight: 10 }
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#0f172a',
          titleFont: { family: 'DM Sans' },
          bodyFont: { family: 'DM Sans' },
          callbacks: {
            label: function(ctx) { return ctx.parsed.y + ' trip(s)'; }
          }
        }
      },
      scales: {
        x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { family: 'DM Sans' } } },
        y: {
          beginAtZero: true, grid: { color: '#eef2f7' },
          ticks: { color: '#94a3b8', font: { family: 'DM Sans' }, precision: 0, stepSize: 1 }
        }
      }
    }
  });
}

function renderChartMonthly(monthlyTrips) {
  const ctx = document.getElementById('tripsChart').getContext('2d');
  if (tripsChart) tripsChart.destroy();

  const reversed = (monthlyTrips || []).slice().reverse();
  const labels = reversed.map(m => m.month || 'Unknown');
  const trips = reversed.map(m => parseInt(m.trip_count) || 0);

  tripsChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
        label: 'Trips',
        data: trips,
        backgroundColor: 'rgba(13,148,136,0.15)',
        borderColor: '#0d9488',
        borderWidth: 2,
        minBarLength: 8,
        borderRadius: { topLeft: 10, topRight: 10 }
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#0f172a',
          titleFont: { family: 'DM Sans' },
          bodyFont: { family: 'DM Sans' },
          callbacks: {
            label: function(ctx) { return ctx.parsed.y + ' trip(s)'; }
          }
        }
      },
      scales: {
        x: { grid: { display: false }, ticks: { color: '#94a3b8', font: { family: 'DM Sans' } } },
        y: {
          beginAtZero: true, grid: { color: '#eef2f7' },
          ticks: { color: '#94a3b8', font: { family: 'DM Sans' }, precision: 0, stepSize: 1 }
        }
      }
    }
  });
}

function renderTable(drivers) {
  let html = '';
  if (drivers.length === 0) {
    html = '<tr><td colspan="8" class="text-center py-4 text-muted">No performance data available</td></tr>';
  } else {
    drivers.forEach(function(d, i) {
      const avgFuel = d.total_trips > 0 ? Math.round((parseFloat(d.total_fuel_cost) || 0) / parseInt(d.total_trips)) : 0;
      html += `
        <tr>
          <td>${i + 1}</td>
          <td><span class="fw-bold text-dark">${d.driver_name || '—'}</span></td>
          <td class="text-center"><span class="badge bg-primary-subtle text-primary">${d.total_assignments || 0}</span></td>
          <td class="text-center">${d.total_trips || 0}</td>
          <td>${Number(d.total_distance || 0).toFixed(1)} km</td>
          <td><span class="fw-semibold">${fmt(d.total_fuel_cost || 0)}</span></td>
          <td>${fmt(avgFuel)}</td>
          <td><span class="fw-semibold text-success">${fmt(d.total_fare || 0)}</span></td>
        </tr>
      `;
    });
  }
  $('#perfTableBody').html(html);
}

function renderTripsTable(trips) {
  let html = '';
  if (!trips || trips.length === 0) {
    html = '<tr><td colspan="7" class="text-center py-4 text-muted">No trips recorded for this driver</td></tr>';
  } else {
    trips.forEach(function(t, i) {
      html += `
        <tr>
          <td>${i + 1}</td>
          <td><span class="fw-bold text-dark">${t.plate_number || '—'}</span></td>
          <td class="text-muted small">${t.trip_date || '—'}</td>
          <td>${t.origin || '—'}</td>
          <td>${t.destination || '—'}</td>
          <td>${Number(t.distance_km || 0).toFixed(1)}</td>
          <td><span class="fw-semibold text-success">${fmt(t.fare || 0)}</span></td>
        </tr>
      `;
    });
  }
  $('#tripsTableBody').html(html);
}

$(document).ready(function() {
  loadDriverDropdown();
  loadPerformance(0);
  $('#btnRefresh').click(function() { loadPerformance(selectedDriver); toastr.info('Data refreshed.'); });
  $('#selectDriver').change(function() {
    selectedDriver = parseInt($(this).val()) || 0;
    loadPerformance(selectedDriver);
  });
  $(window).on('resize', function() { if (tripsChart) tripsChart.resize(); });
});
</script>
</body>
</html>
