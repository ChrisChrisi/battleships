<?php

class UserAction
{
    private $data;
    private $specialCommands = array('show', 'reset');
    public function __construct($data)
    {
        $this->data = strtolower($data);
    }

    public function processCommand() {
        $command = 'fail';
        $coordinates = false;
        if(strlen($this->data))
        if(in_array($this->data, $this->specialCommands)){
            $command = $this->data;
        } else {
            preg_match('/([a-z]{1,})([0-9]{1,})/i', $this->data, $match);
            if(isset($match[1]) && isset($match[2])){
                $coordinates = array($match[1], $match[2]);
                $command = 'play';
            }
        }
        return $this->formatResult($command, $coordinates);
    }

    public function formatResult($command, $coordinates = false){
        $result = array('command' => $command);
        if(isset($coordinates)){
            $result['coordinates'] = array('rindex' => $coordinates[0], 'cindex' => $coordinates[1]);
        }
        return $result;
    }
}