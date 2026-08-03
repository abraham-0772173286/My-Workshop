<?php
declare(strict_types=1);

require_once __DIR__ . '/../inc/app.php';

workshop_session_start();

$f = (string) ($_GET['f'] ?? '');

if ($f === 'logout') {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
    }
    session_destroy();
    header('Location: ' . workshop_base_path() . 'inc/landing.php');
    exit;
}

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $f !== 'login') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'msg' => 'Login requests must use POST.']);
    exit;
}

$username = trim((string) ($_POST['username'] ?? ''));
$password = (string) ($_POST['password'] ?? '');

/*
 * Starter accounts for the UI prototype. Replace this list with a users-table
 * lookup once the database layer is added.
 */
$accounts = [
    'admin' => [
        'name' => 'Garage Owner',
        'role' => 'admin',
        'password_hash' => '$2y$10$vxtWqddpnjyYKLa1nQUMJOdpFpJKWqrpfLIve9rOBdDazkPW76YVO', // 2212Aa@0
    ],
    'cashier' => [
        'name' => 'Cashier',
        'role' => 'cashier',
        'password_hash' => '$2y$10$vxtWqddpnjyYKLa1nQUMJOdpFpJKWqrpfLIve9rOBdDazkPW76YVO', // 2212Aa@0
    ],
];

if ($username === '' || $password === '' || !isset($accounts[$username]) || !password_verify($password, $accounts[$username]['password_hash'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'msg' => 'Invalid username or password.']);
    exit;
}

session_regenerate_id(true);
$_SESSION['user'] = [
    'username' => $username,
    'name' => $accounts[$username]['name'],
    'role' => $accounts[$username]['role'],
    'device_id' => trim((string) ($_POST['deviceId'] ?? '')),
    'logged_in_at' => date(DATE_ATOM),
];

echo json_encode([
    'status' => 'success',
    'msg' => 'Login successful.',
    'redirect' => workshop_base_path() . 'public/',
]);
