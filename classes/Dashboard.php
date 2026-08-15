<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../configs/database.php';

function reply(array $payload, int $code = 200): never
{
    http_response_code($code);
    echo json_encode($payload);
    exit;
}

try {
    $db = database_connection();
    ensure_workshop_schema($db);
    $action = (string) ($_GET['f'] ?? '');

    // ── KPI cards ────────────────────────────────────────────────────────────
    if ($action === 'stats') {
        $stats = [];

        // total repair jobs
        $stats['total_jobs']     = (int) $db->query('SELECT COUNT(*) FROM repair_jobs')->fetch_row()[0];
        $stats['pending_jobs']   = (int) $db->query("SELECT COUNT(*) FROM repair_jobs WHERE status='REPAIR PENDING'")->fetch_row()[0];
        $stats['done_jobs']      = (int) $db->query("SELECT COUNT(*) FROM repair_jobs WHERE status='REPAIR DONE'")->fetch_row()[0];
        $stats['total_customers']= (int) $db->query('SELECT COUNT(*) FROM customers')->fetch_row()[0];
        $stats['total_vehicles'] = (int) $db->query('SELECT COUNT(*) FROM vehicles')->fetch_row()[0];

        // revenue (parts + labour of DONE jobs)
        $rev = $db->query("SELECT COALESCE(SUM(parts_cost+labour_cost),0) FROM repair_jobs WHERE status='REPAIR DONE'")->fetch_row()[0];
        $stats['total_revenue']  = (float) $rev;

        // jobs this month
        $stats['jobs_this_month']= (int) $db->query(
            "SELECT COUNT(*) FROM repair_jobs WHERE YEAR(created_at)=YEAR(CURDATE()) AND MONTH(created_at)=MONTH(CURDATE())"
        )->fetch_row()[0];

        // revenue this month
        $mrev = $db->query(
            "SELECT COALESCE(SUM(parts_cost+labour_cost),0) FROM repair_jobs
             WHERE status='REPAIR DONE' AND YEAR(created_at)=YEAR(CURDATE()) AND MONTH(created_at)=MONTH(CURDATE())"
        )->fetch_row()[0];
        $stats['revenue_this_month'] = (float) $mrev;

        reply($stats);
    }

    // ── Monthly revenue chart — last 6 months ────────────────────────────────
    if ($action === 'monthly_revenue') {
        $rows = $db->query(
            "SELECT DATE_FORMAT(created_at,'%b %Y') AS month,
                    YEAR(created_at) AS yr,
                    MONTH(created_at) AS mo,
                    COALESCE(SUM(parts_cost+labour_cost),0) AS revenue,
                    COUNT(*) AS jobs
             FROM repair_jobs
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
             GROUP BY DATE_FORMAT(created_at,'%b %Y'), yr, mo
             ORDER BY yr ASC, mo ASC"
        )->fetch_all(MYSQLI_ASSOC);
        reply($rows);
    }

    // ── Repair-type breakdown (pie) ───────────────────────────────────────────
    if ($action === 'repair_types') {
        $rows = $db->query(
            "SELECT repair_type AS label, COUNT(*) AS value
             FROM repair_jobs
             GROUP BY repair_type
             ORDER BY value DESC
             LIMIT 8"
        )->fetch_all(MYSQLI_ASSOC);
        reply($rows);
    }

    // ── Recent 8 jobs ────────────────────────────────────────────────────────
    if ($action === 'recent_jobs') {
        $rows = $db->query(
            "SELECT CONCAT('RJ-',LPAD(rj.id,5,'0')) AS job_no,
                    c.fullname AS customer,
                    v.plate_number AS plate,
                    COALESCE(NULLIF(v.model,''),'—') AS model,
                    rj.repair_type,
                    (rj.parts_cost+rj.labour_cost) AS total,
                    rj.status,
                    DATE_FORMAT(rj.created_at,'%d %b %Y') AS date
             FROM repair_jobs rj
             INNER JOIN vehicles  v ON v.id = rj.vehicle_id
             INNER JOIN customers c ON c.id = v.customer_id
             ORDER BY rj.id DESC LIMIT 8"
        )->fetch_all(MYSQLI_ASSOC);
        reply($rows);
    }

    // ── Pending jobs list ────────────────────────────────────────────────────
    if ($action === 'pending_jobs') {
        $rows = $db->query(
            "SELECT CONCAT('RJ-',LPAD(rj.id,5,'0')) AS job_no,
                    c.fullname AS customer, c.contact,
                    v.plate_number AS plate,
                    rj.repair_type,
                    (rj.parts_cost+rj.labour_cost) AS total,
                    DATE_FORMAT(rj.created_at,'%d %b %Y') AS date
             FROM repair_jobs rj
             INNER JOIN vehicles  v ON v.id = rj.vehicle_id
             INNER JOIN customers c ON c.id = v.customer_id
             WHERE rj.status='REPAIR PENDING'
             ORDER BY rj.created_at ASC"
        )->fetch_all(MYSQLI_ASSOC);
        reply($rows);
    }

    reply(['status' => 'error', 'msg' => 'Unknown action.'], 404);

} catch (Throwable $e) {
    error_log('Dashboard.php: ' . $e->getMessage());
    reply(['status' => 'error', 'msg' => 'Server error.'], 500);
}
