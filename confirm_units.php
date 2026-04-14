<?php
session_start();
include("db.php");

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

if (isset($_POST['confirm_units']) && isset($_POST['units'])) {
    foreach ($_POST['units'] as $unit_id) {
        $check = $conn->prepare("SELECT id FROM unit_registrations WHERE student_id = ? AND unit_id = ?");
        $check->bind_param("ii", $student_id, $unit_id);
        $check->execute();
        $exists = $check->get_result();

        if ($exists->num_rows == 0) {
            $insert = $conn->prepare("INSERT INTO unit_registrations (student_id, unit_id, status) VALUES (?, ?, 'pending')");
            $insert->bind_param("ii", $student_id, $unit_id);
            $insert->execute();
        }
    }
}

header("Location: dashboard.php");
exit();
?>