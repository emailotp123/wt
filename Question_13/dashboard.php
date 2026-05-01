<?php
session_start();
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<div class="container mt-4" style="max-width:550px">
    <div class="card shadow-sm">
        <div class="card-body text-center">
            <h3>Welcome, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h3>
            <p class="text-muted">Email: <?= htmlspecialchars($_SESSION['user_email']) ?></p>
            <hr>
            <h6>Session Info</h6>
            <p class="small text-muted">Session ID: <code><?= session_id() ?></code></p>
            <h6>Cookie Info</h6>
            <p class="small text-muted">
                <?= isset($_COOKIE['remember_email'])
                    ? 'Remember-me cookie set: ' . htmlspecialchars($_COOKIE['remember_email'])
                    : 'No remember-me cookie set.' ?>
            </p>
            <a href="logout.php" class="btn btn-danger mt-2">Logout</a>
        </div>
    </div>
</div>
</body>
</html>
