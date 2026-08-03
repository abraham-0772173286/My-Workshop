<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
session_save_path(__DIR__ . '/.sessions');
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || ($_GET['f'] ?? '') !== 'login') {
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
    'admin' =>[
        'name' => 'Garage Owner',
        'role' => 'admin',
        'password_hash' => '$2y$10$IgTJblHVGrW0Sc3TESd0le/ZzRYYTC6DhORdop55r4XQ1lX9Gie4.',
    ],
    'cashier' =>[
        'name' => 'Cashier',
        'role' => 'cashier',
        'password_hash' => '$2y$10$gR89LqV/Eq9XIhxC.avXx.CnQRNsIwug1Uupby3v/yNVn/lD/oY9W',
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
    'redirect' => '../public/',
]);
