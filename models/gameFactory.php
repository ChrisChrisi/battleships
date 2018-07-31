<?php

class GameFactory
{
    private $board = array();
    private $remainingShips;
    private $handler;

    public static function create($type)
    {
        return ($type == 'console')? new ConsoleGame() : new WebGame();

    }


}