<?php
require_once __DIR__ . '/../../inc/app.php';
workshop_require_login();

// Only admins can access user management
workshop_require_role('admin');

$workshopUser = $_SESSION['user'];
$workshopBase = workshop_base_path();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>User Management – SHENGCHI AUTO LTD</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <link rel="stylesheet" href="../layout.css.php?v=<?= time() ?>">
</head>

<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<div class="app-wrapper">

<?php include '../navbar.php'; ?>
<?php $activePage = 'users'; include '../sidebar.php'; ?>

<main class="app-main">
  <!-- Breadcrumb -->
  <div class="app-content-header px-4 pt-3 pb-0">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-people-fill me-2"></i><span data-i18n="userManagement">User Management</span></h4>
        <p class="text-muted small mb-0" data-i18n="manageUsersSubtitle">Manage system users, roles, and access permissions.</p>
      </div>
      <ol class="breadcrumb mb-0" style="--bs-breadcrumb-divider:'›';">
        <li class="breadcrumb-item"><a href="../index.php" data-i18n="home">Home</a></li>
        <li class="breadcrumb-item active" data-i18n="userManagement">User Management</li>
      </ol>
    </div>
  </div>

  <div class="app-content p-4">
    <!-- Role Summary Cards -->
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-shrink-0">
                <i class="bi bi-shield-lock fs-2 text-danger"></i>
              </div>
              <div class="ms-3">
                <h6 class="mb-0" data-i18n="administrators">Administrators</h6>
                <p class="text-muted small mb-0" data-i18n="fullAccess">Full system access</p>
                <span class="badge bg-danger" id="adminCount">0</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-shrink-0">
                <i class="bi bi-person-badge fs-2 text-primary"></i>
              </div>
              <div class="ms-3">
                <h6 class="mb-0" data-i18n="owners">Owners</h6>
                <p class="text-muted small mb-0" data-i18n="businessOversight">Business oversight</p>
                <span class="badge bg-primary" id="ownerCount">0</span>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card border-0 shadow-sm">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-shrink-0">
                <i class="bi bi-cash-stack fs-2 text-warning"></i>
              </div>
              <div class="ms-3">
                <h6 class="mb-0" data-i18n="cashiers">Cashiers</h6>
                <p class="text-muted small mb-0" data-i18n="dailyOperations">Daily operations</p>
                <span class="badge bg-warning text-dark" id="cashierCount">0</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Users Table -->
    <div class="row">
      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-white border-0 pb-0">
            <div class="d-flex justify-content-between align-items-center">
              <h6 class="mb-0 fw-bold" data-i18n="systemUsers">System Users</h6>
              <div>
                <button class="btn btn-success btn-sm me-2" data-bs-toggle="modal" data-bs-target="#addUserModal">
                  <i class="bi bi-plus-circle me-1"></i><span data-i18n="addUser">Add User</span>
                </button>
                <button class="btn btn-outline-primary btn-sm" onclick="loadUsers()" title="Refresh">
                  <i class="bi bi-arrow-clockwise"></i>
                </button>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover" id="usersTable">
                <thead>
                  <tr>
                    <th data-i18n="user">User</th>
                    <th data-i18n="role">Role</th>
                    <th data-i18n="status">Status</th>
                    <th data-i18n="lastLogin">Last Login</th>
                    <th data-i18n="actions">Actions</th>
                  </tr>
                </thead>
                <tbody id="usersTableBody">
                  <tr>
                    <td colspan="5" class="text-center py-4">
                      <div class="spinner-border spinner-border-sm me-2"></div><span data-i18n="loadingUsers">Loading users...</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</main>

</div>

<!-- User Management Modal -->
<div class="modal fade" id="userManagementModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="userManagementModalTitle" data-i18n="userManagement">User Management</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="userManagementModalBody">
        <!-- Dynamic content will be loaded here -->
      </div>
    </div>
  </div>
</div>

<!-- Lock User Modal -->
<div class="modal fade" id="lockUserModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="lockUserForm">
        <div class="modal-header">
          <h5 class="modal-title" data-i18n="lockUserTitle">Lock User Account</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <span data-i18n="aboutToLock">You are about to lock user:</span> <strong id="lockUserName"></strong>
          </div>
          <div class="mb-3">
            <label for="lockReason" class="form-label" data-i18n="reasonForLocking">Reason for locking</label>
            <select class="form-select" id="lockReason" name="reason" required>
              <option value="" data-i18n="selectReason">Select reason</option>
              <option value="Security violation" data-i18n="securityViolation">Security violation</option>
              <option value="Policy breach" data-i18n="policyBreach">Policy breach</option>
              <option value="Suspicious activity" data-i18n="suspiciousActivity">Suspicious activity</option>
              <option value="Administrative action" data-i18n="administrativeAction">Administrative action</option>
              <option value="Other" data-i18n="other">Other</option>
            </select>
          </div>
          <div class="mb-3" id="customReasonGroup" style="display: none;">
            <label for="customReason" class="form-label" data-i18n="customReason">Custom Reason</label>
            <input type="text" class="form-control" id="customReason" name="custom_reason" 
                   placeholder="Enter custom reason">
          </div>
          <div class="mb-3">
            <label for="lockDuration" class="form-label" data-i18n="lockDuration">Lock Duration</label>
            <select class="form-select" id="lockDuration" name="duration">
              <option value="0" data-i18n="permanent">Permanent (until manually unlocked)</option>
              <option value="15" data-i18n="minutes15">15 minutes</option>
              <option value="60" data-i18n="hour1">1 hour</option>
              <option value="480" data-i18n="hours8">8 hours</option>
              <option value="1440" data-i18n="hours24">24 hours</option>
              <option value="10080" data-i18n="week1">1 week</option>
            </select>
          </div>
          <input type="hidden" id="lockUserId" name="user_id">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><span data-i18n="cancel">Cancel</span></button>
          <button type="submit" class="btn btn-danger">
            <i class="bi bi-lock me-1"></i><span data-i18n="lockUserBtn">Lock User</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="resetPasswordForm">
        <div class="modal-header">
          <h5 class="modal-title" data-i18n="resetPasswordTitle">Reset Password</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            <span data-i18n="resettingPasswordFor">Resetting password for user:</span> <strong id="resetPasswordUserName"></strong>
          </div>
          <div class="mb-3">
            <label for="newPassword" class="form-label" data-i18n="newPassword">New Password</label>
            <input type="password" class="form-control" id="newPassword" name="new_password"
                   required minlength="8" autocomplete="new-password">
            <div class="form-text" data-i18n="passwordMinLength">Password must be at least 8 characters long.</div>
          </div>
          <div class="mb-3">
            <label for="confirmPassword" class="form-label" data-i18n="confirmPassword">Confirm Password</label>
            <input type="password" class="form-control" id="confirmPassword" 
                   required minlength="8">
          </div>
          <input type="hidden" id="resetPasswordUserId" name="user_id">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><span data-i18n="cancel">Cancel</span></button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-key me-1"></i><span data-i18n="resetPasswordBtn">Reset Password</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="addUserForm">
        <div class="modal-header">
          <h5 class="modal-title" data-i18n="addNewUserTitle">Add New User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="addUsername" class="form-label" data-i18n="username">Username</label>
            <input type="text" class="form-control" id="addUsername" name="username" 
                   required pattern="[a-zA-Z0-9_]+" 
                   title="Username can only contain letters, numbers, and underscores">
          </div>
          <div class="mb-3">
            <label for="addFullName" class="form-label" data-i18n="fullName">Full Name</label>
            <input type="text" class="form-control" id="addFullName" name="full_name" required>
          </div>
          <div class="mb-3">
            <label for="addRole" class="form-label" data-i18n="role">Role</label>
            <select class="form-select" id="addRole" name="role" required>
              <option value="" data-i18n="selectRole">Select Role</option>
              <option value="admin" data-i18n="roleAdmin">Administrator - Full System Access</option>
              <option value="owner" data-i18n="roleOwner">Owner - Business Oversight</option>
              <option value="cashier" data-i18n="roleCashier">Cashier - Daily Operations</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="addPassword" class="form-label" data-i18n="password">Password</label>
            <input type="password" class="form-control" id="addPassword" name="password" 
                   required minlength="8" autocomplete="new-password">
            <div class="form-text">Password must be at least 8 characters long.</div>
          </div>
          <div class="mb-3">
            <label for="addConfirmPassword" class="form-label" data-i18n="confirmPassword">Confirm Password</label>
            <input type="password" class="form-control" id="addConfirmPassword" 
                   required minlength="8">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><span data-i18n="cancel">Cancel</span></button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i><span data-i18n="createUserBtn">Create User</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
const API_BASE = '../../classes/UserManagement.php';

function loadUsers() {
  $.getJSON(`${API_BASE}?f=list_users`)
    .done(function(response) {
      if (response.status === 'success') {
        renderUsersTable(response.users);
        updateRoleCounts(response.users);
      } else {
        toastr.error(response.msg || 'Failed to load users');
      }
    })
    .fail(function(xhr) {
      console.error('Failed to load users:', xhr);
      toastr.error('Failed to connect to user management service');
    });
}

function renderUsersTable(users) {
  let html = '';
  
  users.forEach(user => {
    const statusBadge = getStatusBadge(user);
    const roleBadge = getRoleBadge(user.role);
    const lastLogin = user.last_login 
      ? new Date(user.last_login).toLocaleDateString('en-US', {
          year: 'numeric', month: 'short', day: 'numeric',
          hour: '2-digit', minute: '2-digit'
        })
      : 'Never';
      
    const isCurrentUser = user.username === '<?= $_SESSION["user"]["username"] ?? "" ?>';
    
    html += `
      <tr ${user.is_locked ? 'class="table-warning"' : ''}>
        <td>
          <div class="d-flex align-items-center">
            <div class="avatar bg-light rounded-circle me-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
              <i class="bi bi-person fs-5 ${user.is_locked ? 'text-warning' : ''}"></i>
            </div>
            <div>
              <div class="fw-semibold">${user.full_name}</div>
              <small class="text-muted">@${user.username}</small>
              ${user.failed_attempts > 0 ? `<br><small class="text-warning">Failed attempts: ${user.failed_attempts}</small>` : ''}
            </div>
          </div>
        </td>
        <td>${roleBadge}</td>
        <td>${statusBadge}</td>
        <td><small class="text-muted">${lastLogin}</small></td>
        <td>
          <div class="btn-group btn-group-sm">
            <button class="btn btn-outline-secondary" onclick="viewUserDetails(${user.id})" title="View Details">
              <i class="bi bi-eye"></i>
            </button>
            ${!isCurrentUser ? `
              ${user.is_locked ? `
                <button class="btn btn-outline-success" onclick="unlockUser(${user.id}, '${user.username}')" title="Unlock User">
                  <i class="bi bi-unlock"></i>
                </button>
              ` : `
                <button class="btn btn-outline-warning" onclick="lockUser(${user.id}, '${user.username}')" title="Lock User">
                  <i class="bi bi-lock"></i>
                </button>
              `}
              <button class="btn btn-outline-primary" onclick="resetPassword(${user.id}, '${user.username}')" title="Reset Password">
                <i class="bi bi-key"></i>
              </button>
            ` : '<span class="badge bg-info">Current User</span>'}
          </div>
        </td>
      </tr>
    `;
  });
  
  $('#usersTableBody').html(html);
}

function updateRoleCounts(users) {
  const counts = {admin: 0, owner: 0, cashier: 0};
  users.forEach(user => counts[user.role]++);
  
  $('#adminCount').text(counts.admin);
  $('#ownerCount').text(counts.owner);
  $('#cashierCount').text(counts.cashier);
}

function getStatusBadge(user) {
  if (user.is_locked) {
    const lockText = user.locked_until ? 'Temporarily Locked' : 'Locked';
    return `<span class="badge bg-warning text-dark">${lockText}</span>`;
  } else if (user.status === 'suspended') {
    return '<span class="badge bg-danger">Suspended</span>';
  } else {
    return '<span class="badge bg-success">Active</span>';
  }
}

function getRoleBadge(role) {
  const badges = {
    admin: '<span class="badge bg-danger">Administrator</span>',
    owner: '<span class="badge bg-primary">Owner</span>',
    cashier: '<span class="badge bg-warning text-dark">Cashier</span>'
  };
  return badges[role] || '<span class="badge bg-secondary">Unknown</span>';
}

function lockUser(userId, username) {
  $('#lockUserId').val(userId);
  $('#lockUserName').text(username);
  $('#lockUserModal').modal('show');
}

function unlockUser(userId, username) {
  if (!confirm(`Are you sure you want to unlock user: ${username}?`)) return;
  
  $.post(`${API_BASE}?f=unlock_user`, {
    user_id: userId,
    reason: 'Manual unlock by administrator'
  })
  .done(function(response) {
    if (response.status === 'success') {
      toastr.success(response.msg);
      loadUsers(); // Reload the table
    } else {
      toastr.error(response.msg || 'Failed to unlock user');
    }
  })
  .fail(function(xhr) {
    toastr.error('Failed to unlock user');
  });
}

function resetPassword(userId, username) {
  $('#resetPasswordUserId').val(userId);
  $('#resetPasswordUserName').text(username);
  $('#newPassword').val('');
  $('#confirmPassword').val('');
  $('#resetPasswordModal').modal('show');
}

function viewUserDetails(userId) {
  // Load user details and show in modal
  $.getJSON(`${API_BASE}?f=get_user_details&user_id=${userId}`)
    .done(function(response) {
      if (response.status === 'success') {
        showUserDetailsModal(response.user);
      } else {
        toastr.error(response.msg || 'Failed to load user details');
      }
    })
    .fail(function() {
      toastr.error('Failed to load user details');
    });
}

function showUserDetailsModal(user) {
  const lockStatus = user.locked_until 
    ? `Locked until: ${new Date(user.locked_until).toLocaleString()}`
    : user.status === 'locked' ? 'Permanently locked' : 'Not locked';
    
  const content = `
    <div class="row">
      <div class="col-md-6">
        <h6>Basic Information</h6>
        <table class="table table-sm">
          <tr><th>Username:</th><td>${user.username}</td></tr>
          <tr><th>Full Name:</th><td>${user.full_name}</td></tr>
          <tr><th>Role:</th><td>${getRoleBadge(user.role)}</td></tr>
          <tr><th>Status:</th><td>${getStatusBadge(user)}</td></tr>
        </table>
      </div>
      <div class="col-md-6">
        <h6>Activity Information</h6>
        <table class="table table-sm">
          <tr><th>Created:</th><td>${new Date(user.created_at).toLocaleString()}</td></tr>
          <tr><th>Last Updated:</th><td>${new Date(user.updated_at).toLocaleString()}</td></tr>
          <tr><th>Last Login:</th><td>${user.last_login ? new Date(user.last_login).toLocaleString() : 'Never'}</td></tr>
          <tr><th>Failed Attempts:</th><td>${user.failed_attempts}</td></tr>
          <tr><th>Lock Status:</th><td>${lockStatus}</td></tr>
        </table>
      </div>
    </div>
  `;
  
  $('#userManagementModalTitle').text(`User Details: ${user.username}`);
  $('#userManagementModalBody').html(content);
  $('#userManagementModal').modal('show');
}

// Event Handlers
$('#lockReason').change(function() {
  $('#customReasonGroup').toggle($(this).val() === 'Other');
});

$('#lockUserForm').submit(function(e) {
  e.preventDefault();
  
  const formData = new FormData(this);
  const reason = formData.get('reason') === 'Other' 
    ? formData.get('custom_reason') 
    : formData.get('reason');
  
  if (!reason) {
    toastr.error('Please provide a reason for locking the user.');
    return;
  }
  
  $.post(`${API_BASE}?f=lock_user`, {
    user_id: formData.get('user_id'),
    duration: formData.get('duration'),
    reason: reason
  })
  .done(function(response) {
    if (response.status === 'success') {
      toastr.success(response.msg);
      $('#lockUserModal').modal('hide');
      loadUsers();
    } else {
      toastr.error(response.msg || 'Failed to lock user');
    }
  })
  .fail(function() {
    toastr.error('Failed to lock user');
  });
});

$('#resetPasswordForm').submit(function(e) {
  e.preventDefault();
  
  const newPassword = $('#newPassword').val();
  const confirmPassword = $('#confirmPassword').val();
  
  if (newPassword !== confirmPassword) {
    toastr.error('Passwords do not match.');
    return;
  }
  
  if (newPassword.length < 8) {
    toastr.error('Password must be at least 8 characters long.');
    return;
  }
  
  $.post(`${API_BASE}?f=reset_password`, {
    user_id: $('#resetPasswordUserId').val(),
    new_password: newPassword
  })
  .done(function(response) {
    if (response.status === 'success') {
      toastr.success(response.msg);
      $('#resetPasswordModal').modal('hide');
      loadUsers();
    } else {
      toastr.error(response.msg || 'Failed to reset password');
    }
  })
  .fail(function() {
    toastr.error('Failed to reset password');
  });
});

$('#addUserForm').submit(function(e) {
  e.preventDefault();
  
  const password = $('#addPassword').val();
  const confirmPassword = $('#addConfirmPassword').val();
  
  if (password !== confirmPassword) {
    toastr.error('Passwords do not match.');
    return;
  }
  
  if (password.length < 8) {
    toastr.error('Password must be at least 8 characters long.');
    return;
  }
  
  const formData = new FormData(this);
  
  $.post(`${API_BASE}?f=create_user`, {
    username: formData.get('username'),
    full_name: formData.get('full_name'),
    role: formData.get('role'),
    password: password
  })
  .done(function(response) {
    if (response.status === 'success') {
      toastr.success(response.msg);
      $('#addUserModal').modal('hide');
      document.getElementById('addUserForm').reset();
      loadUsers();
    } else {
      toastr.error(response.msg || 'Failed to create user');
    }
  })
  .fail(function() {
    toastr.error('Failed to create user');
  });
});

$(document).ready(function() {
  loadUsers();
  
  // Auto-refresh every 30 seconds
  setInterval(loadUsers, 30000);
});
</script>

</body>
</html>