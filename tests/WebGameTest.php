<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Exception;

chdir(dirname(__FILE__));
require_once dirname(__FILE__).'/../libs/config.php';
require_once LIBS_PATH.'autoloader.php';

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

    public function testBoardSize()
    {
        $webGame = GameFactory::create('web');
        $webGame->newGame();
        $gameBoard = $webGame->getBoard();
        try {
            $this->assertEquals(BOARD_ROWS, count($gameBoard));
        } catch (Exception $exception) {
            $this->fail('Board should have '.BOARD_ROWS.' rows');
        }
        try {
            $this->assertEquals(BOARD_COLS, count($gameBoard['A']));

        } catch (Exception $exception) {
            $this->fail('Board should have '.BOARD_COLS.' columns, but have '.count($gameBoard['A']));
        }
    }

    public function testShipsCountAndType()
    {
        $webGame = GameFactory::create('web');
        $webGame->newGame();
        $ships = $webGame->getShips();
        $totalShips = 0;
        $shipTypes = array();
        $typesCount = true;
        foreach ($ships as $ship) {
            $type = $ship->getShipType();
            if (!isset($shipTypes[$type])) {
                $shipTypes[$type] = 1;
            } else {
                $shipTypes[$type]++;
            }
        }
        foreach (SHIPS as $name => $ship) {
            if ($ship['count'] != $shipTypes[$name]) {
                $typesCount = false;
            }
            $totalShips += $ship['count'];
        }
        try {
            $this->assertEquals($totalShips, count($ships));
        } catch (Exception $exception) {
            $this->fail('Invalid ships count');
        }

        try {
            $this->assertTrue($typesCount);
        } catch (Exception $exception) {
            $this->fail('Game ships count for a type is wrong');
        }

    }

    public function testShipsPlaced()
    {
        $webGame = GameFactory::create('web');
        $webGame->newGame();
        $board = $webGame->getBoard();
        $ships = $webGame->getShips();
        foreach ($ships as $sIndex => $ship) {
            $places = $ship->getPlacement();
            $allShipsOnBoard = true;
            foreach ($places as $place) {
                if (!isset($board[$place['rIndex']]) || !isset($board[$place['rIndex']][$place['cIndex']]) || $board[$place['rIndex']][$place['cIndex']]['ship'] != $sIndex) {
                    $allShipsOnBoard = false;
                    break;
                }
            }

            try {
                $this->assertTrue($allShipsOnBoard, count($ships));

            } catch (Exception $exception) {
                $this->fail('Ships placement is wrong');
            }

        }
    }

    public function testInvalidCommand()
    {
        $webGame = GameFactory::create('web');
        $webGame->newGame();
        $_POST['coord'] = 'kfa5d52';
        $webGame->play();

        try {
            $this->assertEquals(Messages::getMessage('error'), $webGame->message);
        } catch (Exception $exception) {
            $this->fail('Invalid command was not detected as error');
        }
        $_POST['coord'] = 'A'.(BOARD_COLS + 1);
        $webGame->play();
        try {
            $this->assertEquals(Messages::getMessage('error'), $webGame->message);
        } catch (Exception $exception) {
            $this->fail('Not existing coordinates was not detected as error');
        }
    }

    public function testShowCommand()
    {
        $webGame = GameFactory::create('web');
        $webGame->newGame();
        $_POST['coord'] = 'show';
        $webGame->play();

        try {
            $this->assertEquals('show', $webGame->getDisplayMode());
        } catch (Exception $exception) {
            $this->fail('Show command was not applied');
        }
    }

    public function testShootCommand()
    {
        $webGame = GameFactory::create('web');
        $webGame->newGame();
        $_POST['coord'] = 'A1';
        $webGame->play();
        $board = $webGame->getBoard();
        $shotCell = $board['A'][1];
        try {
            $this->assertTrue((!isset($shotCell['ship']) && $shotCell['symbol'] === MISS_SYMBOL) || (isset($shotCell['ship']) && $shotCell['symbol'] === HIT_SYMBOL));
        } catch (Exception $exception) {
            $this->fail('The shot board cell is not properly marked as shot');
        }

        try {
            $this->assertTrue((!isset($shotCell['ship']) && $webGame->message === Messages::getMessage('miss'))
                || (isset($shotCell['ship']) && ($webGame->message === Messages::getMessage('hit') || ($webGame->message === Messages::getMessage('shut')))));
        } catch (Exception $exception) {
            $this->fail('The action message is wrong');
        }
    }

    public function testShipShoot()
    {
        $webGame = GameFactory::create('web');
        $webGame->newGame();
        $ships = $webGame->getShips();
        $shipPart = $ships[0]->getPlacement()[0];
        $_POST['coord'] = $shipPart['rIndex'].$shipPart['cIndex'];
        $webGame->play();
        $ships = $webGame->getShips();
        try {
            $this->assertTrue($ships[0]->getHitsNumber() === 1);
        } catch (Exception $exception) {
            $this->fail('Shooting is not register in the shot ship');
        }
    }

    public function testPlayerTurns()
    {
        $webGame = GameFactory::create('web');
        $webGame->newGame();
        $_POST['coord'] = 'A1';
        $webGame->play();
        $_POST['coord'] = 'A'.BOARD_COLS;
        $webGame->play();
        try {
            $this->assertTrue($webGame->getPlayerTurns() === 2);
        } catch (Exception $exception) {
            $this->fail('Player turns counter is not working properly');
        }
    }

    public function testResetCommand()
    {
        $webGame = GameFactory::create('web');
        $webGame->newGame();
        $_POST['coord'] = 'A1';
        $webGame->play();
        $_POST['coord'] = 'reset';
        $webGame->play();
        try {
            $this->assertTrue($webGame->getPlayerTurns() === 0);
        } catch (Exception $exception) {
            $this->fail('Reset command not executed properly');
        }
    }

    public function testShipSink()
    {
        $webGame = GameFactory::create('web');
        $webGame->newGame();
        $ships = $webGame->getShips();
        $shipPlacement = $ships[0]->getPlacement();
        foreach ($shipPlacement as $place) {
            $_POST['coord'] = $place['rIndex'].$place['cIndex'];
            $webGame->play();
        }
        $ships = $webGame->getShips();
        try {
            $this->assertTrue($webGame->getRemainingShips() + 1 === count($ships) && $ships[0]->isSunk);
        } catch (Exception $exception) {
            $this->fail('Ship not sunk after all its parts are shot');
        }
    }

}