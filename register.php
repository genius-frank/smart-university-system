<?php
session_start();
include("db.php");

$error = "";
$success = "";

if (isset($_POST['signup'])) {
    $fullname = trim($_POST['fullname']);
    $adm_number = trim($_POST['adm_number']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $education_level = trim($_POST['education_level']);
    $course = trim($_POST['course']);

    if (empty($fullname) || empty($adm_number) || empty($email) || empty($_POST['password']) || empty($education_level) || empty($course)) {
        $error = "Please fill in all fields.";
    } else {
        $check = $conn->prepare("SELECT id FROM students WHERE adm_number = ? OR email = ?");
        $check->bind_param("ss", $adm_number, $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "Admission number or email already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO students (fullname, adm_number, email, password, education_level, course, fees_paid, total_fees) VALUES (?, ?, ?, ?, ?, ?, 0, 100000)");
            $stmt->bind_param("ssssss", $fullname, $adm_number, $email, $password, $education_level, $course);

            if ($stmt->execute()) {
                $student_id = $stmt->insert_id;

                $_SESSION['student_id'] = $student_id;
                $_SESSION['student_name'] = $fullname;

                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Registration failed. Try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Sign Up - Smart University</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="container">
    <h1>SMART UNIVERSITY</h1>
    <h2>Student Sign Up</h2>

    <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
    <?php if (!empty($success)) echo "<div class='success'>$success</div>"; ?>

    <form method="POST">
        <input type="text" name="fullname" placeholder="Full Name" required>
        <input type="text" name="adm_number" placeholder="Admission Number" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password" required>

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

        <button type="submit" name="signup">Create Student Account</button>
    </form>

    <p class="mt-20 text-center">
        Already have an account? <a href="login.php">Login here</a>
    </p>
</div>
</body>
</html>