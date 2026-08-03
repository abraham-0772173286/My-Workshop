<?php
declare(strict_types=1);

function database_connection(): mysqli
{
    $server = 'localhost';
    $username = 'root';
    $password = '2212Aa@0';
    $database = 'workshop';

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $connection = new mysqli($server, $username, $password);
    $connection->set_charset('utf8mb4');
    $connection->query("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $connection->select_db($database);

    return $connection;
}

function ensure_workshop_schema(mysqli $connection): void
{
    $connection->query('CREATE TABLE IF NOT EXISTS customers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        fullname VARCHAR(120) NOT NULL,
        contact VARCHAR(40) NOT NULL UNIQUE,
        address VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB');

    // Ensure required columns exist in customers table
    $result = $connection->query('DESCRIBE customers');
    $columnMap = [];
    while ($row = $result->fetch_assoc()) {
        $columnMap[$row['Field']] = true;
    }
    
    if (!isset($columnMap['contact'])) {
        $connection->query('ALTER TABLE customers ADD COLUMN contact VARCHAR(40) NOT NULL UNIQUE AFTER fullname');
    }
    if (!isset($columnMap['address'])) {
        $connection->query('ALTER TABLE customers ADD COLUMN address VARCHAR(255) NULL AFTER contact');
    }

    $connection->query('CREATE TABLE IF NOT EXISTS vehicles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT NOT NULL,
        car_owner VARCHAR(120) NOT NULL,
        plate_number VARCHAR(25) NOT NULL UNIQUE,
        model VARCHAR(120) NULL,
        date_received DATE NOT NULL,
        CONSTRAINT fk_vehicles_customer FOREIGN KEY (customer_id) REFERENCES customers(id)
    ) ENGINE=InnoDB');

    $connection->query("CREATE TABLE IF NOT EXISTS repair_jobs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        vehicle_id INT NOT NULL,
        repair_type VARCHAR(255) NOT NULL,
        parts_cost DECIMAL(12,2) NOT NULL DEFAULT 0,
        labour_cost DECIMAL(12,2) NOT NULL DEFAULT 0,
        status ENUM('REPAIR PENDING', 'REPAIR DONE') NOT NULL DEFAULT 'REPAIR PENDING',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_repair_jobs_vehicle FOREIGN KEY (vehicle_id) REFERENCES vehicles(id)
    ) ENGINE=InnoDB");

    // Drop any legacy NOT NULL columns that were added before this schema was finalised
    $rjCols = [];
    $rjResult = $connection->query('DESCRIBE repair_jobs');
    while ($row = $rjResult->fetch_assoc()) {
        $rjCols[$row['Field']] = $row;
    }
    // 'problem' column existed in an earlier version with no default — give it one so old rows don't break inserts
    if (isset($rjCols['problem']) && $rjCols['problem']['Default'] === null && $rjCols['problem']['Null'] === 'NO') {
        $connection->query("ALTER TABLE repair_jobs MODIFY COLUMN problem VARCHAR(255) NOT NULL DEFAULT ''");
    }
}
