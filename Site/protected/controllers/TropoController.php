<?php

Yii::import('application.modules.profile.models.YumProfile');
include(Yii::app()->basePath . '/components/tropo/tropo.class.php');

class TropoController extends Controller {

    public $layout = '';

    public function actionIndex() {
        $tropo = new Tropo();
        $session = new Session();

        $from = $session->getFrom();
        $to = $session->getTo();

        $type = $session->getParameters('type');

        $headers = $session->getHeaders();

        if (isset($headers->x_type))
            $type = $headers->x_type;

        if ($type == null) {
            $callee = YumProfile::model()->find('provisioned_number=:provisioned_number', array(':provisioned_number' => '+1' . $to['id']));
            if ($callee) {

                $callLog = new CallLog();
                $callLog->profile_id = $callee->id;
                $callLog->start = time();
                $callLog->originator = $from['id'];
                $callLog->save();

                $this->StartCalls($callee, $from['id']);
                $callee->tropo_session = $session->getId();
                $callee->update();
                $tropo->say(Yii::app()->getBaseUrl(true) . '/audio/ringback.mp3', array('allowSignals' => 'transfer'));
                $tropo->say(Yii::app()->getBaseUrl(true) . '/audio/ringback.mp3', array('allowSignals' => 'transfer'));
                $tropo->say(Yii::app()->getBaseUrl(true) . '/audio/ringback.mp3', array('allowSignals' => 'transfer'));
                $tropo->on(array('event' => 'transfer', 'next' => '/tropo/transfer?pid=' . $callee->id . '&lid=' . $callLog->id));
                $tropo->on(array('event' => 'hangup', 'next' => '/tropo/endcall?logid=' . $callLog->id));
                $tropo->say("Sorry. Nobody was available to answer your call. Goodbye.");
            } else {
                $tropo->say("Sorry, we could not find a user with that number.");
            }
        } else {
            switch ($type) {
                case "contact":
                    $contact = Contact::model()->findByPk($session->getParameters('cid'));
                    $profile = YumProfile::model()->findByPk($contact->profile_id);
                    $tropo->call('+1' . $contact->phone, array('from' => $profile->provisioned_number, 'timeout' => 25));
                    $tropo->on(array('event' => 'continue', 'next' => '/tropo/answer?cid=' . $contact->id));
                    $tropo->on(array('event' => 'incomplete', 'next' => '/tropo/incomplete?cid=' . $contact->id));
                    break;
                case "webcall":
                    $sid = $session->getParameters('ses');
                    $profile = YumProfile::model()->find('phono_session=:phono_session', array(':phono_session' => $sid));
                    $tropo->call('sip:' . $sid, array('from' => $profile->provisioned_number, 'timeout' => 25));
                    $tropo->on(array('event' => 'continue', 'next' => '/tropo/answerweb?sid=' . $sid));
                    $tropo->on(array('event' => 'incomplete', 'next' => '/tropo/incomplete?sid=' . $sid));
                    break;
                case "webcontact":
                    $contact = Contact::model()->findByPk($session->getParameters('cid'));
                    $profile = YumProfile::model()->findByPk($contact->profile_id);
                    $tropo->call('+1' . $contact->phone, array('from' => $profile->provisioned_number, 'timeout' => 25));
                    $tropo->on(array('event' => 'continue', 'next' => '/tropo/answer?cid=' . $contact->id));
                    $tropo->on(array('event' => 'incomplete', 'next' => '/tropo/incompleteweb?cid=' . $contact->id));
                    break;
                case "callpush":
                    $profile = YumProfile::model()->findByPk($session->getParameters('pid'));
                    $tropo->call('+1' . $profile->phone, array('from' => $profile->provisioned_number, 'timeout' => 25));
                    $tropo->on(array('event' => 'continue', 'next' => '/tropo/answerpush?pid=' . $profile->id));
                    break;
                case "web":
                    $ids = $headers->x_contacts;
                    $idarr = explode(',', $ids);
                    $contactList = array();
                    foreach ($idarr as $id) {
                        if ($id) {
                            $contact = Contact::model()->findByPk($id);
                            if ($contact) {
                                array_push($contactList, $contact);
                            }
                        }
                    }

                    $profile = YumProfile::model()->findByPk($contactList[0]->profile_id);
                    $profile->tropo_session = $session->getId();
                    $profile->update();

                    $callLog = new CallLog();
                    $callLog->profile_id = $profile->id;
                    $callLog->start = time();
                    $callLog->originator = $from['id'];
                    $callLog->save();

                    $tropo->say(Yii::app()->getBaseUrl(true) . '/audio/ringback.mp3', array('allowSignals' => 'transfer'));
                    $tropo->say("Sorry. Nobody was available to answer your call. Goodbye.");
                    $tropo->on(array('event' => 'transfer', 'next' => '/tropo/transfer?pid=' . $profile->id . '&lid=' . $callLog->id));
                    foreach ($contactList as $callContact) {
                        $options = array(
                            'type' => 'webcontact',
                            'cid' => $callContact->id
                        );
                        $this->TropoCreate($options);
                    }

                    break;
            }
        }


        $tropo->renderJSON();

        Yii::app()->end();
    }

    public function actionEndcall() {
        $id = $_GET['logid'];
        $callLog = CallLog::model()->findByPk($id);
        $callLog->stop = time();
        $elapsed = abs($callLog->stop - $callLog->start);
        $callLog->duration = $elapsed;
        $callLog->update();

        $profile = YumProfile::model()->findByPk($callLog->profile_id);
        $minutes = $profile->available_voice_minutes;
        $durMin = round($callLog->duration / 60, 0, PHP_ROUND_HALF_UP);
        $totalMinutes = $minutes - $durMin;
        $profile->available_voice_minutes = $totalMinutes;
        $profile->update();

        $textOptions = array(
            'type' => 'numbercalled',
            'number' => urlencode($profile->phone),
            'orig' => urlencode($profile->provisioned_number),
            'msg' => urlencode('Your myaNUMBER was dialed! ' . date('n/j/y') . ' ' . date('h:i A') . ' Duration: ' . $callLog->duration . ' seconds. ' . ' Originating Number: ' . $callLog->originator)
        );

        $this->TropoCreateMsg($textOptions);

        $body = 'Here is a helpful report to keep you up to speed.<br />' .
                '<br />' .
                'Date of Call: ' . date('F j, Y') . '<br />' .
                'Time of Call: ' . date('h:i A') . '<br />' .
                'Call Duration: ' . $callLog->duration . ' seconds<br />' .
                'Originating Number: ' . $callLog->originator . '<br />' .
                '<br />' .
                'Thank you for using myaNUMBER<br />' .
                '<br />' .
                'You have received this email because you have been included as a contact on a myaNUMBER plan. Please contact the myaNUMBER account owner for more information or to change their settings.';

        $mail = array(
            'from' => 'CallReport@myaNUMBER.com',
            'to' => $profile->email,
            'subject' => 'Your myaNUMBER was dialed!',
            'body' => $body,
        );

        $sent = YumMailer::send($mail);
    }

    public function actionTransfer() {
        $tropo = new Tropo();
        $pid = $_GET['pid'];
        $logid = $_GET['lid'];
        $profile = YumProfile::model()->findByPk($pid);
        $profile->tropo_session = null;
        $profile->update();
        $tropo->say("You are joining the conference.");
        $tropo->startRecording(array('url' => Yii::app()->getBaseUrl(true) . '/tropo/record?lid=' . $logid,
            'transcriptionID' => '',
            'transcriptionEmailFormat' => 'encoded',
            'transcriptionOutURI' => Yii::app()->getBaseUrl(true) . "/tropo/transcription?lid=" . $logid));
        $tropo->conference($pid, array('name' => 'myanumber', 'id' => $pid));
        $tropo->on(array('event' => 'hangup', 'next' => '/tropo/endcall?logid=' . $logid));
        $tropo->renderJSON();
        Yii::app()->end();
    }

    public function actionTranscription() {
        $lid = $_GET['lid'];
        $log = CallLog::model()->findByPk($lid);
        $data = file_get_contents('php://input');
        $json = json_decode($data);
        $log->transcription = $json->result->transcription;
        $log->update();
    }

    public function actionRecord() {
        $info = pathinfo($_FILES['filename']['name']);
        $id = $_GET['lid'];
        $callLog = CallLog::model()->findByPk($id);
        $callLog->audio = $_FILES['filename']['name'];
        $callLog->update();
        $target = 'protected/recordings/' . $_FILES['filename']['name'];
        move_uploaded_file($_FILES['filename']['tmp_name'], $target);
    }

    public function actionIncompletechoice() {
        Yii::app()->end();
    }

    public function actionAnswer() {
        $cid = $_GET['cid'];
        $tropo = new Tropo();
        $options = array('choices' => '1', 'name' => 'digit', 'timeout' => 20, 'mode' => 'dtmf');
        $tropo->ask('Press 1 to answer the call.', $options);
        $tropo->on(array('event' => 'continue', 'next' => '/tropo/joinconference?cid=' . $cid));
        $tropo->on(array('event' => 'incomplete', 'next' => '/tropo/incompletechoice'));
        $tropo->on(array('event' => 'hangup', 'next' => '/tropo/incompletechoice'));
        $tropo->renderJSON();
        Yii::app()->end();
    }

    public function actionAnswerweb() {
        $sid = $_GET['sid'];
        $tropo = new Tropo();
        $profile = YumProfile::model()->find('phono_session=:phono_session', array(':phono_session' => $sid));
        $sessionid = $profile->tropo_session;
        if ($sessionid)
            $this->TropoInterrupt($sessionid, 'transfer');
        $tropo->say("You are joining the conference.");
        $tropo->conference($profile->id, array('name' => 'myanumber', 'id' => $profile->id));
        $tropo->renderJSON();
        Yii::app()->end();
    }

    public function actionJoinconference() {
        $tropo = new Tropo();
        $cid = $_GET['cid'];
        $contact = Contact::model()->findByPk($cid);
        $profile = YumProfile::model()->findByPk($contact->profile_id);
        $sessionid = $profile->tropo_session;
        if ($sessionid)
            $this->TropoInterrupt($sessionid, 'transfer');
        $tropo->say("You are joining the conference.");
        $tropo->conference($profile->id, array('name' => 'myanumber', 'id' => $profile->id));
        $tropo->renderJSON();
        Yii::app()->end();
    }

    public function actionIncomplete() {
        $profile = null;
        if (isset($_GET['cid'])) {
            $cid = $_GET['cid'];
            $contact = Contact::model()->findByPk($cid);
            $profile = YumProfile::model()->findByPk($contact->profile_id);
        } else {
            $sid = $_GET['sid'];
            $profile = YumProfile::model()->find('phono_session=:phono_session', array(':phono_session' => $sid));
        }

        $contacts = Contact::model()->findAll('profile_id=:profile_id ORDER BY `order`', array(':profile_id' => $profile->id));
        $callContacts = array();
        for ($i = 0; $i < count($contacts); $i++) {
            if (isset($contact)) {
                if ($contacts[$i]->id == $contact->id) {
                    array_push($callContacts, $contacts[$i]);
                    $i++;
                    array_push($callContacts, $contacts[$i]);
                    break;
                } else {
                    array_push($callContacts, $contacts[$i]);
                }
            } else {
                array_push($callContacts, $contacts[$i]);
            }
        }
        foreach ($callContacts as $callContact) {
            $options = array(
                'type' => 'contact',
                'cid' => $callContact->id
            );
            $this->TropoCreate($options);
        }
    }

    public function actionIncompleteweb() {
        $cid = $_GET['cid'];
        $contact = Contact::model()->findByPk($cid);
        $profile = YumProfile::model()->findByPk($contact->profile_id);
        $sessionid = $profile->tropo_session;
    }

    public function actionNoanswer() {
        $tropo = new Tropo();
        $tropo->say("Nobody was available to answer your call. Goodbye.");
        $tropo->renderJSON();
        Yii::app()->end();
    }

    public function actionCallpush() {
        if (isset($_POST['pid'])) {
            $options = array(
                'type' => 'callpush',
                'pid' => $_POST['pid']
            );
            $this->TropoCreate($options);
        }

        Yii::app()->end();
    }

    public function actionAnswerpush() {
        $pid = $_GET['pid'];
        $tropo = new Tropo();
        $tropo->conference($pid, array('name' => 'myanumber', 'id' => $pid));
        $tropo->renderJSON();
        Yii::app()->end();
    }

    function StartCalls($profile, $incoming = null) {
        if (!empty($profile->phono_session)) {
            $options = array(
                'type' => 'webcall',
                'ses' => $profile->phono_session
            );
            $this->TropoCreate($options);
        } else {
            $contacts = Contact::model()->findAll('profile_id=:profile_id ORDER BY `order`', array(':profile_id' => $profile->id));
            $options = array(
                'type' => 'contact',
                'cid' => $contacts[0]->id
            );

            $this->TropoCreate($options);
        }
    }

    public function actionContinue() {
        $tropo = new Tropo();
        $tropo->say("I am continuing.");
        $tropo->renderJSON();
        Yii::app()->end();
    }

    function callContacts($profile, $id = null) {
        $contacts = Contact::model()->findAll('profile_id=:profile_id', array(':profile_id' => $profile->id));
        $contact = new Contact();
        if ($id) {
            for ($i = 0; $i < count($contacts); $i++) {
                if ($contacts[$i]->id == $id) {
                    $contact = $contacts[$i];
                }
            }
        } else {
            $contact = $contacts[0];
        }


        $url = "http://api.tropo.com/1.0/sessions?action=create" .
                "&token=" . Yii::app()->params->tropo['voicetoken'] .
                "&cid=" . $contact->id;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }

    public function actionJoinconf() {
        $tropo = new Tropo();
        $sessionid = $_GET['sessionid'];
        $conferenceid = $_GET['conferenceid'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.tropo.com/1.0/sessions/" . $sessionid . "/signals?action=signal&value=joinconf");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        $tropo->say("Joining conference.");
        $tropo->conference(null, array("name" => "conference", "id" => $conferenceid, "allowSignals" => ""));
        echo $tropo->renderJSON();
        Yii::app()->end();
    }

    public function actionJoinconforigin() {
        $tropo = new Tropo();
        $confid = $_GET["confid"];
        $tropo->say("Connecting you.");
        $tropo->conference(null, array("name" => "conference", "id" => $confid, "allowSignals" => ""));
        echo $tropo->renderJSON();
        Yii::app()->end();
    }

    public function actionDocall() {
        $tropo = new Tropo();
        $session = new Session();
        $numberToCall = $session->getParameters("number");
        $conference = $session->getParameters("conference");
        $delay = $session->getParameters("delay");
        $from = $session->getParameters("from");
        $sessionid = $session->getParameters("sessionid");
        sleep($delay);
        $tropo->call("+1" . $numberToCall, array('network' => 'VOICE', 'from' => $from));
        $tropo->on(array('event' => 'continue', 'next' => Yii::app()->getBaseUrl(true) . '/tropo/joinconf?sessionid=' . $sessionid . "&conferenceid=" . $conference, 'say' => 'Transfering you. Please wait.'));
        echo $tropo->renderJSON();
        Yii::app()->end();
    }

    public function actionHangup() {
        $tropo = new Tropo();
        $session = new Session();
    }

    public function actionMessage() {
        $tropo = new Tropo();
        $session = new Session();

        $type = $session->getParameters('type');
        $to = $session->getTo();
        $from = $session->getFrom();

        switch ($type) {
            case 'welcome':
                $number = urldecode($session->getParameters('number'));
                $message = urldecode($session->getParameters('msg'));
                $tropo->call('+1' . $number, array('network' => 'SMS', 'from' => $session->getParameters('orig')));
                $tropo->say($message);
                break;
            case 'welcomesignup':
                $number = urldecode($session->getParameters('number'));
                $message = urldecode($session->getParameters('msg'));
                $tropo->call('+1' . $number, array('network' => 'SMS', 'from' => $session->getParameters('orig')));
                $tropo->say($message);
                break;
            case 'numbercalled':
                $number = urldecode($session->getParameters('number'));
                $message = urldecode($session->getParameters('msg'));
                $tropo->call('+1' . $number, array('network' => 'SMS', 'from' => $session->getParameters('orig')));
                $tropo->say($message);
                break;
            case 'websms':
                $number = urldecode($session->getParameters('number'));
                $msg = $session->getParameters('name') . ': ' . urldecode($session->getParameters('text'));
                $profile = YumProfile::model()->find('provisioned_number=:provisioned_number', array(':provisioned_number' => '+' . $session->getParameters('orig')));
                $profile->available_sms_minutes = $profile->available_sms_minutes - 1;
                $profile->update();
                $tropo->call('+1' . $number, array('network' => 'SMS', 'from' => $session->getParameters('orig')));
                $tropo->say($msg);
                break;
            case 'locate':
                $number = urldecode($session->getParameters('number'));
                $pid = $session->getParameters('pid');
                $profile = YumProfile::model()->findByPk($pid);
                $profile->available_sms_minutes--;
                $profile->update();
                $msg = 'myaNUMBER Locator: ' . $profile->name . ' would like to request your location. Locator map: ' . Yii::app()->getBaseUrl(true) . '/locator/mobilerequest?h=' . $session->getParameters('hash');
                $tropo->call('+1' . $number, array('network' => 'SMS', 'from' => '+' . trim($session->getParameters('orig'))));
                $tropo->say($msg);
                break;
            case 'repeat':
                $profile = YumProfile::model()->find('provisioned_number=:provisioned_number', array(':provisioned_number' => $session->getParameters('orig')));
                $profile->available_sms_minutes = $profile->available_sms_minutes - 1;
                $profile->update();
                $msg = $session->getParameters('msg');
                $tropo->call('+1' . $session->getParameters('number'), array('network' => 'SMS', 'from' => $session->getParameters('orig')));
                $tropo->say($msg);
                break;
        }

        if (!$type) {
            $profile = YumProfile::model()->find('provisioned_number=:provisioned_number', array(':provisioned_number' => '+' . $to['id']));
            $msg = $session->getInitialText();
            if ($profile->available_sms_minutes > 0) {
                $profile->available_sms_minutes--;
                $profile->update();
                $message = new TextMessage();
                $message->profile_id = $profile->id;
                $message->thread_id = 0;
                $fromNumber = substr($from['id'], 1, strlen($from['id']));
                $contact = Contact::model()->find('phone=:phone AND profile_id=:profile_id', array(':phone' => $fromNumber, ':profile_id' => $profile->id));

                $fromName = $from['id'];
                if ($contact)
                    $fromName = $contact->name;

                $contacts = Contact::model()->findAll('profile_id=:profile_id AND enable_sms=:enable_sms', array(':profile_id' => $profile->id, ':enable_sms' => 1));
                $message->from = $fromName;
                $message->message = $msg;
                $message->createtime = time();
                $message->save();

                foreach ($contacts as $c) {
                    if ($contact) {
                        if ($c->id != $contact->id) {
                            $options = array(
                                'type' => 'repeat',
                                'number' => urlencode($c->phone),
                                'msg' => urlencode($fromName . ': ' . $message->message),
                                'orig' => urlencode($profile->provisioned_number)
                            );
                            $this->TropoCreateMsg($options);
                        }
                    } else {
                        $options = array(
                            'type' => 'repeat',
                            'number' => urlencode($c->phone),
                            'msg' => urlencode($fromName . ': ' . $message->message),
                            'orig' => urlencode($profile->provisioned_number)
                        );
                        $this->TropoCreateMsg($options);
                    }
                }
            } else {
                //Do reminders here of being out of SMS
            }
        }

        $tropo->renderJSON();

        Yii::app()->end();
    }

    public function actionSendwelcome() {
        $tropo = new Tropo();
        $session = new Session();
        $number = urldecode($session->getParameters('number'));
        $message = urldecode($session->getParameters('msg'));

        $tropo->call($number, array('network' => 'SMS'));
        $tropo->say($message);

        $tropo->renderJSON();
        Yii::app()->end();
    }

    function TropoCreate($options) {
        $url = "http://api.tropo.com/1.0/sessions?action=create" .
                "&token=" . Yii::app()->params->tropo['voicetoken'];
        foreach ($options as $key => $value) {
            $url .= "&$key=" . $value;
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }

    function TropoCreateMsg($options) {
        $url = "http://api.tropo.com/1.0/sessions?action=create" .
                "&token=" . Yii::app()->params->tropo['messagetoken'];
        foreach ($options as $key => $value) {
            $url .= "&$key=" . $value;
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }

    function TropoInterrupt($sessionid, $signal, $options = null) {
        $url = "https://api.tropo.com/1.0/sessions/" . $sessionid . "/signals?action=signal&value=" . $signal;
        if ($options) {
            foreach ($options as $key => $value) {
                $url .= "&$key=" . $value;
            }
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }

}

?>
