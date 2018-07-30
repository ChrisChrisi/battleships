<?php

class Ship
{
    protected $shipSize = 0;
    protected $hitsNumber = 0;
    public $isSunk = false;

    /**
     * @return bool - return true if the ship is sunk, false if it is not
     */
    public function hit(){
        if($this->isSunk === false){
            $this->hitsNumber += 1;
            if($this->shipSize === $this->hitsNumber){
                $this->isSunk = true;
            }
        }
        return $this->isSunk;
    }

    public function getSize(){
        return $this->shipSize;
    }

}