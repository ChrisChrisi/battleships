<?php

class Messages
{
    private static $results = array(
        'error' => '*** Error ***',
        'sunk' => '*** Sunk ***',
        'miss' => '*** Miss ***',
        'hit' => '*** Hit ***',
        'win' => 'Well done! You completed the game in %count shots',
    );

    public static function getMessage($name){
        return self::$results[$name];
    }
}