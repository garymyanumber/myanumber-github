<?php

Yii::import('application.modules.user.controllers.*');
Yii::import('application.modules.user.models.*');
Yii::import('application.modules.user.vendors.facebook.*');
Yii::import('application.modules.profile.models.YumProfile');
Yii::import('application.modules.registration.models.YumRegistrationForm');
Yii::import('application.modules.profile.models.*');
Yii::import('application.modules.registration.models.*');

class LoginController extends Controller {

    public $layout = "site";
    public $loginForm = null;

    public function actionIndex() {
        if (!Yii::app()->user->guestName)
            $this->redirect('/me');

        $fburl;
        $this->loginForm = new YumUserLogin('login');
        $fbconfig = Yum::module()->facebookConfig;
        $facebook = new Facebook($fbconfig);
        if (isset($facebook)) {
            if (!isset(Yii::app()->session['fb_state']))
                Yii::app()->session['fb_state'] = md5(uniqid(rand(), TRUE));

            $fburl = $facebook->getLoginUrl(array(
                'scope' => 'email',
                'redirect_uri' => Yii::app()->getBaseUrl(true) . "/login/fbredirect"
                    ));
        }

        if (isset($_POST['YumUserLogin'])) {
            $this->loginForm->attributes = $_POST['YumUserLogin'];
            // validate user input for the rest of login methods
            if ($this->loginForm->validate()) {
                $success = $this->loginByUsername();
                if ($success instanceof YumUser) {
                    //cookie with login type for later flow control in app
                    $cookie = new CHttpCookie('login_type', serialize('username'));
                    $cookie->expire = time() + (3600 * 24 * 30);
                    Yii::app()->request->cookies['login_type'] = $cookie;
                    Yum::log(Yum::t(
                                    'User {username} successfully logged in (Ip: {ip})', array(
                                '{ip}' => Yii::app()->request->getUserHostAddress(),
                                '{username}' => $success->username)));
                    if (Yum::module()->afterLogin !== false)
                        call_user_func(Yum::module()->afterLogin);

                    $this->redirect("/me");
                }
                if (isset($this->loginForm->errors['status'])) {
                    if (in_array('This account is not activated.', $this->loginForm->errors['status'])) {
                        $user = YumUser::model()->find('username=:username', array(':username' => $this->loginForm->username));
                        $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => $user->id));
                        if ($profile->step == 'six') {
                            $this->redirect('/register/stepsix');
                        } else {
                            $this->redirect('/register/step' . $profile->step);
                        }
                    }
                }
            }
        }

        $this->render('index', array('model' => $this->loginForm, 'fblogin' => $fburl));
    }

    public function actionFbredirect() {
        $this->render('fbredirect');
    }

    public function actionFacebook() {
        //Yii::app()->user->logout();
        $facebook = new Facebook(Yum::module()->facebookConfig);
        Facebook::$CURL_OPTS[CURLOPT_SSL_VERIFYPEER] = false;
        Facebook::$CURL_OPTS[CURLOPT_SSL_VERIFYHOST] = false;
        $fb_uid = $facebook->getUser();
        $success = $this->loginFacebook($fb_uid, $facebook, Yii::app()->session['fb_state']);
        if ($success instanceof YumUser) {
            //cookie with login type for later flow control in app
            $cookie = new CHttpCookie('login_type', serialize('facebook'));
            $cookie->expire = time() + (3600 * 24 * 30);
            Yii::app()->request->cookies['login_type'] = $cookie;
            Yum::log(Yum::t(
                            'User {username} successfully logged in (Ip: {ip})', array(
                        '{ip}' => Yii::app()->request->getUserHostAddress(),
                        '{username}' => $success->username)));
            if (Yum::module()->afterLogin !== false)
                call_user_func(Yum::module()->afterLogin);

            $this->redirect("/login/fbredirect?complete=1");
        }
        else {
            $profile = YumProfile::model()->findByAttributes(array('facebook_id' => $fb_uid));
            if (!$profile) {
                $this->redirect("/login/fbredirect?register=1");
            } else {
                Yii::app()->session['user_id'] = $profile->user_id;
                $this->redirect("/login/fbredirect?step=" . $profile->step);
            }
        }
    }

    public function loginFacebook($fb_uid, $facebook, $fb_state) {
        if (!Yum::module()->loginType & UserModule::LOGIN_BY_FACEBOOK)
            throw new Exception('actionFacebook was called, but is not activated in application configuration');

        if (!isset($_GET['state']))
            return false;

        if (!isset($fb_state))
            return false;

        if (isset($_GET['error']))
            return false;

        if ($fb_uid) {
            $profile = YumProfile::model()->findByAttributes(array('facebook_id' => $fb_uid));
            $user = ($profile) ? YumUser::model()->findByPk($profile->user_id) : null;
            try {
                $uid = $facebook->getUser();
                $fb_user = $facebook->api('/' . $uid);
                if (isset($fb_user['email']))
                    $profile = YumProfile::model()->findByAttributes(array('email' => $fb_user['email']));
                else
                    return false;
                if ($user === null && $profile === null) {
                    return false;
                }

                $identity = new YumUserIdentity($fb_uid, $user->id);
                $identity->authenticateFacebook(true);
                
                switch ($identity->errorCode) {
                    case YumUserIdentity::ERROR_NONE:
                        $duration = 3600 * 24 * 30; //30 days
                        Yii::app()->user->login($identity, $duration);
                        Yum::log('User ' . $user->username . ' logged in via facebook');
                        return $user;
                        break;
                    case YumUserIdentity::ERROR_STATUS_INACTIVE:
                        $user->addError('status', Yum::t('Your account is not activated.'));
                        break;
                    case YumUserIdentity::ERROR_STATUS_BANNED:
                        $user->addError('status', Yum::t('Your account is blocked.'));
                        break;
                    case YumUserIdentity::ERROR_STATUS_REMOVED:
                        $user->addError('status', Yum::t('Your account has been deleted.'));
                        break;
                    case YumUserIdentity::ERROR_PASSWORD_INVALID:
                        Yum::log(Yum::t('Failed login attempt for {username} via facebook', array(
                                    '{username}' => $user->username)), 'error');
                        $user->addError('status', Yum::t('Password incorrect.'));
                        break;
                }
                return false;
            } catch (Exception $e) {
                /* FIXME: Workaround for avoiding the 'Error validating access token.'
                 * inmediatly after a user logs out. This is nasty. Any other
                 * approach to solve this issue is more than welcomed.
                 */

                Yum::log('Error: ' . $e->getMessage(), 'error');
                return false;
            }
        } else {
            return false;
        }
    }

    public function actionLogout() {
        if (Yii::app()->user->isGuest)
            $this->redirect("/");

        //let's delete the login_type cookie
        $cookie = Yii::app()->request->cookies['login_type'];
        if ($cookie) {
            $cookie->expire = time() - (3600 * 72);
            Yii::app()->request->cookies['login_type'] = $cookie;
        }

        if ($user = YumUser::model()->findByPk(Yii::app()->user->id)) {
            $username = $user->username;
            $user->logout();

            if (Yii::app()->user->name == 'facebook') {
                if (!Yum::module()->loginType & UserModule::LOGIN_BY_FACEBOOK)
                    throw new Exception('actionLogout for Facebook was called, but is not activated in main.php');

                Yii::import('application.modules.user.vendors.facebook.*');
                require_once('Facebook.php');
                $facebook = new Facebook(Yum::module()->facebookConfig);
                $fb_cookie = 'fbs_' . Yum::module()->facebookConfig['appId'];
                $cookie = Yii::app()->request->cookies[$fb_cookie];
                if ($cookie) {
                    $cookie->expire = time() - 1 * (3600 * 72);
                    Yii::app()->request->cookies[$cookie->name] = $cookie;
                    $servername = '.' . Yii::app()->request->serverName;
                    setcookie("$fb_cookie", "", time() - 3600);
                    setcookie("$fb_cookie", "", time() - 3600, "/", "$servername", 1);
                }
                Yum::log('Facebook logout from user ' . $username);
                Yii::app()->user->logout();
                $facebook->destroySession();
                $this->redirect('/');
                //$this->redirect($facebook->getLogoutUrl(array('next' => Yii::app()->getBaseUrl(true) . '/')));
            } else {
                Yum::log(Yum::t('User {username} logged off', array(
                            '{username}' => $username)));

                Yii::app()->user->logout();
            }
        }

        $this->redirect("/");
    }

    public function loginByUsername() {
        if (Yum::module()->caseSensitiveUsers)
            $user = YumUser::model()->find('username = :username', array(
                ':username' => $this->loginForm->username));
        else
            $user = YumUser::model()->find('upper(username) = :username', array(
                ':username' => strtoupper($this->loginForm->username)));

        if ($user)
            return $this->authenticate($user);
        else {
            Yum::log(Yum::t(
                            'Non-existent user {username} tried to log in (Ip-Address: {ip})', array(
                        '{ip}' => Yii::app()->request->getUserHostAddress(),
                        '{username}' => $this->loginForm->username)), 'error');

            $this->loginForm->addError('password', Yum::t('Username or Password is incorrect'));
        }

        return false;
    }

    public function authenticate($user) {
        $identity = new YumUserIdentity($user->username, $this->loginForm->password);
        $identity->authenticate();
        switch ($identity->errorCode) {
            case YumUserIdentity::ERROR_NONE:
                $duration = $this->loginForm->rememberMe ? 3600 * 24 * 30 : 0; // 30 days
                Yii::app()->user->login($identity, $duration);
                return $user;
                break;
            case YumUserIdentity::ERROR_EMAIL_INVALID:
                $this->loginForm->addError("password", Yum::t('Username or Password is incorrect'));
                break;
            case YumUserIdentity::ERROR_STATUS_INACTIVE:
                $this->loginForm->addError("status", Yum::t('This account is not activated.'));
                break;
            case YumUserIdentity::ERROR_STATUS_BANNED:
                $this->loginForm->addError("status", Yum::t('This account is blocked.'));
                break;
            case YumUserIdentity::ERROR_STATUS_REMOVED:
                $this->loginForm->addError('status', Yum::t('Your account has been deleted.'));
                break;
            case YumUserIdentity::ERROR_STATUS_DELETED:
                $this->loginForm->addError('status', Yum::t('Your account has been deleted.'));
                break;
            case YumUserIdentity::ERROR_PASSWORD_INVALID:
                Yum::log(Yum::t(
                                'Password invalid for user {username} (Ip-Address: {ip})', array(
                            '{ip}' => Yii::app()->request->getUserHostAddress(),
                            '{username}' => $this->loginForm->username)), 'error');

                if (!$this->loginForm->hasErrors())
                    $this->loginForm->addError("password", Yum::t('Username or Password is incorrect'));
                break;
                return false;
        }
    }

    public function actionLostpassword($email = null, $key = null) {
        $form = new YumPasswordRecoveryForm;

        if ($email != null && $key != null) {
            if ($profile = YumProfile::model()->find('email = :email', array(
                'email' => $email))) {
                $user = $profile->user;
                if ($user->activationKey == $key) {
                    $passwordform = new YumUserChangePassword;
                    if (isset($_POST['YumUserChangePassword'])) {
                        $passwordform->attributes = $_POST['YumUserChangePassword'];
                        if ($passwordform->validate()) {
                            $user->password = YumEncrypt::encrypt($passwordform->password, $user->salt);
                            $user->activationKey = YumEncrypt::encrypt(microtime() . $passwordform->password, $user->salt);
                            $user->save();
                            Yum::setFlash('Your new password has been saved.');
                            $this->redirect('/login');
                        }
                    }
                    $this->render('/login/recover', array(
                        'form' => $passwordform));
                    Yii::app()->end();
                } else {
                    $form->addError('login_or_email', Yum::t('Invalid recovery key'));
                }
            }
        } else {
            if (isset($_POST['YumPasswordRecoveryForm'])) {
                $form->attributes = $_POST['YumPasswordRecoveryForm'];

                if ($form->validate()) {
                    Yum::setFlash(
                            'Instructions have been sent to you. Please check your email.');

                    if ($form->user instanceof YumUser) {
                        $form->user->generateActivationKey();
                        $recovery_url = $this->createAbsoluteUrl(
                                '/login/lostpassword', array(
                            'key' => $form->user->activationKey,
                            'email' => $form->user->profile->email));

                        $mail = array(
                            'from' => Yii::app()->params['adminEmail'],
                            'to' => $form->user->profile->email,
                            'subject' => 'You requested a new password',
                            'body' => strtr(
                                    'You have requested a new password. Please use this URL to continue: {recovery_url}', array(
                                '{recovery_url}' => $recovery_url)),
                        );
                        $sent = YumMailer::send($mail);
                    } else
                        $this->redirect('/login');
                }
            }
        }
        $this->render('/login/lostpassword', array(
            'form' => $form));
    }

}

?>
