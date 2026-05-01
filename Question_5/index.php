<?php
// Simple single-file CRUD for Question 5
// Assumes default local setup: root/@localhost with no password

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_db";

// 1. & 2. Create DB and Table if they don't exist
$conn = new mysqli($servername, $username, $password);
if (!$conn->connect_error) {
    $conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
    $conn->select_db($dbname);
    $table_sql = "CREATE TABLE IF NOT EXISTS students (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL,
        email VARCHAR(50) NOT NULL
    )";
    $conn->query($table_sql);
}

// Ensure DB is selected for CRUD
$conn = new mysqli($servername, $username, $password, $dbname);

// Handling CRUD requests
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 4. Insert Record
    if (isset($_POST["add"])) {
        $name = $_POST["name"];
        $email = $_POST["email"];
        $stmt = $conn->prepare("INSERT INTO students (name, email) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $email);
        $stmt->execute() ? $message = "Student added successfully." : $message = "Error adding student.";
    }
    // Update Record
    elseif (isset($_POST["update"])) {
        $id = $_POST["id"];
        $name = $_POST["name"];
        $email = $_POST["email"];
        $stmt = $conn->prepare("UPDATE students SET name=?, email=? WHERE id=?");
        $stmt->bind_param("ssi", $name, $email, $id);
        $stmt->execute() ? $message = "Student updated successfully." : $message = "Error updating student.";
    }
} elseif (isset($_GET["delete"])) {
    // Delete Record
    $id = $_GET["delete"];
    $stmt = $conn->prepare("DELETE FROM students WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute() ? $message = "Student deleted successfully." : $message = "Error deleting student.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Q5: PHP CRUD Operations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">
    <h2>Student Database (PHP & MySQL)</h2>
    <?php if ($message) echo "<div class='alert alert-info'>$message</div>"; ?>
    
    <!-- Add Student Form -->
    <div class="card p-3 mb-4">
        <h4>Add / Update Student</h4>
        <form method="POST">
            <input type="hidden" name="id" id="edit_id">
            <div class="mb-3">
                <input type="text" name="name" id="edit_name" class="form-control" placeholder="Name" required>
            </div>
            <div class="mb-3">
                <input type="email" name="email" id="edit_email" class="form-control" placeholder="Email" required>
            </div>
            <button type="submit" name="add" class="btn btn-success" id="btn_add">Add Record</button>
            <button type="submit" name="update" class="btn btn-warning" id="btn_update" style="display:none;">Update Record</button>
        </form>
    </div>

    <!-- 5. Display Records -->
    <h4>Existing Students</h4>
    <table class="table table-bordered">
        <tr><th>ID</th><th>Name</th><th>Email</th><th>Actions</th></tr>
        <?php
        $result = $conn->query("SELECT * FROM students");
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['name']}</td>
                        <td>{$row['email']}</td>
                        <td>
                            <button class='btn btn-sm btn-primary' onclick='editStudent({$row['id']}, \"{$row['name']}\", \"{$row['email']}\")'>Edit</button>
                            <a href='index.php?delete={$row['id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='4' class='text-center'>No records found</td></tr>";
        }
        $conn->close();
        ?>
    </table>

    <script>
        function editStudent(id, name, email) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('btn_add').style.display = 'none';
            document.getElementById('btn_update').style.display = 'inline-block';
        }
    </script>
</body>
</html>
