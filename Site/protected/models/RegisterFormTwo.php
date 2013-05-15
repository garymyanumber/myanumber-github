<?php

class RegisterFormTwo extends CFormModel
{
    public $prefix;
    public $number;
    
    public function rules() {
        return array(
            array('prefix', 'required'),
            array('number', 'safe')
        );
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels() {
        return array(
            'prefix' => 'Prefix',
            'number' => 'Number'
        );
    }
}

?>
