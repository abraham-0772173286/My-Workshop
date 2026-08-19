<?php
require_once __DIR__ . '/configs/database.php';

try {
    echo "<h2>Setting up Workshop Database...</h2>\n";
    
    $pdo = get_database_connection();
    echo "<p>✓ Database connection successful</p>\n";
    
    // Create users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `users` (
            `id`                INT          NOT NULL AUTO_INCREMENT,
            `username`          VARCHAR(50)  NOT NULL               COMMENT 'Unique login username',
            `password_hash`     VARCHAR(255) NOT NULL               COMMENT 'Bcrypt hashed password',
            `full_name`         VARCHAR(120) NOT NULL               COMMENT 'Display name of the user',
            `role`              ENUM(
                                  'admin',
                                  'owner',
                                  'cashier'
                                )            NOT NULL DEFAULT 'cashier' COMMENT 'User access level',
            `status`            ENUM(
                                  'active',
                                  'locked',
                                  'suspended'
                                )            NOT NULL DEFAULT 'active'  COMMENT 'Account status',
            `last_login`        TIMESTAMP        NULL               COMMENT 'Last successful login time',
            `failed_attempts`   INT          NOT NULL DEFAULT 0     COMMENT 'Failed login attempts counter',
            `locked_until`      TIMESTAMP        NULL               COMMENT 'Account lock expiration time',
            `created_at`        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at`        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uq_users_username` (`username`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
          COMMENT='System users with role-based access control'
    ");
    echo "<p>✓ Users table created/verified</p>\n";
    
    // Check if users exist, if not create them
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    
    if ($result['count'] == 0) {
        echo "<p>Creating default users...</p>\n";
        
        // Password: 2212Aa@0
        $passwordHash = '$2y$10$vxtWqddpnjyYKLa1nQUMJOdpFpJKWqrpfLIve9rOBdDazkPW76YVO';
        
        $users = [
            ['admin', $passwordHash, 'System Administrator', 'admin'],
            ['owner', $passwordHash, 'Garage Owner', 'owner'],
            ['cashier', $passwordHash, 'Cashier Staff', 'cashier']
        ];
        
        $insertStmt = $pdo->prepare("
            INSERT INTO users (username, password_hash, full_name, role) 
            VALUES (?, ?, ?, ?)
        ");
        
        foreach ($users as $user) {
            $insertStmt->execute($user);
            echo "<p>✓ Created user: {$user[0]} ({$user[3]})</p>\n";
        }
    } else {
        echo "<p>✓ Users already exist in database</p>\n";
    }
    
    echo "<h3>Setup Complete!</h3>\n";
    echo "<p>You can now log in with:</p>\n";
    echo "<ul>\n";
    echo "<li><strong>Admin:</strong> username: admin, password: 2212Aa@0</li>\n";
    echo "<li><strong>Owner:</strong> username: owner, password: 2212Aa@0</li>\n";
    echo "<li><strong>Cashier:</strong> username: cashier, password: 2212Aa@0</li>\n";
    echo "</ul>\n";
    echo "<p><a href='inc/landing.php'>Go to Login Page</a></p>\n";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>\n";
}
?>