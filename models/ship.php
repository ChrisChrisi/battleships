<?php

//core ship class
abstract class Ship
{
    protected $shipSize = 0;
    protected $placement; // the position of the ship on the board
    protected $hitsNumber = 0; // how many hits are made on the ship so far
    public $isSunk = false; // is the ship is sunken

    /**
     * apply ship hit
     * @return bool - return true if the ship is sunk, false if it is not
     */
    public function hit()
    {
        if ($this->isSunk === false) {
            $this->hitsNumber += 1;
            if ($this->shipSize === $this->hitsNumber) {
                $this->isSunk = true;
            }
        }
        return $this->isSunk;
    }

    public function getSize()
    {
        return $this->shipSize;
    }

    public function setPlacement($placement)
    {
        $this->placement = $placement;
    }

    public function getPlacement()
    {
        return $this->placement;
    }

    public function getHitsNumber(){
        return $this->hitsNumber;
    }

    public function getShipSize(){
        return $this->shipSize;
    }
    public abstract function getShipType();

}