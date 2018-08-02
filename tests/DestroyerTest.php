<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Exception;

chdir(dirname(__FILE__));
require_once dirname(__FILE__).'/../libs/config.php';
require_once LIBS_PATH.'autoloader.php';

class DestroyerTest extends TestCase
{
    public function testDestroyerShipInstance()
    {
        try {
            $this->assertInstanceOf(Ship::class, ShipFactory::create('destroyer'));
        } catch (Exception $exception) {
            $this->fail('destroyer should be instance of the Ship class');
        }
    }

    public function testDestroyerDestroyerInstance()
    {
        try {
            $this->assertInstanceOf(Destroyer::class, ShipFactory::create('destroyer'));
        } catch (Exception $exception) {
            $this->fail('destroyer should be instance of the Destroyer class');
        }
    }

    public function testDestroyerLength()
    {
        $destroyer = ShipFactory::create('destroyer');
        try {
            $this->assertEquals($destroyer->getSize(), SHIPS['destroyer']['size']);
        } catch (Exception $exception) {
            $this->fail('destroyer size should be '.SHIPS['destroyer']['size']);
        }
    }

    public function testDestroyerSink()
    {
        $destroyer = ShipFactory::create('destroyer');
        $shots = 1;
        do {
            $destroyer->hit();
            $shots++;
        } while ($shots <= SHIPS['destroyer']['size']);
        $shots--;

        try {
            $this->assertTrue($destroyer->isSunk);
        } catch (Exception $exception) {
            $this->fail('destroyer should be sunk after '.$shots.' shots');
        }
    }

    public function testShipPlacement()
    {
        $placement = array(array('rIndex' => 'A', 'cIndex' => 1), array('rIndex' => 'A', 'cIndex' => 2), array('rIndex' => 'A', 'cIndex' => 3), array('rIndex' => 'A', 'cIndex' => 4), array('rIndex' => 'A', 'cIndex' => 5));
        $ship = ShipFactory::create('destroyer');
        $ship->setPlacement($placement);
        try {
            $this->assertEquals($ship->getPlacement(), $placement);
        } catch (Exception $exception) {
            $this->fail('wrong battleship placement');
        }
    }

}