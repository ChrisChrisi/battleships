<?php

abstract class Game
{
    protected $playerTurns = 0;
    protected $board;
    protected $rowsNames;
    protected $ships;
    private $letters = array();
    protected $displayMode = 'play';
    protected $remainingShips;
    public $message = false;

    public function __construct()
    {
        $this->setLetters();
    }

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

    protected function createNewGame()
    {
        $this->initBoard();
        $this->initShips();
        $this->playerTurns = 0;
        $this->displayMode = 'play';
    }

    private function initBoard()
    {
        foreach ($this->letters as $rindex) {
            $this->board[$rindex] = array();
            for ($cindex = 1; $cindex <= BOARD_COLS; $cindex++) {
                $this->board[$rindex][$cindex] = array('ship' => null, 'symbol' => HIDDEN_SYMBOL);
            }
        }
    }

    private function initShips()
    {
        $shipIndex = 0;
        foreach (SHIPS as $type => $info) {
            for ($index = 0; $index < $info['count']; $index++) {
                $this->ships[] = $this->setShipToBoard(ShipFactory::create($type), $shipIndex);
                $shipIndex++;
            }
        }
        $this->remainingShips = $shipIndex;
    }

    private function setShipToBoard($ship, $shipIndex)
    {
        $rNum = mt_rand(0, BOARD_ROWS - 1);
        $rindex = $this->letters[$rNum];
        $cindex = mt_rand(1, BOARD_COLS);
        if (isset($this->board[$rindex]) && isset($this->board[$rindex][$cindex]) && is_null($this->board[$rindex][$cindex]['ship'])) {
            $placement = $this->getAvailablePlace($rNum, $rindex, $cindex, $ship->getSize());
            if ($placement !== false) {
                $ship->setPlacement($placement);
                $this->ships[$shipIndex] = $ship;
                foreach ($placement as $place) {
                    $this->board[$place['rindex']][$place['cindex']]['ship'] = $shipIndex;
                }
                return true;
            }
        }
        $this->setShipToBoard($ship, $shipIndex);
    }

    private function getAvailablePlace($rindexNum, $rindex, $cindex, $size)
    {
        $availablePlace = false;
        $directons = array('up', 'down', 'right', 'left');
        shuffle($directons);
        foreach ($directons as $directon) {
            $available = $this->checkAvailability($rindexNum, $rindex, $cindex, $size, $directon);
            if ($available !== false) {
                $availablePlace = $available;
                break;
            }
        }
        return $availablePlace;
    }

    private function checkAvailability($rindexNum, $rindex, $cindex, $size, $direction)
    {
        $size--;
        switch ($direction) {
            case 'up':
                $firstRIndexNum = $rindexNum - $size;
                $lastRIndexNum = $rindexNum;
                break;
            case 'down':
                $firstRIndexNum = $rindexNum;
                $lastRIndexNum = $rindexNum + $size;
                break;
            case 'right':
                $firstCIndex = $cindex;
                $lastCIndex = $cindex + $size;
                break;
            case 'left':
                $firstCIndex = $cindex - $size;
                $lastCIndex = $cindex;
                break;

        }

        $available = false;
        if (isset($firstRIndexNum)) {
            if (isset($this->letters[$firstRIndexNum]) && isset($this->letters[$lastRIndexNum]) && is_null($this->board[$this->letters[$lastRIndexNum]][$cindex]['ship'])) {
                for ($index = $firstRIndexNum; $index <= $lastRIndexNum; $index++) {
                    if (!is_null($this->board[$this->letters[$index]][$cindex]['ship'])) {
                        $available = false;
                        break;
                    }
                    $available[] = array('rindex' => $this->letters[$index], 'cindex' => $cindex);
                }
            }
        } elseif ($firstCIndex > 0 && $lastCIndex < BOARD_COLS && isset($this->board[$rindex][$lastCIndex]) && is_null($this->board[$rindex][$lastCIndex]['ship'])) {
            for ($index = $firstCIndex; $index <= $lastCIndex; $index++) {
                if (!is_null($this->board[$rindex][$index]['ship'])) {
                    $available = false;
                    break;
                }
                $available[] = array('rindex' => $rindex, 'cindex' => $index);
            }
        }
        return $available;
    }

    protected function play()
    {
        $result = false;
        $userInput = $this->getUserInput();
        if (strlen($userInput) == 0) {
            $result = true;
            $this->message = false;
        } else {
            $userAction = new UserAction($userInput);
            $action = $userAction->processCommand();
            $functionName = 'command' . lcfirst($action['command']);
            if ($action['command'] === 'play') {
                $result = $this->commandPlay($action['coordinates']);
            } else {
                $result = is_callable(array($this, $functionName)) ? $this->{$functionName}() : false;
            }
        }
        if ($result === false) {
            $this->commandError();
        }
    }

    public function commandPlay($coordinates)
    {
        $this->displayMode = 'play';
        if (!isset($this->board[$coordinates['rindex']]) || !isset($this->board[$coordinates['rindex']][$coordinates['cindex']])) {

            $this->commandError();
            return false;
        }
        $this->playerTurns++;
        $this->shoot($coordinates);
        return true;
    }

    public function commandShow()
    {
        $this->displayMode = 'show';
        $this->message = false;
    }

    public function commandReset()
    {
        $this->displayMode = 'play';
        $this->createNewGame();
    }

    public function commandError()
    {
        $this->displayMode = 'play';
        $this->message = Messages::getMessage('error');
    }

    private function shoot($coordinates)
    {
        $cell = $this->board[$coordinates['rindex']][$coordinates['cindex']];
        if ($cell['symbol'] !== HIDDEN_SYMBOL) {
            $this->message = Messages::getMessage('miss');
        } else {
            if (is_null($cell['ship'])) {
                $this->board[$coordinates['rindex']][$coordinates['cindex']]['symbol'] = MISS_SYMBOL;
                $this->message = Messages::getMessage('miss');
            } else {
                $this->board[$coordinates['rindex']][$coordinates['cindex']]['symbol'] = HIT_SYMBOL;
                $ship = $this->ships[$cell['ship']];
                $ship->hit();
                if ($ship->isSunk) {
                    --$this->remainingShips;
                    if($this->remainingShips < 1){
                        $this->message = Messages::getMessage('win');
                        $this->message = str_replace("%count",$this->playerTurns,$this->message);
                    } else {
                        $this->message = Messages::getMessage('sunk');
                    }
                } else {
                    $this->message = Messages::getMessage('hit');
                }
            }
        }
    }

    public function stringifyBoard()
    {
        return $this->displayMode === 'play' ? $this->showPlayBoard() : $this->showRemainingShips();

    }

    public function showRemainingShips()
    {
        $string = '';
        for ($i = 1; $i <= BOARD_COLS; $i++) {
            $string .= SPACE . SPACE . $i;
        }
        $string .= NEW_LINE;
        foreach ($this->board as $index => $row) {
            $string .= $index . SPACE;
            foreach ($row as $cell) {
                if (!is_null($cell['ship']) && $cell['symbol'] != HIT_SYMBOL) {
                    $string .= 'X' . SPACE . SPACE;
                } else {
                    $string .= SPACE . SPACE . SPACE;
                }
            }
            $string .= NEW_LINE;
        }
        return $string;
    }

    public function showPlayBoard()
    {
        $string = '';
        for ($i = 1; $i <= BOARD_COLS; $i++) {
            $string .= SPACE . SPACE . $i;
        }
        $string .= NEW_LINE;

        foreach ($this->board as $index => $row) {
            $string .= $index . SPACE;
            foreach ($row as $cell) {
                $string .= $cell['symbol'] . SPACE . SPACE;
            }
            $string .= NEW_LINE;
        }
        return $string;
    }

    abstract public function initGame();

    abstract public function newGame();

    abstract protected function getUserInput();

    abstract public function show();
}