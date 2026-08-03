<?php
/**
 * sidebar.php — shared sidebar include.
 * Set $activePage before including:
 *   $activePage = 'dashboard' | 'repair_jobs' | 'customers' | 'vehicles' | ...
 */
$activePage = $activePage ?? '';
function sidebarLink(string $page, string $href, string $icon, string $label, string $active, bool $arrow = false): string {
    $cls = $active === $page ? 'nav-link active' : 'nav-link';
    $arr = $arrow ? '<i class="bi bi-chevron-right nav-arrow"></i>' : '';
    return "<div class=\"nav-item\"><a href=\"{$href}\" class=\"{$cls}\"><i class=\"bi {$icon}\"></i><span>{$label}</span>{$arr}</a></div>";
}
?>
<aside class="garage-sidebar" id="garageSidebar">
  <a href="index.php" class="sidebar-brand">
    <span class="brand-mark"><i class="bi bi-wrench-adjustable"></i></span>
    <span class="brand-name">SHENGCHI AUTO LTD<small>金龙汽车维修</small></span>
  </a>
  <nav class="nav-sidebar">
    <div class="nav-header">WORKSPACE</div>
    <?= sidebarLink('dashboard',   'index.php',       'bi-speedometer2',    'Dashboard',   $activePage) ?>
    <?= sidebarLink('repair_jobs', 'repair_jobs.php', 'bi-clipboard2-check','Repair Jobs', $activePage) ?>
    <?= sidebarLink('customers',   'customers.php',   'bi-people',          'Customers',   $activePage) ?>
    <?= sidebarLink('vehicles',    'vehicles.php',    'bi-car-front',       'Vehicles',    $activePage) ?>
    <?= sidebarLink('payments',    '#',               'bi-cash-stack',      'Payments',    $activePage, true) ?>
    <?= sidebarLink('receipts',    '#',               'bi-receipt',         'Receipts',    $activePage, true) ?>
    <?= sidebarLink('reports',     '#',               'bi-bar-chart-line',  'Reports',     $activePage, true) ?>
    <?= sidebarLink('settings',    '#',               'bi-gear',            'Settings',    $activePage, true) ?>
    <div class="nav-item"><a href="../classes/Login.php?f=logout" class="nav-link"><i class="bi bi-box-arrow-left"></i><span>Sign Out</span></a></div>
    <div class="sidebar-footer"><i class="bi bi-shield-check me-1"></i> Garage operations system</div>
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
  });
});
</script>
