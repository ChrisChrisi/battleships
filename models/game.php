<?php

abstract class Game
{
    protected $playerTurns = 0;
    protected $board;
    protected $rowsNames;
    protected $ships;
    private $letters = array();
    private $displayMode = 'default';
    public $message = false;

    //protected $ships = array(0 => $shipObj0, 1=>$shipObj1);

    public function __construct()
    {
        $this->setLetters();
//        $this->createNewGame();
//        print_r('<pre>');
//        echo($this->stringifyBoard(true));
    }

    private function getLetter($letterNumber, $offset, $add = true)
    {
        $offset -= 1;
        if ($add) {
            return isset($this->letters[$letterNumber + $offset]) ? $this->letters[$letterNumber + $offset] : false;
        } else {
            return isset($this->letters[$letterNumber - $offset]) ? $this->letters[$letterNumber - $offset] : false;
        }
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
            $functionName = 'command'.lcfirst($action['command']);
            if ($action['command'] === 'play') {
                $this->commandPlay($action['coordinates']);
            } else {
                $result = is_callable(array($this, $functionName)) ? $this->{$functionName}() : false;
            }
        }
        if($result === false){
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

    //debug function should be cleared
    public function stringifyBoard($ships = true)
    {
        $count = 0;
        $string = '';
        for ($i = 1; $i <= BOARD_COLS; $i++) {
            $string .= '  '.$i;
        }
        $string .= chr(10);
        if ($ships) {
            foreach ($this->board as $index => $row) {
                $string .= $index;
                foreach ($row as $cell) {
                    if (!is_null($cell['ship'])) {
                        $string .= ' X';
                        $count += 1;
                    } else {
                        $string .= '  ';
                    }
                }
                $string .= chr(10);
            }
//            echo('total ship cells : '.$count.chr(10));
        } else {
            foreach ($this->board as $index => $row) {
                $string .= $index;
                foreach ($row as $cell) {
                    $string .= '  '.$cell['symbol'];
                }
                $string .= chr(10);
            }
        }
        return $string;

    }

    abstract public function initGame();

    abstract public function newGame();

    abstract protected function getUserInput();

    abstract public function show();
}