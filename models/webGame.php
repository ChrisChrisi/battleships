<?php

class WebGame extends Game
{
    /**
     * initialize the game
     * from scratch or from the session
     */
    public function initGame()
    {
        session_start();

        if (!isset($_SESSION['game']) || $_SESSION['game']['remaining_ships'] < 1) {
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

    /**
     * store the game information in the session
     */
    private function storeGame()
    {
        $_SESSION['game'] = array(
            'remaining_ships' => $this->remainingShips,
            'player_turns' => $this->playerTurns,
            'ships' => $this->ships,
            'board' => $this->board
        );
    }

    /**
     * load already started game
     * @param $game
     */
    private function loadGame($game)
    {
        $this->remainingShips = $game['remaining_ships'];
        $this->playerTurns = $game['player_turns'];
        $this->ships = $game['ships'];
        $this->board = $game['board'];
    }

    /**
     * load the view and visualize the game
     */
    public function show()
    {
        //the board string is used in the view
        $data = $this->getBoardAsString();
        require VIEW_PATH . 'web.php';

    }
}