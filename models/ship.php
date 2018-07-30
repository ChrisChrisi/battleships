<?php


abstract class Ship
{
    protected $shipSize;
    protected $shipCells; // array('b3' => true, 'c3' => false, 'd4' => true );
    protected $isShipSunk = false;


    abstract public function getShipType();

    /**
     * @param $position - the shooting position
     * @return bool - returns whether the ship is sunk or not
     */
    public function shootShip($position){
        if($this->isShipSunk === false){
            $this->shipCells[$position] = false;
            if(!in_array(true, $this->shipCells)){
                $this->isShipSunk = true;
            }
        }
        return $this->isShipSunk;
    }

}