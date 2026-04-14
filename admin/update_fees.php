<?php
session_start();
include("../db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$message = "";

if (isset($_POST['update_fees'])) {
    $adm_number = trim($_POST['adm_number']);
    $fees_paid = floatval($_POST['fees_paid']);

    $stmt = $conn->prepare("UPDATE students SET fees_paid = ? WHERE adm_number = ?");
    $stmt->bind_param("ds", $fees_paid, $adm_number);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $message = "<div class='success'>Fees updated successfully for $adm_number.</div>";
    } else {
        $message = "<div class='error'>Student not found or no changes made.</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Update Fees - Smart University</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
<div class="container">
    <h1>Update Student Fees</h1>

    <?php echo $message; ?>

    <form method="POST">
        <input type="text" name="adm_number" placeholder="Admission Number" required>
        <input type="number" name="fees_paid" placeholder="Fees Paid (KES)" required>
        <button type="submit" name="update_fees">Update Fees</button>
    </form>

    <a class="btn mt-20" href="admin_dashboard.php">Back to Dashboard</a>
</div>
</body>
</html>