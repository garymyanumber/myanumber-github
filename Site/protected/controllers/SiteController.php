<?php
Yii::import('application.modules.user.components.*');

class SiteController extends Controller {

    public $layout = 'site';

    public function accessRules() {
        return array(
            array('allow',
                'users' => array('*')
            )
        );
    }

    public function actionIndex() {
        $this->layout = '';
        $this->render('index');
    }

    public function actionError() {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    public function actionContact() {
        $this->render('contact');
    }

    public function actionAbout() {
        $this->render('about');
    }

    public function actionContactform() {
        $model = new ContactForm();
        $model->attributes = $_POST;
        if ($model->validate()) {
            //send email
            $body = $model->body;

            $mail = array(
                'from' => 'no-reply@myanumber.com',
                'to' => 'support@myanumber.com',
                'subject' => $model->subject,
                'body' => $body,
            );

            $sent = YumMailer::send($mail);
            if($sent) {
                echo CJavaScript::jsonEncode(array('success' => true));
            }
            else {
                echo CJavaScript::jsonEncode(array('success' => false, 'msg' => 'There was a problem sending email. Please try again later.'));
            }
        } else {
            echo CJavaScript::jsonEncode(array('success' => false, 'errors' => $model->errors));
        }

        Yii::app()->end();
    }

}