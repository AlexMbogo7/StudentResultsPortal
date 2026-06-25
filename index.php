<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'database.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Results Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="logo.png" alt="Logo" width="150" height="150" class="d-inline-block align-text-top me-2">
                Students Results Portal
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item me-3 text-white">
                         <?php echo $_SESSION['full_name']; ?> (<?php echo ucfirst($_SESSION['role']); ?>)
                    </li>
                    <!-- Show 'Manage Results' link only to Admins -->
                    <?php if ($_SESSION['role'] == 'admin') { ?>
                        <li class="nav-item me-3">
                            <a class="nav-link text-warning" href="add_result.php"> Manage Results</a>
                        </li>
                    <?php } ?>
                    <li class="nav-item">
                        <a class="btn btn-outline-light btn-sm" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="text-center mb-4">Student Grade Viewer</h2>

        <div class="table-responsive"> 
            <table class="table table-bordered table-hover text-center" id="resultTable">
                <thead class="table-dark">
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Unit</th>
                        <th>Marks</th>
                        <th>Grade</th>
                        <th>Semester</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT r.student_id, u.full_name, un.unit_name, r.marks, r.grade, r.semester 
                            FROM results r
                            JOIN students s ON r.student_id = s.id
                            JOIN users u ON s.user_id = u.id
                            JOIN units un ON r.unit_id = un.id
                            GROUP BY r.student_id, r.unit_id
                            ORDER BY u.full_name ASC"; 
                            
                    $result = $conn->query($sql);
                    $last_student_id = 0; 

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            
                            if($row["student_id"] != $last_student_id) {
                                echo "<td><b>" . $row["student_id"] . "</b></td>";
                                $last_student_id = $row["student_id"]; 
                            } else {
                                echo "<td></td>";
                            }

                            if($row["student_id"] == $last_student_id) {
                                echo "<td>" . $row["full_name"] . "</td>";
                            } else {
                                echo "<td></td>";
                            }

                            echo "<td>" . $row["unit_name"] . "</td>";
                            echo "<td>" . $row["marks"] . "</td>";
                            echo "<td><span class='badge bg-success'>" . $row["grade"] . "</span></td>";
                            echo "<td>" . $row["semester"] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No records found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>