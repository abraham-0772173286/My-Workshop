<?php
declare(strict_types=1);

require_once __DIR__ . '/../inc/app.php';
require_once __DIR__ . '/../configs/database.php';

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
$selectedRole = (string) ($_POST['selectedRole'] ?? '');
$deviceId = trim((string) ($_POST['deviceId'] ?? ''));

if ($username === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => 'Username and password are required.']);
    exit;
}

try {
    $pdo = get_database_connection();
    
    // Get user from database
    $stmt = $pdo->prepare("
        SELECT id, username, password_hash, full_name, role, status, failed_attempts, locked_until
        FROM users 
        WHERE username = ? AND status != 'suspended'
    ");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        // Log failed attempt for non-existent user
        error_log("Login attempt for non-existent user: $username from IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        http_response_code(401);
        echo json_encode(['status' => 'error', 'msg' => 'Invalid username or password.']);
        exit;
    }
    
    // Check if account is locked
    if ($user['status'] === 'locked') {
        // Check if time-based lock has expired
        if ($user['locked_until'] && new DateTime($user['locked_until']) <= new DateTime()) {
            // Automatically unlock the account
            $unlockStmt = $pdo->prepare("
                UPDATE users 
                SET status = 'active', locked_until = NULL, failed_attempts = 0
                WHERE id = ?
            ");
            $unlockStmt->execute([$user['id']]);
            
            // Update user data for continued processing
            $user['status'] = 'active';
            $user['locked_until'] = null;
            $user['failed_attempts'] = 0;
            
            error_log("Auto-unlocked user: {$user['username']} (time-based lock expired)");
        } else {
            // Still locked
            $lockMessage = $user['locked_until'] 
                ? 'Account is temporarily locked until ' . (new DateTime($user['locked_until']))->format('Y-m-d H:i:s')
                : 'Account is permanently locked. Please contact administrator.';
                
            http_response_code(423);
            echo json_encode(['status' => 'error', 'msg' => $lockMessage]);
            exit;
        }
    } elseif ($user['locked_until'] && new DateTime($user['locked_until']) > new DateTime()) {
        // Account has a future lock time but status is not locked - enforce the lock
        http_response_code(423);
        echo json_encode([
            'status' => 'error', 
            'msg' => 'Account is temporarily locked until ' . (new DateTime($user['locked_until']))->format('Y-m-d H:i:s')
        ]);
        exit;
    }
    
    // Verify password
    if (!password_verify($password, $user['password_hash'])) {
        // Increment failed attempts
        $failedAttempts = $user['failed_attempts'] + 1;
        $lockUntil = null;
        
        // Lock account after 5 failed attempts for 15 minutes
        if ($failedAttempts >= 5) {
            $lockUntil = (new DateTime())->add(new DateInterval('PT15M'))->format('Y-m-d H:i:s');
        }
        
        $updateStmt = $pdo->prepare("
            UPDATE users 
            SET failed_attempts = ?, locked_until = ?, status = IF(? >= 5, 'locked', status)
            WHERE id = ?
        ");
        $updateStmt->execute([$failedAttempts, $lockUntil, $failedAttempts, $user['id']]);
        
        error_log("Failed login attempt for user: $username (attempt $failedAttempts) from IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
        
        http_response_code(401);
        echo json_encode([
            'status' => 'error', 
            'msg' => $failedAttempts >= 5 ? 'Account locked due to multiple failed attempts.' : 'Invalid username or password.'
        ]);
        exit;
    }
    
    // Check role permissions if specific role was selected
    if ($selectedRole && $selectedRole !== $user['role']) {
        // Allow admin and owner to access any role, but cashier can only access cashier
        if ($user['role'] !== 'admin' && $user['role'] !== 'owner') {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'msg' => 'Insufficient permissions for selected role.']);
            exit;
        }
    }
    
    // Successful login - reset failed attempts and update last login
    $updateStmt = $pdo->prepare("
        UPDATE users 
        SET failed_attempts = 0, locked_until = NULL, last_login = NOW(), status = 'active'
        WHERE id = ?
    ");
    $updateStmt->execute([$user['id']]);
    
    // Create session
    session_regenerate_id(true);
    $_SESSION['user'] = [
        'id' => $user['id'],
        'username' => $user['username'],
        'name' => $user['full_name'],
        'role' => $user['role'],
        'effective_role' => $selectedRole ?: $user['role'], // Role being used for this session
        'device_id' => $deviceId,
        'logged_in_at' => date(DATE_ATOM),
        'permissions' => getUserPermissions($user['role'])
    ];
    
    // Log successful login
    error_log("Successful login: {$user['username']} (role: {$user['role']}) from IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    
    echo json_encode([
        'status' => 'success',
        'msg' => 'Login successful.',
        'redirect' => workshop_base_path() . 'public/',
        'user' => [
            'name' => $user['full_name'],
            'role' => $user['role'],
            'effective_role' => $selectedRole ?: $user['role']
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => 'Login service temporarily unavailable.']);
}

/**
 * Get user permissions based on role
 */
function getUserPermissions(string $role): array {
    $permissions = [
        'admin' => [
            'view_dashboard' => true,
            'manage_users' => true,
            'view_all_data' => true,
            'manage_customers' => true,
            'manage_vehicles' => true,
            'manage_repair_jobs' => true,
            'view_payments' => true,
            'manage_payments' => true,
            'view_receipts' => true,
            'manage_receipts' => true,
            'view_reports' => true,
            'manage_settings' => true,
            'delete_records' => true,
            'export_data' => true
        ],
        'owner' => [
            'view_dashboard' => true,
            'view_all_data' => true,
            'manage_customers' => true,
            'manage_vehicles' => true,
            'manage_repair_jobs' => true,
            'view_payments' => true,
            'manage_payments' => true,
            'view_receipts' => true,
            'manage_receipts' => true,
            'view_reports' => true,
            'delete_records' => true,
            'export_data' => true
        ],
        'cashier' => [
            'view_dashboard' => true,
            'manage_customers' => true,
            'manage_vehicles' => true,
            'manage_repair_jobs' => true,
            'view_payments' => true,
            'manage_payments' => true,
            'view_receipts' => true,
            'manage_receipts' => true
        ]
    ];
    
    return $permissions[$role] ?? [];
}
