<?php
class Game
{
    public array $players;
    public int $currentPlayer;
    public int $currentPoints;
    public string $word;
    public array $openLetters;
    public string $textMsg;
    public bool $lock;
    public string $alphabet;

    private function loadGame()
    {
        $savedPlayers = $_SESSION['players'];
        $players = [];
        foreach ($savedPlayers as $player) {
            $players[] = new Player($player['name'], $player['points'], $player['help']);
        }
        $this->players = $players;
        $this->currentPlayer = $_SESSION['currentPlayer'];
        $this->currentPoints = $_SESSION['currentPoints'];
        $this->word = $_SESSION['word'];
        $this->openLetters = $_SESSION['openLetters'];
        $this->textMsg = $_SESSION['textMsg'];
        $this->lock = $_SESSION['lock'];
        $this->alphabet = $_SESSION['alphabet'];
    }

    private function saveGame()
    {
        $sessionPlayers = [];
        foreach ($this->players as $player) {
            $sessionPlayers[] = ['name' => $player->name, 'points' => $player->points, 'help' => $player->help];
        }

        $_SESSION = [
            'players' => $sessionPlayers,
            'openLetters' => $this->openLetters,
            'currentPlayer' => $this->currentPlayer,
            'currentPoints' => $this->currentPoints,
            'word' => $this->word,
            'textMsg' => $this->textMsg,
            'lock' => $this->lock,
            'alphabet' => $this->alphabet,

        ];
    }

    public function __construct(array $players)
    {
        if (empty($_SESSION)) {
            $this->resetGame($players);
        }
        $this->loadGame();
    }
    public function resetGame(array $players = [])
    {
        if (!empty($players)) {
            $this->players = $players;
        } else {
            foreach ($this->players as $player) {
                $player->points = 0;
                $player->help = false;
            }
        }

        $this->currentPlayer = 0;
        $this->currentPoints = 100;
        $this->word = newWord();
        $this->openLetters = [];
        $this->textMsg = '';
        $this->lock = false;
        $this->alphabet = 'АБВГДЕЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ';

        $this->saveGame();
    }

    public function wordAsLetters()
    {
        return mb_str_split($this->word);
    }

    public function openLetter(string $letter)
    {
        $result = false;
        if (in_array($letter, $this->wordAsLetters())) {
            $multiply = substr_count($this->word, $letter);
            $this->players[$this->currentPlayer]->addPoints($this->currentPoints * $multiply);
            $result = true;
        } else {
            if (++$this->currentPlayer === count($this->players)) {
                $this->currentPlayer = 0;
            }
        }
        $this->openLetters[] = $letter;
        $this->saveGame();
        return $result;
    }

    public function help()
    {
        if (!$this->players[$this->currentPlayer]->help) {
            $this->players[$this->currentPlayer]->help = true;
            $closeLetters = array_diff(mb_str_split($this->alphabet), $this->openLetters);
            $otherLetters = array_values(array_diff($closeLetters, $this->wordAsLetters()));
            $this->openLetters[] = $otherLetters[random_int(0, count($otherLetters))];
        }
        $this->saveGame();
    }

    public function spin()
    {
        $this->currentPoints = random_int(1, 10) * 100;
    }
}
