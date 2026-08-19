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
    /* ── Dashboard-style typography ─────────────────────────────────────── */
    .dash-value {
      font-size: 1.9rem;
      font-weight: 800;
      line-height: 1;
      color: #0f172a;
    }
    .dash-label {
      font-size: .78rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: .5px;
      margin-top: 4px;
      color: #64748b;
    }
    .dash-sub {
      font-size: .75rem;
    }

    /* ── Page cards ─────────────────────────────────────────────────────── */
    .sec-card {
      background: #fff;
      border: none;
      border-radius: 20px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, .06);
      height: 100%;
    }
    .sec-card .card-header {
      background: #fff;
      border-bottom: 1px solid #f1f5f9;
      border-radius: 20px 20px 0 0 !important;
      padding: 16px 20px;
    }
    .sec-card .card-header h6 {
      font-weight: 800;
      font-size: .9rem;
      color: #0f172a;
      margin: 0;
    }

    /* ── Stat cards ─────────────────────────────────────────────────────── */
    .security-card {
      background: #fff;
      border: none;
      border-left: 4px solid;
      border-radius: 20px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, .06);
      transition: transform .2s, box-shadow .2s;
    }
    .security-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 28px rgba(0, 0, 0, .1);
    }
    .security-card.warning { border-left-color: #f59e0b; }
    .security-card.danger  { border-left-color: #ef4444; }
    .security-card.success { border-left-color: #10b981; }
    .security-card.info    { border-left-color: #06b6d4; }

    .security-icon {
      width: 52px;
      height: 52px;
      border-radius: 14px;
      display: grid;
      place-items: center;
      font-size: 22px;
      flex-shrink: 0;
    }
    .security-card.warning .security-icon { background: #fffbeb; color: #d97706; }
    .security-card.danger  .security-icon { background: #fef2f2; color: #dc2626; }
    .security-card.success .security-icon { background: #ecfdf5; color: #059669; }
    .security-card.info    .security-icon { background: #ecfeff; color: #0891b2; }

    /* ── Security event items ───────────────────────────────────────────── */
    .event-item {
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 14px 16px;
      border-radius: 14px;
      background: #f8fafc;
      border: 1px solid #eef2f7;
      margin-bottom: 12px;
      transition: background .2s;
    }
    .event-item:hover { background: #f1f5f9; }
    .event-icon {
      width: 42px;
      height: 42px;
      border-radius: 12px;
      display: grid;
      place-items: center;
      font-size: 18px;
      flex-shrink: 0;
    }
    .event-item.success .event-icon { background: #ecfdf5; color: #059669; }
    .event-item.warning .event-icon { background: #fffbeb; color: #d97706; }
    .event-item.danger  .event-icon { background: #fef2f2; color: #dc2626; }
    .event-item.info    .event-icon { background: #ecfeff; color: #0891b2; }

    .event-body { min-width: 0; }
    .event-msg {
      font-weight: 700;
      font-size: .9rem;
      color: #0f172a;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    .event-meta { color: #94a3b8; }

    /* ── Locked user boxes ──────────────────────────────────────────────── */
    #lockedUsersGrid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 12px;
    }
    .locked-user-box {
      background: #fff;
      border: 1px solid #eef2f7;
      border-radius: 14px;
      padding: 14px;
      transition: box-shadow .2s, transform .2s;
    }
    .locked-user-box:hover {
      box-shadow: 0 6px 18px rgba(0, 0, 0, .08);
      transform: translateY(-2px);
    }
    .user-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: grid;
      place-items: center;
      font-size: 18px;
      flex-shrink: 0;
    }
    .user-avatar.permanent { background: #fef2f2; color: #dc2626; }
    .user-avatar.temporary { background: #fffbeb; color: #d97706; }
    .locked-user-box .user-name {
      font-weight: 700;
      color: #0f172a;
      line-height: 1.2;
    }

    /* ── Quick action buttons ───────────────────────────────────────────── */
    .quick-action-btn {
      border-radius: 12px;
      padding: .65rem 1rem;
      font-weight: 600;
    }

    /* ── Home pill breadcrumb ───────────────────────────────────────────── */
    .home-pill {
      display: inline-flex;
      align-items: center;
      gap: .4rem;
      background: #eef2ff;
      color: #4f46e5;
      font-weight: 700;
      font-size: .78rem;
      text-transform: uppercase;
      letter-spacing: .5px;
      text-decoration: none;
      padding: .42rem .95rem;
      border-radius: 50px;
      border: 1px solid #e0e7ff;
      transition: background .2s, color .2s;
    }
    .home-pill:hover {
      background: #4f46e5;
      color: #fff;
    }

    /* ── Empty state ────────────────────────────────────────────────────── */
    .empty-state {
      color: #94a3b8;
      font-size: .85rem;
    }
  </style>
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<div class="app-wrapper">

<?php include '../navbar.php'; ?>
<?php $activePage = 'security'; include '../sidebar.php'; ?>

<main class="app-main">
  <!-- Breadcrumb -->
  <div class="app-content-header px-4 pt-3 pb-0">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
      <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-shield-exclamation me-2 text-danger"></i><span data-i18n="securityMonitor">Security Monitor</span></h4>
        <p class="text-muted small mb-0" data-i18n="securityMonitorSub">Monitor system security, login attempts, and user activities.</p>
      </div>
      <ol class="breadcrumb mb-0" style="--bs-breadcrumb-divider:'›';">
        <li class="breadcrumb-item">
          <a href="../index.php" class="home-pill">
            <i class="bi bi-house-door-fill"></i>
            <span data-i18n="dashboard">Dashboard</span>
          </a>
        </li>
        <li class="breadcrumb-item active" data-i18n="securityMonitor">Security Monitor</li>
      </ol>
    </div>
  </div>

  <div class="app-content p-4">

    <!-- Security Status Cards -->
    <div class="row g-3 mb-4">
      <div class="col-md-6 col-xl-3">
        <div class="card security-card warning">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="security-icon"><i class="bi bi-exclamation-triangle"></i></div>
            <div>
              <div class="dash-value" id="lockedUsersCount">0</div>
              <div class="dash-label" data-i18n="lockedUsers">Locked Users</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="card security-card danger">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="security-icon"><i class="bi bi-shield-x"></i></div>
            <div>
              <div class="dash-value" id="failedAttemptsCount">0</div>
              <div class="dash-label">Failed Attempts (24h)</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="card security-card success">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="security-icon"><i class="bi bi-shield-check"></i></div>
            <div>
              <div class="dash-value" id="activeSessionsCount">0</div>
              <div class="dash-label">Active Sessions</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-xl-3">
        <div class="card security-card info">
          <div class="card-body d-flex align-items-center gap-3">
            <div class="security-icon"><i class="bi bi-activity"></i></div>
            <div>
              <div class="dash-value" id="loginAttemptsCount">0</div>
              <div class="dash-label">Login Attempts (24h)</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Security Activities -->
    <div class="row g-3 mb-4">
      <!-- Recent Activities -->
      <div class="col-lg-7">
        <div class="card sec-card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="bi bi-clock-history me-2 text-primary"></i><span data-i18n="recentSecurityEvents">Recent Security Events</span></h6>
            <button class="btn btn-sm btn-primary" onclick="loadSecurityEvents()">
              <i class="bi bi-arrow-clockwise me-1"></i><span data-i18n="refresh">Refresh</span>
            </button>
          </div>
          <div class="card-body" style="max-height: 560px; overflow-y: auto;">
            <div id="securityEventsContainer">
              <div class="text-center py-4 empty-state">
                <div class="spinner-border spinner-border-sm me-2"></div>Loading security events...
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="col-lg-5">
        <div class="card sec-card">
          <div class="card-header">
            <h6 class="mb-0"><i class="bi bi-tools me-2 text-primary"></i><span data-i18n="quickActions">Quick Actions</span></h6>
          </div>
          <div class="card-body">
            <div class="d-grid gap-2">
              <button class="btn btn-primary quick-action-btn" onclick="unlockAllExpiredUsers()">
                <i class="bi bi-unlock me-2"></i><span data-i18n="unlockExpiredUsers">Unlock Expired Users</span>
              </button>
              <button class="btn btn-warning text-dark quick-action-btn" onclick="resetAllFailedAttempts()">
                <i class="bi bi-arrow-clockwise me-2"></i><span data-i18n="resetFailedAttempts">Reset Failed Attempts</span>
              </button>
              <button class="btn btn-info text-dark quick-action-btn" onclick="exportSecurityLog()">
                <i class="bi bi-download me-2"></i><span data-i18n="exportSecurityLog">Export Security Log</span>
              </button>
              <hr class="my-2">
              <button class="btn btn-success quick-action-btn" onclick="showSystemHealth()">
                <i class="bi bi-heart-pulse me-2"></i><span data-i18n="systemHealthCheck">System Health Check</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Locked Users -->
    <div class="row">
      <div class="col-12">
        <div class="card sec-card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="bi bi-lock me-2 text-warning"></i><span data-i18n="lockedUsers">Locked Users</span></h6>
            <span class="badge bg-warning-subtle text-warning rounded-pill" id="lockedUsersBadge">0</span>
          </div>
          <div class="card-body">
            <div id="lockedUsersGrid">
              <div class="text-center py-3 empty-state">Loading...</div>
            </div>
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
  $('#lockedUsersBadge').text('2');
}

function loadSecurityEvents() {
  let html = '';

  securityEvents.forEach(event => {
    const timeAgo = getTimeAgo(event.timestamp);
    const icon = getEventIcon(event.type);
    const badgeColor = event.severity === 'danger' ? 'danger'
      : event.severity === 'warning' ? 'warning text-dark'
      : event.severity === 'success' ? 'success' : 'info';

    html += `
      <div class="event-item ${event.severity}">
        <div class="event-icon"><i class="${icon}"></i></div>
        <div class="event-body flex-grow-1">
          <div class="event-msg">${event.message}</div>
          <small class="event-meta">User: ${event.user} • ${timeAgo}</small>
        </div>
        <span class="badge bg-${badgeColor} rounded-pill text-uppercase">${event.type.replace('_', ' ')}</span>
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
    html = '<div class="text-center empty-state py-3"><i class="bi bi-check2-circle text-success me-1"></i>No locked users</div>';
  } else {
    lockedUsers.forEach(user => {
      const isTemp = !!user.locked_until;
      const lockType = isTemp ? 'Temporary' : 'Permanent';
      const lockInfo = isTemp
        ? `<i class="bi bi-clock me-1"></i>Until: ${new Date(user.locked_until).toLocaleString()}`
        : '<i class="bi bi-lock-fill me-1"></i>Permanent lock';

      html += `
        <div class="locked-user-box">
          <div class="d-flex align-items-center gap-3">
            <div class="user-avatar ${isTemp ? 'temporary' : 'permanent'}"><i class="bi bi-person-fill"></i></div>
            <div class="flex-grow-1">
              <div class="user-name">${user.username}</div>
              <small class="event-meta d-block">${user.reason}</small>
            </div>
            <span class="badge ${isTemp ? 'bg-warning text-dark' : 'bg-danger'} rounded-pill">${lockType}</span>
          </div>
          <small class="d-block mt-2 text-warning dash-sub">${lockInfo}</small>
        </div>
      `;
    });
  }

  $('#lockedUsersGrid').html(html);
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
