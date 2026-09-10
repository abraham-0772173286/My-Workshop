-- ============================================================
--  SHENGCHI AUTO LTD (金龙汽车维修)
--  Workshop Management System — Full Database Schema

-- ============================================================

-- Drop and recreate for a clean slate (remove these two lines if you
-- want to keep existing data and just add missing tables)
--DROP DATABASE IF EXISTS `workshop`;
--CREATE DATABASE `workshop`
    --CHARACTER SET utf8mb4
    --COLLATE utf8mb4_unicode_ci;

USE `workshop`;

-- ============================================================
-- TABLE 0 — users (User Authentication & Role Management)
-- User accounts with role-based access control
-- ============================================================
CREATE TABLE `users` (
    `id`INT NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50)  NOT NULL COMMENT 'Unique login username',
    `password_hash` VARCHAR(255) NOT NULL COMMENT 'Bcrypt hashed password',
    `full_name` VARCHAR(120) NOT NULL COMMENT 'Display name of the user',
    `role`ENUM('admin','owner','cashier') NOT NULL DEFAULT 'cashier' COMMENT 'User access level',
    `status`ENUM('active','locked','suspended')NOT NULL DEFAULT 'active'  COMMENT 'Account status',
    `last_login`TIMESTAMP NULL COMMENT 'Last successful login time',
    `failed_attempts` INT NOT NULL DEFAULT 0  COMMENT 'Failed login attempts counter',
    `locked_until` TIMESTAMP NULL COMMENT 'Account lock expiration time',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
UNIQUE KEY `uq_users_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='System users with role-based access control';

-- Insert default admin users
INSERT INTO `users` (`username`, `password_hash`, `full_name`, `role`) VALUES
('admin', '$2y$10$vxtWqddpnjyYKLa1nQUMJOdpFpJKWqrpfLIve9rOBdDazkPW76YVO', 'System Administrator', 'admin'),
('owner', '$2y$10$vxtWqddpnjyYKLa1nQUMJOdpFpJKWqrpfLIve9rOBdDazkPW76YVO', 'Garage Owner', 'owner'),
('cashier', '$2y$10$vxtWqddpnjyYKLa1nQUMJOdpFpJKWqrpfLIve9rOBdDazkPW76YVO', 'Cashier Staff', 'cashier');

-- ============================================================
-- TABLE 1 — customers
-- One row per customer. A customer can own many vehicles.
-- ============================================================
CREATE TABLE `customers` (
    `id`         INT           NOT NULL AUTO_INCREMENT,
    `fullname`   VARCHAR(120)  NOT NULL COMMENT 'Full name of the customer',
    `contact`    VARCHAR(40)   NOT NULL COMMENT 'Phone number — must be unique per active customer',
    `address`    VARCHAR(255)  NULL COMMENT 'Optional physical address',
    `created_at` TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `deleted_at` TIMESTAMP     NULL     DEFAULT NULL COMMENT 'Soft-delete timestamp — NULL means active',
    `deleted_by` INT           NULL     DEFAULT NULL COMMENT 'FK → users.id who deleted this customer',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_customers_contact` (`contact`),
    CONSTRAINT `fk_customers_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Registered vehicle owners (soft-delete enabled)';

-- ============================================================
-- TABLE 2 — vehicles
-- One row per vehicle (plate number is unique).
-- A vehicle belongs to one customer.
-- ============================================================
CREATE TABLE `vehicles` (
    `id`INT NOT NULL AUTO_INCREMENT,
    `customer_id`INT NOT NULL COMMENT 'FK → customers.id',
    `car_owner` VARCHAR(120) NOT NULL COMMENT 'Denormalised owner name for quick display',
    `plate_number` VARCHAR(25) NOT NULL COMMENT 'Registration plate, e.g. KDD 821T',
    `model` VARCHAR(120) NULL COMMENT 'Make / model, e.g. Toyota Prado',
    `date_received` DATE NOT NULL COMMENT 'Date the vehicle was dropped off',
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_vehicles_plate` (`plate_number`),
    CONSTRAINT `fk_vehicles_customer`
    FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Vehicles brought in for repair';

-- ============================================================
-- TABLE 3 — repair_jobs
-- One row per repair job.
-- A repair job belongs to one vehicle (and transitively one customer).
-- ============================================================
CREATE TABLE `repair_jobs` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `vehicle_id` INT NOT NULL COMMENT 'FK → vehicles.id',
    `repair_type` VARCHAR(255)  NOT NULL COMMENT 'Description of repairs performed',
    `parts_cost`  DECIMAL(12,2) NOT NULL DEFAULT 0.00  COMMENT 'Cost of spare parts (UGX)',
    `labour_cost` DECIMAL(12,2) NOT NULL DEFAULT 0.00  COMMENT 'Labour / service charge (UGX)',
    `status`ENUM(
                'REPAIR PENDING',
                'REPAIR DONE'
                ) NOT NULL DEFAULT 'REPAIR PENDING',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_repair_jobs_vehicle`
    FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`)ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Repair orders — one row per job';

-- ============================================================
-- TABLE 4 — payments
-- One payment row per repair job (can be extended to many).
-- ============================================================
CREATE TABLE `payments` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `repair_job_id` INT NOT NULL COMMENT 'FK → repair_jobs.id',
    `amount_paid` DECIMAL(12,2) NOT NULL DEFAULT 0.00  COMMENT 'Amount received (UGX)',
    `payment_method` ENUM('CASH','MOBILE MONEY','BANK')                       NOT NULL DEFAULT 'CASH',
    `reference` VARCHAR(80) NULL COMMENT 'M-Pesa code, cheque number, etc.',
    `paid_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_payments_repair_job`
        FOREIGN KEY (`repair_job_id`) REFERENCES `repair_jobs` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Payments received for completed repair jobs';

-- ============================================================
-- TABLE 5 — drivers
-- Driver registry — core driver information.
-- ============================================================
CREATE TABLE `drivers` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `driver_name` VARCHAR(120) NOT NULL COMMENT 'Full name of the driver',
    `driver_mobile` VARCHAR(30) NULL COMMENT 'Driver mobile phone number',
    `license_no` VARCHAR(50) NULL COMMENT 'Driving license number',
    `id_number` VARCHAR(50) NULL COMMENT 'National ID or passport number',
    `address` VARCHAR(255) NULL COMMENT 'Physical address',
    `emergency_contact` VARCHAR(120) NULL COMMENT 'Emergency contact person',
    `status` ENUM('active','inactive') NOT NULL DEFAULT 'active' COMMENT 'Account status',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Driver registry — core driver information';

-- ============================================================
-- TABLE 6 — driver_assignments
-- Links drivers to vehicles with date ranges.
-- ============================================================
CREATE TABLE `driver_assignments` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `driver_id` INT NOT NULL COMMENT 'FK → drivers.id',
    `vehicle_id` INT NOT NULL COMMENT 'FK → vehicles.id',
    `assigned_date` DATE NOT NULL COMMENT 'Date the assignment started',
    `return_date` DATE NULL COMMENT 'Date the vehicle was returned',
    `notes` TEXT NULL COMMENT 'Assignment notes',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_da_driver` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT `fk_da_vehicle` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Driver-vehicle assignments';

-- ============================================================
-- TABLE 7 — driver_trips
-- Individual trip records.
-- ============================================================
CREATE TABLE `driver_trips` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `driver_id` INT NOT NULL COMMENT 'FK → drivers.id',
    `vehicle_id` INT NOT NULL COMMENT 'FK → vehicles.id',
    `assignment_id` INT NULL COMMENT 'FK → driver_assignments.id',
    `trip_date` DATE NOT NULL COMMENT 'Date of the trip',
    `origin` VARCHAR(150) NOT NULL DEFAULT '' COMMENT 'Trip origin',
    `destination` VARCHAR(150) NOT NULL DEFAULT '' COMMENT 'Trip destination',
    `distance_km` DECIMAL(10,2) NULL DEFAULT 0 COMMENT 'Distance in kilometres',
    `start_time` DATETIME NULL COMMENT 'Trip start date/time',
    `end_time` DATETIME NULL COMMENT 'Trip end date/time',
    `fare` DECIMAL(12,2) NULL DEFAULT 0 COMMENT 'Trip fare (UGX)',
    `notes` TEXT NULL COMMENT 'Trip notes',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_dt_driver` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT `fk_dt_vehicle` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Driver trip log';

-- ============================================================
-- TABLE 8 — fuel_records
-- Fuel purchase / consumption records.
-- ============================================================
CREATE TABLE `fuel_records` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `driver_id` INT NULL COMMENT 'FK → drivers.id',
    `vehicle_id` INT NOT NULL COMMENT 'FK → vehicles.id',
    `trip_id` INT NULL COMMENT 'FK → driver_trips.id',
    `fuel_date` DATE NOT NULL COMMENT 'Date fuel was purchased',
    `liters` DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'Litres purchased',
    `cost_per_liter` DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'Cost per litre (UGX)',
    `total_cost` DECIMAL(12,2) NOT NULL DEFAULT 0 COMMENT 'Total cost (UGX)',
    `fuel_type` ENUM('DIESEL','PETROL','OTHER') NOT NULL DEFAULT 'DIESEL' COMMENT 'Fuel type',
    `station` VARCHAR(150) NULL COMMENT 'Fuel station name',
    `receipt_no` VARCHAR(80) NULL COMMENT 'Fuel receipt number',
    `notes` TEXT NULL COMMENT 'Additional notes',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_fr_driver` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT `fk_fr_vehicle` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT `fk_fr_trip` FOREIGN KEY (`trip_id`) REFERENCES `driver_trips` (`id`)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Fuel purchase and consumption records';

-- ============================================================
-- TABLE 9 — receipts
-- One receipt issued per payment.
-- ============================================================
CREATE TABLE `receipts` (
    `id`  INT NOT NULL AUTO_INCREMENT,
    `payment_id` INT NOT NULL COMMENT 'FK → payments.id',
    `receipt_no` VARCHAR(30) NOT NULL  COMMENT 'Human-readable receipt number, e.g. RC-000001',
    `issued_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_receipts_no` (`receipt_no`),
    CONSTRAINT `fk_receipts_payment`
    FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`)ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Receipts issued to customers';

-- ============================================================
-- USEFUL JOIN QUERIES
-- Run these in Workbench to view live data from the UI
-- ============================================================

-- ------------------------------------------------------------
-- Q1. Repair Jobs Register (mirrors the UI table exactly)
--     JOIN: repair_jobs → vehicles → customers
--     Excludes soft-deleted customers
-- ------------------------------------------------------------
SELECT CONCAT('RJ-', LPAD(rj.id, 5, '0')) AS `Job No`,
    c.fullname AS `Customer`,
    c.contact AS `Phone`,
    CONCAT(v.plate_number, ' · ',
    COALESCE(NULLIF(v.model,''), 'Vehicle')) AS `Vehicle`,
    rj.repair_type AS `Repair Type`,
    rj.parts_cost AS `Parts Cost (UGX)`,
    rj.labour_cost AS `Labour Cost (UGX)`,
    (rj.parts_cost + rj.labour_cost) AS `Total Cost (UGX)`,
    rj.status   AS `Status`,
    DATE_FORMAT(rj.created_at, '%d %b %Y') AS `Date`
FROM repair_jobs rj
INNER JOIN vehicles  v  ON v.id  = rj.vehicle_id
INNER JOIN customers c  ON c.id  = v.customer_id AND c.deleted_at IS NULL
ORDER BY rj.id DESC;

-- ------------------------------------------------------------
-- Q2. Full payment summary with receipt
--     JOIN: payments → repair_jobs → vehicles → customers → receipts
--     Excludes soft-deleted customers
-- ------------------------------------------------------------
SELECT
    CONCAT('RJ-', LPAD(rj.id, 5, '0')) AS `Job No`,
    c.fullname  AS `Customer`,
    v.plate_number  AS `Plate`,
    (rj.parts_cost + rj.labour_cost) AS `Job Total (UGX)`,
    p.amount_paid  AS `Paid (UGX)`,
    p.payment_method AS `Method`,
    p.reference  AS `Reference`,
    r.receipt_no AS `Receipt No`,
    DATE_FORMAT(p.paid_at, '%d %b %Y') AS `Paid On`
FROM payments p
INNER JOIN repair_jobs rj ON rj.id = p.repair_job_id
INNER JOIN vehicles v ON v.id = rj.vehicle_id
INNER JOIN customers c ON c.id = v.customer_id AND c.deleted_at IS NULL
LEFT  JOIN receipts r ON r.payment_id = p.id
ORDER BY p.paid_at DESC;

-- ------------------------------------------------------------
-- Q3. All vehicles per customer
--     JOIN: customers → vehicles
--     Excludes soft-deleted customers
-- ------------------------------------------------------------
SELECT
    c.id AS `Customer ID`,
    c.fullname AS `Customer`,
    c.contact AS `Phone`,
    v.plate_number  AS `Plate`,
    COALESCE(NULLIF(v.model,''), '—') AS `Model`,
    COUNT(rj.id) AS `Total Jobs`,
    SUM(rj.parts_cost + rj.labour_cost) AS `Lifetime Value (UGX)`,
    DATE_FORMAT(v.date_received, '%d %b %Y') AS `First Seen`
FROM customers c
LEFT JOIN vehicles v ON v.customer_id = c.id
LEFT JOIN repair_jobs rj ON rj.vehicle_id = v.id
WHERE c.deleted_at IS NULL
GROUP BY c.id, v.id
ORDER BY c.fullname;

-- ------------------------------------------------------------
-- Q4. Pending jobs only
--     Excludes soft-deleted customers
-- ------------------------------------------------------------
SELECT
    CONCAT('RJ-', LPAD(rj.id, 5, '0')) AS `Job No`,
    c.fullname AS `Customer`,
    c.contact AS `Phone`,
    v.plate_number AS `Plate`,
    rj.repair_type AS `Repair`,
    (rj.parts_cost + rj.labour_cost) AS `Amount (UGX)`,
    DATE_FORMAT(rj.created_at, '%d %b %Y') AS `Logged On`
FROM repair_jobs rj
INNER JOIN vehicles  v ON v.id  = rj.vehicle_id
INNER JOIN customers c ON c.id  = v.customer_id AND c.deleted_at IS NULL
WHERE rj.status = 'REPAIR PENDING'
ORDER BY rj.created_at ASC;

-- ============================================================
-- SOFT-DELETE HELPER QUERIES
-- ============================================================

-- ------------------------------------------------------------
-- Q5. View all deleted customers with full history
-- ------------------------------------------------------------
SELECT
    c.id AS `Customer ID`,
    c.fullname AS `Customer`,
    c.contact AS `Phone`,
    c.address AS `Address`,
    COUNT(DISTINCT v.id) AS `Total Vehicles`,
    COUNT(DISTINCT rj.id) AS `Total Jobs`,
    COALESCE(SUM(rj.parts_cost + rj.labour_cost), 0) AS `Lifetime Value (UGX)`,
    DATE_FORMAT(c.created_at, '%d %b %Y') AS `Registered`,
    DATE_FORMAT(c.deleted_at, '%d %b %Y') AS `Deleted On`,
    u.full_name AS `Deleted By`
FROM customers c
LEFT JOIN vehicles v ON v.customer_id = c.id
LEFT JOIN repair_jobs rj ON rj.vehicle_id = v.id
LEFT JOIN users u ON u.id = c.deleted_by
WHERE c.deleted_at IS NOT NULL
GROUP BY c.id
ORDER BY c.deleted_at DESC;

-- ------------------------------------------------------------
-- Q6. Restore a soft-deleted customer
--     Replace 123 with the actual customer id
-- ------------------------------------------------------------
-- UPDATE customers
-- SET deleted_at = NULL, deleted_by = NULL
-- WHERE id = 123 AND deleted_at IS NOT NULL;

-- ------------------------------------------------------------
-- Q7. Permanently purge old deleted customers (older than 1 year)
--     ⚠️ This is irreversible — backup first!
-- ------------------------------------------------------------
-- DELETE FROM customers
-- WHERE deleted_at IS NOT NULL
--   AND deleted_at < DATE_SUB(NOW(), INTERVAL 1 YEAR);

-- ============================================================
-- MIGRATION: Add soft-delete to existing customers table
-- Run this if the customers table already exists
-- ============================================================
-- ALTER TABLE `customers`
--     ADD COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'Soft-delete timestamp — NULL means active' AFTER `created_at`,
--     ADD COLUMN `deleted_by` INT NULL DEFAULT NULL COMMENT 'FK → users.id who deleted this customer' AFTER `deleted_at`,
--     ADD CONSTRAINT `fk_customers_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`)
--         ON UPDATE CASCADE ON DELETE SET NULL;

-- ============================================================
-- END OF SCHEMA
-- ============================================================
