<?php

Yii::import('application.modules.profile.controllers.YumProfileController');
include(Yii::app()->basePath . '/components/att/att-rest.class.php');

class LocatorController extends YumProfileController {

    public $layout = '';

    public function accessRules() {
        return array(
            array('allow',
                'actions' => array('mobilerequest', 'mobilesuccess', 'mobiledeny', 'mobileprivacy', 'mobilelearn', 'mobileatt', 'reportlocation', 'test'),
                'users' => array('*')
            ),
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
        $model = new UserProfile;
        $model->contacts = Contact::model()->findAll('profile_id=:profile_id AND type=:type AND `enable_sms` = 1', array(':profile_id' => $profile->id, ':type' => 1));
        $this->render('index', array('model' => $model));
    }

    public function actionGetcontacts() {
        $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->user->id));
        $contacts = Contact::model()->findAll('profile_id=:profile_id', array(':profile_id' => $profile->id));
        $sql = "SELECT id, name, last_accuracy, last_location_update, astext(last_location) AS last_location FROM contact WHERE type = 1 AND profile_id = " . $profile->id;
        $cmd = Yii::app()->db->createCommand($sql);
        $result = $cmd->queryAll();
        echo CJavaScript::jsonEncode($result);
        Yii::app()->end();
    }

    public function actionGetcontactlocation() {
        $cid = $_GET['contact'];
        $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->user->id));
        $contact = Contact::model()->find('id=:id AND profile_id=:profile_id', array(':id' => $cid, ':profile_id' => $profile->id));

        if (!$contact->att_token) {
            if (($contact->last_location_request + 300) < time()) {
                $hash = $this->generateKey();
                $url = "http://api.tropo.com/1.0/sessions?action=create" .
                        "&token=" . Yii::app()->params->tropo['messagetoken'] .
                        "&type=locate" .
                        "&number=" . $contact->phone .
                        "&orig=" . $profile->provisioned_number .
                        "&pid=" . $profile->id .
                        "&hash=" . $hash;

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_exec($ch);
                curl_close($ch);

                $contact->location_hash = $hash;
                $contact->last_location_request = time();
                $contact->update();
                echo CJavaScript::jsonEncode($contact);
            } else {
                echo CJavaScript::jsonEncode(false);
            }
        } else {
            $location = new LocationAPI(Yii::app()->params->att['client_id'], Yii::app()->params->att['secret_id']);
            try {
                $deviceLocation = $location->getLocation($contact->att_token);
                $contact->last_accuracy = $deviceLocation->accuracy;
                $contact->last_location = new CDbExpression('POINT(:lat, :lon)', array(':lat' => $deviceLocation->latitude, ':lon' => $deviceLocation->longitude));
                $contact->last_location_update = time();
                $contact->update();
                echo CJavaScript::jsonEncode($contact);
            } catch (Exception $e) {
                Yum::log('Error: ' . $e->getMessage(), 'error');
                echo CJavaScript::jsonEncode(false);
            }
        }


        Yii::app()->end();
    }

    public function actionGetpoint($id) {
        $this->layout = '';
        if ($id) {
            $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->user->id));
            $contact = Contact::model()->find('id=:id AND profile_id=:profile_id', array(':id' => $id, ':profile_id' => $profile->id));
            if ($contact) {
                echo CJavaScript::jsonEncode($contact);
            } else {
                echo CJavascript::jsonEncode(false);
            }
        }

        Yii::app()->end();
    }

    public function actionLocationrequest($id) {
        $this->layout = '';
        if ($id) {
            $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->user->id));
            $contact = Contact::model()->find('id=:id AND profile_id=:profile_id', array(':id' => $id, ':profile_id' => $profile->id));
            if ($contact) {
                if (($contact->last_location_update + 300) < time() || !$contact->last_location_update) {
                    $hash = $this->generateKey();
                    $url = "http://api.tropo.com/1.0/sessions?action=create" .
                            "&token=" . Yii::app()->params->tropo['messagetoken'] .
                            "&type=locate" .
                            "&number=" . $contact->phone .
                            "&orig=" . $profile->provisioned_number .
                            "&pid=" . $profile->id .
                            "&hash=" . $hash;

                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_exec($ch);
                    curl_close($ch);

                    $contact->location_hash = $hash;
                    $contact->last_location_update = time();
                    $contact->update();

                    echo CJavaScript::jsonEncode(array('success' => true, 'msg' => 'Request sent.'));
                }
            } else {
                echo CJavascript::jsonEncode(array('success' => false, 'msg' => 'Invalid Contact ID.'));
            }
        } else {
            echo CJavaScript::jsonEncode(array('success' => false, 'msg' => 'Contact ID not set.'));
        }

        Yii::app()->end();
    }

    public function actionMap() {
        $this->render('map');
    }

    public function actionMobilerequest() {
        if (isset($_GET['h'])) {
            $hash = $_GET['h'];
            $oauth = new OAuthAPI(Yii::app()->params->att['client_id'], Yii::app()->params->att['secret_id']);
            $url = $oauth->getUserAuthorizationUrl(Yii::app()->getBaseUrl(true) . "/locator/mobileatt?h=" . $hash);
            $contact = Contact::model()->find('location_hash=:location_hash', array(':location_hash' => $hash));
            if ($contact) {
                $profile = YumProfile::model()->findByPk($contact->profile_id);
                $this->render('mobilerequest', array('number' => $profile->provisioned_number, 'hash' => $hash, 'att' => $url));
            } else {
                $this->redirect('/locator/mobiledeny');
            }
        } else {
            $this->redirect('/locator/mobiledeny');
        }
    }

    public function actionMobileatt() {
        try {
            $code = $_GET['code'];
            $oauth = new OAuthAPI(Yii::app()->params->att['client_id'], Yii::app()->params->att['secret_id']);
            $token = $oauth->getAccessToken($code);
            $hash = $_GET['h'];
            $contact = Contact::model()->find('location_hash=:location_hash', array(':location_hash' => $hash));
            $contact->att_token = $token->AccessToken;
            $location = new LocationAPI(Yii::app()->params->att['client_id'], Yii::app()->params->att['secret_id']);
            $deviceLocation = $location->getLocation($token->AccessToken);
            $contact->last_accuracy = $deviceLocation->accuracy;
            $contact->last_location = new CDbExpression('POINT(:lat, :lon)', array(':lat' => $deviceLocation->latitude, ':lon' => $deviceLocation->longitude));
            $contact->last_location_update = time();
            $contact->update();
            $this->redirect('/locator/mobilesuccess');
        } catch (Exception $e) {
            $this->redirect('/locator/mobiledeny');
        }
    }

    public function actionMobilesuccess() {
        $this->render('mobilesuccess');
    }

    public function actionMobiledeny() {
        $hash = $_GET['h'];
        $contact = Contact::model()->find('location_hash=:location_hash', array(':location_hash' => $hash));
        $profile = YumProfile::model()->findByPk($contact->profile_id);
        $this->render('mobiledeny', array('number' => $profile->provisioned_number));
    }

    public function actionMobileprivacy() {
        $this->render('mobileprivacy');
    }

    public function actionMobilelearn() {
        $this->render('mobilelearn');
    }

    public function actionReportlocation() {
        $lat = $_POST['lat'];
        $lon = $_POST['lon'];
        $hash = $_POST['hash'];
        $accuracy = $_POST['accuracy'];
        $contact = Contact::model()->find('location_hash=:hash', array(':hash' => $hash));
        if ($contact) {
            $contact->last_location = new CDbExpression('POINT(:lat, :lon)', array(':lat' => $lat, ':lon' => $lon));
            $contact->last_accuracy = $accuracy;
            $contact->last_location_update = time();
            $contact->update();
            $this->redirect('/locator/mobilesuccess');
        } else {
            $this->redirect('/locator/mobiledeny');
        }
    }

    function generateKey($len = 8) {
        $k = str_repeat('.', $len);
        while ($len--) {
            $k[$len] = substr('acefghjkpqrstwxyz23456789', mt_rand(0, strlen('acefghjkpqrstwxyz23456789') - 1), 1);
        }
        return $k;
    }

}

?>
