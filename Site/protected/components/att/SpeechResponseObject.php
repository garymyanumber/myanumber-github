<?php

class SpeechResponseObject {
    public $Status;
    public $ResponseId;
    public $Results;
    
    public function __construct() {
        $this->Results = array();
    }
}

?>
