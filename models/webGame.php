<?php

class WebGame extends Game
{
    public function initGame()
    {
        session_start();
        $this->newGame();
        if (!isset($_SESSION['game'])) {
            $this->newGame();
        } else {
            $this->loadGame($_SESSION['game']);
        }

        print_r('<pre>');
        echo($this->stringifyBoard());
    }

    public function newGame()
    {
        $this->createNewGame();
        $this->storeGame();
    }

    protected function getUserInput()
    {
    }

    private function storeGame()
    {
        $_SESSION['game'] = array(
            'ships' => $this->ships,
            'board' => $this->board
        );
    }

    private function loadGame($game)
    {
        $this->ships = $game['ships'];
        $this->board = $game['board'];
    }
}