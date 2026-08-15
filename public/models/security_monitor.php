<?php
require_once __DIR__ . '/../../inc/app.php';
workshop_require_login();

// Only admins can access security monitoring
workshop_require_role('admin');

$workshopUser = $_SESSION['user'];
$workshopBase = workshop_base_path();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Security Monitor – SHENGCHI AUTO LTD</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <link rel="stylesheet" href="../layout.css.php?v=<?= time() ?>">
  
  <style>
    .security-card {
      border-left: 4px solid;
      transition: transform 0.2s ease;
    }
    .security-card:hover {
      transform: translateY(-2px);
    }
    .security-card.warning {
      border-left-color: #ffc107;
    }
    .security-card.danger {
      border-left-color: #dc3545;
    }
    .security-card.success {
      border-left-color: #198754;
    }
    .security-card.info {
      border-left-color: #0dcaf0;
    }
    .activity-item {
      border-left: 3px solid #e9ecef;
      padding-left: 1rem;
      margin-left: 0.5rem;
      position: relative;
    }
    .activity-item::before {
      content: '';
      position: absolute;
      left: -6px;
      top: 8px;
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background: #6c757d;
    }
    .activity-item.success::before { background: #198754; }
    .activity-item.warning::before { background: #ffc107; }
    .activity-item.danger::before { background: #dc3545; }
  </style>
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<div class="app-wrapper">

<?php include '../navbar.php'; ?>
<?php $activePage = 'security'; include '../sidebar.php'; ?>

<main class="app-main">
  <!-- Breadcrumb -->
  <div class="app-content-header px-4 pt-3 pb-0">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-shield-exclamation me-2 text-danger"></i>Security Monitor</h4>
        <p class="text-muted small mb-0">Monitor system security, login attempts, and user activities.</p>
      </div>
      <ol class="breadcrumb mb-0" style="--bs-breadcrumb-divider:'›';">
        <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
        <li class="breadcrumb-item active">Security Monitor</li>
      </ol>
    </div>
  </div>

  <div class="app-content p-4">
    
    <!-- Security Status Cards -->
    <div class="row g-3 mb-4">
      <div class="col-md-3">
        <div class="card security-card warning bg-warning">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <i class="bi bi-exclamation-triangle fs-2 text-dark me-3"></i>
              <div>
                <h5 class="mb-0" id="lockedUsersCount">0</h5>
                <small class="text-dark opacity-75">Locked Users</small>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card security-card danger bg-danger">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <i class="bi bi-shield-x fs-2 text-white me-3"></i>
              <div>
                <h5 class="mb-0" id="failedAttemptsCount">0</h5>
                <small class="text-white opacity-75">Failed Attempts (24h)</small>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card security-card success bg-success">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <i class="bi bi-shield-check fs-2 text-white me-3"></i>
              <div>
                <h5 class="mb-0" id="activeSessionsCount">0</h5>
                <small class="text-white opacity-75">Active Sessions</small>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card security-card info bg-info">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <i class="bi bi-activity fs-2 text-dark me-3"></i>
              <div>
                <h5 class="mb-0" id="loginAttemptsCount">0</h5>
                <small class="text-dark opacity-75">Login Attempts (24h)</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Security Activities -->
    <div class="row g-3">
      <!-- Recent Activities -->
      <div class="col-lg-8">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Security Events</h6>
            <button class="btn btn-sm btn-primary" onclick="loadSecurityEvents()">
              <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
          </div>
          <div class="card-body" style="max-height: 500px; overflow-y: auto;">
            <div id="securityEventsContainer">
              <div class="text-center py-4">
                <div class="spinner-border spinner-border-sm me-2"></div>Loading security events...
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="col-lg-4">
        <div class="card">
          <div class="card-header">
            <h6 class="mb-0"><i class="bi bi-tools me-2"></i>Quick Actions</h6>
          </div>
          <div class="card-body">
            <div class="d-grid gap-2">
              <button class="btn btn-primary" onclick="unlockAllExpiredUsers()">
                <i class="bi bi-unlock me-2"></i>Unlock Expired Users
              </button>
              <button class="btn btn-warning text-dark" onclick="resetAllFailedAttempts()">
                <i class="bi bi-arrow-clockwise me-2"></i>Reset Failed Attempts
              </button>
              <button class="btn btn-info text-dark" onclick="exportSecurityLog()">
                <i class="bi bi-download me-2"></i>Export Security Log
              </button>
              <hr>
              <button class="btn btn-success" onclick="showSystemHealth()">
                <i class="bi bi-heart-pulse me-2"></i>System Health Check
              </button>
            </div>
          </div>
        </div>

        <!-- Locked Users Quick View -->
        <div class="card mt-3">
          <div class="card-header">
            <h6 class="mb-0"><i class="bi bi-lock me-2"></i>Locked Users</h6>
          </div>
          <div class="card-body" id="lockedUsersContainer">
            <div class="text-center text-muted py-3">Loading...</div>
          </div>
        </div>
      </div>
    </div>

  </div>
</main>

</div>

<script>
// Mock security data for demonstration
const securityEvents = [
  {
    type: 'LOGIN_FAILED',
    user: 'unknown_user',
    message: 'Failed login attempt from IP 192.168.1.100',
    timestamp: new Date(Date.now() - 300000).toISOString(), // 5 minutes ago
    severity: 'warning'
  },
  {
    type: 'USER_LOCKED',
    user: 'test_user',
    message: 'User account locked due to multiple failed attempts',
    timestamp: new Date(Date.now() - 600000).toISOString(), // 10 minutes ago
    severity: 'danger'
  },
  {
    type: 'LOGIN_SUCCESS',
    user: 'admin',
    message: 'Successful login from IP 192.168.1.50',
    timestamp: new Date(Date.now() - 900000).toISOString(), // 15 minutes ago
    severity: 'success'
  },
  {
    type: 'PASSWORD_RESET',
    user: 'cashier',
    message: 'Password reset by administrator',
    timestamp: new Date(Date.now() - 1200000).toISOString(), // 20 minutes ago
    severity: 'info'
  }
];

function loadSecurityMetrics() {
  // Simulate loading metrics
  $('#lockedUsersCount').text('2');
  $('#failedAttemptsCount').text('15');
  $('#activeSessionsCount').text('3');
  $('#loginAttemptsCount').text('47');
}

function loadSecurityEvents() {
  let html = '';
  
  securityEvents.forEach(event => {
    const timeAgo = getTimeAgo(event.timestamp);
    const icon = getEventIcon(event.type);
    
    html += `
      <div class="activity-item ${event.severity} mb-3">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="fw-semibold">
              <i class="${icon} me-2"></i>${event.message}
            </div>
            <small class="text-muted">User: ${event.user} • ${timeAgo}</small>
          </div>
          <span class="badge bg-${event.severity === 'danger' ? 'danger' : event.severity === 'warning' ? 'warning' : event.severity === 'success' ? 'success' : 'info'}">
            ${event.type.replace('_', ' ')}
          </span>
        </div>
      </div>
    `;
  });
  
  $('#securityEventsContainer').html(html);
}

function loadLockedUsers() {
  const lockedUsers = [
    { username: 'test_user', reason: 'Multiple failed attempts', locked_until: '2026-08-15 18:30:00' },
    { username: 'temp_cashier', reason: 'Administrative action', locked_until: null }
  ];
  
  let html = '';
  
  if (lockedUsers.length === 0) {
    html = '<div class="text-center text-muted py-3">No locked users</div>';
  } else {
    lockedUsers.forEach(user => {
      const lockType = user.locked_until ? 'Temporary' : 'Permanent';
      const lockInfo = user.locked_until 
        ? `Until: ${new Date(user.locked_until).toLocaleString()}`
        : 'Permanent lock';
      
      html += `
        <div class="border-bottom pb-2 mb-2">
          <div class="fw-semibold">${user.username}</div>
          <small class="text-muted d-block">${user.reason}</small>
          <small class="text-warning">${lockType} - ${lockInfo}</small>
        </div>
      `;
    });
  }
  
  $('#lockedUsersContainer').html(html);
}

function getEventIcon(type) {
  const icons = {
    'LOGIN_SUCCESS': 'bi bi-shield-check',
    'LOGIN_FAILED': 'bi bi-shield-x',
    'USER_LOCKED': 'bi bi-lock',
    'PASSWORD_RESET': 'bi bi-key',
    'USER_UNLOCKED': 'bi bi-unlock'
  };
  return icons[type] || 'bi bi-info-circle';
}

function getTimeAgo(timestamp) {
  const now = new Date();
  const time = new Date(timestamp);
  const diffMs = now - time;
  const diffMins = Math.floor(diffMs / 60000);
  
  if (diffMins < 1) return 'Just now';
  if (diffMins < 60) return `${diffMins}m ago`;
  
  const diffHours = Math.floor(diffMins / 60);
  if (diffHours < 24) return `${diffHours}h ago`;
  
  const diffDays = Math.floor(diffHours / 24);
  return `${diffDays}d ago`;
}

function unlockAllExpiredUsers() {
  if (confirm('This will unlock all users whose lock time has expired. Continue?')) {
    // Simulate API call
    setTimeout(() => {
      toastr.success('All expired user locks have been cleared.');
      loadSecurityMetrics();
      loadLockedUsers();
    }, 1000);
  }
}

function resetAllFailedAttempts() {
  if (confirm('This will reset failed login attempts for all users. Continue?')) {
    // Simulate API call
    setTimeout(() => {
      toastr.success('All failed login attempts have been reset.');
      loadSecurityMetrics();
    }, 1000);
  }
}

function exportSecurityLog() {
  toastr.info('Security log export feature would be implemented here.');
}

function showSystemHealth() {
  const healthData = {
    database: 'healthy',
    sessions: 'healthy', 
    security: 'warning',
    performance: 'healthy'
  };
  
  let status = 'System Health Status:\n\n';
  status += `Database: ${healthData.database}\n`;
  status += `Sessions: ${healthData.sessions}\n`;
  status += `Security: ${healthData.security} (2 locked users)\n`;
  status += `Performance: ${healthData.performance}\n`;

  toastr.info(status.replace(/\n/g, '<br>'), 'System Health', { escapeHtml: false });
}

$(document).ready(function() {
  loadSecurityMetrics();
  loadSecurityEvents();
  loadLockedUsers();
  
  // Auto-refresh every 30 seconds
  setInterval(() => {
    loadSecurityMetrics();
    loadSecurityEvents();
    loadLockedUsers();
  }, 30000);
});
</script>

</body>
</html>