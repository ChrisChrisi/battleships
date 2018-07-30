<?php

abstract class Game
{
    protected $playerTurns = 0;
    protected $board;
    protected $rowsNames;
    protected $ships;
    private $firstRIndex = 'A';

    //protected $ships = array(0 => $shipObj0, 1=>$shipObj1);

    public function __construct()
    {
        $this->initBoard();
    }

    private function initBoard($hits = null)
    {
        $lastRowLetter = $this->firstRIndex + BOARD_ROWS;
        for ($rindex = $this->firstRIndex; $rindex <= $lastRowLetter; $rindex++) {
            $this->board[$rindex] = array();
            for ($cindex = 1; $cindex < BOARD_COLS; $cindex++) {
                $this->board[$rindex][$cindex] = array('ship' => null, 'symbol' => HIDDEN_SYMBOL);
                //$this->board[$rindex][$cindex] =( isset($hits) && isset($hits[$rindex][$cindex])) ? $hits[$rindex][$cindex] : HIDDEN_SYMBOL;
            }
        }
    }

    private function initShips()
    {
        foreach (SHIPS as $type => $info) {
            for ($index = 0; $index < $info['count']; $index++) {
                $this->ships[] = $this->setShipToBoard(new ShipFactory($type));
            }
        }
    }

    private function setShipToBoard($ship)
    {
        $rindex = $this->firstRIndex + mt_rand(0, BOARD_ROWS);
        $cindex = mt_rand(1, BOARD_COLS);
        if(is_null($this->board[$rindex][$cindex]['ship'])){
            $place = $this->getAvailablePlace($rindex, $cindex, $ship->getSize());
            if($place !== false){

            }

        }
        return $this->setShipToBoard($ship);

    }

    private function getAvailablePlace($rindex, $cindex, $size){
        $availablePlaces = array();
        $directons = array('up', 'down', 'right', 'left');
        foreach($directons as $directon){
            $available = $this->checkAvailability($rindex, $cindex, $size, $directon);
            if($available !== false){
                $availablePlaces[] = $available;
            }
        }

        if(count($availablePlaces) == 0){
            return false;
        }

        return $availablePlaces[mt_rand(0, count($availablePlaces) - 1)];
    }

    private function checkAvailability($rindex, $cindex, $size, $direction){
        $size -= 1;
        switch($direction){
            case 'up':
                $firstRIndex = $rindex - $size;
                $lastRIndex = $rindex;
                break;
            case 'down':
                $firstRIndex = $rindex;
                $lastRIndex = $rindex + $size;
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
        $available = array();
        if(isset($firstRIndex)){
            if(isset($this->board[$lastRIndex][$cindex]) && is_null($this->board[$lastRIndex][$cindex]['ship'])){
                for($index = $lastRIndex - 1; $index > $rindex; $index--){
                    if(!is_null($this->board[$index][$cindex]['ship'])){
                        $available = false;
                        break;
                    }
                    $available[] = array($index, $cindex);
                }
            }
        } else {
            if(isset($this->board[$rindex][$lastCIndex]) && is_null($this->board[$rindex][$lastCIndex]['ship'])){
                for($index = $lastCIndex - 1; $index > $cindex; $index--){
                    if(!is_null($this->board[$rindex][$index]['ship'])){
                        $available = false;
                        break;
                    }
                    $available[] = array($rindex, $index);
                }
            }

        }
        return $available;
    }

    abstract protected function readInput();
}