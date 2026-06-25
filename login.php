<?php
session_start();
include 'database.php';

$error = "";

if (isset($_POST['login'])) {
    $id = $_POST['id'];
    $password = $_POST['password'];

    // Simple query to find the user
    $sql = "SELECT * FROM users WHERE id = '$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // TEMPORARY PLAIN-TEXT CHECK (Ignores all hashing)
        if ($password == $row['password']) {
            
            // Login Successful!
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['full_name'] = $row['full_name'];
            $_SESSION['role'] = $row['role'];

            if ($row['role'] == 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $error = "❌ Incorrect Password! (Try typing '123456' or 'alexmbogo123')";
        }
    } else {
        $error = "❌ ID $id not found in the system!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Student Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5" style="max-width: 500px;">
        <div class="card shadow p-4">
            <h3 class="text-center mb-4">🔐 Portal Login</h3>
            
            <?php if(!empty($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">User ID</label>
                    <input type="number" name="id" class="form-control" placeholder="Enter ID (e.g., 1)" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
            </form>   
        </div>
    </div>
</body>
</html>