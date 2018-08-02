<?php

class consoleGame extends Game
{
    private $userInput;

    /**
     * Initializing the game
     */
    public function initGame()
    {
        $this->newGame();
        $this->makeTurn();
    }

    public function newGame()
    {
        $this->createNewGame();
    }

    /**
     * process the user's command and display the board
     */
    public function makeTurn()
    {
        $this->play();
        $this->show();
    }

    protected function getUserInput()
    {
        return $this->userInput;
    }

    /**
     * load the view and show the game
     */
    public function show()
    {
        // the board string is used in the view
        $data = $this->getBoardAsString();
        require VIEW_PATH . 'console.php';
    }

    /**
     * Set user input and if a special console command is typed - execute it
     * @param $userInput
     */
    public function setUserInput($userInput)
    {
        //prepare the input
        $this->userInput = trim(strtolower($userInput));
        if ($this->userInput == 'exit' || $this->remainingShips < 1 && ($userInput === 'no' || $userInput === 'n' )) {
            exit();
        } elseif ($this->remainingShips < 1 && ($userInput === 'yes' || $userInput === 'y' )) {
            $this->newGame();
            $this->makeTurn();
        }
    }

}