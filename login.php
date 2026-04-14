<?php
session_start();
include("db.php");

$error = "";

if (isset($_SESSION['student_id'])) {
    header("Location: dashboard.php");
    exit();
}

if (isset($_POST['login'])) {
    $adm_number = trim($_POST['adm_number']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM students WHERE adm_number = ?");
    $stmt->bind_param("s", $adm_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $student = $result->fetch_assoc();

        if (password_verify($password, $student['password'])) {
            $_SESSION['student_id'] = $student['id'];
            $_SESSION['student_name'] = $student['fullname'];

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Student not found!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Login - Smart University</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1>SMART UNIVERSITY</h1>
    <h2>Student Login</h2>

    <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>

    <form method="POST">
        <input type="text" name="adm_number" placeholder="Admission Number" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login as Student</button>
    </form>

    <p class="mt-20 text-center">
        New student? <a href="register.php">Sign up here</a>
    </p>
</div>
</body>
</html>