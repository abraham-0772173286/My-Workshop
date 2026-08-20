<?php
/**
 * sidebar.php — shared sidebar include with role-based navigation.
 * Set $activePage before including:
 *   $activePage = 'dashboard' | 'repair_jobs' | 'customers' | 'vehicles' | 'drivers' | ...
 */
$activePage = $activePage ?? '';
$user = workshop_get_user();
$userRole = $user['effective_role'] ?? $user['role'] ?? 'cashier';

function sidebarLink(string $page, string $href, string $icon, string $label, string $active, string $i18n = ''): string {
    $cls = $active === $page ? 'nav-link active' : 'nav-link';
    $attr = $i18n !== '' ? " data-i18n=\"{$i18n}\"" : '';
    return "<div class=\"nav-item\"><a href=\"{$href}\" class=\"{$cls}\"><i class=\"bi {$icon}\"></i><span{$attr}>{$label}</span></a></div>";
}

function sidebarParent(string $id, string $icon, string $label, bool $isOpen, string $i18n = ''): string {
    $cls = $isOpen ? 'nav-link nav-parent open' : 'nav-link nav-parent';
    $attr = $i18n !== '' ? " data-i18n=\"{$i18n}\"" : '';
    return "<div class=\"nav-item nav-item-parent\" data-submenu=\"{$id}\"><a href=\"javascript:void(0)\" class=\"{$cls}\" data-toggle=\"submenu\"><i class=\"bi {$icon}\"></i><span{$attr}>{$label}</span><i class=\"bi bi-chevron-down nav-chevron\"></i></a></div>";
}

function sidebarSubItem(string $page, string $href, string $icon, string $label, string $active, string $i18n = ''): string {
    $cls = $active === $page ? 'nav-link sub-link active' : 'nav-link sub-link';
    $attr = $i18n !== '' ? " data-i18n=\"{$i18n}\"" : '';
    return "<div class=\"nav-subitem\"><a href=\"{$href}\" class=\"{$cls}\"><i class=\"bi {$icon}\"></i><span{$attr}>{$label}</span></a></div>";
}

$driverPages = ['drivers_all','drivers_register','drivers_assignments','drivers_trips','drivers_fuel','drivers_performance','drivers_history'];
$vehiclesOpen = in_array($activePage, ['vehicles']);
$driversOpen  = in_array($activePage, $driverPages);
?>

<aside class="garage-sidebar" id="garageSidebar">
  <a href="index.php" class="sidebar-brand">
    <span class="brand-mark"><i class="bi bi-wrench-adjustable"></i></span>
    <span class="brand-name">SHENGCHI AUTO LTD<small>金龙汽车维修</small></span>
  </a>
  <nav class="nav-sidebar">
    <div class="nav-header" data-i18n="<?= $userRole === 'admin' ? 'adminWorkspace' : ($userRole === 'owner' ? 'ownerWorkspace' : 'cashierWorkspace') ?>">
        <?php if ($userRole === 'admin'): ?>
            ADMIN WORKSPACE
        <?php elseif ($userRole === 'owner'): ?>
            OWNER WORKSPACE  
        <?php else: ?>
            CASHIER WORKSPACE
        <?php endif; ?>
    </div>
    
    <?= sidebarLink('dashboard', 'index.php', 'bi-speedometer2', 'Dashboard', $activePage, 'dashboard') ?>
    <?= sidebarLink('repair_jobs', 'repair_jobs.php', 'bi-clipboard2-check', 'Repair Jobs', $activePage, 'repairJobs') ?>
    <?= sidebarLink('customers', 'customers.php', 'bi-people', 'Customers', $activePage, 'customers') ?>

    <!-- Vehicles (simple link) -->
    <?= sidebarLink('vehicles', 'vehicles.php', 'bi-car-front', 'Vehicles', $activePage, 'vehicles') ?>

    <!-- Drivers (parent with submenu) -->
    <?= sidebarParent('drivers-submenu', 'bi-person-workspace', 'Drivers', $driversOpen, 'drivers') ?>
    <div class="nav-submenu" id="drivers-submenu" style="<?= $driversOpen ? 'display:block;' : 'display:none;' ?>">
        <?= sidebarSubItem('drivers_all', 'models/drivers.php', 'bi-list-ul', 'All Drivers', $activePage, 'driversAll') ?>
        <?= sidebarSubItem('drivers_register', 'models/driver_register.php', 'bi-person-plus', 'Register Driver', $activePage, 'driversRegister') ?>
        <?= sidebarSubItem('drivers_assignments', 'models/driver_assignments.php', 'bi-link-45deg', 'Driver Assignments', $activePage, 'driversAssignments') ?>
        <?= sidebarSubItem('drivers_trips', 'models/driver_trips.php', 'bi-map', 'Driver Trips', $activePage, 'driversTrips') ?>
        <?= sidebarSubItem('drivers_fuel', 'models/fuel_records.php', 'bi-fuel-pump', 'Fuel Records', $activePage, 'driversFuel') ?>
        <?= sidebarSubItem('drivers_performance', 'models/driver_performance.php', 'bi-graph-up', 'Driver Performance', $activePage, 'driversPerformance') ?>
        <?= sidebarSubItem('drivers_history', 'models/driver_history.php', 'bi-clock-history', 'Driver History', $activePage, 'driversHistory') ?>
    </div>

    <?= sidebarLink('payments', 'models/payment.php', 'bi-cash-stack', 'Payments', $activePage, 'payments') ?>
    <?= sidebarLink('receipts', 'models/receipts.php', 'bi-receipt', 'Receipts', $activePage, 'receipts') ?>

    <?php if (in_array($userRole, ['admin', 'owner'])): ?>
        <?= sidebarLink('reports', 'models/reports.php', 'bi-bar-chart-line', 'Reports', $activePage, 'reports') ?>
    <?php endif; ?>

    <?php if ($userRole === 'admin'): ?>
        <?= sidebarLink('settings', 'models/settings.php', 'bi-gear', 'Settings', $activePage, 'settings') ?>
        <?= sidebarLink('users', 'models/user_management.php', 'bi-people-fill', 'User Management', $activePage, 'userManagement') ?>
        <?= sidebarLink('security', 'models/security_monitor.php', 'bi-shield-exclamation', 'Security Monitor', $activePage, 'securityMonitor') ?>
    <?php endif; ?>
    
    <div class="nav-item">
        <a href="../classes/Login.php?f=logout" class="nav-link">
            <i class="bi bi-box-arrow-left"></i>
            <span data-i18n="signOut">Sign Out</span>
        </a>
    </div>
    
    <div class="sidebar-footer">
        <i class="bi bi-shield-check me-1"></i>
        <span data-i18n="loggedInAs">Logged in as</span> <?= htmlspecialchars($user['name'] ?? 'User', ENT_QUOTES) ?>
        <?php if ($userRole !== ($user['role'] ?? '')): ?>
            <br><small class="text-warning">Acting as <?= ucfirst($userRole) ?></small>
        <?php endif; ?>
    </div>
  </nav>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function () {
  var toggle  = document.getElementById('sidebarToggle');
  var sidebar = document.getElementById('garageSidebar');
  if (toggle && sidebar) {
    toggle.addEventListener('click', function (e) {
      e.preventDefault();
      var compact = window.matchMedia('(max-width:991px)').matches;
      sidebar.classList.toggle(compact ? 'is-open' : 'is-collapsed');
      document.body.classList.toggle('garage-sidebar-collapsed',
        !compact && sidebar.classList.contains('is-collapsed'));
      window.dispatchEvent(new Event('resize'));
      setTimeout(function () { window.dispatchEvent(new Event('resize')); }, 300);
    });
  }

  document.querySelectorAll('[data-toggle="submenu"]').forEach(function(link) {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      var parent = this.closest('.nav-item-parent');
      var submenu = document.getElementById(parent.getAttribute('data-submenu'));
      if (!submenu) return;
      var isOpen = submenu.style.display === 'block';
      if (isOpen) { submenu.style.display = 'none'; this.classList.remove('open'); }
      else        { submenu.style.display = 'block'; this.classList.add('open'); }
    });
  });

  document.querySelectorAll('.sub-link').forEach(function(link) {
    link.addEventListener('click', function() {
      if (window.matchMedia('(max-width:991px)').matches && sidebar) {
        sidebar.classList.remove('is-open');
      }
    });
  });
});
</script>
