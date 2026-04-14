<?php
session_start();
include("db.php");

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

$fee_percent = 0;
if ($student['total_fees'] > 0) {
    $fee_percent = ($student['fees_paid'] / $student['total_fees']) * 100;
}

$units_stmt = $conn->prepare("SELECT * FROM units WHERE course = ? AND education_level = ?");
$units_stmt->bind_param("ss", $student['course'], $student['education_level']);
$units_stmt->execute();
$units_result = $units_stmt->get_result();

$reg_stmt = $conn->prepare("
    SELECT ur.*, u.unit_code, u.unit_name
    FROM unit_registrations ur
    JOIN units u ON ur.unit_id = u.id
    WHERE ur.student_id = ?
");
$reg_stmt->bind_param("i", $student_id);
$reg_stmt->execute();
$registered_units = $reg_stmt->get_result();

$registered_count = $registered_units->num_rows;

$verification_stmt = $conn->prepare("
    SELECT 
        COUNT(*) as total_registered,
        SUM(CASE WHEN status = 'verified' THEN 1 ELSE 0 END) as total_verified
    FROM unit_registrations
    WHERE student_id = ?
");
$verification_stmt->bind_param("i", $student_id);
$verification_stmt->execute();
$verification_data = $verification_stmt->get_result()->fetch_assoc();

$total_registered = $verification_data['total_registered'] ?? 0;
$total_verified = $verification_data['total_verified'] ?? 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard - Smart University</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1>SMART UNIVERSITY</h1>
    <h2>Student Dashboard</h2>

    <div class="card">
        <h3>Welcome <?php echo htmlspecialchars($student['fullname']); ?>!</h3>
        <p><strong>Admission Number:</strong> <?php echo htmlspecialchars($student['adm_number']); ?></p>
        <p><strong>Course:</strong> <?php echo htmlspecialchars($student['course']); ?></p>
        <p><strong>Education Level:</strong> <?php echo htmlspecialchars($student['education_level']); ?></p>
        <p><strong>Fees Paid:</strong> KES <?php echo number_format($student['fees_paid']); ?> / KES <?php echo number_format($student['total_fees']); ?></p>
        <p><strong>Fee Progress:</strong> <?php echo round($fee_percent, 1); ?>%</p>
    </div>

    <div class="card">
        <h3>Available Units (Your Course Only)</h3>

        <?php if ($units_result->num_rows > 0): ?>
            <form method="POST" action="confirm_units.php">
                <?php while ($unit = $units_result->fetch_assoc()): ?>
                    <div class="badge" style="display:block; margin-bottom:10px; padding:10px;">
                        <label>
                            <input type="checkbox" name="units[]" value="<?php echo $unit['id']; ?>" style="width:auto; margin-right:10px;">
                            <strong><?php echo htmlspecialchars($unit['unit_code']); ?></strong> - <?php echo htmlspecialchars($unit['unit_name']); ?>
                        </label>
                    </div>
                <?php endwhile; ?>

                <button type="submit" name="confirm_units">Confirm Selected Units</button>
            </form>
        <?php else: ?>
            <div class="warning">No units available yet for your course and level.</div>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3>Registered Units</h3>
        <?php
        if ($registered_count > 0) {
            $registered_units->data_seek(0);
            echo "<table>";
            echo "<tr><th>Unit Code</th><th>Unit Name</th><th>Status</th></tr>";
            while ($reg = $registered_units->fetch_assoc()) {
                $badgeClass = ($reg['status'] == 'verified') ? 'badge-success' : 'badge-warning';

                echo "<tr>";
                echo "<td>" . htmlspecialchars($reg['unit_code']) . "</td>";
                echo "<td>" . htmlspecialchars($reg['unit_name']) . "</td>";
                echo "<td><span class='badge " . $badgeClass . "'>" . ucfirst($reg['status']) . "</span></td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<div class='warning'>No units registered yet.</div>";
        }
        ?>
    </div>

    <div class="card">
        <h3>Verification Status</h3>
        <p><strong>Total Registered Units:</strong> <?php echo $total_registered; ?></p>
        <p><strong>Verified Units:</strong> <?php echo $total_verified; ?></p>

        <?php if ($total_registered > 0 && $total_verified == $total_registered): ?>
            <div class="success">All selected units have been verified successfully.</div>
        <?php elseif ($total_registered > 0 && $total_verified > 0): ?>
            <div class="warning">Some units are verified. Complete verification for all units.</div>
        <?php elseif ($total_registered > 0): ?>
            <div class="warning">Units selected but verification pending.</div>
        <?php else: ?>
            <div class="warning">No units selected for verification yet.</div>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3>Verification & Scanner</h3>
        <?php if ($fee_percent >= 40): ?>
            <div class="success">Fees requirement met. You can proceed to scanner / verification.</div>
            <a class="btn" href="scanner.php">Open Scanner / Verification</a>
        <?php else: ?>
            <div class="error">You need at least 40% fees paid to access scanner / verification.</div>
        <?php endif; ?>
    </div>

    <a class="btn mt-20" href="logout.php">Logout</a>
</div>
</body>
</html>