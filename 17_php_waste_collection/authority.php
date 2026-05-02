<?php
$conn = new mysqli('localhost','root','','waste_db');
if ($conn->connect_error) die('DB Error: ' . $conn->connect_error);

if (isset($_GET['status'], $_GET['id'])) {
    $id = (int)$_GET['id'];
    $s = in_array($_GET['status'], ['Pending','Assigned','Collected']) ? $_GET['status'] : 'Pending';
    $stmt = $conn->prepare("UPDATE waste_reports SET status=? WHERE id=?");
    $stmt->bind_param('si', $s, $id);
    $stmt->execute();
    header("Location: authority.php"); exit;
}

$reports = $conn->query("SELECT * FROM waste_reports ORDER BY reported_at DESC");
$statusColors = ['Pending'=>'secondary','Assigned'=>'warning text-dark','Collected'=>'success'];
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Authority Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>♻️ Authority Dashboard — Waste Reports</h3>
        <a href="index.php" class="btn btn-sm btn-outline-primary">← Report Waste</a>
    </div>
    <div class="card"><div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead><tr><th>#</th><th>Reporter</th><th>Location</th><th>Type</th><th>Description</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
            <tbody>
            <?php while ($r = $reports->fetch_assoc()): ?>
                <tr>
                    <td><?= $r['id'] ?></td>
                    <td><?= htmlspecialchars($r['reporter_name']) ?></td>
                    <td><?= htmlspecialchars($r['location']) ?></td>
                    <td><span class="badge bg-info"><?= $r['waste_type'] ?></span></td>
                    <td class="small"><?= htmlspecialchars($r['description'] ?? '—') ?></td>
                    <td><span class="badge bg-<?= $statusColors[$r['status']] ?>"><?= $r['status'] ?></span></td>
                    <td class="small"><?= date('d M Y', strtotime($r['reported_at'])) ?></td>
                    <td>
                        <a href="?id=<?= $r['id'] ?>&status=Assigned" class="btn btn-warning btn-sm">Assign</a>
                        <a href="?id=<?= $r['id'] ?>&status=Collected" class="btn btn-success btn-sm">Collected</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div></div>
</div>
</body>
</html>
