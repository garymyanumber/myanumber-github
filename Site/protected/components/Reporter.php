<?php

///Reporter class for email and text reports
class Reporter {

    private $template;

    public function __construct() {
        
    }

    public function GetTemplate($templateName) {
        
    }

    public function Load($vars) {
        
    }

    public function Send() {
        $mail = array(
            'from' => Yii::app()->modules['registration']['registrationEmail'],
            'to' => $user->profile->email,
            'subject' => 'Activate your myaNUMBER account',
            'body' => $body,
        );

        $sent = YumMailer::send($mail);
    }

}

?>
