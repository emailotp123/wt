<?php require 'config.php'; $db = getDB();
$students = $db->query("SELECT s.*, COUNT(a.id) as total, SUM(a.status='Present') as present FROM students s LEFT JOIN attendance a ON s.id=a.student_id GROUP BY s.id ORDER BY s.roll_no");
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Q12: Attendance System</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light">
<div class="container mt-4">
    <h3>Attendance Management System</h3>
    <div class="mb-3 d-flex gap-2 flex-wrap">
        <a href="register.php" class="btn btn-primary">+ Register Student</a>
        <a href="attendance.php" class="btn btn-success">Take Attendance</a>
    </div>
    <div class="card">
        <div class="card-header"><strong>Students & Attendance Summary</strong></div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead><tr><th>Roll No</th><th>Name</th><th>Course</th><th>Email</th><th>Classes</th><th>Present</th><th>%</th></tr></thead>
                <tbody>
                <?php while ($s = $students->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($s['roll_no']) ?></td>
                        <td><?= htmlspecialchars($s['name']) ?></td>
                        <td><?= htmlspecialchars($s['course']) ?></td>
                        <td><?= htmlspecialchars($s['email']) ?></td>
                        <td><?= $s['total'] ?></td>
                        <td><?= $s['present'] ?? 0 ?></td>
                        <td><?= $s['total'] > 0 ? round(($s['present']/$s['total'])*100) . '%' : 'N/A' ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
