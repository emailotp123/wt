<?php
session_start();
require 'config.php';
$msg = ''; $type = 'danger';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];
    if (!$name || !$email || !$pass) { $msg = 'All fields required.'; }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $msg = 'Invalid email.'; }
    else {
        $db = getDB();
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (name, email, password) VALUES (?,?,?)");
        $stmt->bind_param('sss', $name, $email, $hash);
        if ($stmt->execute()) { $msg = 'Registered! You can now login.'; $type = 'success'; }
        else $msg = 'Email already registered.';
        $db->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Register</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<div class="container mt-5" style="max-width:440px">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-3">Create Account</h4>
            <?php if ($msg): ?><div class="alert alert-<?= $type ?>"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
            <form method="POST">
                <div class="mb-3"><label class="form-label">Name</label><input class="form-control" name="name" required></div>
                <div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control" name="email" required></div>
                <div class="mb-3"><label class="form-label">Password</label><input type="password" class="form-control" name="password" required></div>
                <button class="btn btn-primary w-100">Register</button>
            </form>
            <p class="text-center mt-3 mb-0"><a href="login.php">Already have an account? Login</a></p>
        </div>
    </div>
</div>
</body>
</html>
