<?php
define('LIBS_PATH',  dirname(__FILE__) . '/');
define('PATH', LIBS_PATH . '../');
define('CN_PATH', PATH . 'controllers/');
define('MD_PATH', PATH . 'models/');
define('VIEWS_PATH', PATH . 'views/');
const BOARD_ROWS = 10;
const BOARD_COLS = 10;
const BATTLESHIP_SIZE = 5;
const BATTLESHIP_COUNT = 1;
const DESTROYER_SIZE = 4;
const DESTROYER_COUNT = 2;
const HIDDEN_SYMBOL = '.';
const MISS_SYMBOL = '-';
const HIT_SYMBOL = 'X';
const SHIPS = array(
    'battleship' => array('count' => 1, 'size' => 5),
    'destroyer' => array('count' => 2, 'size' => 4)
);

