<?php

class WebGame extends Game
{
    public function initGame()
    {
        session_start();
        if (!isset($_SESSION['game'])) {
            $this->newGame();
        } else {
            $this->loadGame($_SESSION['game']);
        }
        $this->play();
        $this->storeGame();
        $this->show();
    }

    public function newGame()
    {
        $this->createNewGame();
    }

    protected function getUserInput()
    {
        return isset($_POST['coord']) ? trim($_POST['coord']) : '';
    }

    private function storeGame()
    {
        $_SESSION['game'] = array(
            'remaining_ships' => $this->remainingShips,
            'player_turns' =>$this->playerTurns,
            'ships' => $this->ships,
            'board' => $this->board
        );
    }

    private function loadGame($game)
    {
        $this->remainingShips = $game['remaining_ships'];
        $this->playerTurns = $game['player_turns'];
        $this->ships = $game['ships'];
        $this->board = $game['board'];
    }

    public function show()
    {
        $data = $this->stringifyBoard();
        require VIEW_PATH.'web.php';

    }
}