<?php
require_once __DIR__ . '/../../inc/app.php';
workshop_require_login();

// Only admins and owners can access reports
workshop_require_role('admin', 'owner');

$workshopUser = $_SESSION['user'];
$workshopBase = workshop_base_path();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Reports – SHENGCHI AUTO LTD</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link rel="stylesheet" href="../layout.css.php?v=<?= time() ?>">
  
  <style>
    .reports-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 2rem 0;
      margin-bottom: 2rem;
      border-radius: 12px;
    }
    
    .period-tabs {
      background: rgba(255, 255, 255, 0.1);
      border-radius: 25px;
      padding: 5px;
      display: inline-flex;
    }
    
    .period-tab {
      background: transparent;
      border: none;
      color: rgba(255, 255, 255, 0.8);
      padding: 8px 20px;
      border-radius: 20px;
      margin: 0 2px;
      transition: all 0.3s ease;
      font-weight: 500;
    }
    
    .period-tab.active {
      background: rgba(255, 255, 255, 0.2);
      color: white;
      transform: translateY(-1px);
    }
    
    .stat-card {
      border-radius: 20px;
      border: 1px solid #eef2f7;
      background: #fff;
      padding: 1.5rem;
      height: 100%;
      box-shadow: 0 4px 20px rgba(0,0,0,0.06);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .stat-card.purple .stat-number { color: #6366f1; }
    .stat-card.green .stat-number { color: #0d9488; }
    .stat-card.orange .stat-number { color: #d97706; }
    .stat-card.red .stat-number { color: #f97316; }
    
    .stat-number {
      font-size: 2.5rem;
      font-weight: 800;
      line-height: 1;
      margin: 0.5rem 0;
    }
    
    .stat-label {
      font-size: 0.9rem;
      color: #6c757d;
      margin: 0;
    }
    
    .report-card {
      background: #fff;
      border: none;
      border-radius: 20px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    }
    
    .report-card .card-title {
      font-weight: 800;
      font-size: 1rem;
      color: #0f172a;
    }
    
    .chart-container {
      position: relative;
      height: 350px;
      width: 100%;
    }
    
    .export-actions {
      position: absolute;
      top: 1rem;
      right: 1rem;
      z-index: 10;
    }
    
    .table-container {
      background: white;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    
    .table-header {
      background: linear-gradient(135deg, #667eea, #764ba2);
      color: white;
      padding: 1rem 1.5rem;
      margin: 0;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    
    .btn-export {
      background: rgba(255, 255, 255, 0.2);
      color: white;
      border: 1px solid rgba(255, 255, 255, 0.3);
      border-radius: 20px;
      padding: 0.4rem 1rem;
      font-size: 0.85rem;
      transition: all 0.3s ease;
    }
    
    .btn-export:hover {
      background: rgba(255, 255, 255, 0.3);
      color: white;
      transform: translateY(-1px);
    }
    
    .btn-print {
      background: #dc3545;
      color: white;
      border: none;
      border-radius: 20px;
      padding: 0.4rem 1rem;
      font-size: 0.85rem;
    }
    
    .custom-table {
      margin: 0;
      border-collapse: collapse;
    }
    
    .custom-table th {
      background: #f8f9fa;
      border: none;
      padding: 1rem;
      font-weight: 600;
      color: #495057;
      font-size: 0.85rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .custom-table td {
      padding: 1rem;
      border-bottom: 1px solid #e9ecef;
      vertical-align: middle;
    }
    
    .custom-table tbody tr:hover {
      background-color: rgba(102, 126, 234, 0.05);
    }
    
    .revenue-cell {
      font-weight: 600;
      color: #28a745;
    }
    
    @media print {
      .no-print { display: none !important; }
      .export-actions { display: none !important; }
      body { margin: 0; font-size: 12px; }
      .table-container { box-shadow: none; margin-top: 0; }
      .reports-header { display: none; }
      .app-wrapper { margin: 0; }
      .app-main { margin: 0 !important; }
      .custom-table { font-size: 11px; }
      .custom-table th, .custom-table td { padding: 0.5rem; }
      
      /* Print header */
      .print-header {
        display: block !important;
        text-align: center;
        margin-bottom: 20px;
        border-bottom: 2px solid #333;
        padding-bottom: 10px;
      }
      
      @page {
        margin: 1cm;
        @top-center {
          content: "SHENGCHI AUTO LTD - Workshop Report";
        }
        @bottom-right {
          content: "Page " counter(page) " of " counter(pages);
        }
      }
    }
    
    .print-header {
      display: none;
    }
  </style>
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<div class="app-wrapper">

<?php include '../navbar.php'; ?>
<?php $activePage = 'reports'; include '../sidebar.php'; ?>

<main class="app-main">
  <div class="app-content p-4">
    
    <!-- Reports Header -->
    <div class="reports-header text-center">
      <div class="container">
        <h1 class="mb-3"><i class="bi bi-bar-chart-line me-3"></i><span data-i18n="reports">Reports</span></h1>
        
        <!-- Period Selection Tabs -->
        <div class="period-tabs">
          <button class="period-tab active" data-period="today" data-i18n="today">Today</button>
          <button class="period-tab" data-period="week" data-i18n="thisWeek">This Week</button>
          <button class="period-tab" data-period="month" data-i18n="thisMonth">This Month</button>
          <button class="period-tab" data-period="all" data-i18n="allTime">All Time</button>
        </div>
      </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row g-4 mb-4">
      <div class="col-md-3">
        <div class="card stat-card purple">
          <div class="text-center">
            <div class="stat-number" id="totalOrders">0</div>
            <p class="stat-label" data-i18n="totalJobs">Total Jobs</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card stat-card green">
          <div class="text-center">
            <div class="stat-number" id="totalRevenue">UGX 0</div>
            <p class="stat-label" data-i18n="totalRevenue">Total Revenue</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card stat-card blue">
          <div class="text-center">
            <div class="stat-number" id="avgOrderValue">UGX 0</div>
            <p class="stat-label" data-i18n="avgOrderValue">Avg Order Value</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card stat-card red">
          <div class="text-center">
            <div class="stat-number" id="itemsSold">0</div>
            <p class="stat-label" data-i18n="partsBought">Parts Bought</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
      <div class="col-lg-8">
        <div class="card report-card">
          <div class="card-body">
            <h6 class="card-title mb-3"><span data-i18n="repairsThisWeek">Repairs This Week</span></h6>
            <div class="chart-container">
              <canvas id="weekChart"></canvas>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="card report-card">
          <div class="card-body">
            <h6 class="card-title mb-3"><span data-i18n="salesByCategory">Sales by Category</span></h6>
            <div class="chart-container">
              <canvas id="categoryChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Vehicles Worked On Table -->
    <div class="print-header">
      <h2 data-i18n="companyName">SHENGCHI AUTO LTD</h2>
      <h3 data-i18n="workshopVehiclesReport">Workshop Vehicles Report</h3>
      <p><span data-i18n="generatedOn">Generated on:</span> <?= date('F j, Y \a\t g:i A') ?> | <span data-i18n="period">Period:</span> <span id="printPeriod" data-i18n="today">Today</span></p>
    </div>
    
    <div class="table-container">
      <div class="table-header">
        <h6 class="mb-0"><i class="bi bi-car-front me-2"></i><span data-i18n="topVehiclesWorkedOn">Top Vehicles Worked On</span></h6>
        <div class="d-flex gap-2 no-print">
          <button class="btn btn-export" onclick="exportData('vehicles')">
            <i class="bi bi-download me-1"></i><span data-i18n="exportExcel">Export Excel</span>
          </button>
          <button class="btn btn-print" onclick="printTable()">
            <i class="bi bi-printer me-1"></i><span data-i18n="print">Print</span>
          </button>
        </div>
      </div>
      
      <div class="p-3">
        <div class="d-flex justify-content-between align-items-center mb-3 no-print">
          <div>
            <label for="entriesSelect" class="form-label me-2" data-i18n="show">Show</label>
            <select id="entriesSelect" class="form-select form-select-sm d-inline-block w-auto">
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
            <span class="ms-2" data-i18n="entries">entries</span>
          </div>
          <div>
            <label for="searchInput" class="form-label me-2" data-i18n="searchColon">Search:</label>
            <input type="text" id="searchInput" class="form-control form-control-sm d-inline-block w-auto" placeholder="Search vehicles..." data-i18n="searchVehicles">
          </div>
        </div>
        
        <div class="table-responsive">
          <table class="table custom-table" id="vehiclesTable">
            <thead>
              <tr>
                <th><i class="bi bi-hash me-1"></i>#</th>
                <th data-i18n="plateNumber">Plate Number</th>
                <th data-i18n="model">Model</th>
                <th data-i18n="customer">Customer</th>
                <th data-i18n="category">Category</th>
                <th data-i18n="jobsDone">Jobs Done</th>
                <th data-i18n="revenueUGX">Revenue (UGX)</th>
              </tr>
            </thead>
            <tbody id="vehiclesTableBody">
              <tr>
                <td colspan="7" class="text-center py-4">
                  <div class="spinner-border spinner-border-sm me-2"></div><span data-i18n="loadingVehiclesData">Loading vehicles data...</span>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
</main>

</div>

<script>
const API_BASE = '../../classes/Reports.php';
let currentPeriod = 'today';
let currentData = [];
let revenueChart = null;
let categoryChart = null;

// Period tab switching
$('.period-tab').click(function() {
  $('.period-tab').removeClass('active');
  $(this).addClass('active');
  currentPeriod = $(this).data('period');
  loadAllData();
});

// Load summary statistics
function loadSummaryStats() {
  $.getJSON(`${API_BASE}?f=summary_stats&period=${currentPeriod}`)
    .done(function(response) {
      if (response.status === 'success') {
        const stats = response.stats;
        $('#totalOrders').text(stats.total_orders.toLocaleString());
        $('#totalRevenue').text('UGX ' + Math.round(stats.total_revenue).toLocaleString());
        $('#avgOrderValue').text('UGX ' + Math.round(stats.avg_order_value).toLocaleString());
        $('#itemsSold').text(stats.items_sold.toLocaleString());
      }
    })
    .fail(function() {
      toastr.error('Failed to load summary statistics');
    });
}

// Load weekly jobs chart (current week, dynamic)
function loadWeekChart() {
  $.getJSON(`${API_BASE}?f=jobs_by_weekday`)
    .done(function(response) {
      if (response.status === 'success') {
        renderWeekChart(response.data);
      }
    })
    .fail(function() {
      toastr.error('Failed to load weekly jobs chart data');
    });
}

function localDateKey(d) {
  const y = d.getFullYear();
  const m = String(d.getMonth() + 1).padStart(2, '0');
  const day = String(d.getDate()).padStart(2, '0');
  return `${y}-${m}-${day}`;
}

// Render weekly jobs chart — days of the week on the Y axis, job count on X axis
function renderWeekChart(data) {
  const ctx = document.getElementById('weekChart').getContext('2d');
  
  if (revenueChart) {
    revenueChart.destroy();
  }
  
  const today = new Date();
  const labels = [];
  const counts = [];
  
  for (let i = 6; i >= 0; i--) {
    const d = new Date(today);
    d.setDate(today.getDate() - i);
    const key = localDateKey(d);
    const row = data.find(r => String(r.date).slice(0, 10) === key);
    labels.push(d.toLocaleDateString('en-US', { weekday: 'short' }) + ' ' + d.getDate());
    counts.push(row ? parseInt(row.jobs) : 0);
  }
  
  revenueChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
        label: 'Jobs',
        data: counts,
        backgroundColor: 'rgba(99,102,241,0.15)',
        borderColor: '#6366f1',
        borderWidth: 2,
        minBarLength: 8,
        borderRadius: { topLeft: 10, topRight: 10 }
      }]
    },
    options: {
      indexAxis: 'x',
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          backgroundColor: '#0f172a',
          titleFont: { family: 'DM Sans' },
          bodyFont: { family: 'DM Sans' },
          callbacks: {
            label: function(context) {
              return context.parsed.y + ' repair job(s)';
            }
          }
        }
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: {
            color: '#94a3b8',
            font: { family: 'DM Sans' }
          }
        },
        y: {
          beginAtZero: true,
          grid: { color: '#eef2f7' },
          ticks: {
            color: '#94a3b8',
            font: { family: 'DM Sans' },
            precision: 0,
            stepSize: 1
          }
        }
      }
    }
  });
}

// Load category chart
function loadCategoryChart() {
  $.getJSON(`${API_BASE}?f=repair_categories&period=${currentPeriod}`)
    .done(function(response) {
      if (response.status === 'success') {
        renderCategoryChart(response.categories);
      }
    })
    .fail(function() {
      toastr.error('Failed to load category chart data');
    });
}

// Render category chart
function renderCategoryChart(categories) {
  const ctx = document.getElementById('categoryChart').getContext('2d');
  
  if (categoryChart) {
    categoryChart.destroy();
  }
  
  const colors = [
    '#6366f1', '#14b8a6', '#f59e0b', '#f97316',
    '#10b981', '#7c3aed', '#ff7f50', '#0ea5e9'
  ];
  
  categoryChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: categories.map(c => c.category),
      datasets: [{
        data: categories.map(c => parseFloat(c.revenue)),
        backgroundColor: colors.slice(0, categories.length),
        borderColor: '#fff',
        borderWidth: 4,
        hoverOffset: 6
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '60%',
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            padding: 20,
            usePointStyle: true,
            color: '#94a3b8',
            font: {
              family: 'DM Sans',
              size: 12
            }
          }
        },
        tooltip: {
          backgroundColor: '#0f172a',
          titleFont: { family: 'DM Sans' },
          bodyFont: { family: 'DM Sans' }
        }
      }
    }
  });
}

// Load vehicles table
function loadVehiclesTable() {
  const limit = $('#entriesSelect').val() || 10;
  
  $.getJSON(`${API_BASE}?f=vehicles_worked_on&period=${currentPeriod}&limit=${limit}`)
    .done(function(response) {
      if (response.status === 'success') {
        currentData = response.vehicles;
        renderVehiclesTable(currentData);
      }
    })
    .fail(function() {
      toastr.error('Failed to load vehicles data');
    });
}

// Render vehicles table
function renderVehiclesTable(vehicles) {
  let html = '';
  
  if (vehicles.length === 0) {
    html = '<tr><td colspan="7" class="text-center py-4 text-muted">No vehicles found for this period</td></tr>';
  } else {
    vehicles.forEach((vehicle, index) => {
      html += `
        <tr>
          <td>${index + 1}</td>
          <td><strong>${vehicle.plate_number}</strong></td>
          <td>${vehicle.model}</td>
          <td>
            <div>${vehicle.customer_name}</div>
            <small class="text-muted">${vehicle.customer_contact}</small>
          </td>
          <td><span class="badge bg-primary">${vehicle.category}</span></td>
          <td>${vehicle.jobs_completed}</td>
          <td class="revenue-cell">UGX ${Math.round(parseFloat(vehicle.total_revenue)).toLocaleString()}</td>
        </tr>
      `;
    });
  }
  
  $('#vehiclesTableBody').html(html);
}

// Search functionality
$('#searchInput').on('input', function() {
  const searchTerm = $(this).val().toLowerCase();
  const filtered = currentData.filter(vehicle => 
    vehicle.plate_number.toLowerCase().includes(searchTerm) ||
    vehicle.model.toLowerCase().includes(searchTerm) ||
    vehicle.customer_name.toLowerCase().includes(searchTerm) ||
    vehicle.category.toLowerCase().includes(searchTerm)
  );
  renderVehiclesTable(filtered);
});

// Entries per page change
$('#entriesSelect').change(function() {
  loadVehiclesTable();
});

// Export functionality
function exportData(type) {
  const url = `${API_BASE}?f=export_data&type=${type}&period=${currentPeriod}`;
  const link = document.createElement('a');
  link.href = url;
  link.download = `workshop_${type}_report_${currentPeriod}_${new Date().toISOString().split('T')[0]}.csv`;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  toastr.success('Export started. Check your downloads folder.');
}

// Print functionality
function printTable() {
  window.print();
}

// Load all data
function loadAllData() {
  loadSummaryStats();
  loadWeekChart();
  loadCategoryChart();
  loadVehiclesTable();
}

// Initialize
$(document).ready(function() {
  loadAllData();
  
  // Auto-refresh every 5 minutes
  setInterval(loadAllData, 300000);
});
</script>

</body>
</html>