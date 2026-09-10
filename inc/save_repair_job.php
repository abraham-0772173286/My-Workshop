<?php
include 'config.php';

$vehicle_id  = $_POST['vehicle_id'];
$problem     = $_POST['problem'];
$parts_cost  = $_POST['parts_cost'];
$labour_cost = $_POST['labour_cost'];
$status      = $_POST['status'];

$sql = "INSERT INTO repair_jobs (vehicle_id, problem, parts_cost, labour_cost, status, date_received)
        VALUES (?, ?, ?, ?, ?, CURDATE())";

$stmt = $conn->prepare($sql);
$stmt->bind_param("isdds", $vehicle_id, $problem, $parts_cost, $labour_cost, $status);

if ($stmt->execute()) {
    header("Location: repair_jobs_register.php?success=1");
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>