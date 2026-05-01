<?php
session_start(); require 'config.php';
if (isset($_SESSION['student_id'])) { header('Location: complaint.php'); exit; }
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']); $pass = $_POST['password'];
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM students WHERE email=?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if ($user && password_verify($pass, $user['password']) && $email !== ADMIN_EMAIL) {
        $_SESSION['student_id'] = $user['id'];
        $_SESSION['student_name'] = $user['name'];
        header('Location: complaint.php'); exit;
    } else $msg = 'Invalid credentials.';
    $db->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Student Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<div class="container mt-5" style="max-width:440px">
    <div class="card shadow-sm"><div class="card-body">
        <h4>Student Login</h4>
        <?php if ($msg): ?><div class="alert alert-danger"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
        <form method="POST">
            <div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control" name="email" required></div>
            <div class="mb-3"><label class="form-label">Password</label><input type="password" class="form-control" name="password" required></div>
            <button class="btn btn-primary w-100">Login</button>
        </form>
        <p class="text-center mt-3 mb-0"><a href="register.php">New student? Register</a> &bull; <a href="admin_login.php">Admin Login</a></p>
    </div></div>
</div>
</body>
</html>
