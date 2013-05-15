<?php

Yii::import('application.modules.profile.controllers.YumProfileController');

class CallsController extends YumProfileController {

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
        $model = new UserProfile;
        $model->contacts = Contact::model()->findAll('profile_id=:profile_id AND type=:type', array(':profile_id' => $profile->id, ':type' => 1));
        $this->render('index', array('model' => $model));
    }

    public function actionStatus() {
        $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->user->id));
        
        if (isset($_GET['ids'])) {
            $ids = $_GET['ids'];
            $idarr = explode(',', $ids);
            $contactList = array();
            foreach ($idarr as $id) {
                if (!empty($id)) {
                    $profile = YumProfile::model()->find("user_id=:user_id", array(':user_id' => Yii::app()->user->id));
                    $contact = Contact::model()->find('profile_id=:profile_id AND id=:id', array(':profile_id' => $profile->id, ':id' => $id));
                    if ($contact) {
                        array_push($contactList, $contact);
                    }
                }
            }

            $sip = Yii::app()->params->tropo['sip_address'];

            $this->render('status', array('contacts' => $contactList, 'sip' => $sip, 'cids' => $ids, 'profile' => $profile));
        }
        else {
            $this->render('status', array('profile' => $profile));
        }
    }

    public function actionGetcalls() {
        $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->user->id));
        $calls = CallLog::model()->findAll('profile_id=:profile_id ORDER BY `id` DESC LIMIT 4', array(':profile_id' => $profile->id));
        $editCalls = array();
        foreach ($calls as $call) {
            if (strlen($call->originator) > 10) {
                $call->originator = "Web";
            }
            array_push($editCalls, $call);
        }
        echo CJavaScript::jsonEncode($editCalls);
        Yii::app()->end();
    }

    public function actionGetfile() {
        try {
            $filename = $_GET['f'];
            $profile = YumProfile::model()->find("user_id=:user_id", array(":user_id" => Yii::app()->user->id));
            $callLog = CallLog::model()->find('profile_id=:profile_id AND audio=:audio', array(':profile_id' => $profile->id, ':audio' => $filename));
            if (file_exists('protected/recordings/' . $callLog->audio)) {
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename=' . $callLog->audio);
                readfile('protected/recordings/' . $callLog->audio);
            } else {
                header('HTTP/1.1 404 Not Found');
            }
        } catch (Exception $e) {
            header('HTTP/1.1 404 Not Found');
        }

        Yii::app()->end();
    }

    public function actionTranscription() {
        try {
            $log = $_GET['log'];
            $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->user->id));
            $callLog = CallLog::model()->find('profile_id=:profile_id AND id=:id', array(':profile_id' => $profile->id, ':id' => $log));
            if ($callLog) {
                if ($callLog->transcription) {
                    echo $callLog->transcription;
                } else {
                    echo "No transcription available.";
                }
            }
        } catch (Exception $e) {
            echo "Something went wrong retrieving the transcription.";
        }

        Yii::app()->end();
    }

    public function actionSavephonosession() {
        $sid = $_POST['sid'];
        $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->user->id));
        $profile->phono_session = $sid;
        $profile->update();
    }

    public function actionRemovephonosession() {
        $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->user->id));
        $profile->phono_session = null;
        $profile->update();
    }

}

?>
