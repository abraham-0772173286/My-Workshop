<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../configs/database.php';

function reply(array $payload, int $statusCode = 200): never
{
    http_response_code($statusCode);
    echo json_encode($payload);
    exit;
}

try {
    $connection = database_connection();
    ensure_workshop_schema($connection);
    $action = $_GET['f'] ?? '';

    // ── viewall ──────────────────────────────────────────────────────────────
    // Returns one row per customer with aggregated vehicle/job summary.
    if ($action === 'viewall') {
        $result = $connection->query(
            "SELECT
                c.id                                              AS customer_id,
                c.fullname,
                c.contact,
                COALESCE(NULLIF(c.address, ''), '—')             AS address,
                COUNT(DISTINCT v.id)                             AS total_vehicles,
                COUNT(DISTINCT r.id)                             AS total_jobs,
                COALESCE(
                    GROUP_CONCAT(DISTINCT v.plate_number ORDER BY v.id DESC SEPARATOR ', '),
                    '—'
                )                                                AS plates,
                DATE_FORMAT(MIN(v.date_received), '%d %b %Y')   AS first_seen,
                DATE_FORMAT(MAX(v.date_received), '%d %b %Y')   AS last_seen,
                DATE_FORMAT(c.created_at, '%d %b %Y')           AS registered_on
            FROM customers c
            LEFT JOIN vehicles v   ON v.customer_id = c.id
            LEFT JOIN repair_jobs r ON r.vehicle_id  = v.id
            GROUP BY c.id
            ORDER BY c.id DESC"
        );
        reply($result->fetch_all(MYSQLI_ASSOC));
    }

    // ── view_jobs ─────────────────────────────────────────────────────────────
    // Returns all repair jobs for a specific customer (used in detail drawer).
    if ($action === 'view_jobs') {
        $customerId = (int) ($_GET['id'] ?? 0);
        if ($customerId < 1) {
            reply(['status' => 'error', 'msg' => 'Invalid customer ID.'], 422);
        }
        $stmt = $connection->prepare(
            "SELECT
                CONCAT('RJ-', LPAD(r.id, 5, '0'))              AS job_no,
                CONCAT(v.plate_number, ' · ', COALESCE(NULLIF(v.model,''),'Vehicle')) AS vehicle,
                r.repair_type,
                r.parts_cost,
                r.labour_cost,
                r.status,
                DATE_FORMAT(v.date_received, '%d %b %Y')        AS date_received,
                DATE_FORMAT(r.created_at, '%d %b %Y')           AS job_date
            FROM repair_jobs r
            INNER JOIN vehicles v ON v.id = r.vehicle_id
            WHERE v.customer_id = ?
            ORDER BY r.id DESC"
        );
        $stmt->bind_param('i', $customerId);
        $stmt->execute();
        reply($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
    }

    reply(['status' => 'error', 'msg' => 'Unsupported request.'], 405);

} catch (Throwable $exception) {
    error_log('Customers.php Error: ' . $exception->getMessage());
    reply(['status' => 'error', 'msg' => 'Could not retrieve customer data.'], 500);
}
