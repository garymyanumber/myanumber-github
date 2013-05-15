<?php

Yii::import('application.modules.profile.controllers.YumProfileController');

class MessagingController extends YumProfileController {

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
        $model = new UserProfile();
        $model->contacts = Contact::model()->findAll('profile_id=:profile_id AND `enable_sms` = 1', array(':profile_id' => $profile->id));
        $model->attauth = $profile->attauth;
        $this->render('index', array('model' => $model));
    }

    public function actionGetthreads() {
        if (isset($_POST['page'])) {
            $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->user->id));
            $page = $_POST['page'];
            $offset = $page * 4;
            $threads = TextMessageThread::model()->findAll('profile_id=:profile_id ORDER BY `last_time` DESC LIMIT 4 OFFSET ' . $offset, array(':profile_id' => $profile->id));
            echo CJavaScript::jsonEncode($threads);
            Yii::app()->end();
        } else {
            echo CJavaScript::jsonEncode(false);
            Yii::app()->end();
        }
    }

    public function actionMessage() {
        $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->user->id));
        //$splitContacts = split(',', $selectedContacts);
        $model = new UserProfile;
        $model->contacts = Contact::model()->findAll('profile_id=:profile_id AND `enable_sms` = 1', array(':profile_id' => $profile->id));
        /*
          foreach ($splitContacts as $cid) {
          $contact = Contact::model()->find('profile_id=:profile_id AND id=:cid', array(':profile_id' => $profile->id, ':cid' => $cid));
          if ($contact) {
          array_push($model->contacts, $contact);
          }
          }
         * 
         */

        $model->attauth = $profile->attauth;
        $this->render('message', array('model' => $model));
    }

    public function actionGetmessages() {
        $this->layout = NULL;
        $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->user->id));
        $messages = TextMessage::model()->findAll('profile_id=:profile_id ORDER BY `createtime` DESC LIMIT 4 OFFSET 0', array(':profile_id' => $profile->id));
        echo CJavaScript::jsonEncode($messages);
        Yii::app()->end();
    }

    public function actionGetpreviousmessages() {
        $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->user->id));
        $messages = TextMessage::model()->findAll('profile_id=:profile_id ORDER BY ID', array(':profile_id' => $profile->id));
        echo CJavaScript::jsonEncode($messages);
        Yii::app()->end();
        /*
          }
          else {
          echo CJavaScript::jsonEncode(null);
          Yii::app()->end();
          }
         * 
         */
    }

    public function actionSendmessage() {
        $text = $_POST['text'];
        $contacts = $_POST['contacts'];
        $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->user->id));
        $contactsArray = explode(",", $contacts);
        $contactList = array();
        $names = "";
        $counter = 1;
        
        $contactList = Contact::model()->findAll('profile_id=:profile_id AND enable_sms=:enable_sms', array(':profile_id' => $profile->id, ':enable_sms' => 1));
        foreach ($contactList as $contactItem) {
            $names .= $contactItem->name;
            if ($contactItem->id != $contactList[(count($contactList) - 1)]->id) {
                $names .= ", ";
            }
        }

        $urls = array();
        foreach ($contactList as $contact) {
            $url = "http://api.tropo.com/1.0/sessions?action=create" .
                    "&token=" . Yii::app()->params->tropo['messagetoken'] .
                    "&type=websms" .
                    "&text=" . urlencode($text) .
                    "&number=" . $contact->phone .
                    "&orig=" . str_replace("+", "", $profile->provisioned_number) .
                    "&name=" . urlencode($profile->name);
            array_push($urls, $url);
        }

        /*
          array_push($urls, "http://api.tropo.com/1.0/sessions?action=create" .
          "&token=1695a81fad241841876cae8aa4487fe683fbe9d3fd426081457754f5f60bbec14176e63f5a3507a26fe36677" .
          "&text=" . urlencode($text) .
          "&number=" . $profile->phone .
          "&orig=" . str_replace("+", "", $profile->provisioned_number) .
          "&name=" . urlencode($profile->name));
         */

        foreach ($urls as $url) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_exec($ch);
            curl_close($ch);
        }

        $message = new TextMessage();

        $message->profile_id = $profile->id;
        $message->name = $profile->name;
        $message->message = $text;
        $message->createtime = time();
        $message->from = "web";
        $message->thread_id = 0;
        $message->save();

        echo CJavaScript::jsonEncode($message);
        Yii::app()->end();
    }

    public function actionGetmessagecount() {
        $this->layout = NULL;
        $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->user->id));
        $count = TextMessage::model()->count('profile_id=:profile_id', array(':profile_id' => $profile->id));
        echo CJavaScript::jsonEncode($count);
        Yii::app()->end();
    }

}

?>
