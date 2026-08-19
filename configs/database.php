<?php
declare(strict_types=1);

function database_connection(): mysqli
{
    $server = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'workshop';

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $connection = new mysqli($server, $username, $password);
    $connection->set_charset('utf8mb4');
    $connection->query("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $connection->select_db($database);

    return $connection;
}

function get_database_connection(): PDO
{
    $server = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'workshop';

    $dsn = "mysql:host=$server;dbname=$database;charset=utf8mb4";
    
    try {
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        
        // Create database if it doesn't exist
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `$database`");
        
        return $pdo;
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        throw new Exception("Database connection failed.");
    }
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

    $connection->query("CREATE TABLE IF NOT EXISTS payments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        repair_job_id INT NOT NULL,
        amount_paid DECIMAL(12,2) NOT NULL DEFAULT 0,
        payment_method ENUM('CASH', 'MPESA', 'BANK TRANSFER', 'OTHER') NOT NULL DEFAULT 'CASH',
        reference VARCHAR(80) NULL,
        paid_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_payments_repair_job FOREIGN KEY (repair_job_id) REFERENCES repair_jobs(id)
    ) ENGINE=InnoDB");

    $connection->query("CREATE TABLE IF NOT EXISTS receipts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        payment_id INT NOT NULL,
        receipt_no VARCHAR(30) NOT NULL UNIQUE,
        issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_receipts_payment FOREIGN KEY (payment_id) REFERENCES payments(id)
    ) ENGINE=InnoDB");

    $connection->query("CREATE TABLE IF NOT EXISTS drivers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        vehicle_id INT NOT NULL,
        driver_name VARCHAR(120) NOT NULL,
        vehicle_type VARCHAR(80) NOT NULL DEFAULT '',
        model_year VARCHAR(30) NULL,
        driver_mobile VARCHAR(30) NULL,
        time_in DATETIME NULL,
        time_out DATETIME NULL,
        description TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        CONSTRAINT fk_drivers_vehicle FOREIGN KEY (vehicle_id) REFERENCES vehicles(id)
            ON UPDATE CASCADE ON DELETE RESTRICT
    ) ENGINE=InnoDB");
}
