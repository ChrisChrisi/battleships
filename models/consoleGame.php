<?php

class consoleGame extends Game
{
    private $userInput;
    public function initGame()
    {
        $this->newGame();
        $this->makeTurn();
    }

    public function newGame()
    {
        $this->createNewGame();
    }

    public function makeTurn(){
        $this->play();
        $this->show();
    }

    protected function getUserInput()
    {
        return $this->userInput;
    }
    public function show(){
        $data = $this->stringifyBoard();
        require VIEW_PATH.'console.php';
    }

    public function setUserInput($userInput){
        $this->userInput = trim(strtolower($userInput));
        if($this->userInput == 'exit'){
            exit();
        }
    }

}