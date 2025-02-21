<?php
function newWord(): string
{
    $words = [
        'СКИЛБОКС',
        'КЛАВИАТУРА',
        'ИНТЕНСИВ',
    ];
    return $words[rand(0, count($words) - 1)];
}

function resetGame()
{
    $_SESSION = [
        'players' => [
            ['points' => 0, 'name' => 'Игрок 1', 'help' => false],
            ['points' => 0, 'name' => 'Игрок 2', 'help' => false]
        ],
        'openLetters' => [],
        'currentPlayer' => 0,
        'currentPoints' => 0,
        'word' => newWord(),
        'textMsg' => 'Такая буква есть',
        'lock' => false,

    ];
}
