<?php

Yii::import('application.modules.profile.controllers.YumProfileController');

class MeController extends YumProfileController {

    var $layout = "me";

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
        //check to see if they have a myanumber
        $user = YumUser::model()->findByPk(Yii::app()->user->id);
        if(!$user->profile->provisioned_number)
            $this->redirect('/register/provision');
        
        $membership = YumMembership::model()->find('user_id=:user_id', array(':user_id' => $user->id));
        if($membership) {
            if($membership->end_date) {
                if($membership->end_date < time()) {
                    Yii::app()->session['user_id'] = $user->id;
                    $this->redirect("/register/stepfour");
                }
            }
        }
        
        $this->render('blank');
    }
    
    public function actionSummary() {
        $this->layout = '';
        $model = array();
        $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->user->id));
        if(preg_match( '/^\+\d(\d{3})(\d{3})(\d{4})$/', $profile->provisioned_number,  $matches )) {
            $model['myanumber'] = $matches[1] . '-' . $matches[2] . '-' . $matches[3];
        }
        //$threads = TextMessageThread::model()->findAll('profile_id=:profile_id ORDER BY `last_time` DESC LIMIT 2', array(':profile_id' => $user->id));
        $model['threads'] = $messages = TextMessage::model()->findAll('profile_id=:profile_id ORDER BY `createtime` DESC LIMIT 2 OFFSET 0', array(':profile_id' => $profile->id));
        
        $model['voice'] = $profile->available_voice_minutes;
        $model['sms'] = $profile->available_sms_minutes;
        
        $model['calls'] = CallLog::model()->findAll('profile_id=:profile_id ORDER BY `id` DESC LIMIT 2', array(':profile_id' => $profile->id));
        
        $this->render('index', array('model' => $model));
    }
}

?>
