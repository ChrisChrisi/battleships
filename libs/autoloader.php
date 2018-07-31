<?php

spl_autoload_register(function ($class) {
    $places = array(CN_PATH, MD_PATH,VIEWS_PATH);
    $filename = lcfirst ($class) . '.php';
    $valid = false;
    foreach ($places as $place){
        $file = $place . $filename;
        $valid = file_exists($file);
        if($valid === true){
            break;
        }
    }
    if ($valid !== false) {
        require_once $file;
    } else {
        return -1;
    }
    return true;
});