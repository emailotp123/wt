<?php
// Q17: Waste Collection - Report waste location
$host='localhost'; $user='root'; $pass=''; $db='waste_db';
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die('DB Error: ' . $conn->connect_error);
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']);
    $location = trim($_POST['location']);
    $type     = $_POST['waste_type'];
    $desc     = trim($_POST['description']);
    if ($name && $location && $type) {
        $stmt = $conn->prepare("INSERT INTO waste_reports (reporter_name, location, waste_type, description) VALUES (?,?,?,?)");
        $stmt->bind_param('ssss', $name, $location, $type, $desc);
        $stmt->execute();
        $msg = 'Thank you! Your waste report has been submitted. The concerned authority will be notified.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Q17: Report Waste</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<div class="container mt-4" style="max-width:560px">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>♻️ Report Waste</h3>
        <a href="authority.php" class="btn btn-sm btn-outline-success">Authority View</a>
    </div>
    <?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <div class="card shadow-sm"><div class="card-body">
        <form method="POST">
            <div class="mb-3"><label class="form-label">Your Name</label><input class="form-control" name="name" required></div>
            <div class="mb-3"><label class="form-label">Waste Location</label><input class="form-control" name="location" placeholder="e.g. Near VIT Gate 2, Bibwewadi" required></div>
            <div class="mb-3">
                <label class="form-label">Waste Type</label>
                <select class="form-select" name="waste_type" required>
                    <option value="">-- Select --</option>
                    <?php foreach (['Plastic','Paper','Metal','Organic','Mixed','Other'] as $t): ?>
                        <option><?= $t ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3"><label class="form-label">Additional Details (optional)</label>
                <textarea class="form-control" name="description" rows="3" placeholder="Describe the waste or quantity..."></textarea>
            </div>
            <button class="btn btn-success w-100">Submit Report</button>
        </form>
    </div></div>
</div>
</body>
</html>
