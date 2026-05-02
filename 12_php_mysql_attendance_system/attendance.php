<?php
require 'config.php';
$db = getDB();
$date = $_POST['date'] ?? date('Y-m-d');
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_attendance'])) {
    $date = $_POST['date'];
    $present = $_POST['present'] ?? [];
    $students = $db->query("SELECT id FROM students");
    while ($s = $students->fetch_assoc()) {
        $sid = $s['id'];
        $status = in_array($sid, $present) ? 'Present' : 'Absent';
        $stmt = $db->prepare("INSERT INTO attendance (student_id, date, status) VALUES (?,?,?) ON DUPLICATE KEY UPDATE status=?");
        $stmt->bind_param('isss', $sid, $date, $status, $status);
        $stmt->execute();
    }
    $msg = "Attendance saved for $date!";
}

$students = $db->query("SELECT * FROM students ORDER BY roll_no");
$attendance = [];
$stmt2 = $db->prepare("SELECT student_id, status FROM attendance WHERE date=?");
$stmt2->bind_param('s', $date);
$stmt2->execute();
$res = $stmt2->get_result();
while ($r = $res->fetch_assoc()) $attendance[$r['student_id']] = $r['status'];
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Take Attendance</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<div class="container mt-4" style="max-width:700px">
    <h3>Teacher — Take Attendance</h3>
    <a href="index.php" class="btn btn-sm btn-secondary mb-3">← Back</a>
    <?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <form method="POST">
        <div class="mb-3 d-flex gap-2 align-items-center">
            <label class="form-label mb-0"><strong>Date:</strong></label>
            <input type="date" class="form-control w-auto" name="date" value="<?= htmlspecialchars($date) ?>">
            <button class="btn btn-outline-secondary btn-sm">Load</button>
        </div>
        <div class="card">
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead><tr><th>Roll No</th><th>Name</th><th>Course</th><th class="text-center">Present ✓</th></tr></thead>
                    <tbody>
                    <?php $students->data_seek(0); while ($s = $students->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['roll_no']) ?></td>
                            <td><?= htmlspecialchars($s['name']) ?></td>
                            <td><?= htmlspecialchars($s['course']) ?></td>
                            <td class="text-center">
                                <input type="checkbox" name="present[]" value="<?= $s['id'] ?>"
                                    <?= ($attendance[$s['id']] ?? '') === 'Present' ? 'checked' : '' ?>>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <button class="btn btn-success mt-3 w-100" name="submit_attendance">Save Attendance</button>
    </form>
</div>
</body>
</html>
