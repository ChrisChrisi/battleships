<?php

class GameController
{
    private $interfaceType;

    public function __construct($interfaceType)
    {
        $this->interfaceType = $interfaceType;
    }

    public function startGame()
    {
        $game = GameFactory::create($this->interfaceType);
        $game->initGame();
    }

}