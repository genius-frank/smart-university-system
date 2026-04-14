<?php
session_start();
include("../db.php");

$error = "";

if (isset($_SESSION['admin_id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();

     if ($password === 'admin123') {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['username'];

            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Invalid admin password!";
        }
    } else {
        $error = "Admin username not found!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login - Smart University</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>
<div class="container">
    <h1>SMART UNIVERSITY</h1>
    <h2>Admin Login</h2>

    <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Admin Username" required>
        <input type="password" name="password" placeholder="Admin Password" required>
        <button type="submit" name="login">Login as Admin</button>
    </form>
</div>
</body>
</html>