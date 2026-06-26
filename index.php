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

        <?php if ($_SESSION['role'] == 'admin') { ?>
            <h2 class="text-center mb-4">All Student Results</h2>
        <?php } else { ?>
            <h2 class="text-center mb-4">My Results</h2>
        <?php } ?>

        <div class="table-responsive"> 
            <table class="table table-bordered table-hover text-center" id="resultTable">
                <thead class="table-dark">
                    <tr>
                        <?php if ($_SESSION['role'] == 'admin') { ?>
                            <th>Student ID</th>
                            <th>Student Name</th>
                        <?php } ?>
                        <th>Unit</th>
                        <th>Marks</th>
                        <th>Grade</th>
                        <th>Semester</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($_SESSION['role'] == 'admin') {

                        // Admin sees everyone's results
                        $sql = "SELECT r.student_id, u.full_name, un.unit_name, r.marks, r.grade, r.semester 
                                FROM results r
                                JOIN students s ON r.student_id = s.id
                                JOIN users u ON s.user_id = u.id
                                JOIN units un ON r.unit_id = un.id
                                GROUP BY r.student_id, r.unit_id
                                ORDER BY u.full_name ASC";
                        $stmt = $conn->prepare($sql);

                    } else {

                        // Student sees only their own results.
                        // $_SESSION['user_id'] is a users.id, so we go through
                        // students.user_id to find the matching students.id first.
                        $sql = "SELECT r.student_id, u.full_name, un.unit_name, r.marks, r.grade, r.semester 
                                FROM results r
                                JOIN students s ON r.student_id = s.id
                                JOIN users u ON s.user_id = u.id
                                JOIN units un ON r.unit_id = un.id
                                WHERE s.user_id = ?
                                GROUP BY r.unit_id
                                ORDER BY un.unit_name ASC";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $_SESSION['user_id']);

                    }

                    $stmt->execute();
                    $result = $stmt->get_result();
                    $last_student_id = 0;

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";

                            if ($_SESSION['role'] == 'admin') {
                                if ($row["student_id"] != $last_student_id) {
                                    echo "<td><b>" . htmlspecialchars($row["student_id"]) . "</b></td>";
                                    echo "<td>" . htmlspecialchars($row["full_name"]) . "</td>";
                                    $last_student_id = $row["student_id"];
                                } else {
                                    echo "<td></td><td></td>";
                                }
                            }

                            echo "<td>" . htmlspecialchars($row["unit_name"]) . "</td>";
                            echo "<td>" . htmlspecialchars($row["marks"]) . "</td>";
                            echo "<td><span class='badge bg-success'>" . htmlspecialchars($row["grade"]) . "</span></td>";
                            echo "<td>" . htmlspecialchars($row["semester"]) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        $colspan = ($_SESSION['role'] == 'admin') ? 6 : 4;
                        echo "<tr><td colspan='$colspan'>No records found</td></tr>";
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
