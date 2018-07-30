<?php

class ShipFactory
{
    public function __construct($type)
    {
        return ($type == 'battleship')? new Battleship() : new Destroyer();

    }

}