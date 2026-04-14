<?php
session_start();
if (isset($_SESSION['student_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Smart University</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container text-center">
    <h1>SMART UNIVERSITY</h1>
    <h2>Unit Registration & Verification System</h2>
    <p>Welcome to the student portal.</p>

    <div class="card">
        <a class="btn mt-10" href="register.php">Student Sign Up</a>
        <a class="btn mt-10" href="login.php">Student Login</a>
    </div>

</div>
</body>
</html>