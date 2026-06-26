<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}
include 'database.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Results Overview</h3>
            <div>
                <span class="me-3 text-dark fw-bold"> <?php echo $_SESSION['full_name']; ?> (Admin)</span>
                <a href="index.php" class="btn btn-outline-primary btn-sm"> View Site</a>
                <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-dark text-white">All Database Results</div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-striped mb-0 text-center">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Student Name</th>
                                        <th>Unit ID</th>
                                        <th>Unit Name</th>
                                        <th>Marks</th>
                                        <th>Grade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $sql = "SELECT r.student_id, u.full_name, un.id as unit_id, un.unit_name, r.marks, r.grade 
                                            FROM results r
                                            JOIN students s ON r.student_id = s.id
                                            JOIN users u ON s.user_id = u.id
                                            JOIN units un ON r.unit_id = un.id
                                            GROUP BY r.student_id, r.unit_id
                                            ORDER BY u.full_name ASC";
                                    $res = $conn->query($sql);
                                    
                                    $last_student_id = 0; 

                                    if ($res->num_rows > 0) {
                                        while($row = $res->fetch_assoc()){
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

                                            echo "<td>" . $row["unit_id"] . "</td>";
                                            echo "<td>" . $row["unit_name"] . "</td>";
                                            echo "<td>" . $row["marks"] . "</td>";
                                            echo "<td><b>" . $row["grade"] . "</b></td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6'>No results found</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>