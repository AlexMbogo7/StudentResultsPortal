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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add / Edit Student Results</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3> Manage Student Results</h3>
            <div>
                <span class="me-3 text-dark fw-bold"> <?php echo $_SESSION['full_name']; ?> (Admin)</span>
                <a href="index.php" class="btn btn-outline-secondary btn-sm"> Home</a>
                <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
            </div>
        </div>

        <?php
        if (isset($_GET['delete'])) {
            $delete_id = $_GET['delete'];
            $conn->query("DELETE FROM results WHERE id=$delete_id");
            echo "<div class='alert alert-danger'>Record Deleted Successfully!</div>";
        }

        $edit_mode = false;
        $edit_id = 0;
        $edit_student_id = "";
        $edit_unit_id = "";
        $edit_marks = "";

        if (isset($_GET['edit'])) {
            $edit_id = $_GET['edit'];
            $edit_mode = true;
            $edit_query = $conn->query("SELECT * FROM results WHERE id=$edit_id");
            if ($edit_query->num_rows > 0) {
                $edit_row = $edit_query->fetch_assoc();
                $edit_student_id = $edit_row['student_id'];
                $edit_unit_id = $edit_row['unit_id'];
                $edit_marks = $edit_row['marks'];
            }
        }

        if (isset($_POST['submit'])) {
            $sid = $_POST['student_id'];
            $uid = $_POST['unit_id'];
            $marks = $_POST['marks'];
            $grade = ($marks >= 70) ? 'A' : (($marks >= 60) ? 'B' : (($marks >= 50) ? 'C' : 'D'));
            
            if ($_POST['action'] == 'update') {
                $id = $_POST['id'];
                $conn->query("UPDATE results SET student_id='$sid', unit_id='$uid', marks='$marks', grade='$grade' WHERE id=$id");
                echo "<div class='alert alert-success'>Result Updated Successfully!</div>";
            } else {
                $conn->query("INSERT INTO results (student_id, unit_id, marks, grade, semester, year) VALUES ('$sid', '$uid', '$marks', '$grade', 1, 2026)");
                echo "<div class='alert alert-success'>New Result Added Successfully!</div>";
            }
        }
        ?>

        <div class="card shadow mb-5">
            <div class="card-header bg-primary text-white">
                <?php echo $edit_mode ? 'Edit Result' : 'Add New Result'; ?>
            </div>
            <div class="card-body">
                <form action="" method="POST">
                    <input type="hidden" name="id" value="<?php echo $edit_id; ?>">
                    <input type="hidden" name="action" value="<?php echo $edit_mode ? 'update' : 'add'; ?>">

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Student ID</label>
                            <input type="number" name="student_id" class="form-control" required value="<?php echo $edit_student_id; ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Unit ID</label>
                            <input type="number" name="unit_id" class="form-control" required value="<?php echo $edit_unit_id; ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Marks</label>
                            <input type="number" name="marks" class="form-control" min="0" max="100" required value="<?php echo $edit_marks; ?>">
                        </div>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary w-100">
                        <?php echo $edit_mode ? 'Update Result' : 'Save Result'; ?>
                    </button>
                    <?php if ($edit_mode) { ?>
                        <a href="add_result.php" class="btn btn-outline-secondary w-100 mt-2">Cancel Edit</a>
                    <?php } ?>
                </form>
            </div>
        </div>

        <h4 class="mt-5 mb-3">Current Results Database</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped text-center bg-white">
                <thead class="table-dark">
                    <tr>
                        <th>Student ID</th>
                        <th>Student</th>
                        <th>Unit ID</th>
                        <th>Unit Name</th>
                        <th>Marks</th>
                        <th>Grade</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT r.id, r.student_id, u.full_name, un.id as unit_id, un.unit_name, r.marks, r.grade 
                            FROM results r
                            JOIN students s ON r.student_id = s.id
                            JOIN users u ON s.user_id = u.id
                            JOIN units un ON r.unit_id = un.id
                            GROUP BY r.student_id, r.unit_id
                            ORDER BY u.full_name ASC";
                    $res = $conn->query($sql);
                    $last_sid = 0; 

                    if ($res->num_rows > 0) {
                        while ($row = $res->fetch_assoc()) {
                            echo "<tr>";
                            
                            if($row["student_id"] != $last_sid) {
                                echo "<td><b>" . $row["student_id"] . "</b></td>";
                                $last_sid = $row["student_id"]; 
                            } else {
                                echo "<td></td>";
                            }

                            if($row["student_id"] == $last_sid) {
                                echo "<td>" . $row["full_name"] . "</td>";
                            } else {
                                echo "<td></td>";
                            }

                            echo "<td>" . $row['unit_id'] . "</td>"; 
                            echo "<td>" . $row['unit_name'] . "</td>";
                            echo "<td>" . $row['marks'] . "</td>";
                            echo "<td><b>" . $row['grade'] . "</b></td>";
                            
                            echo "<td>
                                    <a href='add_result.php?edit=" . $row['id'] . "' class='btn btn-warning btn-sm'>Edit</a>
                                    <a href='add_result.php?delete=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\");'>Delete</a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No results found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>