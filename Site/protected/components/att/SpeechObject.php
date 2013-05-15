<?php

class SpeechObject {
    public $Words;
    public $Confidence;
    public $Grade;
    public $ResultText;
    public $LanguageId;
    public $Hypothesis;
    
    public function __construct() {
        $this->Words = array();
    }
}

?>
