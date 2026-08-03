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

function input(string $key): string
{
    return trim((string) ($_POST[$key] ?? ''));
}

try {
    $db = database_connection();
    ensure_workshop_schema($db);
    $action = (string) ($_GET['f'] ?? '');

    // ── List all customers (with vehicle + job counts) ────────────────────────
    if ($action === 'viewall') {
        $rows = $db->query(
            "SELECT c.id AS customer_id,
                    c.fullname,
                    c.contact,
                    COALESCE(c.address,'—') AS address,
                    COUNT(DISTINCT v.id)    AS total_vehicles,
                    COUNT(DISTINCT rj.id)   AS total_jobs,
                    COALESCE(SUM(rj.parts_cost + rj.labour_cost), 0) AS lifetime_value,
                    DATE_FORMAT(c.created_at, '%d %b %Y') AS joined
             FROM customers c
             LEFT JOIN vehicles    v  ON v.customer_id  = c.id
             LEFT JOIN repair_jobs rj ON rj.vehicle_id  = v.id
             GROUP BY c.id
             ORDER BY c.id DESC"
        )->fetch_all(MYSQLI_ASSOC);
        reply($rows);
    }

    // ── Single customer detail ────────────────────────────────────────────────
    if ($action === 'get') {
        $id = (int) ($_GET['id'] ?? 0);
        if (!$id) reply(['status' => 'error', 'msg' => 'Invalid ID.'], 422);

        $stmt = $db->prepare(
            'SELECT id AS customer_id, fullname, contact, COALESCE(address,"") AS address
             FROM customers WHERE id = ? LIMIT 1'
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if (!$row) reply(['status' => 'error', 'msg' => 'Customer not found.'], 404);
        reply($row);
    }

    // ── Save (insert or update) ───────────────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save') {
        $id       = (int) input('customer_id');
        $fullname = input('fullname');
        $contact  = input('contact');
        $address  = input('address');

        if ($fullname === '' || $contact === '') {
            reply(['status' => 'error', 'msg' => 'Name and contact are required.'], 422);
        }

        if ($id > 0) {
            // update
            $stmt = $db->prepare(
                'UPDATE customers SET fullname=?, contact=?, address=? WHERE id=?'
            );
            $stmt->bind_param('sssi', $fullname, $contact, $address, $id);
            $stmt->execute();
            reply(['status' => 'success', 'msg' => 'Customer updated successfully.']);
        } else {
            // insert — check duplicate contact
            $chk = $db->prepare('SELECT id FROM customers WHERE contact=? LIMIT 1');
            $chk->bind_param('s', $contact);
            $chk->execute();
            if ($chk->get_result()->fetch_assoc()) {
                reply(['status' => 'error', 'msg' => 'A customer with this contact already exists.'], 409);
            }
            $ins = $db->prepare(
                'INSERT INTO customers (fullname, contact, address) VALUES (?,?,?)'
            );
            $ins->bind_param('sss', $fullname, $contact, $address);
            $ins->execute();
            reply(['status' => 'success', 'msg' => 'Customer registered successfully.', 'id' => $db->insert_id]);
        }
    }

    // ── Delete ────────────────────────────────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {
        $id = (int) input('id');
        if (!$id) reply(['status' => 'error', 'msg' => 'Invalid ID.'], 422);

        // guard: customer still has vehicles
        $veh = $db->prepare('SELECT COUNT(*) FROM vehicles WHERE customer_id=?');
        $veh->bind_param('i', $id);
        $veh->execute();
        if ((int) $veh->get_result()->fetch_row()[0] > 0) {
            reply(['status' => 'error', 'msg' => 'Cannot delete — customer has linked vehicles.'], 409);
        }

        $del = $db->prepare('DELETE FROM customers WHERE id=?');
        $del->bind_param('i', $id);
        $del->execute();
        reply(['status' => 'success', 'msg' => 'Customer deleted.']);
    }

    reply(['status' => 'error', 'msg' => 'Unsupported request.'], 405);

} catch (Throwable $e) {
    error_log('Customers.php: ' . $e->getMessage());
    reply(['status' => 'error', 'msg' => 'Server error: ' . $e->getMessage()], 500);
}
