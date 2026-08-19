<?php
declare(strict_types=1);

function workshop_session_start(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_save_path(__DIR__ . '/../classes/.sessions');
        session_start();
    }
}

/** Web path to project root, e.g. /workshop/ */
function workshop_base_path(): string
{
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $dir = dirname($script);
    if (str_ends_with($dir, '/public') || str_ends_with($dir, '/inc') || str_ends_with($dir, '/classes')) {
        $dir = dirname($dir);
    }

    return rtrim($dir, '/') . '/';
}

function workshop_require_login(): void
{
    workshop_session_start();
    if (empty($_SESSION['user'])) {
        header('Location: ' . workshop_base_path() . 'inc/landing.php');
        exit;
    }
}

function workshop_redirect_if_logged_in(): void
{
    workshop_session_start();
    if (!empty($_SESSION['user'])) {
        header('Location: ' . workshop_base_path() . 'public/');
        exit;
    }
}

/**
 * Check if current user has a specific permission
 */
function workshop_has_permission(string $permission): bool
{
    workshop_session_start();
    
    if (empty($_SESSION['user']) || empty($_SESSION['user']['permissions'])) {
        return false;
    }
    
    return $_SESSION['user']['permissions'][$permission] ?? false;
}

/**
 * Require specific permission or redirect to access denied page
 */
function workshop_require_permission(string $permission, string $redirectUrl = null): void
{
    if (!workshop_has_permission($permission)) {
        if ($redirectUrl) {
            header("Location: $redirectUrl");
        } else {
            http_response_code(403);
            include __DIR__ . '/users/403.html';
        }
        exit;
    }
}

/**
 * Check if current user has any of the specified roles
 */
function workshop_has_role(string ...$roles): bool
{
    workshop_session_start();
    
    if (empty($_SESSION['user'])) {
        return false;
    }
    
    $userRole = $_SESSION['user']['effective_role'] ?? $_SESSION['user']['role'] ?? '';
    return in_array($userRole, $roles, true);
}

/**
 * Require specific role(s) or redirect to access denied page
 */
function workshop_require_role(string ...$roles): void
{
    if (!workshop_has_role(...$roles)) {
        http_response_code(403);
        include __DIR__ . '/users/403.html';
        exit;
    }
}

/**
 * Get current user information
 */
function workshop_get_user(): ?array
{
    workshop_session_start();
    return $_SESSION['user'] ?? null;
}

/**
 * Check if current user is admin
 */
function workshop_is_admin(): bool
{
    return workshop_has_role('admin');
}

/**
 * Check if current user is owner
 */
function workshop_is_owner(): bool
{
    return workshop_has_role('owner');
}

/**
 * Check if current user is cashier
 */
function workshop_is_cashier(): bool
{
    return workshop_has_role('cashier');
}

/**
 * Filter navigation items based on user permissions
 */
function workshop_filter_nav_items(array $items): array
{
    $user = workshop_get_user();
    if (!$user) {
        return [];
    }
    
    $role = $user['effective_role'] ?? $user['role'] ?? '';
    
    // Define which nav items are available for each role
    $roleNavItems = [
        'admin' => ['dashboard', 'repair_jobs', 'customers', 'vehicles', 'payments', 'receipts', 'reports', 'settings', 'users'],
        'owner' => ['dashboard', 'repair_jobs', 'customers', 'vehicles', 'payments', 'receipts', 'reports'],
        'cashier' => ['dashboard', 'repair_jobs', 'customers', 'vehicles', 'payments', 'receipts']
    ];
    
    $allowedItems = $roleNavItems[$role] ?? [];
    
    return array_filter($items, function($item) use ($allowedItems) {
        return in_array($item['key'], $allowedItems, true);
    });
}
