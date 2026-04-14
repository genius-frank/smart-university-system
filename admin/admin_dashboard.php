<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Smart University</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
<div class="container">
    <h1>SMART UNIVERSITY</h1>
    <h2>Admin Dashboard</h2>

    <div class="card">
        <h3>Welcome Admin</h3>
        <p>Manage students, units, fees, registrations and statistics.</p>
    </div>

    <ul>
        <li><a href="add_unit.php">Add Unit</a></li>
        <li><a href="view_students.php">View Students</a></li>
        <li><a href="view_registrations.php">View Registrations</a></li>
        <li><a href="update_fees.php">Update Student Fees</a></li>
        <li><a href="statistics.php">View Statistics</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>
</body>
</html>