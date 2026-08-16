<?php
declare(strict_types=1);

/**
 * Receipts.php — JSON API for the Receipts feature.
 *
 * Actions (via ?f=):
 *   viewall  → list every payment together with its receipt (LEFT JOIN).
 *   issue    → POST payment_id: create a receipt (RCP-xxxxx) if one is missing.
 *   get      → GET  payment_id: full receipt details for the printable layout.
 */

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

    // ------------------------------------------------------------
    // viewall — every payment with its receipt (if issued)
    // ------------------------------------------------------------
    if ($action === 'viewall') {
        $sql = "SELECT
                    COALESCE(r.receipt_no, '')        AS receipt_no,
                    COALESCE(r.id, 0)                 AS receipt_id,
                    p.id                              AS payment_id,
                    CONCAT('RJ-', LPAD(rj.id, 5, '0')) AS job_no,
                    c.fullname                        AS customer,
                    c.contact                         AS contact,
                    CONCAT(v.plate_number, ' · ', COALESCE(NULLIF(v.model, ''), 'Vehicle')) AS vehicle,
                    rj.repair_type                    AS repair_type,
                    rj.parts_cost                     AS parts_cost,
                    rj.labour_cost                    AS labour_cost,
                    (rj.parts_cost + rj.labour_cost)  AS total_cost,
                    p.amount_paid                     AS amount_paid,
                    p.payment_method                  AS payment_method,
                    COALESCE(p.reference, '')         AS reference,
                    DATE_FORMAT(p.paid_at, '%d %b %Y') AS paid_on,
                    COALESCE(DATE_FORMAT(r.issued_at, '%d %b %Y'), '') AS issued_on
                FROM payments p
                INNER JOIN repair_jobs rj ON rj.id = p.repair_job_id
                INNER JOIN vehicles  v   ON v.id  = rj.vehicle_id
                INNER JOIN customers c   ON c.id  = v.customer_id
                LEFT  JOIN receipts  r   ON r.payment_id = p.id
                ORDER BY p.paid_at DESC, p.id DESC";
        reply($connection->query($sql)->fetch_all(MYSQLI_ASSOC));
    }

    // ------------------------------------------------------------
    // get — one receipt's full details (for the printable layout)
    // ------------------------------------------------------------
    if ($action === 'get') {
        $paymentId = filter_var($_GET['payment_id'] ?? 0, FILTER_VALIDATE_INT) ?: 0;
        if ($paymentId < 1) {
            reply(['status' => 'error', 'msg' => 'Invalid payment id.'], 422);
        }

        $stmt = $connection->prepare(
            "SELECT
                r.receipt_no AS receipt_no,
                DATE_FORMAT(r.issued_at, '%d %b %Y %H:%i') AS issued_on,
                CONCAT('RJ-', LPAD(rj.id, 5, '0')) AS job_no,
                c.fullname AS customer,
                c.contact  AS contact,
                COALESCE(NULLIF(c.address, ''), '—') AS address,
                v.plate_number AS plate,
                COALESCE(NULLIF(v.model, ''), 'Vehicle') AS model,
                rj.repair_type AS repair_type,
                rj.parts_cost  AS parts_cost,
                rj.labour_cost AS labour_cost,
                (rj.parts_cost + rj.labour_cost) AS total_cost,
                p.amount_paid AS amount_paid,
                p.payment_method AS payment_method,
                COALESCE(NULLIF(p.reference, ''), '—') AS reference,
                DATE_FORMAT(p.paid_at, '%d %b %Y') AS paid_on
            FROM payments p
            INNER JOIN repair_jobs rj ON rj.id = p.repair_job_id
            INNER JOIN vehicles  v   ON v.id  = rj.vehicle_id
            INNER JOIN customers c   ON c.id  = v.customer_id
            INNER JOIN receipts  r   ON r.payment_id = p.id
            WHERE p.id = ?
            LIMIT 1"
        );
        $stmt->bind_param('i', $paymentId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        if (!$row) {
            reply(['status' => 'error', 'msg' => 'Receipt not found. Issue it first.'], 404);
        }
        reply(['status' => 'success', 'data' => $row]);
    }

    // ------------------------------------------------------------
    // issue — create the receipt for a payment (idempotent)
    // ------------------------------------------------------------
    if ($action === 'issue') {
        $paymentId = filter_var($_POST['payment_id'] ?? 0, FILTER_VALIDATE_INT) ?: 0;
        if ($paymentId < 1) {
            reply(['status' => 'error', 'msg' => 'Invalid payment id.'], 422);
        }

        // Is the payment real?
        $check = $connection->prepare('SELECT id FROM payments WHERE id = ? LIMIT 1');
        $check->bind_param('i', $paymentId);
        $check->execute();
        if (!$check->get_result()->fetch_assoc()) {
            reply(['status' => 'error', 'msg' => 'Payment does not exist.'], 404);
        }

        // Already issued? Return the existing number (idempotent).
        $existing = $connection->prepare('SELECT receipt_no FROM receipts WHERE payment_id = ? LIMIT 1');
        $existing->bind_param('i', $paymentId);
        $existing->execute();
        if ($row = $existing->get_result()->fetch_assoc()) {
            reply(['status' => 'success', 'msg' => 'Receipt already issued.', 'receipt_no' => $row['receipt_no']]);
        }

        // Generate the next receipt number, e.g. RCP-00007.
        $max = $connection->query('SELECT COALESCE(MAX(id), 0) + 1 AS next FROM receipts')->fetch_assoc();
        $receiptNo = sprintf('RCP-%05d', (int) $max['next']);

        $insert = $connection->prepare('INSERT INTO receipts (payment_id, receipt_no) VALUES (?, ?)');
        $insert->bind_param('is', $paymentId, $receiptNo);
        $insert->execute();

        reply(['status' => 'success', 'msg' => 'Receipt issued.', 'receipt_no' => $receiptNo]);
    }

    reply(['status' => 'error', 'msg' => 'Unsupported request.'], 405);
} catch (Throwable $exception) {
    error_log('Receipts.php Error: ' . $exception->getMessage());
    reply(['status' => 'error', 'msg' => 'Could not complete the request. Check the database connection.'], 500);
}
