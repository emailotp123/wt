<?php
session_start(); require 'config.php';
if (!isset($_SESSION['student_id'])) { header('Location: student_login.php'); exit; }
$db = getDB(); $msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']); $desc = trim($_POST['description']);
    if ($title && $desc) {
        $sid = $_SESSION['student_id'];
        $stmt = $db->prepare("INSERT INTO complaints (student_id, title, description) VALUES (?,?,?)");
        $stmt->bind_param('iss', $sid, $title, $desc);
        $stmt->execute();
        $msg = 'Complaint submitted!';
    }
}
$complaints = $db->query("SELECT * FROM complaints WHERE student_id=" . (int)$_SESSION['student_id'] . " ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>My Complaints</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<div class="container mt-4" style="max-width:700px">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>My Complaints — <?= htmlspecialchars($_SESSION['student_name']) ?></h4>
        <a href="logout.php" class="btn btn-sm btn-danger">Logout</a>
    </div>
    <?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <div class="card mb-4"><div class="card-body">
        <h5>Submit New Complaint</h5>
        <form method="POST">
            <div class="mb-3"><label class="form-label">Title</label><input class="form-control" name="title" required></div>
            <div class="mb-3"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="3" required></textarea></div>
            <button class="btn btn-primary">Submit Complaint</button>
        </form>
    </div></div>
    <h5>My Previous Complaints</h5>
    <?php while ($c = $complaints->fetch_assoc()): ?>
        <div class="card mb-2"><div class="card-body">
            <div class="d-flex justify-content-between">
                <strong><?= htmlspecialchars($c['title']) ?></strong>
                <span class="badge bg-<?= $c['status']==='Resolved'?'success':($c['status']==='In Progress'?'warning text-dark':'secondary') ?>"><?= $c['status'] ?></span>
            </div>
            <p class="mb-0 mt-1 text-muted small"><?= htmlspecialchars($c['description']) ?></p>
            <small class="text-muted"><?= $c['created_at'] ?></small>
        </div></div>
    <?php endwhile; ?>
</div>
</body>
</html>
