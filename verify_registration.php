<?php
session_start();
include("db.php");

if (!isset($_SESSION['student_id'])) {
    echo "unauthorized";
    exit();
}

$student_id = $_SESSION['student_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "invalid_request";
    exit();
}

$method = isset($_POST['method']) ? trim($_POST['method']) : 'unknown';
$payload = isset($_POST['payload']) ? trim($_POST['payload']) : '';

$check_stmt = $conn->prepare("SELECT COUNT(*) as total FROM unit_registrations WHERE student_id = ?");
$check_stmt->bind_param("i", $student_id);
$check_stmt->execute();
$check_data = $check_stmt->get_result()->fetch_assoc();

if ($check_data['total'] == 0) {
    echo "no_units";
    exit();
}

$update_stmt = $conn->prepare("
    UPDATE unit_registrations 
    SET status = 'verified',
        verification_method = ?,
        verification_payload = ?,
        verified_at = NOW()
    WHERE student_id = ?
");
$update_stmt->bind_param("ssi", $method, $payload, $student_id);

if ($update_stmt->execute()) {
    echo "success";
} else {
    echo "db_error";
}
?>