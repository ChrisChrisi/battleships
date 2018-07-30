<?php

class Battleship extends Ship
{
    public function __construct(){
        $this->shipSize =  SHIPS['battleship']['size'];
    }
}