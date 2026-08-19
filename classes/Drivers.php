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

    // ── List all drivers with trip count, assignment count, total fuel cost ────
    if ($action === 'viewall') {
        $rows = $db->query(
            "SELECT d.id, d.driver_name, d.driver_mobile, d.license_no, d.id_number,
                    d.address, d.emergency_contact, d.status,
                    DATE_FORMAT(d.created_at,'%d %b %Y') AS created_at,
                    COUNT(DISTINCT dt.id)  AS total_trips,
                    COUNT(DISTINCT da.id)  AS total_assignments,
                    COALESCE(SUM(fr.total_cost), 0) AS total_fuel_cost
             FROM drivers d
             LEFT JOIN driver_assignments da ON da.driver_id = d.id
             LEFT JOIN driver_trips dt       ON dt.driver_id = d.id
             LEFT JOIN fuel_records fr       ON fr.driver_id = d.id
             GROUP BY d.id
             ORDER BY d.id DESC"
        )->fetch_all(MYSQLI_ASSOC);
        reply($rows);
    }

    // ── Single driver ─────────────────────────────────────────────────────────
    if ($action === 'get') {
        $id = (int) ($_GET['id'] ?? 0);
        if (!$id) reply(['status' => 'error', 'msg' => 'Invalid ID.'], 422);

        $stmt = $db->prepare(
            'SELECT id, driver_name, driver_mobile, license_no, id_number,
                    address, emergency_contact, status,
                    DATE_FORMAT(created_at,\'%d %b %Y\') AS created_at
             FROM drivers WHERE id=? LIMIT 1'
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if (!$row) reply(['status' => 'error', 'msg' => 'Driver not found.'], 404);
        reply($row);
    }

    // ── Active drivers list for dropdowns ──────────────────────────────────────
    if ($action === 'drivers') {
        $rows = $db->query(
            "SELECT id, driver_name FROM drivers WHERE status='active' ORDER BY driver_name ASC"
        )->fetch_all(MYSQLI_ASSOC);
        reply($rows);
    }

    // ── Vehicles list for dropdowns ────────────────────────────────────────────
    if ($action === 'vehicles') {
        $rows = $db->query(
            'SELECT id, plate_number, car_owner, model FROM vehicles ORDER BY plate_number ASC'
        )->fetch_all(MYSQLI_ASSOC);
        reply($rows);
    }

    // ── Save driver ───────────────────────────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save') {
        $id               = (int) input('driver_id');
        $driverName       = input('driver_name');
        $driverMobile     = input('driver_mobile');
        $licenseNo        = input('license_no');
        $idNumber         = input('id_number');
        $address          = input('address');
        $emergencyContact = input('emergency_contact');
        $status           = input('status') ?: 'active';

        if ($driverName === '') {
            reply(['status' => 'error', 'msg' => 'Driver name is required.'], 422);
        }

        if ($id > 0) {
            $stmt = $db->prepare(
                'UPDATE drivers SET driver_name=?, driver_mobile=?, license_no=?, id_number=?, address=?, emergency_contact=?, status=? WHERE id=?'
            );
            $stmt->bind_param('sssssssi', $driverName, $driverMobile, $licenseNo, $idNumber, $address, $emergencyContact, $status, $id);
            $stmt->execute();
            reply(['status' => 'success', 'msg' => 'Driver updated successfully.']);
        } else {
            $ins = $db->prepare(
                'INSERT INTO drivers (driver_name, driver_mobile, license_no, id_number, address, emergency_contact, status) VALUES (?,?,?,?,?,?,?)'
            );
            $ins->bind_param('sssssss', $driverName, $driverMobile, $licenseNo, $idNumber, $address, $emergencyContact, $status);
            $ins->execute();
            reply(['status' => 'success', 'msg' => 'Driver added successfully.', 'id' => $db->insert_id]);
        }
    }

    // ── Delete driver ─────────────────────────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {
        $id = (int) input('id');
        if (!$id) reply(['status' => 'error', 'msg' => 'Invalid ID.'], 422);

        $chk = $db->prepare(
            "SELECT COUNT(*) FROM driver_assignments WHERE driver_id=? AND return_date IS NULL"
        );
        $chk->bind_param('i', $id);
        $chk->execute();
        if ((int) $chk->get_result()->fetch_row()[0] > 0) {
            reply(['status' => 'error', 'msg' => 'Cannot delete — driver has active assignments.'], 409);
        }

        $del = $db->prepare('DELETE FROM drivers WHERE id=?');
        $del->bind_param('i', $id);
        $del->execute();
        reply(['status' => 'success', 'msg' => 'Driver deleted.']);
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  ASSIGNMENTS
    // ══════════════════════════════════════════════════════════════════════════

    // ── List all assignments ──────────────────────────────────────────────────
    if ($action === 'assignments_viewall') {
        $rows = $db->query(
            "SELECT da.id, da.driver_id, da.vehicle_id,
                    d.driver_name, v.plate_number,
                    DATE_FORMAT(da.assigned_date,'%d %b %Y') AS assigned_date,
                    DATE_FORMAT(da.return_date,'%d %b %Y')    AS return_date,
                    COALESCE(da.notes,'') AS notes,
                    DATE_FORMAT(da.created_at,'%d %b %Y') AS created_at
             FROM driver_assignments da
             INNER JOIN drivers  d ON d.id = da.driver_id
             INNER JOIN vehicles v ON v.id = da.vehicle_id
             ORDER BY da.id DESC"
        )->fetch_all(MYSQLI_ASSOC);
        reply($rows);
    }

    // ── Single assignment ─────────────────────────────────────────────────────
    if ($action === 'assignments_get') {
        $id = (int) ($_GET['id'] ?? 0);
        if (!$id) reply(['status' => 'error', 'msg' => 'Invalid ID.'], 422);

        $stmt = $db->prepare(
            'SELECT id, driver_id, vehicle_id,
                    DATE_FORMAT(assigned_date,\'%Y-%m-%d\') AS assigned_date,
                    DATE_FORMAT(return_date,\'%Y-%m-%d\')    AS return_date,
                    COALESCE(notes,\'\') AS notes
             FROM driver_assignments WHERE id=? LIMIT 1'
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if (!$row) reply(['status' => 'error', 'msg' => 'Assignment not found.'], 404);
        reply($row);
    }

    // ── Save assignment ───────────────────────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'assignments_save') {
        $id            = (int) input('assignment_id');
        $driverId      = (int) input('driver_id');
        $vehicleId     = (int) input('vehicle_id');
        $assignedDate  = input('assigned_date') ?: date('Y-m-d');
        $returnDate    = input('return_date');
        $notes         = input('notes');

        if (!$driverId || !$vehicleId) {
            reply(['status' => 'error', 'msg' => 'Driver and vehicle are required.'], 422);
        }

        if ($id > 0) {
            $stmt = $db->prepare(
                'UPDATE driver_assignments SET driver_id=?, vehicle_id=?, assigned_date=?, return_date=?, notes=? WHERE id=?'
            );
            $stmt->bind_param('iisssi', $driverId, $vehicleId, $assignedDate, $returnDate, $notes, $id);
            $stmt->execute();
            reply(['status' => 'success', 'msg' => 'Assignment updated successfully.']);
        } else {
            $ins = $db->prepare(
                'INSERT INTO driver_assignments (driver_id, vehicle_id, assigned_date, return_date, notes) VALUES (?,?,?,?,?)'
            );
            $ins->bind_param('iisss', $driverId, $vehicleId, $assignedDate, $returnDate, $notes);
            $ins->execute();
            reply(['status' => 'success', 'msg' => 'Assignment created successfully.', 'id' => $db->insert_id]);
        }
    }

    // ── Delete assignment ─────────────────────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'assignments_delete') {
        $id = (int) input('id');
        if (!$id) reply(['status' => 'error', 'msg' => 'Invalid ID.'], 422);

        $del = $db->prepare('DELETE FROM driver_assignments WHERE id=?');
        $del->bind_param('i', $id);
        $del->execute();
        reply(['status' => 'success', 'msg' => 'Assignment deleted.']);
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  TRIPS
    // ══════════════════════════════════════════════════════════════════════════

    // ── List all trips ────────────────────────────────────────────────────────
    if ($action === 'trips_viewall') {
        $rows = $db->query(
            "SELECT dt.id, dt.driver_id, dt.vehicle_id, dt.assignment_id,
                    d.driver_name, v.plate_number,
                    DATE_FORMAT(dt.trip_date,'%d %b %Y') AS trip_date,
                    dt.origin, dt.destination, dt.distance_km,
                    DATE_FORMAT(dt.start_time,'%H:%i') AS start_time,
                    DATE_FORMAT(dt.end_time,'%H:%i')   AS end_time,
                    dt.fare,
                    COALESCE(dt.notes,'') AS notes,
                    DATE_FORMAT(dt.created_at,'%d %b %Y') AS created_at
             FROM driver_trips dt
             INNER JOIN drivers  d ON d.id = dt.driver_id
             INNER JOIN vehicles v ON v.id = dt.vehicle_id
             ORDER BY dt.id DESC"
        )->fetch_all(MYSQLI_ASSOC);
        reply($rows);
    }

    // ── Single trip ───────────────────────────────────────────────────────────
    if ($action === 'trips_get') {
        $id = (int) ($_GET['id'] ?? 0);
        if (!$id) reply(['status' => 'error', 'msg' => 'Invalid ID.'], 422);

        $stmt = $db->prepare(
            'SELECT id, driver_id, vehicle_id, assignment_id,
                    DATE_FORMAT(trip_date,\'%Y-%m-%d\') AS trip_date,
                    origin, destination, distance_km,
                    DATE_FORMAT(start_time,\'%Y-%m-%dT%H:%i\') AS start_time,
                    DATE_FORMAT(end_time,\'%Y-%m-%dT%H:%i\')   AS end_time,
                    fare, COALESCE(notes,\'\') AS notes
             FROM driver_trips WHERE id=? LIMIT 1'
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if (!$row) reply(['status' => 'error', 'msg' => 'Trip not found.'], 404);
        reply($row);
    }

    // ── Save trip ─────────────────────────────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'trips_save') {
        $id           = (int) input('trip_id');
        $driverId     = (int) input('driver_id');
        $vehicleId    = (int) input('vehicle_id');
        $assignmentId = (int) input('assignment_id') ?: 0;
        $tripDate     = input('trip_date') ?: date('Y-m-d');
        $origin       = input('origin');
        $destination  = input('destination');
        $distanceKm   = (float) input('distance_km');
        $startTime    = input('start_time');
        $endTime      = input('end_time');
        $fare         = (float) input('fare');
        $notes        = input('notes');

        if (!$driverId || !$vehicleId) {
            reply(['status' => 'error', 'msg' => 'Driver and vehicle are required.'], 422);
        }

        if ($id > 0) {
            $stmt = $db->prepare(
                'UPDATE driver_trips SET driver_id=?, vehicle_id=?, assignment_id=?, trip_date=?, origin=?, destination=?, distance_km=?, start_time=?, end_time=?, fare=?, notes=? WHERE id=?'
            );
            $stmt->bind_param('iiisssdddssi', $driverId, $vehicleId, $assignmentId, $tripDate, $origin, $destination, $distanceKm, $startTime, $endTime, $fare, $notes, $id);
            $stmt->execute();
            reply(['status' => 'success', 'msg' => 'Trip updated successfully.']);
        } else {
            $ins = $db->prepare(
                'INSERT INTO driver_trips (driver_id, vehicle_id, assignment_id, trip_date, origin, destination, distance_km, start_time, end_time, fare, notes) VALUES (?,?,?,?,?,?,?,?,?,?,?)'
            );
            $ins->bind_param('iiisssdddss', $driverId, $vehicleId, $assignmentId, $tripDate, $origin, $destination, $distanceKm, $startTime, $endTime, $fare, $notes);
            $ins->execute();
            reply(['status' => 'success', 'msg' => 'Trip recorded successfully.', 'id' => $db->insert_id]);
        }
    }

    // ── Delete trip ───────────────────────────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'trips_delete') {
        $id = (int) input('id');
        if (!$id) reply(['status' => 'error', 'msg' => 'Invalid ID.'], 422);

        $del = $db->prepare('DELETE FROM driver_trips WHERE id=?');
        $del->bind_param('i', $id);
        $del->execute();
        reply(['status' => 'success', 'msg' => 'Trip deleted.']);
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  FUEL RECORDS
    // ══════════════════════════════════════════════════════════════════════════

    // ── List all fuel records ─────────────────────────────────────────────────
    if ($action === 'fuel_viewall') {
        $rows = $db->query(
            "SELECT fr.id, fr.driver_id, fr.vehicle_id, fr.trip_id,
                    d.driver_name, v.plate_number,
                    DATE_FORMAT(fr.fuel_date,'%d %b %Y') AS fuel_date,
                    fr.liters, fr.cost_per_liter, fr.total_cost,
                    fr.fuel_type,
                    COALESCE(fr.station,'') AS station,
                    COALESCE(fr.receipt_no,'') AS receipt_no,
                    COALESCE(fr.notes,'') AS notes,
                    DATE_FORMAT(fr.created_at,'%d %b %Y') AS created_at
             FROM fuel_records fr
             LEFT JOIN drivers  d ON d.id = fr.driver_id
             INNER JOIN vehicles v ON v.id = fr.vehicle_id
             ORDER BY fr.id DESC"
        )->fetch_all(MYSQLI_ASSOC);
        reply($rows);
    }

    // ── Single fuel record ────────────────────────────────────────────────────
    if ($action === 'fuel_get') {
        $id = (int) ($_GET['id'] ?? 0);
        if (!$id) reply(['status' => 'error', 'msg' => 'Invalid ID.'], 422);

        $stmt = $db->prepare(
            'SELECT id, driver_id, vehicle_id, trip_id,
                    DATE_FORMAT(fuel_date,\'%Y-%m-%d\') AS fuel_date,
                    liters, cost_per_liter, total_cost,
                    fuel_type,
                    COALESCE(station,\'\') AS station,
                    COALESCE(receipt_no,\'\') AS receipt_no,
                    COALESCE(notes,\'\') AS notes
             FROM fuel_records WHERE id=? LIMIT 1'
        );
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if (!$row) reply(['status' => 'error', 'msg' => 'Fuel record not found.'], 404);
        reply($row);
    }

    // ── Save fuel record ──────────────────────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'fuel_save') {
        $id            = (int) input('fuel_id');
        $driverId      = (int) input('driver_id') ?: 0;
        $vehicleId     = (int) input('vehicle_id');
        $tripId        = (int) input('trip_id') ?: 0;
        $fuelDate      = input('fuel_date') ?: date('Y-m-d');
        $liters        = (float) input('liters');
        $costPerLiter  = (float) input('cost_per_liter');
        $totalCost     = (float) input('total_cost');
        $fuelType      = input('fuel_type') ?: 'DIESEL';
        $station       = input('station');
        $receiptNo     = input('receipt_no');
        $notes         = input('notes');

        if (!$vehicleId) {
            reply(['status' => 'error', 'msg' => 'Vehicle is required.'], 422);
        }

        if ($totalCost <= 0 && $liters > 0 && $costPerLiter > 0) {
            $totalCost = round($liters * $costPerLiter, 2);
        }

        if ($id > 0) {
            $stmt = $db->prepare(
                'UPDATE fuel_records SET driver_id=?, vehicle_id=?, trip_id=?, fuel_date=?, liters=?, cost_per_liter=?, total_cost=?, fuel_type=?, station=?, receipt_no=?, notes=? WHERE id=?'
            );
            $stmt->bind_param('iiiidddddssi', $driverId, $vehicleId, $tripId, $fuelDate, $liters, $costPerLiter, $totalCost, $fuelType, $station, $receiptNo, $notes, $id);
            $stmt->execute();
            reply(['status' => 'success', 'msg' => 'Fuel record updated successfully.']);
        } else {
            $ins = $db->prepare(
                'INSERT INTO fuel_records (driver_id, vehicle_id, trip_id, fuel_date, liters, cost_per_liter, total_cost, fuel_type, station, receipt_no, notes) VALUES (?,?,?,?,?,?,?,?,?,?,?)'
            );
            $ins->bind_param('iiiidddddss', $driverId, $vehicleId, $tripId, $fuelDate, $liters, $costPerLiter, $totalCost, $fuelType, $station, $receiptNo, $notes);
            $ins->execute();
            reply(['status' => 'success', 'msg' => 'Fuel record added successfully.', 'id' => $db->insert_id]);
        }
    }

    // ── Delete fuel record ────────────────────────────────────────────────────
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'fuel_delete') {
        $id = (int) input('id');
        if (!$id) reply(['status' => 'error', 'msg' => 'Invalid ID.'], 422);

        $del = $db->prepare('DELETE FROM fuel_records WHERE id=?');
        $del->bind_param('i', $id);
        $del->execute();
        reply(['status' => 'success', 'msg' => 'Fuel record deleted.']);
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  PERFORMANCE
    // ══════════════════════════════════════════════════════════════════════════

    if ($action === 'performance') {
        $rows = $db->query(
            "SELECT
                d.id AS driver_id,
                d.driver_name,
                COUNT(DISTINCT dt.id) AS total_trips,
                COALESCE(SUM(dt.distance_km), 0) AS total_distance,
                COALESCE(SUM(fr.total_cost), 0) AS total_fuel_cost,
                CASE
                    WHEN COUNT(DISTINCT dt.id) > 0
                    THEN ROUND(COALESCE(SUM(fr.total_cost), 0) / COUNT(DISTINCT dt.id), 2)
                    ELSE 0
                END AS avg_fuel_per_trip,
                COALESCE(SUM(dt.fare), 0) AS total_fare
             FROM drivers d
             LEFT JOIN driver_trips dt ON dt.driver_id = d.id
             LEFT JOIN fuel_records fr ON fr.driver_id = d.id
             GROUP BY d.id, d.driver_name
             ORDER BY d.driver_name ASC"
        )->fetch_all(MYSQLI_ASSOC);

        foreach ($rows as &$row) {
            $stmt = $db->prepare(
                "SELECT DATE_FORMAT(dt.trip_date,'%Y-%m') AS month, COUNT(*) AS trip_count
                 FROM driver_trips dt
                 WHERE dt.driver_id = ?
                   AND dt.trip_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                 GROUP BY month
                 ORDER BY month DESC"
            );
            $stmt->bind_param('i', $row['driver_id']);
            $stmt->execute();
            $row['monthly_trips'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        unset($row);

        reply($rows);
    }

    // ══════════════════════════════════════════════════════════════════════════
    //  HISTORY
    // ══════════════════════════════════════════════════════════════════════════

    // ── Single driver history ─────────────────────────────────────────────────
    if ($action === 'history') {
        $driverId = (int) ($_GET['driver_id'] ?? 0);
        if (!$driverId) reply(['status' => 'error', 'msg' => 'Driver ID is required.'], 422);

        $timeline = [];

        // Assignments
        $stmt = $db->prepare(
            "SELECT 'assignment' AS type, da.id,
                    v.plate_number AS detail,
                    DATE_FORMAT(da.assigned_date,'%d %b %Y') AS event_date,
                    CONCAT('Assigned vehicle ', v.plate_number) AS description
             FROM driver_assignments da
             INNER JOIN vehicles v ON v.id = da.vehicle_id
             WHERE da.driver_id = ?
             ORDER BY da.assigned_date DESC"
        );
        $stmt->bind_param('i', $driverId);
        $stmt->execute();
        $timeline = array_merge($timeline, $stmt->get_result()->fetch_all(MYSQLI_ASSOC));

        // Trips
        $stmt = $db->prepare(
            "SELECT 'trip' AS type, dt.id,
                    v.plate_number AS detail,
                    DATE_FORMAT(dt.trip_date,'%d %b %Y') AS event_date,
                    CONCAT(dt.origin, ' → ', dt.destination, ' (', COALESCE(dt.distance_km, 0), ' km)') AS description
             FROM driver_trips dt
             INNER JOIN vehicles v ON v.id = dt.vehicle_id
             WHERE dt.driver_id = ?
             ORDER BY dt.trip_date DESC"
        );
        $stmt->bind_param('i', $driverId);
        $stmt->execute();
        $timeline = array_merge($timeline, $stmt->get_result()->fetch_all(MYSQLI_ASSOC));

        // Fuel records
        $stmt = $db->prepare(
            "SELECT 'fuel' AS type, fr.id,
                    v.plate_number AS detail,
                    DATE_FORMAT(fr.fuel_date,'%d %b %Y') AS event_date,
                    CONCAT(fr.liters, 'L ', fr.fuel_type, ' — KES ', fr.total_cost) AS description
             FROM fuel_records fr
             INNER JOIN vehicles v ON v.id = fr.vehicle_id
             WHERE fr.driver_id = ?
             ORDER BY fr.fuel_date DESC"
        );
        $stmt->bind_param('i', $driverId);
        $stmt->execute();
        $timeline = array_merge($timeline, $stmt->get_result()->fetch_all(MYSQLI_ASSOC));

        // Sort by event_date descending (string comparison works with 'DD Mon YYYY')
        usort($timeline, function ($a, $b) {
            return strtotime($b['event_date']) - strtotime($a['event_date']);
        });

        reply($timeline);
    }

    // ── All drivers history ───────────────────────────────────────────────────
    if ($action === 'history_all') {
        $timeline = [];

        // Assignments
        $rows = $db->query(
            "SELECT 'assignment' AS type, da.id,
                    d.driver_name, v.plate_number AS detail,
                    DATE_FORMAT(da.assigned_date,'%d %b %Y') AS event_date,
                    CONCAT(d.driver_name, ' — assigned ', v.plate_number) AS description
             FROM driver_assignments da
             INNER JOIN drivers  d ON d.id = da.driver_id
             INNER JOIN vehicles v ON v.id = da.vehicle_id
             ORDER BY da.assigned_date DESC"
        )->fetch_all(MYSQLI_ASSOC);
        $timeline = array_merge($timeline, $rows);

        // Trips
        $rows = $db->query(
            "SELECT 'trip' AS type, dt.id,
                    d.driver_name, v.plate_number AS detail,
                    DATE_FORMAT(dt.trip_date,'%d %b %Y') AS event_date,
                    CONCAT(d.driver_name, ': ', dt.origin, ' → ', dt.destination, ' (', COALESCE(dt.distance_km, 0), ' km)') AS description
             FROM driver_trips dt
             INNER JOIN drivers  d ON d.id = dt.driver_id
             INNER JOIN vehicles v ON v.id = dt.vehicle_id
             ORDER BY dt.trip_date DESC"
        )->fetch_all(MYSQLI_ASSOC);
        $timeline = array_merge($timeline, $rows);

        // Fuel records
        $rows = $db->query(
            "SELECT 'fuel' AS type, fr.id,
                    d.driver_name, v.plate_number AS detail,
                    DATE_FORMAT(fr.fuel_date,'%d %b %Y') AS event_date,
                    CONCAT(d.driver_name, ': ', fr.liters, 'L ', fr.fuel_type, ' — KES ', fr.total_cost) AS description
             FROM fuel_records fr
             INNER JOIN drivers  d ON d.id = fr.driver_id
             INNER JOIN vehicles v ON v.id = fr.vehicle_id
             ORDER BY fr.fuel_date DESC"
        )->fetch_all(MYSQLI_ASSOC);
        $timeline = array_merge($timeline, $rows);

        usort($timeline, function ($a, $b) {
            return strtotime($b['event_date']) - strtotime($a['event_date']);
        });

        reply($timeline);
    }

    reply(['status' => 'error', 'msg' => 'Unsupported request.'], 405);

} catch (Throwable $e) {
    error_log('Drivers.php: ' . $e->getMessage());
    reply(['status' => 'error', 'msg' => 'Server error: ' . $e->getMessage()], 500);
}
