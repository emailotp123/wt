<?php
session_start(); require 'config.php';
if (!isset($_SESSION['admin'])) { header('Location: admin_login.php'); exit; }
$db = getDB();
if (isset($_GET['status'], $_GET['id'])) {
    $id = (int)$_GET['id'];
    $status = in_array($_GET['status'], ['Pending','In Progress','Resolved']) ? $_GET['status'] : 'Pending';
    $stmt = $db->prepare("UPDATE complaints SET status=? WHERE id=?");
    $stmt->bind_param('si', $status, $id);
    $stmt->execute();
}
$complaints = $db->query("SELECT c.*, s.name, s.email FROM complaints c JOIN students s ON c.student_id=s.id ORDER BY c.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Admin — All Complaints</h4>
        <a href="logout.php" class="btn btn-sm btn-danger">Logout</a>
    </div>
    <div class="card"><div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead><tr><th>#</th><th>Student</th><th>Title</th><th>Description</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
            <tbody>
            <?php while ($c = $complaints->fetch_assoc()): ?>
                <tr>
                    <td><?= $c['id'] ?></td>
                    <td><?= htmlspecialchars($c['name']) ?><br><small class="text-muted"><?= htmlspecialchars($c['email']) ?></small></td>
                    <td><?= htmlspecialchars($c['title']) ?></td>
                    <td class="small"><?= htmlspecialchars(substr($c['description'], 0, 80)) ?>...</td>
                    <td><span class="badge bg-<?= $c['status']==='Resolved'?'success':($c['status']==='In Progress'?'warning text-dark':'secondary') ?>"><?= $c['status'] ?></span></td>
                    <td class="small"><?= date('d M Y', strtotime($c['created_at'])) ?></td>
                    <td>
                        <a href="?id=<?= $c['id'] ?>&status=In+Progress" class="btn btn-warning btn-sm">In Progress</a>
                        <a href="?id=<?= $c['id'] ?>&status=Resolved" class="btn btn-success btn-sm">Resolve</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div></div>
</div>
</body>
</html>
