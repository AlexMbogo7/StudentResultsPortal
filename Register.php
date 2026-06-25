<?php
session_start();
include 'config/db.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name   = $_POST['full_name'];
    $email       = $_POST['email'];
    $password    = MD5($_POST['password']);
    $reg_number  = $_POST['reg_number'];
    $course_name = $_POST['course_name'];
    $year        = $_POST['year'];

    // Check if email already exists
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if (mysqli_num_rows($check) > 0) {
        $error = "Email already registered!";
    } else {
        // Insert into users table
        $sql = "INSERT INTO users (full_name, email, password, role)
                VALUES ('$full_name', '$email', '$password', 'student')";

        if (mysqli_query($conn, $sql)) {
            $user_id = mysqli_insert_id($conn);

            // Insert into students table
            $sql2 = "INSERT INTO students (user_id, reg_number, course_name, year)
                     VALUES ('$user_id', '$reg_number', '$course_name', '$year')";
            mysqli_query($conn, $sql2);

            $success = "Registration successful! You can now login.";
        } else {
            $error = "Something went wrong. Try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Result Portal - Register</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-page">
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
        <div class="card p-4 shadow" style="width: 450px;">
            <h3 class="text-center mb-4">🎓 Student Registration</h3>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label>Full Name</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Registration Number</label>
                    <input type="text" name="reg_number" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Course Name</label>
                    <input type="text" name="course_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Year of Study</label>
                    <select name="year" class="form-control" required>
                        <option value="">-- Select Year --</option>
                        <option value="1">Year 1</option>
                        <option value="2">Year 2</option>
                        <option value="3">Year 3</option>
                        <option value="4">Year 4</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">Register</button>
                <p class="text-center mt-3">Already have an account? <a href="index.php">Login here</a></p>
            </form>
        </div>
    </div>
</body>
</html>