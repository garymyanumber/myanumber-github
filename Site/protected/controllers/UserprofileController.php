<?php

Yii::import('application.modules.profile.controllers.YumProfileController');
Yii::import('application.components.att.*');
include(Yii::app()->basePath . '/components/tropo/tropo.class.php');

Yii::app()->theme = 'myanumber2';

class UserprofileController extends YumProfileController {

    var $layout = "account";
    
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

    public function actionIndex() {
        $profile = YumProfile::model()->findByPk(Yii::app()->user->id);
        $this->render('index', array('model' => $profile));
    }

    public function actionProvision() {
        $profile = YumProfile::model()->findByPk(Yii::app()->user->id);
        
        if(!$profile->provisioned_number)
        {
            $appUrl = $this->getApplicationUrl("Myanumber Test");
        
            $number = $this->provisionNumber($appUrl);
        
            $profile->provisioned_number = $number;

            $profile->update();
        }
        
        $this->redirect("/userprofile");
    }
    
    public function actionAddContact()
    {
        $profile = YumProfile::model()->findByPk(Yii::app()->user->id);
        
        if(isset($_POST['name']) && isset($_POST['number']) && isset($_POST['email']) && isset($_POST['delay']))
        {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $number = $_POST['number'];
            $delay = $_POST['delay'];
            $contact = new Contact();
            $contact->profile_id = $profile->id;
            $contact->nickname = $name;
            $contact->email = $email;
            $contact->phone = $number;
            $contact->delay = $delay;
            $contact->save();
        }
        
        $this->redirect("/userprofile");
    }
    
    public function actionSubscription()
    {
        if(isset($_GET['success']))
        {
            if($_GET['success'] == "false")
            {
                $model = array();
                $model['success'] = $_GET['success'];
                $model['message'] = $_GET['faultDescription'];
                $this->render('subscription', array('model' => $model));
                Yii::app()->end();
            }
        }
        
        $hasSubscription = false;
        
        if(!$hasSubscription)
        {
            $subscription = new PaymentObject();
            $subscription->Amount = 1.99;
            $subscription->Category = 1;
            $subscription->Description = "This is a description.";
            $subscription->MerchantTransactionId = uniqid();
            $subscription->MerchantPaymentRedirectUrl = "http://test.myanumber.com/userprofile/subscription";
            $subscription->MerchantSubscriptionIdList = substr(uniqid(), 0, 12);
            
            $json = json_encode($subscription);
            
            $api = new Att("76b9bf6fa258a81722fe91be6b24b8f5", "63c08643d139f8ad");
            
            $notary = $api->Notarize($json);
            
            $subUrl = $api->GetSubscriptionUrl($notary);
            
            $this->redirect($subUrl);
            
        }
        
        $this->render('subscription');
    }
    
    public function actionGetdevicelocation()
    {
        $api = new Att("76b9bf6fa258a81722fe91be6b24b8f5", "63c08643d139f8ad");
        $token = $api->RefreshAccessToken(Yii::app()->session['token']);
        Yii::app()->session['token'] = $token;
        $accessToken = $token->AccessToken;
        $location = $api->GetDeviceLocation("500", $accessToken);
        $this->render('devicelocation', array('model' => $location));
    }
    
    public function actionGetdevicecapabilities()
    {
        $api = new Att("76b9bf6fa258a81722fe91be6b24b8f5", "63c08643d139f8ad");
        $token = $api->RefreshAccessToken(Yii::app()->session['token']);
        $capabilities = $api->GetDeviceCapabilities($token);
        $this->render('devicecapabilities', array('model' => $capabilities));
    }
    
    public function actionRefresh()
    {
        
    }
    
    function getApplicationUrl($applicationName)
    {
        $url = "https://api.tropo.com/v1/applications";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERPWD, "myanumberdev:fog.pretzel18");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	$jsonResponse = curl_exec($ch);
	curl_close ($ch);
        
	$jobj = json_decode($jsonResponse);
        
        foreach($jobj as $application)
        {
            if($application->name == $applicationName)
            {
                $appUrl = $application->href;
                return $appUrl;
            }
        }
        
        return null;
    }
}

?>