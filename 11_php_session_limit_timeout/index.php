<?php
/**
 * Q11: Limit max 3 concurrent sessions per user; expire sessions after 5 minutes.
 * Uses a file-based session registry stored in sys_get_temp_dir().
 */

define('MAX_SESSIONS', 3);
define('SESSION_TIMEOUT', 300); // 5 minutes in seconds
define('REGISTRY_FILE', sys_get_temp_dir() . '/session_registry.json');

session_start();

// ── Helpers ───────────────────────────────────────────────────────────────────

function loadRegistry(): array {
    if (!file_exists(REGISTRY_FILE)) return [];
    return json_decode(file_get_contents(REGISTRY_FILE), true) ?? [];
}

function saveRegistry(array $reg): void {
    file_put_contents(REGISTRY_FILE, json_encode($reg));
}

function purgeExpired(array $reg): array {
    $now = time();
    return array_filter($reg, fn($s) => ($now - $s['last_active']) < SESSION_TIMEOUT);
}

function countUserSessions(array $reg, string $username): int {
    return count(array_filter($reg, fn($s) => $s['username'] === $username));
}

// ── Actions ───────────────────────────────────────────────────────────────────

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$message = '';
$messageType = 'info';

if ($action === 'login') {
    $username = trim($_POST['username'] ?? '');
    if ($username === '') {
        $message = 'Username is required.';
        $messageType = 'danger';
    } else {
        $reg = purgeExpired(loadRegistry());
        $activeSessions = countUserSessions($reg, $username);

        if ($activeSessions >= MAX_SESSIONS) {
            $message = "Login denied: '$username' already has $activeSessions active session(s). Maximum allowed is " . MAX_SESSIONS . ".";
            $messageType = 'danger';
        } else {
            $sid = session_id();
            $reg[$sid] = ['username' => $username, 'last_active' => time(), 'started' => time()];
            saveRegistry($reg);
            $_SESSION['username'] = $username;
            $_SESSION['login_time'] = time();
            $message = "Login successful! You now have " . ($activeSessions + 1) . " active session(s).";
            $messageType = 'success';
        }
    }
}

if ($action === 'heartbeat' && isset($_SESSION['username'])) {
    $reg = loadRegistry();
    $sid = session_id();
    if (isset($reg[$sid])) {
        if (time() - $reg[$sid]['last_active'] >= SESSION_TIMEOUT) {
            unset($reg[$sid]);
            saveRegistry($reg);
            session_destroy();
            $message = 'Session expired (5-minute timeout).';
            $messageType = 'warning';
        } else {
            $reg[$sid]['last_active'] = time();
            saveRegistry($reg);
        }
    }
}

if ($action === 'logout' && isset($_SESSION['username'])) {
    $reg = loadRegistry();
    unset($reg[session_id()]);
    saveRegistry($reg);
    session_destroy();
    header('Location: index.php?msg=logged_out');
    exit;
}

// Load current registry state for display
$reg = purgeExpired(loadRegistry());

// Check session timeout for logged-in users
$sessionExpired = false;
if (isset($_SESSION['username'])) {
    $sid = session_id();
    if (!isset($reg[$sid])) {
        session_destroy();
        $sessionExpired = true;
    }
}

$loggedIn = !$sessionExpired && isset($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Q11: Concurrent Session Limiter</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-4" style="max-width:680px">
    <h3 class="mb-1">Session Limiter Demo</h3>
    <p class="text-muted small">Max <strong><?= MAX_SESSIONS ?></strong> concurrent sessions per user &bull; Timeout: <strong>5 minutes</strong></p>

    <?php if ($_GET['msg'] ?? '' === 'logged_out'): ?>
        <div class="alert alert-info">You have been logged out.</div>
    <?php endif; ?>
    <?php if ($sessionExpired): ?>
        <div class="alert alert-warning">Your session expired due to 5-minute inactivity.</div>
    <?php endif; ?>
    <?php if ($message): ?>
        <div class="alert alert-<?= htmlspecialchars($messageType) ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($loggedIn): ?>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</h5>
                <p class="text-muted small">Session started: <?= date('H:i:s', $_SESSION['login_time']) ?></p>
                <p>Your session will expire after <strong>5 minutes</strong> of inactivity.</p>
                <a href="?action=logout" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>
    <?php else: ?>
        <div class="card mb-3">
            <div class="card-header"><strong>Login</strong></div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="login">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input class="form-control" name="username" placeholder="e.g. alice" required>
                        <div class="form-text">Use the same username in multiple tabs to test the session limit.</div>
                    </div>
                    <button class="btn btn-primary" type="submit">Login</button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header"><strong>Active Sessions</strong> (<?= count($reg) ?>)</div>
        <div class="card-body p-0">
            <?php if (empty($reg)): ?>
                <p class="p-3 text-muted mb-0">No active sessions.</p>
            <?php else: ?>
                <table class="table table-sm mb-0">
                    <thead><tr><th>Session ID</th><th>Username</th><th>Last Active</th><th>Idle (s)</th></tr></thead>
                    <tbody>
                    <?php foreach ($reg as $sid => $s): ?>
                        <tr>
                            <td class="text-muted small"><?= htmlspecialchars(substr($sid, 0, 12)) ?>...</td>
                            <td><strong><?= htmlspecialchars($s['username']) ?></strong></td>
                            <td><?= date('H:i:s', $s['last_active']) ?></td>
                            <td><?= time() - $s['last_active'] ?>s / <?= SESSION_TIMEOUT ?>s</td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
