<?php

class BraintreeForm extends CFormModel {
    
    public $tr_data;
    public $firstName;
    public $lastName;
    public $email;
    public $address1;
    public $address2;
    public $locality;
    public $region;
    public $postal;
    public $cardNumber;
    public $cardExpiry;
    
    public function rules() {
        return array(
            array('tr_data,firstName,lastName,email,address1,address2,locality,region,postal,cardNumber,cardExpiry', 'safe')
        );
    }
}

?>
