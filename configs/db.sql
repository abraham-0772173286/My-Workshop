-- ============================================================
--  SHENGCHI AUTO LTD (金龙汽车维修)
--  Workshop Management System — Full Database Schema
--
--  HOW TO USE IN MYSQL WORKBENCH:
--  1. Open MySQL Workbench and connect to localhost (root / your password)
--  2. Open this file:  File → Open SQL Script → select this file
--  3. Click the lightning-bolt ⚡ button to execute
--  4. Refresh the Schemas panel — you will see the "workshop" database
-- ============================================================

-- Drop and recreate for a clean slate (remove these two lines if you
-- want to keep existing data and just add missing tables)
DROP DATABASE IF EXISTS `workshop`;
CREATE DATABASE `workshop`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

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
    `contact`    VARCHAR(40)   NOT NULL COMMENT 'Phone number — must be unique',
    `address`    VARCHAR(255)  NULL COMMENT 'Optional physical address',
    `created_at` TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_customers_contact` (`contact`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Registered vehicle owners';

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
-- TABLE 5 — receipts
-- One receipt issued per payment.
-- ============================================================
CREATE TABLE `receipts` (
    `id`  INT NOT NULL AUTO_INCREMENT,
    `payment_id` INT NOT NULL COMMENT 'FK → payments.id',
    `receipt_no` VARCHAR(30) NOT NULL  COMMENT 'Human-readable receipt number, e.g. RCP-00001',
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
-- ------------------------------------------------------------
SELECT CONCAT('RJ-', LPAD(rj.id, 5, '0')) AS `Job No`
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
INNER JOIN customers c  ON c.id  = v.customer_id
ORDER BY rj.id DESC;

-- ------------------------------------------------------------
-- Q2. Full payment summary with receipt
--     JOIN: payments → repair_jobs → vehicles → customers → receipts
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
INNER JOIN customers c ON c.id = v.customer_id
LEFT  JOIN receipts r ON r.payment_id = p.id
ORDER BY p.paid_at DESC;

-- ------------------------------------------------------------
-- Q3. All vehicles per customer
--     JOIN: customers → vehicles
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
GROUP BY c.id, v.id
ORDER BY c.fullname;

-- ------------------------------------------------------------
-- Q4. Pending jobs only
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
INNER JOIN vehicles  v ON v.id = rj.vehicle_id
INNER JOIN customers c ON c.id = v.customer_id
WHERE rj.status = 'REPAIR PENDING'
ORDER BY rj.created_at ASC;

-- ============================================================
-- END OF SCHEMA
-- ============================================================
