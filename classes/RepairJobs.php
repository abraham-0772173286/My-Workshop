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

    if ($action === 'viewall') {
        $result = $connection->query("SELECT r.id AS repair_job_id, CONCAT('RJ-', LPAD(r.id, 5, '0')) AS job_no,
            c.fullname AS customer, CONCAT(v.plate_number, ' · ', COALESCE(NULLIF(v.model, ''), 'Vehicle')) AS vehicle,
            r.repair_type, r.parts_cost, r.labour_cost, r.status, DATE_FORMAT(r.created_at, '%d %b %Y') AS date
            FROM repair_jobs r
            INNER JOIN vehicles v ON v.id = r.vehicle_id
            INNER JOIN customers c ON c.id = v.customer_id
            ORDER BY r.id DESC");
        reply($result->fetch_all(MYSQLI_ASSOC));
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $action !== 'save') {
        reply(['status' => 'error', 'msg' => 'Unsupported request.'], 405);
    }

    $customerName = input('customer_name');
    $contact = input('contact');
    $address = input('address');
    $plateNumber = strtoupper(preg_replace('/\s+/', ' ', input('plate_number')));
    $model = input('model');
    $repairType = input('repair_type');
    $partsCost = filter_var($_POST['parts_cost'] ?? null, FILTER_VALIDATE_FLOAT);
    $labourCost = filter_var($_POST['labour_cost'] ?? null, FILTER_VALIDATE_FLOAT);
    $status = input('status');

    if ($customerName === '' || $contact === '' || $plateNumber === '' || $repairType === '' || $partsCost === false || $labourCost === false) {
        reply(['status' => 'error', 'msg' => 'Please complete all required fields.'], 422);
    }
    if ($partsCost < 0 || $labourCost < 0 || !in_array($status, ['REPAIR PENDING', 'REPAIR DONE'], true)) {
        reply(['status' => 'error', 'msg' => 'Enter valid costs and repair status.'], 422);
    }

    $connection->begin_transaction();
    $customerQuery = $connection->prepare('SELECT id FROM customers WHERE contact = ? LIMIT 1');
    $customerQuery->bind_param('s', $contact);
    $customerQuery->execute();
    $customer = $customerQuery->get_result()->fetch_assoc();

    if ($customer) {
        $customerId = (int) $customer['id'];
        $updateCustomer = $connection->prepare('UPDATE customers SET fullname = ?, address = ? WHERE id = ?');
        $updateCustomer->bind_param('ssi', $customerName, $address, $customerId);
        $updateCustomer->execute();
    } else {
        $insertCustomer = $connection->prepare('INSERT INTO customers (fullname, contact, address) VALUES (?, ?, ?)');
        $insertCustomer->bind_param('sss', $customerName, $contact, $address);
        $insertCustomer->execute();
        $customerId = (int) $connection->insert_id;
    }

    $vehicleQuery = $connection->prepare('SELECT id FROM vehicles WHERE plate_number = ? LIMIT 1');
    $vehicleQuery->bind_param('s', $plateNumber);
    $vehicleQuery->execute();
    $vehicle = $vehicleQuery->get_result()->fetch_assoc();
    $dateReceived = date('Y-m-d');

    if ($vehicle) {
        $vehicleId = (int) $vehicle['id'];
        $updateVehicle = $connection->prepare('UPDATE vehicles SET customer_id = ?, car_owner = ?, model = ?, date_received = ? WHERE id = ?');
        $updateVehicle->bind_param('isssi', $customerId, $customerName, $model, $dateReceived, $vehicleId);
        $updateVehicle->execute();
    } else {
        $insertVehicle = $connection->prepare('INSERT INTO vehicles (customer_id, car_owner, plate_number, model, date_received) VALUES (?, ?, ?, ?, ?)');
        $insertVehicle->bind_param('issss', $customerId, $customerName, $plateNumber, $model, $dateReceived);
        $insertVehicle->execute();
        $vehicleId = (int) $connection->insert_id;
    }

    $insertRepairJob = $connection->prepare('INSERT INTO repair_jobs (vehicle_id, repair_type, parts_cost, labour_cost, status) VALUES (?, ?, ?, ?, ?)');
    $insertRepairJob->bind_param('isdds', $vehicleId, $repairType, $partsCost, $labourCost, $status);
    $insertRepairJob->execute();
    $repairJobId = (int) $connection->insert_id;
    $connection->commit();

    reply(['status' => 'success', 'msg' => 'Repair job saved successfully.', 'repair_job_id' => $repairJobId]);
} catch (Throwable $exception) {
    if (isset($connection) && $connection instanceof mysqli) {
        try { $connection->rollback(); } catch (Throwable) { }
    }
    // Log the actual error for debugging
    error_log('RepairJobs.php Error: ' . $exception->getMessage());
    error_log('Stack: ' . $exception->getTraceAsString());
    reply(['status' => 'error', 'msg' => 'Could not save the repair job. Check the database connection.'], 500);
}
