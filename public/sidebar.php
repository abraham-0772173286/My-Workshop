<?php
/**
 * sidebar.php — shared sidebar include with role-based navigation.
 * Set $activePage before including:
 *   $activePage = 'dashboard' | 'repair_jobs' | 'customers' | 'vehicles' | ...
 */
$activePage = $activePage ?? '';
$user = workshop_get_user();
$userRole = $user['effective_role'] ?? $user['role'] ?? 'cashier';

function sidebarLink(string $page, string $href, string $icon, string $label, string $active, string $i18n = '', bool $arrow = false): string {
    $cls = $active === $page ? 'nav-link active' : 'nav-link';
    $arr = $arrow ? '<i class="bi bi-chevron-right nav-arrow"></i>' : '';
    $attr = $i18n !== '' ? " data-i18n=\"{$i18n}\"" : '';
    return "<div class=\"nav-item\"><a href=\"{$href}\" class=\"{$cls}\"><i class=\"bi {$icon}\"></i><span{$attr}>{$label}</span>{$arr}</a></div>";
}

// Define navigation items based on roles
$navItems = [];

// Dashboard - available to all roles
$navItems[] = ['page' => 'dashboard', 'href' => 'index.php', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard', 'i18n' => 'dashboard'];

// Core operations - available to all roles
$navItems[] = ['page' => 'repair_jobs', 'href' => 'repair_jobs.php', 'icon' => 'bi-clipboard2-check', 'label' => 'Repair Jobs', 'i18n' => 'repairJobs'];
$navItems[] = ['page' => 'customers', 'href' => 'customers.php', 'icon' => 'bi-people', 'label' => 'Customers', 'i18n' => 'customers'];
$navItems[] = ['page' => 'vehicles', 'href' => 'vehicles.php', 'icon' => 'bi-car-front', 'label' => 'Vehicles', 'i18n' => 'vehicles'];

// Financial operations - available to all roles but with different permissions
$navItems[] = ['page' => 'payments', 'href' => 'models/payment.php', 'icon' => 'bi-cash-stack', 'label' => 'Payments', 'i18n' => 'payments'];
$navItems[] = ['page' => 'receipts', 'href' => 'models/receipts.php', 'icon' => 'bi-receipt', 'label' => 'Receipts', 'i18n' => 'receipts'];

// Admin and Owner only features
if (in_array($userRole, ['admin', 'owner'])) {
    $navItems[] = ['page' => 'reports', 'href' => 'models/reports.php', 'icon' => 'bi-bar-chart-line', 'label' => 'Reports', 'i18n' => 'reports'];
}

// Admin only features
if ($userRole === 'admin') {
    $navItems[] = ['page' => 'settings', 'href' => 'models/settings.php', 'icon' => 'bi-gear', 'label' => 'Settings', 'i18n' => 'settings'];
    $navItems[] = ['page' => 'users', 'href' => 'models/user_management.php', 'icon' => 'bi-people-fill', 'label' => 'User Management', 'i18n' => 'userManagement'];
    $navItems[] = ['page' => 'security', 'href' => 'models/security_monitor.php', 'icon' => 'bi-shield-exclamation', 'label' => 'Security Monitor', 'i18n' => 'securityMonitor'];
}
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
    
    <?php foreach ($navItems as $item): ?>
        <?= sidebarLink(
            $item['page'],
            $item['href'],
            $item['icon'],
            $item['label'],
            $activePage,
            $item['i18n'] ?? '',
            $item['arrow'] ?? false
        ) ?>
    <?php endforeach; ?>
    
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
  const toggle  = document.getElementById('sidebarToggle');
  const sidebar = document.getElementById('garageSidebar');
  if (!toggle || !sidebar) return;
  toggle.addEventListener('click', function (e) {
    e.preventDefault();
    const compact = window.matchMedia('(max-width:991px)').matches;
    sidebar.classList.toggle(compact ? 'is-open' : 'is-collapsed');
    document.body.classList.toggle('garage-sidebar-collapsed',
      !compact && sidebar.classList.contains('is-collapsed'));
    // Let DataTables / Chart.js re-layout after the width transition
    window.dispatchEvent(new Event('resize'));
    setTimeout(function () { window.dispatchEvent(new Event('resize')); }, 300);
  });
});
</script>
