<?php

class Destroyer extends Ship
{
    public function __construct()
    {
        $this->shipSize = SHIPS['destroyer']['size'];
    }

}