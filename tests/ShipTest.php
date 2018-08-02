<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Exception;

chdir(dirname(__FILE__));
require_once dirname(__FILE__) . '/../libs/config.php';
require_once LIBS_PATH . 'autoloader.php';

class ShipTest extends TestCase
{
    public function testShipPlacement(){
        $placement = array(array('rIndex' => 'A', 'cIndex' => 1),array('rIndex' => 'A', 'cIndex' => 2),array('rIndex' => 'A', 'cIndex' => 3),array('rIndex' => 'A', 'cIndex' => 4),array('rIndex' => 'A', 'cIndex' => 5));
        $ship = new Ship();
        $ship->setPlacement($placement);
        try {
            $this->assertEquals($ship->getPlacement(), $placement);
        } catch (Exception $exception) {
            $this->fail('wrong battleship placement');
        }
    }

}