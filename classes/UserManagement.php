<?php
declare(strict_types=1);

require_once __DIR__ . '/../inc/app.php';
require_once __DIR__ . '/../configs/database.php';

workshop_session_start();

header('Content-Type: application/json; charset=utf-8');

// Only admins can access user management
if (!workshop_has_role('admin')) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'msg' => 'Access denied. Admin privileges required.']);
    exit;
}

$f = (string) ($_GET['f'] ?? '');

try {
    $pdo = get_database_connection();
    
    switch ($f) {
        case 'list_users':
            listUsers($pdo);
            break;
            
        case 'lock_user':
            lockUser($pdo);
            break;
            
        case 'unlock_user':
            unlockUser($pdo);
            break;
            
        case 'reset_password':
            resetPassword($pdo);
            break;
            
        case 'create_user':
            createUser($pdo);
            break;
            
        case 'update_user':
            updateUser($pdo);
            break;
            
        case 'get_user_details':
            getUserDetails($pdo);
            break;
            
        case 'get_login_attempts':
            getLoginAttempts($pdo);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['status' => 'error', 'msg' => 'Invalid function specified.']);
    }
    
} catch (Exception $e) {
    error_log("UserManagement error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => 'Internal server error.']);
}

function listUsers(PDO $pdo): void {
    $stmt = $pdo->prepare("
        SELECT 
            id, username, full_name, role, status, 
            last_login, failed_attempts, locked_until,
            created_at, updated_at
        FROM users 
        ORDER BY 
            CASE role 
                WHEN 'admin' THEN 1 
                WHEN 'owner' THEN 2 
                WHEN 'cashier' THEN 3 
            END,
            full_name
    ");
    $stmt->execute();
    $users = $stmt->fetchAll();
    
    // Format the data for frontend
    $formattedUsers = array_map(function($user) {
        return [
            'id' => (int) $user['id'],
            'username' => $user['username'],
            'full_name' => $user['full_name'],
            'role' => $user['role'],
            'status' => $user['status'],
            'last_login' => $user['last_login'],
            'failed_attempts' => (int) $user['failed_attempts'],
            'locked_until' => $user['locked_until'],
            'is_locked' => $user['status'] === 'locked' || 
                          ($user['locked_until'] && new DateTime($user['locked_until']) > new DateTime()),
            'created_at' => $user['created_at'],
            'updated_at' => $user['updated_at']
        ];
    }, $users);
    
    echo json_encode([
        'status' => 'success',
        'users' => $formattedUsers,
        'total' => count($formattedUsers)
    ]);
}

function lockUser(PDO $pdo): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['status' => 'error', 'msg' => 'POST method required.']);
        return;
    }
    
    $userId = (int) ($_POST['user_id'] ?? 0);
    $reason = trim((string) ($_POST['reason'] ?? 'Manual lock by administrator'));
    $duration = (int) ($_POST['duration'] ?? 0); // Duration in minutes, 0 = permanent
    
    if ($userId <= 0) {
        echo json_encode(['status' => 'error', 'msg' => 'Invalid user ID.']);
        return;
    }
    
    // Check if user exists and is not the current admin
    $stmt = $pdo->prepare("SELECT username, role, status FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo json_encode(['status' => 'error', 'msg' => 'User not found.']);
        return;
    }
    
    // Prevent admin from locking themselves
    if ($user['username'] === $_SESSION['user']['username']) {
        echo json_encode(['status' => 'error', 'msg' => 'Cannot lock your own account.']);
        return;
    }
    
    // Calculate lock expiration
    $lockedUntil = null;
    if ($duration > 0) {
        $lockedUntil = (new DateTime())->add(new DateInterval("PT{$duration}M"))->format('Y-m-d H:i:s');
    }
    
    // Update user status
    $stmt = $pdo->prepare("
        UPDATE users 
        SET status = 'locked', locked_until = ?, updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$lockedUntil, $userId]);
    
    // Log the action
    logUserAction($pdo, $userId, 'LOCKED', $reason, $_SESSION['user']['id']);
    
    $lockType = $duration > 0 ? "for {$duration} minutes" : "permanently";
    echo json_encode([
        'status' => 'success',
        'msg' => "User '{$user['username']}' locked {$lockType}."
    ]);
}

function unlockUser(PDO $pdo): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['status' => 'error', 'msg' => 'POST method required.']);
        return;
    }
    
    $userId = (int) ($_POST['user_id'] ?? 0);
    $reason = trim((string) ($_POST['reason'] ?? 'Manual unlock by administrator'));
    
    if ($userId <= 0) {
        echo json_encode(['status' => 'error', 'msg' => 'Invalid user ID.']);
        return;
    }
    
    // Check if user exists
    $stmt = $pdo->prepare("SELECT username, status FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo json_encode(['status' => 'error', 'msg' => 'User not found.']);
        return;
    }
    
    // Update user status
    $stmt = $pdo->prepare("
        UPDATE users 
        SET status = 'active', locked_until = NULL, failed_attempts = 0, updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$userId]);
    
    // Log the action
    logUserAction($pdo, $userId, 'UNLOCKED', $reason, $_SESSION['user']['id']);
    
    echo json_encode([
        'status' => 'success',
        'msg' => "User '{$user['username']}' unlocked successfully."
    ]);
}

function resetPassword(PDO $pdo): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['status' => 'error', 'msg' => 'POST method required.']);
        return;
    }
    
    $userId = (int) ($_POST['user_id'] ?? 0);
    $newPassword = (string) ($_POST['new_password'] ?? '');
    $reason = trim((string) ($_POST['reason'] ?? 'Password reset by administrator'));
    
    if ($userId <= 0 || strlen($newPassword) < 8) {
        echo json_encode(['status' => 'error', 'msg' => 'Invalid user ID or password too short (minimum 8 characters).']);
        return;
    }
    
    // Check if user exists
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo json_encode(['status' => 'error', 'msg' => 'User not found.']);
        return;
    }
    
    // Hash the new password
    $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
    
    // Update password and reset failed attempts
    $stmt = $pdo->prepare("
        UPDATE users 
        SET password_hash = ?, failed_attempts = 0, locked_until = NULL, updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$passwordHash, $userId]);
    
    // Log the action
    logUserAction($pdo, $userId, 'PASSWORD_RESET', $reason, $_SESSION['user']['id']);
    
    echo json_encode([
        'status' => 'success',
        'msg' => "Password reset successfully for user '{$user['username']}'."
    ]);
}

function createUser(PDO $pdo): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['status' => 'error', 'msg' => 'POST method required.']);
        return;
    }
    
    $username = trim((string) ($_POST['username'] ?? ''));
    $fullName = trim((string) ($_POST['full_name'] ?? ''));
    $role = (string) ($_POST['role'] ?? '');
    $password = (string) ($_POST['password'] ?? '');
    
    // Validation
    if (empty($username) || empty($fullName) || empty($role) || empty($password)) {
        echo json_encode(['status' => 'error', 'msg' => 'All fields are required.']);
        return;
    }
    
    if (!in_array($role, ['admin', 'owner', 'cashier'])) {
        echo json_encode(['status' => 'error', 'msg' => 'Invalid role specified.']);
        return;
    }
    
    if (strlen($password) < 8) {
        echo json_encode(['status' => 'error', 'msg' => 'Password must be at least 8 characters long.']);
        return;
    }
    
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        echo json_encode(['status' => 'error', 'msg' => 'Username can only contain letters, numbers, and underscores.']);
        return;
    }
    
    // Check if username already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        echo json_encode(['status' => 'error', 'msg' => 'Username already exists.']);
        return;
    }
    
    // Create user
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("
        INSERT INTO users (username, password_hash, full_name, role, status, created_at, updated_at)
        VALUES (?, ?, ?, ?, 'active', NOW(), NOW())
    ");
    $stmt->execute([$username, $passwordHash, $fullName, $role]);
    $newUserId = $pdo->lastInsertId();
    
    // Log the action
    logUserAction($pdo, $newUserId, 'CREATED', "User created by administrator", $_SESSION['user']['id']);
    
    echo json_encode([
        'status' => 'success',
        'msg' => "User '{$username}' created successfully.",
        'user_id' => $newUserId
    ]);
}

function updateUser(PDO $pdo): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['status' => 'error', 'msg' => 'POST method required.']);
        return;
    }
    
    $userId = (int) ($_POST['user_id'] ?? 0);
    $fullName = trim((string) ($_POST['full_name'] ?? ''));
    $role = (string) ($_POST['role'] ?? '');
    
    if ($userId <= 0 || empty($fullName) || empty($role)) {
        echo json_encode(['status' => 'error', 'msg' => 'User ID, full name, and role are required.']);
        return;
    }
    
    if (!in_array($role, ['admin', 'owner', 'cashier'])) {
        echo json_encode(['status' => 'error', 'msg' => 'Invalid role specified.']);
        return;
    }
    
    // Check if user exists
    $stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo json_encode(['status' => 'error', 'msg' => 'User not found.']);
        return;
    }
    
    // Update user
    $stmt = $pdo->prepare("
        UPDATE users 
        SET full_name = ?, role = ?, updated_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$fullName, $role, $userId]);
    
    // Log the action
    logUserAction($pdo, $userId, 'UPDATED', "User details updated by administrator", $_SESSION['user']['id']);
    
    echo json_encode([
        'status' => 'success',
        'msg' => "User '{$user['username']}' updated successfully."
    ]);
}

function getUserDetails(PDO $pdo): void {
    $userId = (int) ($_GET['user_id'] ?? 0);
    
    if ($userId <= 0) {
        echo json_encode(['status' => 'error', 'msg' => 'Invalid user ID.']);
        return;
    }
    
    $stmt = $pdo->prepare("
        SELECT id, username, full_name, role, status, last_login, failed_attempts, 
               locked_until, created_at, updated_at
        FROM users 
        WHERE id = ?
    ");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo json_encode(['status' => 'error', 'msg' => 'User not found.']);
        return;
    }
    
    echo json_encode([
        'status' => 'success',
        'user' => $user
    ]);
}

function getLoginAttempts(PDO $pdo): void {
    $userId = (int) ($_GET['user_id'] ?? 0);
    
    if ($userId <= 0) {
        echo json_encode(['status' => 'error', 'msg' => 'Invalid user ID.']);
        return;
    }
    
    // Get user action logs (we'll create this table)
    try {
        $stmt = $pdo->prepare("
            SELECT action, reason, performed_at, performed_by_user_id
            FROM user_action_logs 
            WHERE user_id = ? 
            ORDER BY performed_at DESC 
            LIMIT 20
        ");
        $stmt->execute([$userId]);
        $logs = $stmt->fetchAll();
        
        echo json_encode([
            'status' => 'success',
            'logs' => $logs
        ]);
    } catch (Exception $e) {
        // Table doesn't exist yet, return empty
        echo json_encode([
            'status' => 'success',
            'logs' => []
        ]);
    }
}

function logUserAction(PDO $pdo, int $userId, string $action, string $reason, int $performedBy): void {
    try {
        // Create table if it doesn't exist
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS user_action_logs (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                action ENUM('LOCKED', 'UNLOCKED', 'PASSWORD_RESET', 'CREATED', 'UPDATED', 'LOGIN_SUCCESS', 'LOGIN_FAILED') NOT NULL,
                reason TEXT,
                performed_by_user_id INT,
                performed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id),
                FOREIGN KEY (performed_by_user_id) REFERENCES users(id)
            ) ENGINE=InnoDB
        ");
        
        $stmt = $pdo->prepare("
            INSERT INTO user_action_logs (user_id, action, reason, performed_by_user_id)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$userId, $action, $reason, $performedBy]);
    } catch (Exception $e) {
        error_log("Failed to log user action: " . $e->getMessage());
    }
}
?>