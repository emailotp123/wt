<?php
// Q19: Airplane Seat Booking
session_start();

// Initialize seats: 6 rows (A-F), 6 cols (1-6), 36 seats total
if (!isset($_SESSION['seats']) || isset($_GET['reset'])) {
    $seats = [];
    foreach (range('A','F') as $row) {
        foreach (range(1,6) as $col) {
            $seats[$row.$col] = ['status'=>'Available','passenger'=>''];
        }
    }
    $_SESSION['seats'] = $seats;
}

$msg = ''; $msgType = 'success';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['seat_id'])) {
    $sid  = strtoupper(trim($_POST['seat_id']));
    $name = trim($_POST['passenger']);
    if (!$name) { $msg = 'Passenger name is required.'; $msgType = 'danger'; }
    elseif (!isset($_SESSION['seats'][$sid])) { $msg = 'Invalid seat ID.'; $msgType = 'danger'; }
    elseif ($_SESSION['seats'][$sid]['status'] === 'Booked') { $msg = "Seat $sid is already booked."; $msgType = 'danger'; }
    else {
        $_SESSION['seats'][$sid] = ['status'=>'Booked','passenger'=>$name];
        $msg = "Seat $sid booked for $name!"; $msgType = 'success';
    }
}

$seats = $_SESSION['seats'];
$booked = count(array_filter($seats, fn($s) => $s['status']==='Booked'));
$available = 36 - $booked;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Q19: Airplane Seat Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .seat { width: 48px; height: 48px; border-radius: 6px 6px 0 0; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold; cursor: pointer; border: none; }
        .seat.available { background: #28a745; color: #fff; }
        .seat.booked    { background: #dc3545; color: #fff; cursor: default; }
        .aisle { width: 24px; }
        .seat-grid { display: flex; flex-direction: column; align-items: center; gap: 6px; }
        .row-seats { display: flex; gap: 6px; align-items: center; }
        .row-label { width: 24px; font-weight: bold; color: #555; font-size: 13px; }
        .col-labels { display: flex; gap: 6px; margin-bottom: 4px; }
        .col-lbl { width: 48px; text-align: center; font-size: 12px; color: #888; font-weight: bold; }
    </style>
</head>
<body class="bg-light">
<div class="container mt-4" style="max-width:750px">
    <h3 class="text-center">✈️ Airplane Seat Booking</h3>
    <div class="d-flex justify-content-center gap-3 mb-3 flex-wrap">
        <span class="badge bg-success p-2">Available: <?= $available ?></span>
        <span class="badge bg-danger p-2">Booked: <?= $booked ?></span>
        <a href="?reset=1" class="btn btn-sm btn-outline-secondary">Reset All</a>
    </div>
    <?php if ($msg): ?><div class="alert alert-<?= $msgType ?>"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <div class="row g-4">
        <div class="col-md-7">
            <div class="card shadow-sm"><div class="card-body text-center">
                <h6 class="mb-3">Seat Map — Click a seat to select</h6>
                <div class="seat-grid">
                    <div class="col-labels ms-4">
                        <?php foreach ([1,2,3] as $c): ?><div class="col-lbl"><?= $c ?></div><?php endforeach; ?>
                        <div class="aisle"></div>
                        <?php foreach ([4,5,6] as $c): ?><div class="col-lbl"><?= $c ?></div><?php endforeach; ?>
                    </div>
                    <?php foreach (range('A','F') as $row): ?>
                        <div class="row-seats">
                            <div class="row-label"><?= $row ?></div>
                            <?php foreach ([1,2,3] as $col): $sid = $row.$col; $s = $seats[$sid]; ?>
                                <form method="POST" style="margin:0">
                                    <input type="hidden" name="seat_id" value="<?= $sid ?>">
                                    <input type="hidden" name="passenger" value="Guest">
                                    <button type="button" class="seat <?= strtolower($s['status']) ?>"
                                        title="<?= $s['status']==='Booked' ? 'Booked: '.$s['passenger'] : $sid ?>"
                                        onclick="selectSeat('<?= $sid ?>')">
                                        <?= $sid ?>
                                    </button>
                                </form>
                            <?php endforeach; ?>
                            <div class="aisle"></div>
                            <?php foreach ([4,5,6] as $col): $sid = $row.$col; $s = $seats[$sid]; ?>
                                <form method="POST" style="margin:0">
                                    <input type="hidden" name="seat_id" value="<?= $sid ?>">
                                    <input type="hidden" name="passenger" value="Guest">
                                    <button type="button" class="seat <?= strtolower($s['status']) ?>"
                                        title="<?= $s['status']==='Booked' ? 'Booked: '.$s['passenger'] : $sid ?>"
                                        onclick="selectSeat('<?= $sid ?>')">
                                        <?= $sid ?>
                                    </button>
                                </form>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="mt-3 d-flex justify-content-center gap-4 small">
                    <span><span class="badge bg-success">■</span> Available</span>
                    <span><span class="badge bg-danger">■</span> Booked</span>
                </div>
            </div></div>
        </div>
        <div class="col-md-5">
            <div class="card shadow-sm"><div class="card-body">
                <h6>Book a Seat</h6>
                <form method="POST" id="bookForm">
                    <div class="mb-3"><label class="form-label">Seat ID</label>
                        <input class="form-control" name="seat_id" id="seatInput" placeholder="e.g. A1, B4" required maxlength="2"></div>
                    <div class="mb-3"><label class="form-label">Passenger Name</label>
                        <input class="form-control" name="passenger" placeholder="Full name" required></div>
                    <button class="btn btn-primary w-100">Book Seat</button>
                </form>
                <hr>
                <h6>Booked Seats</h6>
                <div style="max-height:200px;overflow-y:auto">
                <?php $hasBooked = false;
                foreach ($seats as $sid => $s): if ($s['status']==='Booked'): $hasBooked=true; ?>
                    <div class="d-flex justify-content-between small py-1 border-bottom">
                        <span class="badge bg-danger"><?= $sid ?></span>
                        <span><?= htmlspecialchars($s['passenger']) ?></span>
                    </div>
                <?php endif; endforeach;
                if (!$hasBooked) echo '<p class="text-muted small">No seats booked yet.</p>'; ?>
                </div>
            </div></div>
        </div>
    </div>
</div>
<script>
function selectSeat(sid) {
    document.getElementById('seatInput').value = sid;
    document.getElementById('seatInput').focus();
}
</script>
</body>
</html>
