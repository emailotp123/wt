<?php
// Q18: Complaint Management System
$conn = new mysqli('localhost','root','','complaint_mgmt_db');
if ($conn->connect_error) die('DB Error: ' . $conn->connect_error);
$msg = ''; $page = $_GET['page'] ?? 'submit';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']); $email = trim($_POST['email']);
    $org   = $_POST['organization']; $type = trim($_POST['complaint_type']);
    $desc  = trim($_POST['description']);
    if ($name && $email && $org && $type && $desc) {
        $stmt = $conn->prepare("INSERT INTO complaints (user_name,email,organization,complaint_type,description) VALUES (?,?,?,?,?)");
        $stmt->bind_param('sssss', $name, $email, $org, $type, $desc);
        $stmt->execute();
        $msg = 'Complaint submitted successfully! Reference ID: ' . $conn->insert_id;
    }
}
if (isset($_GET['status'], $_GET['id'])) {
    $id = (int)$_GET['id'];
    $s = in_array($_GET['status'], ['Open','In Review','Resolved','Closed']) ? $_GET['status'] : 'Open';
    $stmt2 = $conn->prepare("UPDATE complaints SET status=? WHERE id=?");
    $stmt2->bind_param('si', $s, $id);
    $stmt2->execute();
    header("Location: index.php?page=admin"); exit;
}
$complaints = $conn->query("SELECT * FROM complaints ORDER BY submitted_at DESC");
$statusColors = ['Open'=>'danger','In Review'=>'warning text-dark','Resolved'=>'success','Closed'=>'secondary'];
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Q18: Complaint Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-primary mb-4 px-4">
    <span class="navbar-brand fw-bold">📋 Complaint Management</span>
    <div>
        <a href="?page=submit" class="btn btn-light btn-sm me-2">Submit Complaint</a>
        <a href="?page=admin" class="btn btn-warning btn-sm">Admin View</a>
    </div>
</nav>
<div class="container" style="max-width:<?= $page==='admin'?'900px':'600px' ?>">
<?php if ($page === 'submit'): ?>
    <?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <div class="card shadow-sm"><div class="card-body">
        <h4>Submit a Complaint</h4>
        <form method="POST">
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Your Name</label><input class="form-control" name="name" required></div>
                <div class="col-md-6"><label class="form-label">Email</label><input type="email" class="form-control" name="email" required></div>
                <div class="col-md-6">
                    <label class="form-label">Organization</label>
                    <select class="form-select" name="organization" required>
                        <option value="">-- Select --</option>
                        <?php foreach (['PMC','PMT','Hospital','University','Bank','Other'] as $o): ?>
                            <option><?= $o ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6"><label class="form-label">Complaint Type</label><input class="form-control" name="complaint_type" placeholder="e.g. Road Damage, Late Bus" required></div>
                <div class="col-12"><label class="form-label">Description</label><textarea class="form-control" name="description" rows="4" required></textarea></div>
            </div>
            <button class="btn btn-primary mt-3 w-100">Submit Complaint</button>
        </form>
    </div></div>
<?php else: ?>
    <h4>All Complaints</h4>
    <div class="card"><div class="card-body p-0">
        <table class="table table-striped mb-0 small">
            <thead><tr><th>#</th><th>Name</th><th>Org</th><th>Type</th><th>Description</th><th>Status</th><th>Date</th><th>Update</th></tr></thead>
            <tbody>
            <?php while ($c = $complaints->fetch_assoc()): ?>
                <tr>
                    <td><?= $c['id'] ?></td>
                    <td><?= htmlspecialchars($c['user_name']) ?><br><small class="text-muted"><?= htmlspecialchars($c['email']) ?></small></td>
                    <td><?= $c['organization'] ?></td>
                    <td><?= htmlspecialchars($c['complaint_type']) ?></td>
                    <td><?= htmlspecialchars(substr($c['description'],0,60)) ?>...</td>
                    <td><span class="badge bg-<?= $statusColors[$c['status']] ?>"><?= $c['status'] ?></span></td>
                    <td><?= date('d M Y', strtotime($c['submitted_at'])) ?></td>
                    <td>
                        <select onchange="location='?page=admin&id=<?= $c['id'] ?>&status='+this.value" class="form-select form-select-sm">
                            <?php foreach (['Open','In Review','Resolved','Closed'] as $s): ?>
                                <option <?= $c['status']===$s?'selected':'' ?>><?= $s ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div></div>
<?php endif; ?>
</div>
</body>
</html>
