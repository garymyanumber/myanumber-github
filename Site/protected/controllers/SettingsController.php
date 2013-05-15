<?php

Yii::import('application.modules.profile.controllers.YumProfileController');
include(Yii::app()->basePath . '/components/braintree/Braintree.php');

class SettingsController extends YumProfileController {

    public $layout = '';

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
        $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->user->id));
        $membership = YumMembership::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->user->id));

        $oauth = new OAuthAPI(Yii::app()->params->att['client_id'], Yii::app()->params->att['secret_id']);
        $url = $oauth->getUserAuthorizationUrl(Yii::app()->getBaseUrl(true) . "/settings/attsettings");

        $this->render('index', array('profile' => $profile, 'membership' => $membership, 'att' => $url));
    }

    public function actionAttsettings() {
        $code = $_GET['code'];
        $oauth = new OAuthAPI(Yii::app()->params->att['client_id'], Yii::app()->params->att['secret_id']);
        $token = $oauth->getAccessToken($code);
        $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->user->id));
        $profile->authtoken = $token->AccessToken;
        $profile->attauth = true;
        $profile->update();
        $this->render('attsettings');
    }

    public function actionSupport() {
        $this->render('support');
    }

    public function actionContacts() {
        $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->user->id));
        $contacts = Contact::model()->findAll('profile_id=:profile_id AND type=:type ORDER BY `order` ASC', array(':profile_id' => $profile->id, ':type' => 1));
        $secondary = Contact::model()->findAll('profile_id=:profile_id AND type=:type', array(':profile_id' => $profile->id, ':type' => 2));
        $this->render('contacts', array('contacts' => $contacts, 'profile' => $profile, 'secondary' => $secondary));
    }

    public function actionSavecontacts() {
        $contacts = $_POST['contacts'];
        $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->user->id));

        //delete any contacts that were removed
        $existingContacts = Contact::model()->findAll('profile_id=:profile_id AND type=:type', array(':profile_id' => $profile->id, ':type' => 1));
        foreach ($existingContacts as $econtact) {
            $inlist = false;
            foreach ($contacts as $contact) {
                if ($contact['id'] == $econtact->id) {
                    $inlist = true;
                }
            }
            if (!$inlist) {
                $econtact->delete();
            }
        }

        //save contacts including new ones and their order
        $count = 0;
        $errors = array();
        foreach ($contacts as $contact) {
            $areaCode = substr($contact['phone'], 0, 3);
            if (!AllowedAreacode::model()->exists('areacode=:areacode', array(':areacode' => $areaCode))) {
                array_push($errors, "That area code is not allowed.");
                break;
            }
            $dataContact = Contact::model()->find('profile_id=:profile_id AND id=:id', array(':profile_id' => $profile->id, ':id' => $contact['id']));
            if ($dataContact) {
                $dataContact->name = $contact['name'];
                $dataContact->phone = $contact['phone'];
                $dataContact->email = $contact['email'];
                $dataContact->order = $count;
                $eSMS = $contact['enable_sms'] == 'true' ? 1 : 0;
                if($dataContact->enable_sms != $eSMS && $eSMS == 1)
                {
                    $url = "http://api.tropo.com/1.0/sessions?action=create" .
                    "&token=" . Yii::app()->params->tropo['messagetoken'] .
                    "&type=welcome" .
                    "&number=" . $dataContact->phone .
                    "&orig=" . $profile->provisioned_number .
                    "&msg=" . urlencode($profile->name . " has added you to their myaNUMBER. Save this number in your contacts for important family communications!");

                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($ch);
                    curl_close($ch);
                }
                $dataContact->enable_sms = $contact['enable_sms'] == 'true' ? 1 : 0;
                if ($dataContact->validate() && $dataContact->phone != '') {
                    $dataContact->update();
                }
            } else {
                $newcontact = new Contact();
                $newcontact->name = $contact['name'];
                $newcontact->email = $contact['email'];
                $newcontact->profile_id = $profile->id;
                $newcontact->phone = $contact['phone'];
                $newcontact->order = $count;
                $newcontact->enable_sms = $contact['enable_sms'] == 'true' ? 1 : 0;
                $newcontact->type = 1;
                if ($newcontact->validate() && $newcontact->phone != '') {
                    $newcontact->save();
                    
                    //send welcome text
                    $url = "http://api.tropo.com/1.0/sessions?action=create" .
                            "&token=" . Yii::app()->params->tropo['messagetoken'] .
                            "&type=welcome" .
                            "&number=" . $newcontact->phone .
                            "&orig=" . $profile->provisioned_number .
                            "&msg=" . urlencode($profile->name . " has added you to their myaNUMBER. Save this number in your contacts for important family communications!");

                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($ch);
                    curl_close($ch);
                }
            }

            $count++;
        }

        $data = array();
        if (count($errors) == 0) {
            $data = array(
                'success' => true,
                'contacts' => $contacts
            );
        } else {
            $data = array(
                'success' => false,
                'message' => array()
            );

            foreach ($errors as $error) {
                array_push($data['message'], $error);
            }
        }

        echo CJavaScript::jsonEncode($data);

        Yii::app()->end();
    }

    public function actionSavesecondary() {
        $user = YumUser::model()->findByPk(Yii::app()->user->id);
        $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->user->id));
        $secondary = Contact::model()->findAll('profile_id=:profile_id AND type=:type', array(':profile_id' => $profile->id, ':type' => 2));
        foreach ($secondary as $scon) {
            $scon->delete();
        }

        if (isset($_POST['secondary'])) {
            foreach ($_POST['secondary'] as $scontact) {
                $ncontact = new Contact();
                $ncontact->profile_id = $profile->id;
                $ncontact->name = 'Secondary';
                $ncontact->phone = $scontact;
                $ncontact->delay = 0;
                $ncontact->type = 2;
                $ncontact->order = 0;
                $ncontact->enable_sms = 0;
                $ncontact->save();
            }
        }

        $profile->name = $_POST['account']['nickname'];
        $profile->email = $_POST['account']['email'];

        if (!$profile->facebook_id) {
            $user->username = $_POST['account']['email'];
        }

        if (isset($_POST['account']['password'])) {
            $user->setPassword($_POST['account']['password'], YumEncrypt::generateSalt());
        }

        $user->update();

        $profile->update();

        echo CJavaScript::jsonEncode($_POST);
    }

    public function actionDisableatt() {
        $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->user->id));
        $setting = $_POST['setting'];
        switch ($setting) {
            case "messages":
                $profile->att_messages = 0;
                break;
            case "locator":
                $profile->att_locator = 0;
                break;
        }

        $profile->update();

        Yii::app()->end();
    }

    public function actionEnableatt() {
        $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->user->id));
        $setting = $_POST['setting'];
        switch ($setting) {
            case "messages":
                $profile->att_messages = 1;
                break;
            case "locator":
                $profile->att_locator = 1;
                break;
        }

        $profile->update();

        Yii::app()->end();
    }

    public function actionUpdatecreditcard() {
        if (Yii::app()->params['braintree']['sandbox']) {
            Braintree_Configuration::environment('sandbox');
        } else {
            Braintree_Configuration::environment('production');
        }

        Braintree_Configuration::merchantId(Yii::app()->params['braintree']['merchantid']);
        Braintree_Configuration::publicKey(Yii::app()->params['braintree']['publickey']);
        Braintree_Configuration::privateKey(Yii::app()->params['braintree']['privatekey']);
        $membership = YumMembership::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->user->id));
        $number = $_POST['number'];
        $expMonth = $_POST['month'];
        $expYear = $_POST['year'];
        if ($membership->customer_id) {
            $customer = Braintree_Customer::find($membership->customer_id);
            $token = $customer->creditCards[0]->token;
            $updateResult = Braintree_Customer::update(
                            $membership->customer_id, array(
                        'creditCard' => array(
                            'number' => $number,
                            'expirationDate' => $expMonth . '/' . $expYear,
                            'options' => array(
                                'updateExistingToken' => $token,
                                'verifyCard' => true
                            )
                        )
                            )
            );

            $result = array('success' => false);

            if ($updateResult->success) {
                $result['success'] = true;
                //cancel paypal subscription if they had one
                
            } else {
                $msg = $updateResult->_attributes['message'];
                $msg = str_replace("\n", "<br />", $msg);
                $result['success'] = false;
                $result['msg'] = $msg;
            }
            echo CJavaScript::jsonEncode($result);
        } else {
            //do registration style billing
            
        }

        Yii::app()->end();
    }

    public function actionDeactivateaccount() {
        $user = YumUser::model()->findByPk(Yii::app()->user->id);
        $user->deleted = 1;
        $user->update();
        Yii::app()->user->logout();
    }
}

?>
