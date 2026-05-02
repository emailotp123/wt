<?php
// Function to calculate the electricity bill
function calculate_bill($units) {
    $first_50_cost = 3.50;
    $next_100_cost = 4.00;
    $next_100_cost_2 = 5.20;
    $above_250_cost = 6.50;

    if($units <= 50) {
        $bill = $units * $first_50_cost;
    }
    else if($units > 50 && $units <= 150) {
        $temp = 50 * $first_50_cost;
        $remaining_units = $units - 50;
        $bill = $temp + ($remaining_units * $next_100_cost);
    }
    else if($units > 150 && $units <= 250) {
        $temp = (50 * $first_50_cost) + (100 * $next_100_cost);
        $remaining_units = $units - 150;
        $bill = $temp + ($remaining_units * $next_100_cost_2);
    }
    else {
        $temp = (50 * $first_50_cost) + (100 * $next_100_cost) + (100 * $next_100_cost_2);
        $remaining_units = $units - 250;
        $bill = $temp + ($remaining_units * $above_250_cost);
    }
    return number_format((float)$bill, 2, '.', '');
}

$result_str = $result = '';
if (isset($_POST['unit-submit'])) {
    $units = floatval($_POST['units']);
    if (!empty($units) && $units >= 0) {
        $result = calculate_bill($units);
        $result_str = 'Total amount of ' . $units . ' units is: <strong>Rs. ' . $result . '</strong>';
    } else {
        $result_str = '<span class="text-danger">Please enter a valid positive number of units.</span>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q6: Electricity Bill Calculator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title text-center mb-0">Electricity Bill Calculator</h3>
                </div>
                <div class="card-body">
                    <form action="" method="post">
                        <div class="mb-3">
                            <label for="units" class="form-label">Enter Number of Units:</label>
                            <input type="number" step="0.01" class="form-control" id="units" name="units" placeholder="e.g., 200" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="unit-submit" class="btn btn-success btn-lg">Calculate</button>
                        </div>
                    </form>

                    <?php if (!empty($result_str)): ?>
                        <div class="mt-4 alert alert-info text-center border-info">
                            <?php echo $result_str; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer text-muted small">
                    <strong>Rates:</strong><br>
                    0-50 units: Rs. 3.50/unit<br>
                    51-150 units: Rs. 4.00/unit<br>
                    151-250 units: Rs. 5.20/unit<br>
                    250+ units: Rs. 6.50/unit
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
