<?php

class RegisterFormOne extends CFormModel {

    public $name;
    public $email;
    public $password;
    public $confirmPassword;
    public $areaCode;
    public $prefix;
    public $line;
    public $facebookLogin;

    public function rules() {
        return array(
            array('name, email, password, confirmPassword, areaCode, prefix, line', 'required'),
            array('email', 'email'),
            array('password', 'compare', 'compareAttribute' => 'confirmPassword')
        );
    }

    public function attributeLabels() {
        return array(
            'email' => 'Email'
        );
    }

}

?>
