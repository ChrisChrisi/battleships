<?php

class GameController
{
    private $interfaceType;
    public function __construct($interfaceType)
    {
        $this->interfaceType = $interfaceType;
    }

    public function startGame(){
        $game = new GameFactory($this->interfaceType);
        $game->play();
    }

}