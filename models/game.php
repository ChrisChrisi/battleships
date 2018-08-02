<?php

// core battleships game class
abstract class Game
{
    protected $playerTurns; //how many turns the player made
    protected $board; // game board
    protected $ships;
    private $letters = array(); // board rows letters
    protected $displayMode = 'play';
    protected $remainingShips; // the count of remaining not sunk ships
    public $message = false; // current display message for the game (miss, hit, ect.)

    public function __construct()
    {
        $this->setLetters();
    }

    /**
     * Generate board rows letters
     */
    private function setLetters()
    {
        $curLetter = 'A';
        $curIndex = 0;
        while ($curIndex < BOARD_ROWS) {
            $this->letters[] = $curLetter;
            $curLetter++;
            $curIndex++;
        }
    }

    /**
     * Generate new game
     * creating and placing new board and ships
     */
    protected function createNewGame()
    {
        $this->initBoard();
        $this->initShips();
        $this->playerTurns = 0;
        $this->displayMode = 'play';
    }

    /**
     * Create empty board without ships
     * the board dimensions are taken form the config
     */
    private function initBoard()
    {
        foreach ($this->letters as $rIndex) {
            $this->board[$rIndex] = array();
            for ($cIndex = 1; $cIndex <= BOARD_COLS; $cIndex++) {
                //ship is index of the placed ship object on the board stored in ships
                $this->board[$rIndex][$cIndex] = array('ship' => null, 'symbol' => HIDDEN_SYMBOL);
            }
        }
    }

    /**
     * Create new ships and place them on the board
     * the ships size and count is taken from the config
     */
    private function initShips()
    {
        $shipIndex = 0;
        foreach (SHIPS as $type => $info) {
            for ($index = 0; $index < $info['count']; $index++) {
                $ship = ShipFactory::create($type);
                $this->ships[] = $ship;
                $this->setShipToBoard(ShipFactory::create($type), $shipIndex);
                $shipIndex++;
            }
        }
        $this->remainingShips = $shipIndex;
    }

    /**
     * Place ship on the board
     * @param Ship $ship - generated ship
     * @param $shipIndex - index of the ship in the ships
     * @return bool
     */
    private function setShipToBoard(Ship $ship, $shipIndex)
    {
        //generate random place on the board
        $rNum = mt_rand(0, BOARD_ROWS - 1);
        //convert the row number to the corresponding letter
        $rIndex = $this->letters[$rNum];
        $cIndex = mt_rand(1, BOARD_COLS);
        //if the place is empty(has ho ship)
        // try to place ship on the board starting from this place
        if (is_null($this->board[$rIndex][$cIndex]['ship'])) {
            $placement = $this->getAvailablePlace($rNum, $rIndex, $cIndex, $ship->getSize());
            if ($placement !== false) {
                $ship->setPlacement($placement);
                $this->ships[$shipIndex] = $ship;
                foreach ($placement as $place) {
                    $this->board[$place['rIndex']][$place['cIndex']]['ship'] = $shipIndex;
                }
                return true;
            }
        }
        // if the ship cannot be place from the chosen starting board place
        // try with another place
        $this->setShipToBoard($ship, $shipIndex);
    }

    /**
     * Find available position for a ship on the game board
     * @param $rIndexNum - starting board place row number
     * @param $rIndex - starting board place row letter
     * @param $cIndex - starting board place column number
     * @param $size - length of the ship to be placed
     * @return array|bool - array with coordinates of the available position for the ship to be placed
     *         or false is such position was not found
     */
    private function getAvailablePlace($rIndexNum, $rIndex, $cIndex, $size)
    {
        $availablePlace = false;
        $directions = array('up', 'down', 'right', 'left');
        // randomize the direction of placing so that each ship could be placed on different direction
        shuffle($directions);
        // try each direction for placement and stop when you find available
        foreach ($directions as $direction) {
            $available = $this->checkAvailability($rIndexNum, $rIndex, $cIndex, $size, $direction);
            if ($available !== false) {
                $availablePlace = $available;
                break;
            }
        }
        return $availablePlace;
    }

    /**
     * Check if a ship can be placed on a position
     * The position is defined with starting cell, length of the ship and direction
     * @param $rIndexNum - starting board place row number
     * @param $rIndex - starting board place row letter
     * @param $cIndex - starting board place column number
     * @param $size - length of the ship to be placed
     * @param $direction - direction to which the ship will be placed
     * @return array|bool - array with coordinates of the available position for the ship to be placed
     *         or false is the position is not available
     */
    private function checkAvailability($rIndexNum, $rIndex, $cIndex, $size, $direction)
    {
        //because the first segment of the ship is placed on the given initial place
        //to find the position of the last segment of the ship we should subtract one from the ship legth
        $size--;
        //calculate the starting and ending places of the ship
        switch ($direction) {
            case 'up':
                $firstRIndexNum = $rIndexNum - $size;
                $lastRIndexNum = $rIndexNum;
                break;
            case 'down':
                $firstRIndexNum = $rIndexNum;
                $lastRIndexNum = $rIndexNum + $size;
                break;
            case 'right':
                $firstCIndex = $cIndex;
                $lastCIndex = $cIndex + $size;
                break;
            case 'left':
                $firstCIndex = $cIndex - $size;
                $lastCIndex = $cIndex;
                break;

        }

        $available = false;
        //checking placing ship vertically
        if (isset($firstRIndexNum)) {
            // if the placement is withing the game board and the last ship placement cell is free
            // check if the other placement cells are free and store the available coordinates
            if (isset($this->letters[$firstRIndexNum]) && isset($this->letters[$lastRIndexNum]) && is_null($this->board[$this->letters[$lastRIndexNum]][$cIndex]['ship'])) {
                for ($index = $firstRIndexNum; $index <= $lastRIndexNum; $index++) {
                    if (!is_null($this->board[$this->letters[$index]][$cIndex]['ship'])) {
                        $available = false;
                        break;
                    }
                    $available[] = array('rIndex' => $this->letters[$index], 'cIndex' => $cIndex);
                }
            }
            //checking placing ship horizontally
            // if the placement is withing the game board and the last ship placement cell is free
            // check if the other placement cells are free and store the available coordinates
        } elseif ($firstCIndex > 0 && $lastCIndex < BOARD_COLS && isset($this->board[$rIndex][$lastCIndex]) && is_null($this->board[$rIndex][$lastCIndex]['ship'])) {
            for ($index = $firstCIndex; $index <= $lastCIndex; $index++) {
                if (!is_null($this->board[$rIndex][$index]['ship'])) {
                    $available = false;
                    break;
                }
                $available[] = array('rIndex' => $rIndex, 'cIndex' => $index);
            }
        }
        return $available;
    }

    /**
     * process and apply the user command
     */
    public function play()
    {
        $userInput = $this->getUserInput();
        if (strlen($userInput) == 0) {
            $result = true;
            $this->message = false;
        } else {
            // convert user action to a command
            $userAction = new UserAction($userInput);
            $action = $userAction->processCommand();
            // execute the user command
            $functionName = 'command'.lcfirst($action['command']);
            if ($action['command'] === 'play') {
                $result = $this->commandPlay($action['coordinates']);
            } else {
                $result = is_callable(array($this, $functionName)) ? $this->{$functionName}() : false;
            }
        }
        //if the user coordinates are wrong execute error
        if ($result === false) {
            $this->commandError();
        }
    }

    /**
     * Process the user shooting
     * @param $coordinates - coordinates of the place where the shooting is made
     * @return bool - if the shooting was made or the coordinates are wrong
     */
    public function commandPlay($coordinates)
    {
        $this->displayMode = 'play';
        if (!isset($this->board[$coordinates['rIndex']]) || !isset($this->board[$coordinates['rIndex']][$coordinates['cIndex']])) {

            $this->commandError();
            return false;
        }
        $this->playerTurns++;
        $this->shoot($coordinates);
        return true;
    }

    // apply "show remaining ships" command
    public function commandShow()
    {
        $this->displayMode = 'show';
        $this->message = false;
    }

    // apply "reset game" command
    public function commandReset()
    {
        $this->displayMode = 'play';
        $this->createNewGame();
    }

    // invalid command
    public function commandError()
    {
        $this->displayMode = 'play';
        $this->message = Messages::getMessage('error');
    }

    /**
     * Make shot on the game board
     * @param $coordinates - coordinates of the shot
     */
    private function shoot($coordinates)
    {
        $cell = $this->board[$coordinates['rIndex']][$coordinates['cIndex']];
        // if that place was already shot, then it's a miss
        if ($cell['symbol'] !== HIDDEN_SYMBOL) {
            $this->message = Messages::getMessage('miss');
        } else {
            //if there is no ship on this place it's a miss
            if (is_null($cell['ship'])) {
                $this->board[$coordinates['rIndex']][$coordinates['cIndex']]['symbol'] = MISS_SYMBOL;
                $this->message = Messages::getMessage('miss');
                //a ship part was shot
            } else {
                // mark the board and the ship
                $this->board[$coordinates['rIndex']][$coordinates['cIndex']]['symbol'] = HIT_SYMBOL;
                $ship = $this->ships[$cell['ship']];
                $ship->hit();
                if ($ship->isSunk) {
                    --$this->remainingShips;
                    if ($this->remainingShips < 1) {
                        // if the last ship is shot then the game is over and the user wins
                        $this->message = Messages::getMessage('win');
                        $this->message = str_replace("%count", $this->playerTurns, $this->message);
                    } else {
                        $this->message = Messages::getMessage('sunk');
                    }
                    // the ship is not sunk just hit
                } else {
                    $this->message = Messages::getMessage('hit');
                }
            }
        }
    }

    public function getBoardAsString()
    {
        return BoardVisualizer::stringifyBoard($this->board, $this->displayMode);

    }

    public function getBoard()
    {
        return $this->board;
    }

    public function getShips()
    {
        return $this->ships;
    }

    public function getDisplayMode()
    {
        return $this->displayMode;
    }

    public function getPlayerTurns()
    {
        return $this->playerTurns;
    }

    public function getRemainingShips()
    {
        return $this->remainingShips;
    }

    abstract public function initGame();

    abstract public function newGame();

    abstract protected function getUserInput();

    abstract public function show();
}