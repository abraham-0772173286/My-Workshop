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

function inp(string $key): string
{
    return trim((string)($_POST[$key] ?? ''));
}

try {
    $db = database_connection();
    ensure_workshop_schema($db);
    $f = $_GET['f'] ?? '';

    // ── viewall ───────────────────────────────────────────────────────────────
    if ($f === 'viewall') {
        $rows = $db->query(
            "SELECT
                i.id,
                CONCAT('INV-', LPAD(i.id, 5, '0'))            AS invoice_no,
                c.fullname                                      AS customer_name,
                c.contact,
                COALESCE(v.plate_number, '—')                  AS plate_number,
                COALESCE(v.model, '')                          AS vehicle_model,
                DATE_FORMAT(i.issue_date, '%d %b %Y')          AS issue_date,
                DATE_FORMAT(i.due_date,   '%d %b %Y')          AS due_date,
                i.status,
                i.notes,
                i.payment_terms,
                COALESCE(SUM(ii.quantity * ii.unit_price), 0)                        AS subtotal,
                COALESCE(SUM(ii.quantity * ii.unit_price * ii.vat_pct / 100), 0)     AS vat_total,
                COALESCE(SUM(ii.quantity * ii.unit_price * (1 + ii.vat_pct/100)), 0) AS grand_total,
                DATE_FORMAT(i.created_at, '%d %b %Y')          AS created_at
            FROM invoices i
            INNER JOIN customers c  ON c.id = i.customer_id
            LEFT  JOIN vehicles  v  ON v.id = i.vehicle_id
            LEFT  JOIN invoice_items ii ON ii.invoice_id = i.id
            GROUP BY i.id
            ORDER BY i.id DESC"
        );
        reply($rows->fetch_all(MYSQLI_ASSOC));
    }

    // ── get (single invoice + items) ─────────────────────────────────────────
    if ($f === 'get') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id < 1) reply(['status' => 'error', 'msg' => 'Invalid ID.'], 422);

        $stmt = $db->prepare(
            "SELECT
                i.id,
                CONCAT('INV-', LPAD(i.id, 5, '0'))            AS invoice_no,
                c.fullname  AS customer_name,
                c.contact,
                COALESCE(c.address,'')                         AS address,
                COALESCE(v.plate_number,'')                    AS plate_number,
                COALESCE(v.model,'')                           AS vehicle_model,
                DATE_FORMAT(i.issue_date, '%d %b %Y')          AS issue_date,
                DATE_FORMAT(i.due_date,   '%d %b %Y')          AS due_date,
                i.status,
                COALESCE(i.notes,'')                           AS notes,
                COALESCE(i.payment_terms,'Payment due on receipt') AS payment_terms,
                DATE_FORMAT(i.created_at, '%d %b %Y %H:%i')   AS created_at
            FROM invoices i
            INNER JOIN customers c ON c.id = i.customer_id
            LEFT  JOIN vehicles  v ON v.id = i.vehicle_id
            WHERE i.id = ? LIMIT 1"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $inv = $stmt->get_result()->fetch_assoc();
        if (!$inv) reply(['status' => 'error', 'msg' => 'Invoice not found.'], 404);

        $iStmt = $db->prepare(
            "SELECT description, item_type, quantity, unit_price, vat_pct,
                    ROUND(quantity * unit_price, 2)                          AS taxable,
                    ROUND(quantity * unit_price * vat_pct / 100, 2)         AS tax_amount,
                    ROUND(quantity * unit_price * (1 + vat_pct/100), 2)     AS line_total
             FROM invoice_items WHERE invoice_id = ? ORDER BY id"
        );
        $iStmt->bind_param('i', $id);
        $iStmt->execute();
        $inv['items'] = $iStmt->get_result()->fetch_all(MYSQLI_ASSOC);

        reply($inv);
    }

    // ── customers_list (for dropdown) ─────────────────────────────────────────
    if ($f === 'customers_list') {
        $rows = $db->query(
            "SELECT c.id, c.fullname, c.contact,
                    GROUP_CONCAT(v.id, '|', v.plate_number, '|', COALESCE(v.model,'') ORDER BY v.id DESC SEPARATOR ';;') AS vehicles
             FROM customers c
             LEFT JOIN vehicles v ON v.customer_id = c.id
             GROUP BY c.id ORDER BY c.fullname"
        );
        reply($rows->fetch_all(MYSQLI_ASSOC));
    }

    // ── add ───────────────────────────────────────────────────────────────────
    if ($f === 'add') {
        $customerId = (int) inp('customer_id');
        $vehicleId  = inp('vehicle_id') !== '' ? (int) inp('vehicle_id') : null;
        $issueDate  = inp('issue_date');
        $dueDate    = inp('due_date') !== '' ? inp('due_date') : null;
        $notes      = inp('notes');
        $payTerms   = inp('payment_terms') !== '' ? inp('payment_terms') : 'Payment due on receipt';
        $status     = inp('status');
        $itemsJson  = $_POST['items'] ?? '[]';

        if ($customerId < 1 || $issueDate === '') {
            reply(['status' => 'error', 'msg' => 'Customer and issue date are required.'], 422);
        }
        if (!in_array($status, ['UNPAID','PAID','PARTIAL','CANCELLED'], true)) {
            $status = 'UNPAID';
        }

        $items = json_decode($itemsJson, true);
        if (!is_array($items) || count($items) === 0) {
            reply(['status' => 'error', 'msg' => 'At least one line item is required.'], 422);
        }

        $db->begin_transaction();

        $ins = $db->prepare(
            "INSERT INTO invoices (invoice_no, customer_id, vehicle_id, issue_date, due_date, notes, payment_terms, status)
             VALUES ('TEMP', ?, ?, ?, ?, ?, ?, ?)"
        );
        $ins->bind_param('iisssss', $customerId, $vehicleId, $issueDate, $dueDate, $notes, $payTerms, $status);
        $ins->execute();
        $newId = $db->insert_id;

        $invoiceNo = 'INV-' . str_pad((string)$newId, 5, '0', STR_PAD_LEFT);
        $db->query("UPDATE invoices SET invoice_no = '$invoiceNo' WHERE id = $newId");

        $iStmt = $db->prepare(
            "INSERT INTO invoice_items (invoice_id, description, item_type, quantity, unit_price, vat_pct)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        foreach ($items as $item) {
            $desc   = trim((string)($item['description'] ?? ''));
            $type   = in_array($item['item_type'] ?? '', ['Labour','Part','Other'], true) ? $item['item_type'] : 'Labour';
            $qty    = max(0.01, (float)($item['quantity']   ?? 1));
            $price  = max(0,    (float)($item['unit_price'] ?? 0));
            $vatPct = max(0,    (float)($item['vat_pct']    ?? 0));
            if ($desc === '') continue;
            $iStmt->bind_param('issddd', $newId, $desc, $type, $qty, $price, $vatPct);
            $iStmt->execute();
        }

        $db->commit();
        reply(['status' => 'ok', 'msg' => "Invoice $invoiceNo created.", 'id' => $newId, 'invoice_no' => $invoiceNo]);
    }

    // ── update_status ─────────────────────────────────────────────────────────
    if ($f === 'update_status') {
        $id     = (int) inp('id');
        $status = inp('status');
        if ($id < 1 || !in_array($status, ['UNPAID','PAID','PARTIAL','CANCELLED'], true)) {
            reply(['status' => 'error', 'msg' => 'Invalid request.'], 422);
        }
        $stmt = $db->prepare('UPDATE invoices SET status = ? WHERE id = ?');
        $stmt->bind_param('si', $status, $id);
        $stmt->execute();
        reply(['status' => 'ok', 'msg' => 'Status updated to ' . $status . '.']);
    }

    // ── delete ────────────────────────────────────────────────────────────────
    if ($f === 'delete') {
        $id = (int) inp('id');
        if ($id < 1) reply(['status' => 'error', 'msg' => 'Invalid ID.'], 422);
        $stmt = $db->prepare('DELETE FROM invoices WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        reply(['status' => 'ok', 'msg' => 'Invoice deleted.']);
    }

    reply(['status' => 'error', 'msg' => 'Unsupported request.'], 405);

} catch (Throwable $e) {
    error_log('Invoices.php: ' . $e->getMessage());
    reply(['status' => 'error', 'msg' => 'Server error: ' . $e->getMessage()], 500);
}
