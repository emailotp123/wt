<?php
session_start();
if (isset($_SESSION['user_id'])) { header('Location: dashboard.php'); exit; }
require 'config.php';
$msg = '';

// Auto-fill from cookie
$savedEmail = $_COOKIE['remember_email'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];
    $remember = isset($_POST['remember']);
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        if ($remember) setcookie('remember_email', $email, time() + 86400 * 30, '/');
        else           setcookie('remember_email', '', time() - 1, '/');
        header('Location: dashboard.php'); exit;
    } else $msg = 'Invalid email or password.';
    $db->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<div class="container mt-5" style="max-width:440px">
    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-3">Login</h4>
            <?php if ($msg): ?><div class="alert alert-danger"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
            <form method="POST">
                <div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control" name="email" value="<?= htmlspecialchars($savedEmail) ?>" required></div>
                <div class="mb-3"><label class="form-label">Password</label><input type="password" class="form-control" name="password" required></div>
                <div class="mb-3 form-check"><input type="checkbox" class="form-check-input" name="remember" id="rem" <?= $savedEmail ? 'checked' : '' ?>><label class="form-check-label" for="rem">Remember me (cookie)</label></div>
                <button class="btn btn-primary w-100">Login</button>
            </form>
            <p class="text-center mt-3 mb-0"><a href="register.php">New user? Register</a></p>
        </div>
    </div>
</div>
</body>
</html>
