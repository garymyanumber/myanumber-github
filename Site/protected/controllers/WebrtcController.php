<?php

Yii::import('application.components.attfoundry.Attfoundry');

class WebrtcController extends Controller {
    public function actionIndex() {
        if(!isset(Yii::app()->session['token'])) {
            $foundry = new Attfoundry();
            $url = $foundry->getAuthUrl(Yii::app()->params->attfoundry['client_id'], 'http://test.myanumber.com/webrtc/oauth');
            $this->redirect($url);
        }
            
        $this->render('index', array('model' => Yii::app()->session['token']));
    }
    
    public function actionOauth() {
        $foundry = new Attfoundry();
        $code = $_GET['code'];
        $token = $foundry->getToken(Yii::app()->params->attfoundry['client_id'],
                Yii::app()->params->attfoundry['secret_id'],
                $code, 
                'http://test.myanumber.com/webrtc/');
        Yii::app()->session['token'] = $token;
        $this->redirect('/webrtc/');
    }
}

?>
