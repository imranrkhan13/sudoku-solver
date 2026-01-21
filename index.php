<?php
session_start();
require_once 'Sudoku.php';

/* ===============================
   INIT GAME
================================ */
if (isset($_GET['new']) || !isset($_SESSION['puzzle'])) {
    $s = new Sudoku();
    $s->generateFull();
    $solution = $s->board;

    $s->makePuzzle(45);
    $_SESSION['puzzle'] = $s->board;
    $_SESSION['solution'] = $solution;
}

$fixed = $_SESSION['puzzle'];
$board = $_POST['board'] ?? $fixed;

/* ===============================
   SOLVE BUTTON
================================ */
if (isset($_POST['solve'])) {
    $board = $_SESSION['solution'];
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Sudoku Game</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="app">
        <div class="header">
            <div class="title">Sudoku</div>
            <div class="theme-bar">
                <select id="themeSelect">
                    <option value="">Dark</option>
                    <option value="theme-light">Light</option>
                    <option value="theme-neon">Neon</option>
                    <option value="theme-paper">Paper</option>
                </select>
            </div>
        </div>
        <div class="game-area">
            <div class="container">
                <form method="post" action="index.php">
                    <table id="sudoku">
                        <?php for ($r = 0; $r < 9; $r++): ?>
                            <tr>
                                <?php for ($c = 0; $c < 9; $c++):
                                    $isFixed = $fixed[$r][$c] !== 0;
                                ?>
                                    <td>
                                        <input
                                            maxlength="1"
                                            name="board[<?= $r ?>][<?= $c ?>]"
                                            value="<?= $board[$r][$c] ?: '' ?>"
                                            <?= $isFixed ? 'readonly class="fixed"' : '' ?>>
                                    </td>
                                <?php endfor; ?>
                            </tr>
                        <?php endfor; ?>
                    </table>

                    <div class="actions">
                        <button name="solve">Solve</button>
                        <a href="?new=1">New Game</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const select = document.getElementById("themeSelect");

        function applyTheme(cls) {
            document.body.className = cls;
            localStorage.setItem("sudoku-theme", cls);
        }

        select.addEventListener("change", () => applyTheme(select.value));

        // Load saved theme
        const saved = localStorage.getItem("sudoku-theme") || "";
        select.value = saved;
        applyTheme(saved);
        const SOLUTION = <?= json_encode($_SESSION['solution']) ?>;
        const inputs = document.querySelectorAll("#sudoku input");

        inputs.forEach(i => i.addEventListener("input", validate));

        function validate() {
            inputs.forEach(i => i.classList.remove("error"));

            inputs.forEach((input, idx) => {
                let r = Math.floor(idx / 9);
                let c = idx % 9;
                let v = parseInt(input.value);

                if (!v) return;

                // ❌ Wrong value compared to solution
                if (SOLUTION[r][c] !== v) {
                    input.classList.add("error");
                }
            });
        }

        function ok(g, r, c, v) {
            for (let i = 0; i < 9; i++) {
                if (i != c && g[r][i] == v) return false;
                if (i != r && g[i][c] == v) return false;
            }
            let sr = r - r % 3,
                sc = c - c % 3;
            for (let i = 0; i < 3; i++)
                for (let j = 0; j < 3; j++)
                    if ((sr + i != r || sc + j != c) && g[sr + i][sc + j] == v) return false;
            return true;
        }
    </script>

</body>

</html>