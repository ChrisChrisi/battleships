<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Exception;

chdir(dirname(__FILE__));
require_once dirname(__FILE__).'/../libs/config.php';
require_once LIBS_PATH.'autoloader.php';

class BattleshipTest extends TestCase
{
    public function testBattleshipShipInstance()
    {
        try {
            $this->assertInstanceOf(Ship::class, ShipFactory::create('battleship'));
        } catch (Exception $exception) {
            $this->fail('battleship should be instance of the Ship class');
        }
    }

    public function testBattleshipBattleshipInstance()
    {
        try {
            $this->assertInstanceOf(Battleship::class, ShipFactory::create('battleship'));
        } catch (Exception $exception) {
            $this->fail('battleship should be instance of the Battleship class');
        }
    }

    public function testBattleshipLength()
    {
        $battleship = ShipFactory::create('battleship');
        try {
            $this->assertEquals($battleship->getSize(), SHIPS['battleship']['size']);
        } catch (Exception $exception) {
            $this->fail('battleship size should be '.SHIPS['battleship']['size']);
        }
    }

    public function testBattleshipSink()
    {
        $battleship = ShipFactory::create('battleship');
        $shots = 1;
        do {
            $battleship->hit();
            $shots++;
        } while ($shots <= SHIPS['battleship']['size']);
        $shots--;

        try {
            $this->assertTrue($battleship->isSunk);
        } catch (Exception $exception) {
            $this->fail('battleship should be sunk after '.$shots.' shots');
        }
    }

    public function testShipPlacement()
    {
        $placement = array(array('rIndex' => 'A', 'cIndex' => 1), array('rIndex' => 'A', 'cIndex' => 2), array('rIndex' => 'A', 'cIndex' => 3), array('rIndex' => 'A', 'cIndex' => 4), array('rIndex' => 'A', 'cIndex' => 5));
        $ship = ShipFactory::create('battleship');
        $ship->setPlacement($placement);
        try {
            $this->assertEquals($ship->getPlacement(), $placement);
        } catch (Exception $exception) {
            $this->fail('wrong battleship placement');
        }
    }

}