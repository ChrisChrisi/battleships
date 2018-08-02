<?php

spl_autoload_register(function ($class) {
    $places = array(MD_PATH, CN_PATH);
    $filename = lcfirst($class).'.php';

    foreach ($places as $place) {
        $file = $place.$filename;
        if (file_exists($file) === true) {
            require_once $file;
            return true;
        }
    }

    return -1;
});