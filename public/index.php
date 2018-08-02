<?php

chdir(dirname(__FILE__));

require_once dirname(__FILE__).'/../libs/config.php';
require_once LIBS_PATH.'autoloader.php';

$interfaceType = (php_sapi_name() === 'cli') ? 'console' : 'web';
$controller = new gameController($interfaceType);
$controller->startGame();

