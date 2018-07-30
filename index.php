<?php
//load config
//load autoload
$interfaceType = (php_sapi_name() === 'cli')? 'console' : 'web';
$controller = new gameController($interfaceType);
$controller->startGame();

