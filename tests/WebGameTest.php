<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Exception;

chdir(dirname(__FILE__));
require_once dirname(__FILE__) . '/../libs/config.php';
require_once LIBS_PATH . 'autoloader.php';

class WebGameTest extends TestCase
{
    public function testGameInstance()
    {
        try {
            $this->assertInstanceOf(Game::class, GameFactory::create('web'));
        } catch (Exception $exception) {
            $this->fail('WebGame should be Game instance');
        }
    }

    public function testWebGameInstance()
    {
        try {
            $this->assertInstanceOf(WebGame::class, GameFactory::create('web'));
        } catch (Exception $exception) {
            $this->fail('WebGame should be WebGame instance');
        }
    }

    public function testBoardSize(){
        $webGame =  GameFactory::create('web');
        $webGame->newGame();
        $gameBoard = $webGame->getBoard();
        try {
            $this->assertEquals(BOARD_ROWS, count($gameBoard));
        } catch (Exception $exception) {
            $this->fail('Board should have '. BOARD_ROWS . ' rows');
        }
        try {
            $this->assertEquals(BOARD_COLS, count($gameBoard['A']));

        } catch (Exception $exception) {
            $this->fail('Board should have '. BOARD_COLS . ' columns, but have '. count($gameBoard['A']));
        }
    }

    public function testShipsCount(){
        $webGame =  GameFactory::create('web');
        $webGame->newGame();
        $ships = $webGame->getShips();

        $totalShips = 0;
        foreach (SHIPS as $ship){
            $totalShips += $ship['count'];
        }
        try {
            $this->assertEquals($totalShips, count($ships));

        } catch (Exception $exception) {
            $this->fail('Invalid ships count');
        }

    }
}