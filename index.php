<?php
session_start();
ini_set('display_errors', true);

require_once('./src/functions.php');
require_once __DIR__ . '/src/Game.php';
require_once __DIR__ . '/src/Player.php';

$player1 = new Player('игрок 1');
$player2 = new Player('игрок 2');
$game = new Game([$player1, $player2]);

$canSpin = true;
$hasError = false;
$hasSuccess = false;

$players = $game->players;
$currentPlayer = $game->currentPlayer;
$currentPoints = $game->currentPoints;
$openLetters = $game->openLetters;
$alphabet = $game->alphabet;
$lettersInWord = $game->wordAsLetters();

if (! empty($_POST)) {
    if (isset($_POST['letter'])) {
        $canSpin = true;
        if ($game->openLetter($_POST['letter'])) {
            $hasSuccess = true;
        } else {
            $hasError = true;
        }
    } else if (isset($_POST['spin'])) {
        $game->spin();
        $canSpin = false;
    } else if (isset($_POST['help'])) {
        $game->help();
    } else if (isset($_POST['restart'])) {
        $game->resetGame();
    }
}

?>
<!doctype html>
<html lang="ru">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Поле чудес</title>

    <link href="tailwind.min.css" rel="stylesheet">
    <style>
        .word-cell {
            min-width: 1em;
        }
    </style>
</head>

<body class="antialiased sans-serif bg-blue-50" style="min-width: 375px">
    <main class="mx-auto max-w-4xl h-screen flex flex-col justify-between">
        <div class="block sm:flex justify-between items-center border bg-gray-100">
            <div class="text-left p-4 flex justify-center sm:justify-items-start items-center">
                <div class="pr-8">Очки: </div>
                <div>
                    <div><span class="text-sm text-gray-700"><span class="text-red-600"><?= $players[0]->name ?></span>:</span> <span class="text-xl text-red-600"><?= $players[0]->points ?></span></div>
                    <div><span class="text-sm text-gray-700"><span class="text-blue-800"><?= $players[1]->name ?></span>:</span> <span class="text-xl text-blue-800"><?= $players[1]->points ?></span></div>
                </div>
            </div>
            <div class="mx-auto text-center">
                Ходит: <span class="text-xl text-red-600"><?= $players[$_SESSION['currentPlayer']]->name ?></span>
            </div>
            <div class="text-right p-4 flex justify-center space-x-2">
                <form method="post" action="">
                    <button <?= $_SESSION['lock'] || $players[$_SESSION['currentPlayer']]->help ? 'disabled' : '' ?> type="submit" class="<?= $_SESSION['lock'] || $players[$_SESSION['currentPlayer']]->help ? 'bg-gray-300' : 'bg-green-500 hover:bg-green-700' ?> text-white font-bold py-2 px-4 rounded text-sm" name="help">Подсказка</button>
                </form>
                <form method="post" action="">
                    <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm" name="restart">Начать заново</button>
                </form>
            </div>
        </div>
        <div class="flex flex-col flex-1 justify-around">
            <div class="border flex justify-center class bg-white p-4 space-x-2 sm:space-x-4 text-3xl sm:text-4xl md:text-6xl">
                <?php foreach ($lettersInWord as $letter) {
                    if (in_array($letter, $openLetters)) { ?>
                        <div class="word-cell border text-center"><?= $letter ?></div>
                    <?php } else {
                    ?>
                        <div class="word-cell border text-center bg-black">&nbsp;</div>
                <?php }
                } ?>
            </div>
            <div class="flex flex-col items-center p-4 space-y-4">
                <?php if ($hasSuccess) {
                    if (!array_diff($lettersInWord, $openLetters)) {
                        $_SESSION['textMsg'] = $players[$currentPlayer]->name . " победил";
                        $_SESSION['lock'] = true;
                    } else {
                        $_SESSION['textMsg'] = 'Такая буква есть';
                    }
                    include __DIR__ . '/views/successMsg.php';
                }
                if ($hasError) {
                    include __DIR__ . '/views/errorMsg.php';
                }
                ?>
                <div class="flex justify-between items-center">
                    <span class="text-gray-700 pr-2">Очков за ход:</span> <span class="text-3xl"><?= $currentPoints ?></span>
                </div>
                <div>
                    <form method="post" action="">
                        <button <?= $_SESSION['lock'] || !$canSpin ? 'disabled'  : '' ?> type="submit" class="<?= $_SESSION['lock'] || !$canSpin ? 'bg-gray-300' : 'bg-blue-500 hover:bg-blue-700' ?> text-white font-bold py-2 px-4 rounded" name="spin">Крутить барабан</button>
                    </form>
                </div>
            </div>
            <div class="flex justify-center px-4">
                <form method='post' action="">
                    <div class="grid grid-cols-8 sm:grid-cols-12 gap-2 text-md sm:text-xl md:text-3xl">
                        <?php foreach (mb_str_split($alphabet) as $letter) {
                            if (in_array($letter, $_SESSION['openLetters'])) {
                        ?>
                                <span class="word-cell p-2 text-center bg-black"><?= $letter ?></span>
                            <?php } else { ?>
                                <button <?= $_SESSION['lock'] || $canSpin ? 'disabled' : '' ?> type="submit" class="<?= $_SESSION['lock'] || $canSpin ? 'bg-gray-300' : 'bg-white hover:bg-blue-700' ?> word-cell p-2 text-center" name="letter" value="<?= $letter ?>"><?= $letter ?></button>
                        <?php }
                        } ?>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>

</html>