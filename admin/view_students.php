<?php
session_start();
include("../db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$result = $conn->query("SELECT * FROM students ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Students - Smart University</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
<div class="container">
    <h1>All Students</h1>

    <table>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Admission Number</th>
            <th>Course</th>
            <th>Level</th>
            <th>Fees Paid</th>
            <th>Total Fees</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['fullname']); ?></td>
            <td><?php echo htmlspecialchars($row['adm_number']); ?></td>
            <td><?php echo htmlspecialchars($row['course']); ?></td>
            <td><?php echo htmlspecialchars($row['education_level']); ?></td>
            <td>KES <?php echo number_format($row['fees_paid']); ?></td>
            <td>KES <?php echo number_format($row['total_fees']); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <a class="btn mt-20" href="admin_dashboard.php">Back to Dashboard</a>
</div>
</body>
</html>