<?php
$conn = new mysqli('localhost','root','','students_db');
if ($conn->connect_error) die('DB Error: ' . $conn->connect_error);

$id = (int)$_GET['id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']); $email = trim($_POST['email']);
    $course = trim($_POST['course']); $year = (int)$_POST['year'];
    $stmt = $conn->prepare("UPDATE students SET name=?,email=?,course=?,year=? WHERE id=?");
    $stmt->bind_param('sssii', $name, $email, $course, $year, $id);
    $stmt->execute();
    header('Location: index.php'); exit;
}

$sel = $conn->prepare("SELECT * FROM students WHERE id=?");
$sel->bind_param('i', $id);
$sel->execute();
$s = $sel->get_result()->fetch_assoc();
if (!$s) { header('Location: index.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Edit Student</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<div class="container mt-4" style="max-width:480px">
    <h4>Edit Student</h4>
    <div class="card shadow-sm"><div class="card-body">
        <form method="POST">
            <div class="mb-3"><label class="form-label">Name</label><input class="form-control" name="name" value="<?= htmlspecialchars($s['name']) ?>" required></div>
            <div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control" name="email" value="<?= htmlspecialchars($s['email']) ?>" required></div>
            <div class="mb-3"><label class="form-label">Course</label><input class="form-control" name="course" value="<?= htmlspecialchars($s['course']) ?>" required></div>
            <div class="mb-3"><label class="form-label">Year</label><input type="number" class="form-control" name="year" value="<?= $s['year'] ?>" min="1" max="4" required></div>
            <div class="d-flex gap-2">
                <button class="btn btn-success">Save Changes</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div></div>
</div>
</body>
</html>
