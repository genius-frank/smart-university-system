<?php
session_start();
include("../db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$sql = "
SELECT ur.id, s.fullname, s.adm_number, u.unit_code, u.unit_name, ur.status
FROM unit_registrations ur
JOIN students s ON ur.student_id = s.id
JOIN units u ON ur.unit_id = u.id
ORDER BY ur.id DESC
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Registrations - Smart University</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
<div class="container">
    <h1>Unit Registrations</h1>

    <table>
        <tr>
            <th>ID</th>
            <th>Student</th>
            <th>Admission Number</th>
            <th>Unit Code</th>
            <th>Unit Name</th>
            <th>Status</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['fullname']); ?></td>
            <td><?php echo htmlspecialchars($row['adm_number']); ?></td>
            <td><?php echo htmlspecialchars($row['unit_code']); ?></td>
            <td><?php echo htmlspecialchars($row['unit_name']); ?></td>
            <td><?php echo ucfirst(htmlspecialchars($row['status'])); ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <a class="btn mt-20" href="admin_dashboard.php">Back to Dashboard</a>
</div>
</body>
</html>