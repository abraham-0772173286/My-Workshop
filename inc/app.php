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
