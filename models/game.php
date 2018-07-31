<?php

abstract class Game
{
    protected $playerTurns = 0;
    protected $board;
    protected $rowsNames;
    protected $ships;
    private $firstCIndex = 0;
    private $firstRIndexNum = 0;
    private $firstRIndex = 'A';
    private $lastRIndex;
    private $letters = array();
    private $specialCommands = array('show', 'reset');
    protected $results = array(
        'error' => '*** Error ***',
        'sunk' => '*** Sunk ***',
        'miss' => '*** Miss ***',
        'hit' => '*** Hit ***'
        );

    //protected $ships = array(0 => $shipObj0, 1=>$shipObj1);

    public function __construct()
    {
        $this->setLetters();
        $this->setLastRIndex();
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
        $curLetter = $this->firstRIndex;
        $curIndex = 0;
        while ($curIndex < BOARD_ROWS) {
            $this->letters[] = $curLetter;
            $curLetter++;
            $curIndex++;
        }
    }

    private function setLastRIndex()
    {
        $this->lastRIndex = $this->getLetter($this->firstRIndexNum, BOARD_ROWS);
    }

    protected function createNewGame()
    {
        $this->initBoard();
        $this->initShips();
    }

    private function initBoard()
    {
        $last = ++$this->lastRIndex;
        for ($rindex = $this->firstRIndex; $rindex !== $last; $rindex++) {
            $this->board[$rindex] = array();
            for ($cindex = 1; $cindex <= BOARD_COLS; $cindex++) {
                $this->board[$rindex][$cindex] = array('ship' => null, 'symbol' => HIDDEN_SYMBOL);
            }
        }
    }

    private function initShips()
    {
        $count= 0;
        foreach (SHIPS as $type => $info) {
            for ($index = 0; $index < $info['count']; $index++) {
                $this->ships[] = $this->setShipToBoard(ShipFactory::create($type), $count);
                $count+=1;
            }
        }
    }

    private function setShipToBoard($ship, $sindex)
    {
        $rNum = mt_rand(0, BOARD_ROWS - 1);
        $rindex = $this->getLetter($this->firstRIndexNum, $rNum);
        $cindex = mt_rand($this->firstCIndex, $this->firstCIndex + BOARD_COLS - 1);
        if (isset($this->board[$rindex]) && isset($this->board[$rindex][$cindex]) && is_null($this->board[$rindex][$cindex]['ship'])) {
            $placement = $this->getAvailablePlace($rNum, $rindex, $cindex, $ship->getSize());
            if ($placement !== false) {
                $ship->setPlacement($placement);
                $this->ships[$sindex] = $ship;
                $count = 0;
                foreach ($placement as $place){
                    $this->board[$place['rindex']][$place['cindex']]['ship'] = $sindex;
                    $count += 1;
                }
                return true;
            }
        }
        $this->setShipToBoard($ship, $sindex);
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
        $size -= 1;

        $this->getLetter($rindexNum, $size, false);
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
        } else if ($firstCIndex > $this->firstCIndex && $lastCIndex < $this->firstCIndex + BOARD_COLS && isset($this->board[$rindex][$lastCIndex]) && is_null($this->board[$rindex][$lastCIndex]['ship'])) {
                for ($index = $firstCIndex; $index <= $lastCIndex; $index++) {
                    if (!is_null($this->board[$rindex][$index]['ship'])) {
                        $available = false;
                        break;
                    }
                    $available[] = array('rindex' => $rindex, 'cindex' => $index);
                }
        } else {
            $available = false;
        }
        return $available;
    }

    protected function processUserMove($value){

    }

    //debug functiom should be cleared
    public function stringifyBoard($ships = true){
        $count = 0;
        $string = ' ';
        for($i = 1; $i<= BOARD_COLS; $i++){
            $string .= ' '. $i;
        }
        $string .= '<br>';
        if($ships){
            foreach ($this->board as $index => $row){
                $string .= $index;
                foreach ($row as $cell){
                    if(!is_null($cell['ship'])){
                        $string .= ' X';
                        $count+=1;
                    } else {
                        $string .= '  ';
                    }
                }
                $string .= '<br>';
            }
            echo('total ship cells : '.$count . '<br><br>');
        } else {
            foreach ($this->board as $index => $row){
                $string .= $index;
                foreach ($row as $cell){
                        $string .= ' '.$cell['symbol'];
                }
                $string .= '<br>';
            }
        }
        return $string;

    }
    abstract public function initGame();
    abstract public function resetGame();
    abstract protected function getUserInput();
}