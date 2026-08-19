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

    // ── List all drivers (joined with vehicle info) ──────────────────────────
    if ($action === 'viewall') {
        $rows = $db->query(
            "SELECT d.id AS driver_id,
                    d.driver_name,
                    d.vehicle_type,
                    d.model_year,
                    d.driver_mobile,
                    DATE_FORMAT(d.time_in, '%Y-%m-%dT%H:%i') AS time_in,
                    DATE_FORMAT(d.time_out, '%Y-%m-%dT%H:%i') AS time_out,
                    DATE_FORMAT(d.time_in, '%d %b %Y %h:%i %p') AS time_in_display,
                    DATE_FORMAT(d.time_out, '%d %b %Y %h:%i %p') AS time_out_display,
                    COALESCE(d.description, '') AS description,
                    d.vehicle_id,
                    v.plate_number,
                    v.car_owner,
                    COALESCE(NULLIF(v.model,''), '—') AS vehicle_model
             FROM drivers d
             INNER JOIN vehicles v ON v.id = d.vehicle_id
             ORDER BY d.id DESC"
        )->fetch_all(MYSQLI_ASSOC);
        reply($rows);
    }

    // ── Single driver ────────────────────────────────────────────────────────
    if ($action === 'get') {
        $id = (int) ($_GET['id'] ?? 0);
        if (!$id) reply(['status' => 'error', 'msg' => 'Invalid ID.'], 422);

        $stmt = $db->prepare(
            "SELECT d.id AS driver_id, d.vehicle_id, d.driver_name, d.vehicle_type,
                    d.model_year, d.driver_mobile,
                    DATE_FORMAT(d.time_in, '%Y-%m-%dT%H:%i') AS time_in,
                    DATE_FORMAT(d.time_out, '%Y-%m-%dT%H:%i') AS time_out,
                    COALESCE(d.description, '') AS description,
                    v.plate_number, v.car_owner
             FROM drivers d
             INNER JOIN vehicles v ON v.id = d.vehicle_id
             WHERE d.id=? LIMIT 1"
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if (!$row) reply(['status' => 'error', 'msg' => 'Driver record not found.'], 404);
        reply($row);
    }

    // ── Vehicle list for dropdown ────────────────────────────────────────────
    if ($action === 'vehicles') {
        $rows = $db->query(
            "SELECT v.id, v.plate_number, v.car_owner, COALESCE(NULLIF(v.model,''), '') AS model,
                    c.contact
             FROM vehicles v
             INNER JOIN customers c ON c.id = v.customer_id
             ORDER BY v.plate_number ASC"
        )->fetch_all(MYSQLI_ASSOC);
        reply($rows);
    }

    // ── Save (insert or update) ──────────────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save') {
        $id            = (int) input('driver_id');
        $vehicleId     = (int) input('vehicle_id');
        $driverName    = input('driver_name');
        $vehicleType   = input('vehicle_type');
        $modelYear     = input('model_year');
        $driverMobile  = input('driver_mobile');
        $timeIn        = input('time_in');
        $timeOut       = input('time_out');
        $description   = input('description');

        if (!$vehicleId || $driverName === '') {
            reply(['status' => 'error', 'msg' => 'Vehicle and driver name are required.'], 422);
        }

        // Convert empty datetime-local values to NULL
        $timeInDb  = $timeIn  !== '' ? $timeIn  : null;
        $timeOutDb = $timeOut !== '' ? $timeOut : null;

        if ($id > 0) {
            $stmt = $db->prepare(
                "UPDATE drivers SET vehicle_id=?, driver_name=?, vehicle_type=?, model_year=?,
                 driver_mobile=?, time_in=?, time_out=?, description=? WHERE id=?"
            );
            $stmt->bind_param('isssssssi', $vehicleId, $driverName, $vehicleType, $modelYear,
                $driverMobile, $timeInDb, $timeOutDb, $description, $id);
            $stmt->execute();
            reply(['status' => 'success', 'msg' => 'Driver record updated successfully.']);
        } else {
            $ins = $db->prepare(
                "INSERT INTO drivers (vehicle_id, driver_name, vehicle_type, model_year,
                 driver_mobile, time_in, time_out, description) VALUES (?,?,?,?,?,?,?,?)"
            );
            $ins->bind_param('isssssss', $vehicleId, $driverName, $vehicleType, $modelYear,
                $driverMobile, $timeInDb, $timeOutDb, $description);
            $ins->execute();
            reply(['status' => 'success', 'msg' => 'Driver record saved successfully.', 'id' => $db->insert_id]);
        }
    }

    // ── Delete ───────────────────────────────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {
        $id = (int) input('id');
        if (!$id) reply(['status' => 'error', 'msg' => 'Invalid ID.'], 422);

        $del = $db->prepare('DELETE FROM drivers WHERE id=?');
        $del->bind_param('i', $id);
        $del->execute();
        if ($del->affected_rows === 0) {
            reply(['status' => 'error', 'msg' => 'Driver record not found.'], 404);
        }
        reply(['status' => 'success', 'msg' => 'Driver record deleted.']);
    }

    reply(['status' => 'error', 'msg' => 'Unsupported request.'], 405);

} catch (Throwable $e) {
    error_log('Drivers.php: ' . $e->getMessage());
    reply(['status' => 'error', 'msg' => 'Server error: ' . $e->getMessage()], 500);
}
