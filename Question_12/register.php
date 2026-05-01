<?php
require 'config.php';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roll  = trim($_POST['roll_no']);
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $course = trim($_POST['course']);
    if ($roll && $name && $email && $course) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO students (roll_no, name, email, course) VALUES (?,?,?,?)");
        $stmt->bind_param('ssss', $roll, $name, $email, $course);
        if ($stmt->execute()) $msg = "Student registered successfully!";
        else $msg = "Error: Roll number may already exist.";
        $db->close();
    } else $msg = "All fields are required.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Student Registration</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<div class="container mt-4" style="max-width:500px">
    <h3>Student Registration</h3>
    <a href="index.php" class="btn btn-sm btn-secondary mb-3">← Back</a>
    <?php if ($msg): ?><div class="alert alert-<?= str_contains($msg,'success')?'success':'danger' ?>"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <div class="card"><div class="card-body">
        <form method="POST">
            <div class="mb-3"><label class="form-label">Roll Number</label><input class="form-control" name="roll_no" required></div>
            <div class="mb-3"><label class="form-label">Full Name</label><input class="form-control" name="name" required></div>
            <div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control" name="email" required></div>
            <div class="mb-3"><label class="form-label">Course</label><input class="form-control" name="course" placeholder="e.g. B.Tech Computer" required></div>
            <button class="btn btn-primary w-100">Register</button>
        </form>
    </div></div>
</div>
</body>
</html>
