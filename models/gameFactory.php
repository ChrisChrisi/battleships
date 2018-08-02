<?php

class GameFactory
{
    public static function create($type)
    {
        return ($type == 'console') ? new ConsoleGame() : new WebGame();

    }

}