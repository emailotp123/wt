<?php
session_start();

$error = '';
$success = '';

// Logout Logic
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    setcookie("username", "", time() - 3600, "/");
    header("Location: index.php");
    exit;
}

// Form Processing
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Basic Validation
    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Create cookie for 1 hour
        setcookie("username", $name, time() + 3600, "/");
        
        // Start session
        $_SESSION["loggedin"] = true;
        $_SESSION["name"] = $name;
        
        $success = "Login successful!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Q4: User Input & Sessions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    
    <h2 class="mb-4">User Login (Sessions & Cookies)</h2>

    <?php if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true): ?>
        <div class="alert alert-success">
            <h4>Welcome, <?php echo htmlspecialchars($_SESSION["name"]); ?>!</h4>
            <p>Your session is active. Cookie stored name: <?php echo isset($_COOKIE['username']) ? htmlspecialchars($_COOKIE['username']) : 'Not set'; ?></p>
            <a href="index.php?action=logout" class="btn btn-danger">Logout</a>
        </div>
    <?php else: ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="card p-4 mx-auto" style="max-width: 400px;">
            <form action="index.php" method="POST">
                <div class="mb-3">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter name">
                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <input type="text" name="email" class="form-control" placeholder="Enter email">
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter password">
                </div>
                <button type="submit" class="btn btn-primary w-100">Login (POST)</button>
            </form>
        </div>

    <?php endif; ?>

</body>
</html>
