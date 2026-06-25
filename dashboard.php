<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}
include '../config/db.php';

$total_students = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM students"))[0];
$total_units = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM units"))[0];
$total_results = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM results"))[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-dark bg-primary px-4">
    <span class="navbar-brand">Academia Portal</span>
    <span class="text-white"><?= $_SESSION['name'] ?></span>
    <a href="../logout.php" class="btn btn-outline-light btn-sm">Logout</a>
</nav>
<div class="container mt-4">
    <h4>System Overview</h4>
    <div class="row mt-3">
        <div class="col-md-4">
            <div class="card text-white bg-primary p-3 text-center">
                <h2><?= $total_students ?></h2>
                <p>Total Registered Students</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success p-3 text-center">
                <h2><?= $total_units ?></h2>
                <p>Total Active Units</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-warning p-3 text-center">
                <h2><?= $total_results ?></h2>
                <p>Total Grades Published</p>
            </div>
        </div>
    </div>
</div>
</body>
</html>