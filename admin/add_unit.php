<?php
session_start();
include("../db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$message = "";

if (isset($_POST['add_unit'])) {
    $unit_code = trim($_POST['unit_code']);
    $unit_name = trim($_POST['unit_name']);
    $course = trim($_POST['course']);
    $education_level = trim($_POST['education_level']);

    $stmt = $conn->prepare("INSERT INTO units (unit_code, unit_name, course, education_level) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $unit_code, $unit_name, $course, $education_level);

    if ($stmt->execute()) {
        $message = "<div class='success'>Unit added successfully.</div>";
    } else {
        $message = "<div class='error'>Failed to add unit.</div>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Unit - Smart University</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
<div class="container">
    <h1>Add Unit</h1>
    <?php echo $message; ?>

    <form method="POST">
        <input type="text" name="unit_code" placeholder="Unit Code" required>
        <input type="text" name="unit_name" placeholder="Unit Name" required>

        <select name="education_level" required>
            <option value="">Select Education Level</option>
            <option value="Certificate">Certificate</option>
            <option value="Diploma">Diploma</option>
            <option value="Degree">Degree</option>
            <option value="Masters">Masters</option>
            <option value="PhD">PhD</option>
        </select>

        <select name="course" required>
            <option value="">Select Course</option>
            <option value="Certificate in Business Information Technology">Certificate in Business Information Technology</option>
            <option value="Diploma in Computer Science">Diploma in Computer Science</option>
            <option value="Diploma in Information Technology">Diploma in Information Technology</option>
            <option value="Bachelor of Computer Science">Bachelor of Computer Science</option>
            <option value="Bachelor of Business Management">Bachelor of Business Management</option>
            <option value="Master of Computer Science">Master of Computer Science</option>
            <option value="Doctor of Philosophy in Computer Science">Doctor of Philosophy in Computer Science</option>
        </select>

        <button type="submit" name="add_unit">Add Unit</button>
    </form>

    <a class="btn mt-20" href="admin_dashboard.php">Back to Dashboard</a>
</div>
</body>
</html>