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
        $this->play();
    }

    public function newGame()
    {
        $this->createNewGame();
        $this->storeGame();
    }

    protected function getUserInput()
    {
        return isset($_POST['coord']) ? trim($_POST['coord']) : '';
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

    public function show()
    {
        $data = $this->stringifyBoard(true);
        $message = $this->message;
        require VIEW_PATH.'web.php';

    }
}