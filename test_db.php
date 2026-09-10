<?php
declare(strict_types=1);

require_once __DIR__ . '/configs/database.php';

try {
    echo "Testing database connection...\n";
    $connection = database_connection();
    echo "✓ Connected to MySQL\n";
    
    echo "Setting up schema...\n";
    ensure_workshop_schema($connection);
    echo "✓ Schema created/verified\n";
    
    // Check tables
    $result = $connection->query("SHOW TABLES FROM workshop");
    echo "\nTables in workshop database:\n";
    while ($table = $result->fetch_row()) {
        echo "- " . $table[0] . "\n";
    }
    
    echo "\n✓ All checks passed!\n";
    
} catch (Throwable $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";
}
