<?php

$inputMsg = $this->remainingShips > 0 ? Messages::getMessage('prompt') .':'. SPACE : Messages::getMessage('play_again') . SPACE . '(yes / no):'.SPACE;
if ($this->message != false) {
    echo NEW_LINE . $this->message.NEW_LINE;
}
if ($this->remainingShips > 0) {
    echo NEW_LINE . $data . NEW_LINE;
}
$this->setUserInput(readline($inputMsg));

$this->makeTurn();