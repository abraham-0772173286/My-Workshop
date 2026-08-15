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
      border-radius: 16px;
      border: none;
      padding: 1.5rem;
      height: 100%;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .stat-card.purple { background: linear-gradient(135deg, #667eea, #764ba2); color: white; }
    .stat-card.green { background: linear-gradient(135deg, #4facfe, #00f2fe); color: white; }
    .stat-card.orange { background: linear-gradient(135deg, #f093fb, #f5576c); color: white; }
    .stat-card.red { background: linear-gradient(135deg, #ff9a9e, #fecfef); color: white; }
    
    .stat-number {
      font-size: 2.5rem;
      font-weight: 800;
      line-height: 1;
      margin: 0.5rem 0;
    }
    
    .stat-label {
      font-size: 0.9rem;
      opacity: 0.9;
      margin: 0;
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
        <h1 class="mb-3"><i class="bi bi-bar-chart-line me-3"></i>Reports</h1>
        
        <!-- Period Selection Tabs -->
        <div class="period-tabs">
          <button class="period-tab active" data-period="today">Today</button>
          <button class="period-tab" data-period="week">This Week</button>
          <button class="period-tab" data-period="month">This Month</button>
          <button class="period-tab" data-period="all">All Time</button>
        </div>
      </div>
    </div>

    <!-- Summary Statistics -->
    <div class="row g-4 mb-4">
      <div class="col-md-3">
        <div class="card stat-card purple">
          <div class="text-center">
            <div class="stat-number" id="totalOrders">0</div>
            <p class="stat-label">Total Orders</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card stat-card green">
          <div class="text-center">
            <div class="stat-number" id="totalRevenue">UGX 0</div>
            <p class="stat-label">Total Revenue</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card stat-card orange">
          <div class="text-center">
            <div class="stat-number" id="avgOrderValue">UGX 0</div>
            <p class="stat-label">Avg Order Value</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card stat-card red">
          <div class="text-center">
            <div class="stat-number" id="itemsSold">0</div>
            <p class="stat-label">Items Sold</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
      <div class="col-lg-8">
        <div class="card">
          <div class="card-body">
            <h6 class="card-title mb-3">Revenue by Day</h6>
            <div class="chart-container">
              <canvas id="revenueChart"></canvas>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="card">
          <div class="card-body">
            <h6 class="card-title mb-3">Sales by Category</h6>
            <div class="chart-container">
              <canvas id="categoryChart"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Vehicles Worked On Table -->
    <div class="print-header">
      <h2>SHENGCHI AUTO LTD</h2>
      <h3>Workshop Vehicles Report</h3>
      <p>Generated on: <?= date('F j, Y \a\t g:i A') ?> | Period: <span id="printPeriod">Today</span></p>
    </div>
    
    <div class="table-container">
      <div class="table-header">
        <h6 class="mb-0"><i class="bi bi-car-front me-2"></i>Top Vehicles Worked On</h6>
        <div class="d-flex gap-2 no-print">
          <button class="btn btn-export" onclick="exportData('vehicles')">
            <i class="bi bi-download me-1"></i>Export Excel
          </button>
          <button class="btn btn-print" onclick="printTable()">
            <i class="bi bi-printer me-1"></i>Print
          </button>
        </div>
      </div>
      
      <div class="p-3">
        <div class="d-flex justify-content-between align-items-center mb-3 no-print">
          <div>
            <label for="entriesSelect" class="form-label me-2">Show</label>
            <select id="entriesSelect" class="form-select form-select-sm d-inline-block w-auto">
              <option value="10">10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
            <span class="ms-2">entries</span>
          </div>
          <div>
            <label for="searchInput" class="form-label me-2">Search:</label>
            <input type="text" id="searchInput" class="form-control form-control-sm d-inline-block w-auto" placeholder="Search vehicles...">
          </div>
        </div>
        
        <div class="table-responsive">
          <table class="table custom-table" id="vehiclesTable">
            <thead>
              <tr>
                <th><i class="bi bi-hash me-1"></i>#</th>
                <th><i class="bi bi-car-front me-1"></i>Plate Number</th>
                <th><i class="bi bi-truck me-1"></i>Model</th>
                <th><i class="bi bi-person me-1"></i>Customer</th>
                <th><i class="bi bi-wrench me-1"></i>Category</th>
                <th><i class="bi bi-clipboard-check me-1"></i>Jobs Done</th>
                <th><i class="bi bi-currency-exchange me-1"></i>Revenue (UGX)</th>
              </tr>
            </thead>
            <tbody id="vehiclesTableBody">
              <tr>
                <td colspan="7" class="text-center py-4">
                  <div class="spinner-border spinner-border-sm me-2"></div>Loading vehicles data...
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

// Load revenue chart
function loadRevenueChart() {
  $.getJSON(`${API_BASE}?f=revenue_by_day&period=${currentPeriod}`)
    .done(function(response) {
      if (response.status === 'success') {
        renderRevenueChart(response.data);
      }
    })
    .fail(function() {
      toastr.error('Failed to load revenue chart data');
    });
}

// Render revenue chart
function renderRevenueChart(data) {
  const ctx = document.getElementById('revenueChart').getContext('2d');
  
  if (revenueChart) {
    revenueChart.destroy();
  }
  
  const labels = data.map(d => new Date(d.date).toLocaleDateString('en-US', {month: 'short', day: 'numeric'}));
  const revenues = data.map(d => parseFloat(d.revenue));
  const orders = data.map(d => parseInt(d.orders));
  
  revenueChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
        label: 'Revenue (UGX)',
        data: revenues,
        backgroundColor: 'rgba(102, 126, 234, 0.8)',
        borderColor: 'rgba(102, 126, 234, 1)',
        borderWidth: 1,
        borderRadius: 8
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return 'UGX ' + value.toLocaleString();
            }
          }
        }
      },
      tooltips: {
        callbacks: {
          label: function(context) {
            return 'Revenue: UGX ' + context.parsed.y.toLocaleString();
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
    '#667eea', '#764ba2', '#f093fb', '#f5576c',
    '#4facfe', '#00f2fe', '#43e97b', '#38f9d7'
  ];
  
  categoryChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: categories.map(c => c.category),
      datasets: [{
        data: categories.map(c => parseFloat(c.revenue)),
        backgroundColor: colors.slice(0, categories.length),
        borderWidth: 0
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            padding: 20,
            usePointStyle: true,
            font: {
              size: 12
            }
          }
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
  loadRevenueChart();
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