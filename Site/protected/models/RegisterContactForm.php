<?php

class RegisterContactForm extends CFormModel
{
    public $name;
    public $email;
    public $areaCode;
    public $prefix;
    public $line;
    public $enable_sms;
    public $numberOfContacts;
    public $profile_id;
    
    public function rules()
    {
        return array(
            array('profile_id, name, email, areaCode, prefix, line, enable_sms', 'required'),
            array('email', 'email')
        );
    }
}

?>
