<?php

Yii::import('application.components.att.*');

class AuthController extends Controller
{
    public function accessRules() {
        return array(
            array('allow',
                'users' => array('@')
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }
    
    public function actionOauth()
    {
        $api = new Att("76b9bf6fa258a81722fe91be6b24b8f5", "63c08643d139f8ad");
        $code = $_GET['code'];
        Yii::app()->session['authcode'] = $code;
        $token = $api->GetAccessToken(Yii::app()->session['authcode']);
        Yii::app()->session['token'] = $token;
        $this->redirect('/userprofile');
    }
    
    public function actionAuth() {
        
    }
}

?>
