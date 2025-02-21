<?php
class Player
{
    public string $name;
    public int $points;
    public bool $help;

    public function __construct(string $name, int $points = 0, bool $help = false)
    {
        $this->name = $name;
        $this->points = $points;
        $this->help = $help;
    }
    public function addPoints($points)
    {
        $this->points += $points;
    }
}
