<?php
session_start(); require 'config.php';
if (isset($_SESSION['admin'])) { header('Location: admin_dashboard.php'); exit; }
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']); $pass = $_POST['password'];
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM students WHERE email=?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if ($user && $email === ADMIN_EMAIL && password_verify($pass, $user['password'])) {
        $_SESSION['admin'] = true;
        header('Location: admin_dashboard.php'); exit;
    } else $msg = 'Invalid admin credentials.';
    $db->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Admin Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<div class="container mt-5" style="max-width:440px">
    <div class="card shadow-sm"><div class="card-body">
        <h4>Admin Login</h4>
        <p class="text-muted small">Default: admin@vit.edu / password</p>
        <?php if ($msg): ?><div class="alert alert-danger"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
        <form method="POST">
            <div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control" name="email" value="admin@vit.edu" required></div>
            <div class="mb-3"><label class="form-label">Password</label><input type="password" class="form-control" name="password" required></div>
            <button class="btn btn-danger w-100">Admin Login</button>
        </form>
        <p class="text-center mt-3 mb-0"><a href="student_login.php">Student Login</a></p>
    </div></div>
</div>
</body>
</html>
