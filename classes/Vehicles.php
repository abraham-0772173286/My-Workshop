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

    // ── List all vehicles (joined with owner + job count) ─────────────────────
    if ($action === 'viewall') {
        $rows = $db->query(
            "SELECT v.id AS vehicle_id,
                    v.plate_number,
                    COALESCE(NULLIF(v.model,''),'—') AS model,
                    v.car_owner,
                    c.contact,
                    c.id AS customer_id,
                    COUNT(rj.id)  AS total_jobs,
                    COALESCE(SUM(rj.parts_cost + rj.labour_cost), 0) AS total_spent,
                    DATE_FORMAT(v.date_received,'%d %b %Y') AS date_received
             FROM vehicles v
             INNER JOIN customers   c  ON c.id  = v.customer_id
             LEFT  JOIN repair_jobs rj ON rj.vehicle_id = v.id
             GROUP BY v.id
             ORDER BY v.id DESC"
        )->fetch_all(MYSQLI_ASSOC);
        reply($rows);
    }

    // ── Single vehicle ────────────────────────────────────────────────────────
    if ($action === 'get') {
        $id = (int) ($_GET['id'] ?? 0);
        if (!$id) reply(['status' => 'error', 'msg' => 'Invalid ID.'], 422);

        $stmt = $db->prepare(
            'SELECT v.id AS vehicle_id, v.customer_id, v.plate_number,
                    COALESCE(v.model,"") AS model, v.car_owner, v.date_received
             FROM vehicles v WHERE v.id=? LIMIT 1'
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if (!$row) reply(['status' => 'error', 'msg' => 'Vehicle not found.'], 404);
        reply($row);
    }

    // ── Customer list for the dropdown ───────────────────────────────────────
    if ($action === 'customers') {
        $rows = $db->query('SELECT id, fullname, contact FROM customers ORDER BY fullname ASC')
                   ->fetch_all(MYSQLI_ASSOC);
        reply($rows);
    }

    // ── Save ─────────────────────────────────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save') {
        $id           = (int) input('vehicle_id');
        $customerId   = (int) input('customer_id');
        $plate        = strtoupper(preg_replace('/\s+/', ' ', input('plate_number')));
        $model        = input('model');
        $carOwner     = input('car_owner');
        $dateReceived = input('date_received') ?: date('Y-m-d');

        if (!$customerId || $plate === '' || $carOwner === '') {
            reply(['status' => 'error', 'msg' => 'Customer, plate number and owner name are required.'], 422);
        }

        if ($id > 0) {
            $stmt = $db->prepare(
                'UPDATE vehicles SET customer_id=?, plate_number=?, model=?, car_owner=?, date_received=? WHERE id=?'
            );
            $stmt->bind_param('issssi', $customerId, $plate, $model, $carOwner, $dateReceived, $id);
            $stmt->execute();
            reply(['status' => 'success', 'msg' => 'Vehicle updated successfully.']);
        } else {
            $chk = $db->prepare('SELECT id FROM vehicles WHERE plate_number=? LIMIT 1');
            $chk->bind_param('s', $plate);
            $chk->execute();
            if ($chk->get_result()->fetch_assoc()) {
                reply(['status' => 'error', 'msg' => 'A vehicle with this plate number already exists.'], 409);
            }
            $ins = $db->prepare(
                'INSERT INTO vehicles (customer_id, plate_number, model, car_owner, date_received) VALUES (?,?,?,?,?)'
            );
            $ins->bind_param('issss', $customerId, $plate, $model, $carOwner, $dateReceived);
            $ins->execute();
            reply(['status' => 'success', 'msg' => 'Vehicle registered successfully.', 'id' => $db->insert_id]);
        }
    }

    // ── Delete ────────────────────────────────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {
        $id = (int) input('id');
        if (!$id) reply(['status' => 'error', 'msg' => 'Invalid ID.'], 422);

        $rj = $db->prepare('SELECT COUNT(*) FROM repair_jobs WHERE vehicle_id=?');
        $rj->bind_param('i', $id);
        $rj->execute();
        if ((int) $rj->get_result()->fetch_row()[0] > 0) {
            reply(['status' => 'error', 'msg' => 'Cannot delete — vehicle has linked repair jobs.'], 409);
        }

        $del = $db->prepare('DELETE FROM vehicles WHERE id=?');
        $del->bind_param('i', $id);
        $del->execute();
        reply(['status' => 'success', 'msg' => 'Vehicle deleted.']);
    }

    reply(['status' => 'error', 'msg' => 'Unsupported request.'], 405);

} catch (Throwable $e) {
    error_log('Vehicles.php: ' . $e->getMessage());
    reply(['status' => 'error', 'msg' => 'Server error: ' . $e->getMessage()], 500);
}
