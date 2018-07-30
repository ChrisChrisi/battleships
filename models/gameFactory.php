<?php

class GameFactory
{
    private $board = array();
    private $remainingShips;
    private $handler;

    public function __construct($type)
    {
        return ($type == 'console')? new consoleHandler() : new consoleHandler();

    }


}