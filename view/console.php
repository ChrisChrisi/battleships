<?php

if ($this->message != false) {
    echo NEW_LINE.$this->message;
}
echo NEW_LINE.$data.NEW_LINE;

$this->setUserInput(readline( Messages::getMessage('prompt') .' '));

$this->makeTurn();