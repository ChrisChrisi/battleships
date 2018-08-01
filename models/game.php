<?php

abstract class Game
{
    protected $playerTurns = 0;
    protected $board;
    protected $rowsNames;
    protected $ships;
    private $letters = array();
    private $displayMode = 'play';
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
        } else {
            $userAction = new UserAction($userInput);
            $action = $userAction->processCommand();
            $functionName = 'command' . lcfirst($action['command']);
            if ($action['command'] === 'play') {
                $this->commandPlay($action['coordinates']);
            } else {
                $result = is_callable(array($this, $functionName)) ? $this->{$functionName}() : false;
            }
        }
        if ($result === false) {
            $this->commandError();
        }
    }

    public function commandPlay()
    {
    }

    public function commandShow()
    {
    }

    public function commandReset()
    {
    }

    public function commandError()
    {
        $this->message = Messages::getMessage('error');
    }

    public function stringifyBoard()
    {
        $newLine = chr(10);
        $space = ' ';
        return $this->displayMode === 'play' ? $this->showPlayBoard($newLine, $space) : $this->showRemainingShips($newLine, $space);

    }

    public function showRemainingShips($newLine, $space)
    {
        $string = '';
        for ($i = 1; $i <= BOARD_COLS; $i++) {
            $string .= $space . $space . $i;
        }
        $string .= $newLine;
        foreach ($this->board as $index => $row) {
            $string .= $index . $space;
            foreach ($row as $cell) {
                if (!is_null($cell['ship']) && $cell['symbol'] != HIT_SYMBOL) {
                    $string .= 'X' . $space . $space;
                } else {
                    $string .= $space . $space . $space;
                }
            }
            $string .= $newLine;
        }
        return $string;
    }

    public function showPlayBoard($newLine, $space)
    {
        $string = '';
        for ($i = 1; $i <= BOARD_COLS; $i++) {
            $string .= $space . $space . $i;
        }
        $string .= $newLine;

        foreach ($this->board as $index => $row) {
            $string .= $index . $space;
            foreach ($row as $cell) {
                $string .= $cell['symbol'] . $space . $space;
            }
            $string .= $newLine;
        }
        return $string;
    }

    abstract public function initGame();

    abstract public function newGame();

    abstract protected function getUserInput();

    abstract public function show();
}