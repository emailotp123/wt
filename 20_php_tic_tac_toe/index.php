<?php
// Q20: Tic-Tac-Toe Game using PHP sessions

session_start();

// Initialize board
if (!isset($_SESSION['board']) || isset($_GET['reset'])) {
    $_SESSION['board']   = array_fill(0, 9, '');
    $_SESSION['current'] = 'X';
    $_SESSION['winner']  = null;
    $_SESSION['gameOver'] = false;
}

// Handle move
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cell']) && !$_SESSION['gameOver']) {
    $cell = (int)$_POST['cell'];
    if ($cell >= 0 && $cell <= 8 && $_SESSION['board'][$cell] === '') {
        $_SESSION['board'][$cell] = $_SESSION['current'];
        $winner = checkWinner($_SESSION['board']);
        if ($winner) {
            $_SESSION['winner']  = $winner;
            $_SESSION['gameOver'] = true;
        } elseif (!in_array('', $_SESSION['board'])) {
            $_SESSION['winner']  = 'Draw';
            $_SESSION['gameOver'] = true;
        } else {
            $_SESSION['current'] = $_SESSION['current'] === 'X' ? 'O' : 'X';
        }
    }
}

function checkWinner(array $b): ?string {
    $lines = [[0,1,2],[3,4,5],[6,7,8],[0,3,6],[1,4,7],[2,5,8],[0,4,8],[2,4,6]];
    foreach ($lines as [$a, $c, $d]) {
        if ($b[$a] !== '' && $b[$a] === $b[$c] && $b[$c] === $b[$d]) return $b[$a];
    }
    return null;
}

$board   = $_SESSION['board'];
$current = $_SESSION['current'];
$winner  = $_SESSION['winner'];
$over    = $_SESSION['gameOver'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Q20: Tic-Tac-Toe</title>
    <style>
        body { font-family: Arial, sans-serif; background: #1a1a2e; color: #eee; display: flex; flex-direction: column; align-items: center; padding: 40px 20px; }
        h2 { margin-bottom: 5px; color: #00d4ff; }
        .status { font-size: 18px; margin: 10px 0 20px; font-weight: bold; }
        .status.x-turn { color: #e74c3c; }
        .status.o-turn { color: #2ecc71; }
        .status.winner { color: #f0c040; font-size: 22px; }
        .board { display: grid; grid-template-columns: repeat(3, 110px); gap: 6px; }
        .cell { width: 110px; height: 110px; background: #16213e; border: none; border-radius: 8px; font-size: 48px; font-weight: bold; cursor: pointer; color: #fff; transition: background 0.15s; }
        .cell:hover:not([disabled]) { background: #0f3460; }
        .cell[disabled] { cursor: default; }
        .cell.x { color: #e74c3c; }
        .cell.o { color: #2ecc71; }
        .btn { margin-top: 20px; padding: 10px 28px; background: #007bff; color: #fff; border: none; border-radius: 6px; font-size: 15px; cursor: pointer; text-decoration: none; display: inline-block; }
        .score { margin-top: 18px; font-size: 14px; color: #aaa; }
    </style>
</head>
<body>
    <h2>Tic-Tac-Toe</h2>

    <?php if ($over): ?>
        <div class="status winner">
            <?= $winner === 'Draw' ? "It's a Draw! 🤝" : "Player $winner Wins! 🎉" ?>
        </div>
    <?php else: ?>
        <div class="status <?= $current === 'X' ? 'x-turn' : 'o-turn' ?>">
            Player <?= $current ?>'s Turn
        </div>
    <?php endif; ?>

    <div class="board">
        <?php for ($i = 0; $i < 9; $i++): ?>
            <form method="POST" style="margin:0">
                <input type="hidden" name="cell" value="<?= $i ?>">
                <button type="submit"
                    class="cell <?= strtolower($board[$i]) ?>"
                    <?= ($board[$i] !== '' || $over) ? 'disabled' : '' ?>>
                    <?= htmlspecialchars($board[$i]) ?>
                </button>
            </form>
        <?php endfor; ?>
    </div>

    <a href="?reset=1" class="btn">New Game</a>
    <div class="score">Session-based game state &bull; Refresh to reset</div>
</body>
</html>
