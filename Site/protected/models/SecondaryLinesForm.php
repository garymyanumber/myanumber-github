<?php

class SecondaryLinesForm extends CFormModel {
    var $areaCode1;
    var $prefix1;
    var $line1;
    var $areaCode2;
    var $prefix2;
    var $line2;
    var $areaCode3;
    var $prefix3;
    var $line3;
    
    public function rules() {
        return array(
            array('areaCode1,prefix1,line1,areaCode2,prefix2,line2,areaCode3,prefix3,line3', 'safe')
        );
    }
}

?>
