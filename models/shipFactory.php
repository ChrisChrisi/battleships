<?php

class ShipFactory
{
    public static function create($type)
    {
        return ($type == 'battleship') ? new Battleship() : new Destroyer();
    }

}