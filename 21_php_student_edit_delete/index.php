<?php
// Q21: Student Records CRUD - Edit & Delete
$conn = new mysqli('localhost','root','','students_db');
if ($conn->connect_error) die('DB Error: ' . $conn->connect_error);
$msg = '';

// Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $del = $conn->prepare("DELETE FROM students WHERE id=?");
    $del->bind_param('i', $id);
    $del->execute();
    $msg = 'Student record deleted.';
}

// Add student
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['edit_id'])) {
    $name = trim($_POST['name']); $email = trim($_POST['email']);
    $course = trim($_POST['course']); $year = (int)$_POST['year'];
    if ($name && $email && $course && $year) {
        $stmt = $conn->prepare("INSERT INTO students (name,email,course,year) VALUES (?,?,?,?)");
        $stmt->bind_param('sssi', $name, $email, $course, $year);
        $stmt->execute() ? $msg = 'Student added.' : $msg = 'Email already exists.';
    }
}

$students = $conn->query("SELECT * FROM students ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Q21: Student Records</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<div class="container mt-4">
    <h3>Student Records — Edit & Delete</h3>
    <?php if ($msg): ?><div class="alert alert-info"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <div class="card mb-4 shadow-sm"><div class="card-body">
        <h5>Add Student</h5>
        <form method="POST" class="row g-2">
            <div class="col-md-3"><input class="form-control" name="name" placeholder="Name" required></div>
            <div class="col-md-3"><input type="email" class="form-control" name="email" placeholder="Email" required></div>
            <div class="col-md-3"><input class="form-control" name="course" placeholder="Course" required></div>
            <div class="col-md-1"><input type="number" class="form-control" name="year" placeholder="Year" min="1" max="4" required></div>
            <div class="col-md-2"><button class="btn btn-primary w-100">Add</button></div>
        </form>
    </div></div>
    <div class="card shadow-sm"><div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead class="table-dark"><tr><th>#</th><th>Name</th><th>Email</th><th>Course</th><th>Year</th><th>Actions</th></tr></thead>
            <tbody>
            <?php $students->data_seek(0); while ($s = $students->fetch_assoc()): ?>
                <tr>
                    <td><?= $s['id'] ?></td>
                    <td><?= htmlspecialchars($s['name']) ?></td>
                    <td><?= htmlspecialchars($s['email']) ?></td>
                    <td><?= htmlspecialchars($s['course']) ?></td>
                    <td><?= $s['year'] ?></td>
                    <td>
                        <a href="edit.php?id=<?= $s['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="?delete=<?= $s['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this student?')">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div></div>
</div>
</body>
</html>
