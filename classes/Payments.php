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

function input(string $key): string
{
    return trim((string) ($_POST[$key] ?? ''));
}

try {
    $connection = database_connection();
    ensure_workshop_schema($connection);
    $action = $_GET['f'] ?? '';

    // ── viewall — every payment with job + customer + vehicle + receipt info ──
    if ($action === 'viewall') {
        $sql = "SELECT
                    p.id                              AS payment_id,
                    CONCAT('RJ-', LPAD(rj.id, 5, '0')) AS job_no,
                    rj.id                             AS repair_job_id,
                    c.fullname                        AS customer,
                    CONCAT(v.plate_number, ' · ', COALESCE(NULLIF(v.model, ''), 'Vehicle')) AS vehicle,
                    rj.repair_type                    AS repair_type,
                    (rj.parts_cost + rj.labour_cost)  AS total_cost,
                    p.amount_paid                     AS amount_paid,
                    p.payment_method                  AS payment_method,
                    COALESCE(p.reference, '')         AS reference,
                    DATE_FORMAT(p.paid_at, '%d %b %Y') AS paid_on,
                    COALESCE(r.receipt_no, '')        AS receipt_no,
                    DATE_FORMAT(r.issued_at, '%d %b %Y') AS issued_on
                FROM payments p
                INNER JOIN repair_jobs rj ON rj.id = p.repair_job_id
                INNER JOIN vehicles  v   ON v.id  = rj.vehicle_id
                INNER JOIN customers c   ON c.id  = v.customer_id
                LEFT  JOIN receipts  r   ON r.payment_id = p.id
                ORDER BY p.paid_at DESC, p.id DESC";
        reply($connection->query($sql)->fetch_all(MYSQLI_ASSOC));
    }

    // ── get — single payment detail ───────────────────────────────────────────
    if ($action === 'get') {
        $id = filter_var($_GET['id'] ?? 0, FILTER_VALIDATE_INT) ?: 0;
        if ($id < 1) reply(['status' => 'error', 'msg' => 'Invalid ID.'], 422);

        $stmt = $connection->prepare(
            "SELECT
                p.id AS payment_id,
                p.repair_job_id,
                CONCAT('RJ-', LPAD(rj.id, 5, '0')) AS job_no,
                c.fullname AS customer,
                CONCAT(v.plate_number, ' · ', COALESCE(NULLIF(v.model, ''), 'Vehicle')) AS vehicle,
                (rj.parts_cost + rj.labour_cost) AS total_cost,
                p.amount_paid,
                p.payment_method,
                COALESCE(p.reference, '') AS reference,
                DATE_FORMAT(p.paid_at, '%Y-%m-%d') AS paid_date
            FROM payments p
            INNER JOIN repair_jobs rj ON rj.id = p.repair_job_id
            INNER JOIN vehicles  v   ON v.id  = rj.vehicle_id
            INNER JOIN customers c   ON c.id  = v.customer_id
            WHERE p.id = ?
            LIMIT 1"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if (!$row) reply(['status' => 'error', 'msg' => 'Payment not found.'], 404);
        reply($row);
    }

    // ── jobs — repair jobs available for payment (for the dropdown) ────────────
    if ($action === 'jobs') {
        $sql = "SELECT
                    rj.id AS repair_job_id,
                    CONCAT('RJ-', LPAD(rj.id, 5, '0')) AS job_no,
                    c.fullname AS customer,
                    v.plate_number AS plate,
                    (rj.parts_cost + rj.labour_cost) AS total_cost,
                    COALESCE(SUM(p.amount_paid), 0) AS total_paid,
                    (rj.parts_cost + rj.labour_cost) - COALESCE(SUM(p.amount_paid), 0) AS balance
                FROM repair_jobs rj
                INNER JOIN vehicles  v ON v.id = rj.vehicle_id
                INNER JOIN customers c ON c.id = v.customer_id
                LEFT  JOIN payments  p ON p.repair_job_id = rj.id
                WHERE rj.status = 'REPAIR DONE'
                GROUP BY rj.id
                ORDER BY rj.id DESC";
        reply($connection->query($sql)->fetch_all(MYSQLI_ASSOC));
    }

    // ── save — insert or update a payment ─────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save') {
        $id            = (int) input('payment_id');
        $repairJobId   = (int) input('repair_job_id');
        $amountPaid    = filter_var($_POST['amount_paid'] ?? null, FILTER_VALIDATE_FLOAT);
        $paymentMethod = strtoupper(input('payment_method'));
        $reference     = input('reference');

        if ($repairJobId < 1 || $amountPaid === false || $amountPaid <= 0) {
            reply(['status' => 'error', 'msg' => 'Please select a repair job and enter a valid amount.'], 422);
        }
        if (!in_array($paymentMethod, ['CASH', 'MPESA', 'BANK TRANSFER', 'OTHER'], true)) {
            reply(['status' => 'error', 'msg' => 'Invalid payment method.'], 422);
        }

        // Verify the repair job exists and is done
        $jobCheck = $connection->prepare('SELECT id FROM repair_jobs WHERE id = ? LIMIT 1');
        $jobCheck->bind_param('i', $repairJobId);
        $jobCheck->execute();
        if (!$jobCheck->get_result()->fetch_assoc()) {
            reply(['status' => 'error', 'msg' => 'Repair job does not exist.'], 404);
        }

        if ($id > 0) {
            // Update existing payment
            $stmt = $connection->prepare(
                'UPDATE payments SET repair_job_id = ?, amount_paid = ?, payment_method = ?, reference = ? WHERE id = ?'
            );
            $stmt->bind_param('idsi', $repairJobId, $amountPaid, $paymentMethod, $reference, $id);
            $stmt->execute();
            reply(['status' => 'success', 'msg' => 'Payment updated successfully.']);
        } else {
            // Insert new payment
            $stmt = $connection->prepare(
                'INSERT INTO payments (repair_job_id, amount_paid, payment_method, reference) VALUES (?, ?, ?, ?)'
            );
            $stmt->bind_param('idss', $repairJobId, $amountPaid, $paymentMethod, $reference);
            $stmt->execute();
            reply(['status' => 'success', 'msg' => 'Payment recorded successfully.', 'payment_id' => $connection->insert_id]);
        }
    }

    // ── delete — remove a payment (only if no receipt has been issued) ────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {
        $id = (int) input('id');
        if ($id < 1) reply(['status' => 'error', 'msg' => 'Invalid ID.'], 422);

        // Guard: check if a receipt exists for this payment
        $rcpt = $connection->prepare('SELECT id FROM receipts WHERE payment_id = ? LIMIT 1');
        $rcpt->bind_param('i', $id);
        $rcpt->execute();
        if ($rcpt->get_result()->fetch_assoc()) {
            reply(['status' => 'error', 'msg' => 'Cannot delete — a receipt has already been issued for this payment. Void the receipt first.'], 409);
        }

        $del = $connection->prepare('DELETE FROM payments WHERE id = ?');
        $del->bind_param('i', $id);
        $del->execute();
        reply(['status' => 'success', 'msg' => 'Payment deleted.']);
    }

    reply(['status' => 'error', 'msg' => 'Unsupported request.'], 405);
} catch (Throwable $exception) {
    error_log('Payments.php Error: ' . $exception->getMessage());
    reply(['status' => 'error', 'msg' => 'Could not complete the request. Check the database connection.'], 500);
}
