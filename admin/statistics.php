<?php
session_start();
include("../db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$total_students = $conn->query("SELECT COUNT(*) as total FROM students")->fetch_assoc()['total'];
$total_units = $conn->query("SELECT COUNT(*) as total FROM units")->fetch_assoc()['total'];
$total_regs = $conn->query("SELECT COUNT(*) as total FROM unit_registrations")->fetch_assoc()['total'];
$verified_regs = $conn->query("SELECT COUNT(*) as total FROM unit_registrations WHERE status = 'verified'")->fetch_assoc()['total'];
$pending_regs = $conn->query("SELECT COUNT(*) as total FROM unit_registrations WHERE status = 'pending'")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Statistics - Smart University</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
<div class="container">
    <h1>System Statistics</h1>

    <div class="card"><h3>Total Students: <?php echo $total_students; ?></h3></div>
    <div class="card"><h3>Total Units: <?php echo $total_units; ?></h3></div>
    <div class="card"><h3>Total Registrations: <?php echo $total_regs; ?></h3></div>
    <div class="card"><h3>Verified Registrations: <?php echo $verified_regs; ?></h3></div>
    <div class="card"><h3>Pending Registrations: <?php echo $pending_regs; ?></h3></div>

    <a class="btn mt-20" href="admin_dashboard.php">Back to Dashboard</a>
</div>
</body>
</html>