<?php

Yii::import('application.modules.profile.models.YumProfile');
Yii::import('application.modules.membership.models.YumMembership');
Yii::import('application.modules.user.models.*');
Yii::import('application.modules.registration.models.YumRegistrationForm');
Yii::import('application.modules.user.vendors.facebook.*');
Yii::import('application.modules.user.components.*');
include(Yii::app()->basePath . '/components/tropo/tropo.class.php');
include(Yii::app()->basePath . '/components/att/att-rest.class.php');
include(Yii::app()->basePath . '/components/braintree/Braintree.php');
include(Yii::app()->basePath . '/components/paypal-digital-goods/paypal-digital-goods.class.php');
include(Yii::app()->basePath . '/components/paypal-digital-goods/paypal-subscription.class.php');

class RegisterController extends Controller {

    public $defaultAction = 'stepone';
    public $layout = 'site';

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

    public function actionSignup() {
        if (isset($_POST['email'])) {
            if ($_POST['email'] == 'code') {
                $this->redirect('/register/stepone');
            } else {
                $signup = new Signup();
                $signup->email = $_POST['email'];
                $signup->save();
                $mail = array(
                    'from' => Yii::app()->modules['registration']['registrationEmail'],
                    'to' => 'kyle@myanumber.com',
                    'subject' => 'myanumber signup',
                    'body' => $_POST['email'] . ' just signed up.',
                );

                $sent = YumMailer::send($mail);
                $this->render('thankyou');
            }
        } else {
            $this->render('signup');
        }
    }

    public function actionStepone() {
        $model = new RegisterFormOne();

        $fbconfig = Yum::module()->facebookConfig;
        $facebook = new Facebook($fbconfig);
        if (isset($facebook)) {
            if (!isset(Yii::app()->session['fb_state']))
                Yii::app()->session['fb_state'] = md5(uniqid(rand(), TRUE));

            $loginUrl = $facebook->getLoginUrl(array(
                'scope' => 'email',
                'redirect_uri' => Yii::app()->getBaseUrl(true) . "/register/fbredirect"
                    ));
            $model->facebookLogin = $loginUrl;
        }

        if (isset($_POST['RegisterFormOne'])) {
            $model->attributes = $_POST['RegisterFormOne'];
            $model->validate();

            if (!$model->hasErrors()) {
                $phone = $model->areaCode . $model->prefix . $model->line;
                if (preg_match('/[0-9]{9}/', $phone)) {
                    $exists = YumProfile::model()->count('email=:email', array(':email' => $model->email));
                    if ($exists == 0) {
                        $profile = new YumProfile();
                        $profile->email = $model->email;
                        $profile->name = $model->name;
                        $profile->phone = $phone;
                        $profile->step = 'two';
                        $user = new YumUser();
                        if($user->register($model->email, $model->password, $profile)) {
                            $admincontact = new Contact();
                            $admincontact->profile_id = $profile->id;
                            $admincontact->name = $profile->name;
                            $admincontact->email = $profile->email;
                            $admincontact->phone = $profile->phone;
                            $admincontact->delay = 0;
                            $admincontact->type = 1;
                            $admincontact->order = 0;
                            $admincontact->enable_sms = 1;
                            $admincontact->save();

                            Yii::app()->session['user_id'] = $user->id;

                            $this->redirect('/register/steptwo');
                        }
                        else {
                            $model->addError('', 'There was a problem registering the user. Please try again.');
                            $this->render('/register/step1', array('model' => $model));
                        }
                    } else {
                        $existingProfile = YumProfile::model()->find("email=:email", array(':email' => $model->email));
                        Yii::app()->session['user_id'] = $existingProfile->user_id;
                        $this->redirect('/register/step' . $existingProfile->step);
                    }
                } else {
                    $model->addError('phone', "The phone number is invalid.");
                    $this->render('/register/step1', array('model' => $model));
                }
            } else {
                $this->render('/register/step1', array('model' => $model));
            }
        } else {
            $this->render('/register/step1', array('model' => $model));
        }
    }

    public function actionRegisterfacebook() {
        $model = new RegisterFormOne();
        if (isset($_POST['RegisterFormOne'])) {
            $model->attributes = $_POST['RegisterFormOne'];
            $fbconfig = Yum::module()->facebookConfig;
            $facebook = new Facebook($fbconfig);
            Facebook::$CURL_OPTS[CURLOPT_SSL_VERIFYPEER] = false;
            Facebook::$CURL_OPTS[CURLOPT_SSL_VERIFYHOST] = false;
            $fb_uid = $facebook->getUser();
            if ($fb_uid) {
                $phone = $model->areaCode . $model->prefix . $model->line;
                if (preg_match('/[0-9]{9}/', $phone)) {
                    $fb_user = $facebook->api('/me');
                    $user = new YumUser();
                    $user->username = 'fb_' . YumRegistrationForm::genRandomString(Yum::module()->usernameRequirements['maxLen'] - 3);
                    $user->password = YumEncrypt::encrypt(YumUserChangePassword::createRandomPassword2(Yum::module()->passwordRequirements['maxLen']));
                    $user->activationKey = YumEncrypt::encrypt(microtime() . $user->password, $user->salt);
                    $user->createtime = time();
                    $user->superuser = 0;
                    $user->status = 0;
                    $user->validate();
                    if (!$user->hasErrors()) {
                        $exists = YumProfile::model()->find('email=:email', array(':email' => $fb_user['email']));
                        if (!$exists) {
                            if ($user->save()) {

                                    $profile = new YumProfile();
                                    $profile->user_id = $user->id;
                                    $profile->facebook_id = $fb_user['id'];
                                    $profile->email = $fb_user['email'];
                                    $profile->phone = $phone;
                                    $profile->name = $fb_user['name'];
                                    $profile->step = 'two';
                                    $profile->save(false);

                                    $admincontact = new Contact();
                                    $admincontact->profile_id = $profile->id;
                                    $admincontact->name = $profile->name;
                                    $admincontact->email = $profile->email;
                                    $admincontact->phone = $profile->phone;
                                    $admincontact->delay = 0;
                                    $admincontact->type = 1;
                                    $admincontact->order = 0;
                                    $admincontact->enable_sms = 1;
                                    $admincontact->save();

                                    Yii::app()->session['user_id'] = $user->id;
                                    $this->redirect('/register/steptwo');

                            }

                            $model->addError(null, 'Could not save user. Please try again.');
                            $this->render('/register/step1', array('model' => $model));
                            Yii::app()->end();
                        } else {
                            $this->redirect('/register/step' . $exists->step);
                            //$model->addError(null, 'User already created in system.');
                            //$this->render('/register/step1', array('model' => $model));
                            //Yii::app()->end();
                        }
                    }
                } else {
                    $fbconfig = Yum::module()->facebookConfig;
                    $facebook = new Facebook($fbconfig);
                    Facebook::$CURL_OPTS[CURLOPT_SSL_VERIFYPEER] = false;
                    Facebook::$CURL_OPTS[CURLOPT_SSL_VERIFYHOST] = false;
                    $fb_user = $facebook->api('/me');
                    $model->addError('', "Invalid phone number.");
                    $this->render('step1facebook', array('model' => $model, 'name' => $fb_user['name']));
                    Yii::app()->end();
                }

                $model->addErrors($user->errors);
                $this->render('/register/step1', array('model' => $model));
                Yii::app()->end();
            } else {
                $this->redirect("/register/fbredirect?state=" . $_GET['state']);
            }
        } else {
            $this->redirect('/register/fbredirect?complete=1');
            //$this->render('/register/step1facebook', array('model' => $model));
            Yii::app()->end();
        }
    }

    public function actionSteponefacebook() {
        $fbconfig = Yum::module()->facebookConfig;
        $facebook = new Facebook($fbconfig);
        Facebook::$CURL_OPTS[CURLOPT_SSL_VERIFYPEER] = false;
        Facebook::$CURL_OPTS[CURLOPT_SSL_VERIFYHOST] = false;
        $fb_user = $facebook->api('/me');
        $model = new RegisterFormOne();
        $this->render('step1facebook', array('model' => $model, 'name' => $fb_user['name']));
    }

    public function actionFbredirect() {
        $this->render('/register/fbredirect');
    }

    public function actionSteptwo() {
        $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->session['user_id']));
        $count = Contact::model()->count('profile_id=:profile_id', array(':profile_id' => $profile->id));
        $model = new RegisterContactForm();
        $model->numberOfContacts = $count;
        $model->profile_id = $profile->id;
        if (isset($_POST['RegisterContactForm'])) {
            //do saving of contact here
            $model->attributes = $_POST['RegisterContactForm'];
            $model->profile_id = $profile->id;
            $model->validate();
            if ($count == 5) {
                $model->addError(null, 'You have already added five contacts.');
            }

            if (!AllowedAreacode::model()->exists("areacode=:areacode", array(':areacode' => $model->areaCode))) {
                $model->addError(null, "That area code isn't allowed.");
            }

            if (!$model->hasErrors()) {
                $contact = new Contact();
                $contact->profile_id = $profile->id;
                $contact->name = $model->name;
                $contact->email = $model->email;
                $contact->phone = $model->areaCode . $model->prefix . $model->line;
                $contact->type = 1;
                $contact->order = $count;
                $contact->enable_sms = $model->enable_sms;
                $contact->save();

                if (isset($_POST['add'])) {
                    $model = new RegisterContactForm();
                    $model->numberOfContacts = Contact::model()->count('profile_id=:profile_id', array(':profile_id' => $profile->id));
                    $this->render('/register/step2', array('model' => $model));
                }
                if (isset($_POST['done'])) {
                    $contacts = Contact::model()->count('profile_id=:profile_id', array(':profile_id' => $profile->id));
                    if ($contacts > 0) {
                        $profile->step = 'three';
                        $profile->update();
                        $this->redirect('/register/stepthree');
                    } else {
                        $model->addError(null, "You must add at least one contact.");
                        $this->render('/register/step2', array('model' => $model));
                    }
                }
            } else {
                if (isset($_POST['done'])) {
                    $contacts = Contact::model()->count('profile_id=:profile_id', array(':profile_id' => $profile->id));
                    if ($contacts > 0) {
                        $profile->step = 'three';
                        $profile->update();
                        $this->redirect('/register/stepthree');
                    } else {
                        $model->addError(null, "You must add at least one contact.");
                        $this->render('/register/step2', array('model' => $model));
                    }
                } else {
                    $this->render('/register/step2', array('model' => $model));
                }
            }
        } else {
            $this->render('/register/step2', array('model' => $model));
        }
    }

    public function actionStepthree() {
        $model = new SecondaryLinesForm();
        if (isset($_POST['SecondaryLinesForm'])) {
            $model->attributes = $_POST['SecondaryLinesForm'];
            if ($model->areaCode1 != "") {
                if (!AllowedAreacode::model()->exists("areacode=:areacode", array(':areacode' => $model->areaCode1))) {
                    $model->addError(null, "That area code isn't allowed.");
                }
            }
            if ($model->areaCode2 != "") {
                if (!AllowedAreacode::model()->exists("areacode=:areacode", array(':areacode' => $model->areaCode2))) {
                    $model->addError(null, "That area code isn't allowed.");
                }
            }
            if ($model->areaCode3 != "") {
                if (!AllowedAreacode::model()->exists("areacode=:areacode", array(':areacode' => $model->areaCode3))) {
                    $model->addError(null, "That area code isn't allowed.");
                }
            }
            if (!$model->hasErrors()) {
                $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->session['user_id']));
                $this->saveSecondaryContact($profile->id, $model->areaCode1 . $model->prefix1 . $model->line1);
                $this->saveSecondaryContact($profile->id, $model->areaCode2 . $model->prefix2 . $model->line2);
                $this->saveSecondaryContact($profile->id, $model->areaCode3 . $model->prefix3 . $model->line3);
                $profile->step = 'four';
                $profile->update();
                $this->redirect("/register/stepfour");
            }
        }
        $this->render('step3', array('model' => $model));
    }

    public function actionStepfour() {
        $model = new CFormModel();
        if (isset($_POST['type'])) {
            if ($_POST['type'] != '' || $_POST['promo'] != '') {
                $membership = YumMembership::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->session['user_id']));
                if (!$membership)
                    $membership = new YumMembership();

                switch ($_POST['type']) {
                    case 'monthly':
                        $subscriptionType = SubscriptionType::model()->find('span=:span', array(':span' => 'monthly'));
                        $membership->subscription_type = $subscriptionType->id;
                        $membership->price = $subscriptionType->price;
                        break;
                    case 'annual':
                        $subscriptionType = SubscriptionType::model()->find('span=:span', array(':span' => 'annual'));
                        $membership->subscription_type = $subscriptionType->id;
                        $membership->price = $subscriptionType->price;
                        break;
                }

                $membership->user_id = Yii::app()->session['user_id'];
                $membership->payment_id = 0;
                $membership->order_date = time();
                $membership->subscribed = 0;
                $membership->membership_id = 0;
                $membership->promo = null;
                $membership->end_date = null;
                $membership->validate();
                $membership->save();

                if (isset($_POST['promo'])) {
                    $this->applyPromoCode($membership, $_POST['promo']);
                }

                $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->session['user_id']));
                $profile->step = 'five';
                $profile->update();
                $this->redirect('/register/stepfive');
            } else {
                $model->addError(null, 'You must select a plan or use a promo code.');
            }
        }

        $this->render("step4", array('model' => $model));
    }

    public function applyPromoCode($membership, $code) {
        $promo = Promo::model()->find('code=:code', array(':code' => $code));
        if ($promo) {
            $promoType = PromoType::model()->findByPk($promo->type);
            $membership->price = $promoType->price;
            $membership->end_date = time() + ($promoType->duration_in_days * 24 * 60 * 60);
            $membership->promo = $promo->id;
            $membership->update();
            $promo->used = $promo->used + 1;
            $promo->update();

            if ($promoType->skip_payment) {
                $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->session['user_id']));
                $profile->step = 'six';
                $profile->update();
                $this->redirect('/register/stepsix');
            }
        }
    }

    public function actionStepfive() {
        PayPal_Digital_Goods_Configuration::username(Yii::app()->params->paypal['username']);
        PayPal_Digital_Goods_Configuration::password(Yii::app()->params->paypal['password']);
        PayPal_Digital_Goods_Configuration::signature(Yii::app()->params->paypal['signature']);
        PayPal_Digital_Goods_Configuration::return_url(Yii::app()->getBaseUrl(true) . '/register/paypalreturn');
        PayPal_Digital_Goods_Configuration::cancel_url(Yii::app()->getBaseUrl(true) . '/register/paypalcancel');
        PayPal_Digital_Goods_Configuration::notify_url(Yii::app()->getBaseUrl(true) . '/register/paypalnotify');
        PayPal_Digital_Goods_Configuration::business_name('myaNUMBER');
        if (!Yii::app()->params->paypal['sandbox']) {
            PayPal_Digital_Goods_Configuration::environment("live");
        }

        $membership = YumMembership::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->session['user_id']));
        $summary = 'Could not get summary description.';
        $subscriptionType = SubscriptionType::model()->findByPk($membership->subscription_type);
        $summary = $subscriptionType->description;
        if ($membership->promo) {
            $promo = Promo::model()->findByPk($membership->promo);
            $promoType = PromoType::model()->findByPk($promo->type);
            $summary = $promoType->description;
        }

        $payment = new PaymentAPI(Yii::app()->params->att['client_id'], Yii::app()->params->att['secret_id']);
        $atturl = $payment->getSubscriptionUrl(10, 1, 'Myanumber Subscription', "MYANUMSUB", Yii::app()->getBaseUrl(true) . "/register/att");

        $subscription_details = array(
            'description' => $summary,
            'initial_amount' => '0.00',
            'amount' => $membership->price,
            'period' => $subscriptionType->span == 'annual' ? 'Year' : 'Month',
            'frequency' => '1',
            'total_cycles' => $subscriptionType->span == 'annual' ? '1' : '12',
        );

        $paypal_subscription = new PayPal_Subscription($subscription_details);

        $this->render('/register/step5', array('atturl' => $atturl, 'paypalsub' => $paypal_subscription, 'summary' => $summary));
    }

    public function actionPaypalcancel() {
        $this->layout = '';
        $this->render("/register/paypalcancel");
    }

    public function actionPaypalnotify() {
        $this->layout = '';
        $this->render("/register/paypalnotify");
    }

    public function actionPaypalreturn() {
        $this->layout = '';

        PayPal_Digital_Goods_Configuration::username(Yii::app()->params->paypal['username']);
        PayPal_Digital_Goods_Configuration::password(Yii::app()->params->paypal['password']);
        PayPal_Digital_Goods_Configuration::signature(Yii::app()->params->paypal['signature']);
        PayPal_Digital_Goods_Configuration::return_url(Yii::app()->getBaseUrl(true) . '/register/paypalreturn');
        PayPal_Digital_Goods_Configuration::cancel_url(Yii::app()->getBaseUrl(true) . '/register/paypalcancel');
        PayPal_Digital_Goods_Configuration::notify_url(Yii::app()->getBaseUrl(true) . '/register/paypalnotify');
        PayPal_Digital_Goods_Configuration::business_name('myaNUMBER');
        if (!Yii::app()->params->paypal['sandbox']) {
            PayPal_Digital_Goods_Configuration::environment("live");
        }

        $userProfile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->session['user_id']));
        $membership = YumMembership::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->session['user_id']));
        $summary = 'Could not get summary description.';
        $subscriptionType = SubscriptionType::model()->findByPk($membership->subscription_type);
        $summary = $subscriptionType->description;
        if ($membership->promo) {
            $promo = Promo::model()->findByPk($membership->promo);
            $promoType = PromoType::model()->findByPk($promo->type);
            $summary = $promoType->description;
        }

        $subscription_details = array(
            'description' => $summary,
            'initial_amount' => '0.00',
            'amount' => $membership->price,
            'period' => $subscriptionType->span == 'annual' ? 'Year' : 'Month',
            'frequency' => '1',
            'total_cycles' => $subscriptionType->span == 'annual' ? '1' : '12',
        );

        $paypal_subscription = new PayPal_Subscription($subscription_details);
        $response = $paypal_subscription->start_subscription();
        $userProfile->step = 'six';
        
        $payment = Payment::model()->find('title=:title', array(':title' => 'Paypal'));
        $membership->payment_id = $payment->id;
        $membership->update();
        
        $userProfile->update();
        $this->render('/register/paypalreturn');
    }

    public function saveSecondaryContact($profileid, $phone) {
        if (strlen($phone) == 10) {
            $contact = new Contact();
            $contact->profile_id = $profileid;
            $contact->name = "Secondary";
            $contact->phone = $phone;
            $contact->type = 2;
            $contact->order = 0;
            $contact->validate();
            $contact->save();
        }
    }

    public function actionBraintree() {
        if (Yii::app()->params['braintree']['sandbox']) {
            Braintree_Configuration::environment('sandbox');
        } else {
            Braintree_Configuration::environment('production');
        }

        Braintree_Configuration::merchantId(Yii::app()->params['braintree']['merchantid']);
        Braintree_Configuration::publicKey(Yii::app()->params['braintree']['publickey']);
        Braintree_Configuration::privateKey(Yii::app()->params['braintree']['privatekey']);

        $model = new BraintreeForm();

        if (isset($_POST['BraintreeForm'])) {
            $model->attributes = $_POST['BraintreeForm'];
        }

        $tr_data = Braintree_TransparentRedirect::createCustomerData(array(
                    'redirectUrl' => Yii::app()->getBaseUrl(true) . '/register/braintreeredirect'
                ));

        $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->session['user_id']));

        $this->render('braintree', array('url' => Braintree_TransparentRedirect::url(), 'trData' => $tr_data, 'email' => $profile->email));
    }

    public function actionBraintreeredirect() {
        if (Yii::app()->params['braintree']['sandbox']) {
            Braintree_Configuration::environment('sandbox');
        } else {
            Braintree_Configuration::environment('production');
        }

        Braintree_Configuration::merchantId(Yii::app()->params['braintree']['merchantid']);
        Braintree_Configuration::publicKey(Yii::app()->params['braintree']['publickey']);
        Braintree_Configuration::privateKey(Yii::app()->params['braintree']['privatekey']);

        $queryString = $_SERVER['QUERY_STRING'];
        $result = Braintree_TransparentRedirect::confirm($queryString);
        if ($result->success) {
            $this->redirect('/register/braintreesubscription?id=' . $result->customer->id);
        } else {
            $this->redirect('/register/braintree');
        }
    }

    public function actionBraintreesubscription() {
        if (Yii::app()->params['braintree']['sandbox']) {
            Braintree_Configuration::environment('sandbox');
        } else {
            Braintree_Configuration::environment('production');
        }

        Braintree_Configuration::merchantId(Yii::app()->params['braintree']['merchantid']);
        Braintree_Configuration::publicKey(Yii::app()->params['braintree']['publickey']);
        Braintree_Configuration::privateKey(Yii::app()->params['braintree']['privatekey']);
        $membership = YumMembership::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->session['user_id']));
        $subscription = SubscriptionType::model()->findByPk($membership->subscription_type);
        $plan = '';
        if ($subscription) {
            $plan = $subscription->plan_name;
        }
        if ($membership->promo) {
            $promo = Promo::model()->findByPk($membership->promo);
            $promoType = PromoType::model()->findByPk($promo->type);
            $plan = $promoType->plan_name;
        }

        $customerId = $_GET['id'];
        $customer = Braintree_Customer::find($customerId);
        $paymentMethodToken = $customer->creditCards[0]->token;
        $result = Braintree_Subscription::create(array(
                    'paymentMethodToken' => $paymentMethodToken,
                    'planId' => $plan
                ));

        if ($result->success) {
            $membership->subscription_id = $result->subscription->id;
            $membership->customer_id = $customerId;
            $membership->update();
            $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->session['user_id']));
            $profile->step = 'six';
            
            $payment = Payment::model()->find('title=:title', array(':title' => 'Braintree'));
            $membership->payment_id = $payment->id;
            $membership->update();
            
            $profile->update();
            $this->redirect('/register/stepsix');
        } else {
            $this->redirect('/register/stepfive');
        }
    }

    public function actionStepsix() {
        $user = YumUser::model()->findByPk(Yii::app()->session['user_id']);
        if ($user) {
            if ($user->status) {
                $this->redirect("/me");
            }
            $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => $user->id));
            if ($this->sendRegistrationEmail($user)) {
                $this->render('/register/step6', array('email' => $profile->email));
            } else {
                $this->render('/register/step6');
            }
        } else {
            $this->redirect("/login");
        }
    }

    public function actionProvision() {
        $user = YumUser::model()->findByPk(Yii::app()->user->id);
        if ($user) {
            $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => $user->id));
            if ($profile->provisioned_number) {
                $this->redirect("/me");
            }
        }
        $this->render('provision');
    }

    public function actionGeneratenumbers($prefix) {
        $allNumbers = $this->getAvailableNumbersForPrefix($prefix);
        if (count($allNumbers) >= 3) {
            $numbers = array();
            $randomKeys = array_rand($allNumbers, 3);
            foreach ($randomKeys as $key) {
                $jNumber = array(
                    'display' => $allNumbers[$key]->displayNumber,
                    'number' => $allNumbers[$key]->number
                );
                array_push($numbers, $jNumber);
            }
            echo CJavaScript::jsonEncode($numbers);
        } else if (count($allNumbers) < 0 && count($allNumbers) < 3) {
            $numbers = array();
            for ($i = 0; $i < count($allNumbers); $i++) {
                $jNumber = array(
                    'display' => $allNumbers[$i]->displayNumber,
                    'number' => $allNumbers[$i]->number
                );
                array_push($numbers, $jNumber);
            }
            echo CJavaScript::jsonEncode($numbers);
        } else {
            echo CJavaScript::jsonEncode(false);
        }
        Yii::app()->end();
    }

    public function actionSavenumber($number) {

        $add = array(
            'type' => 'number',
            'number' => str_replace('+1', '', $number)
        );

        $tropo = new Tropo();
        $appsJson = $tropo->viewApplications(Yii::app()->params->tropo['username'], Yii::app()->params->tropo['password']);
        $apps = json_decode($appsJson);
        foreach ($apps as $app) {
            if ($app->name == Yii::app()->params->tropo['appname']) {
                $url = "https://api.tropo.com/v1/applications/" . $app->id . "/addresses";
                $ch = curl_init($url);
                $headers = array(
                    'Accept: application/json',
                    'Content-type: application/json'
                );
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_USERPWD, Yii::app()->params->tropo['username'] . ':' . Yii::app()->params->tropo['password']);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($add));
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                $info = curl_getinfo($ch);
                curl_close($ch);
                $resultData = json_decode($result);
                if ($resultData->href) {
                    $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->user->id));
                    $profile->provisioned_number = $number;
                    $profile->available_voice_minutes = 200;
                    $profile->available_sms_minutes = 100;
                    $profile->update();
                }

                echo $result;
            }
        }

        Yii::app()->end();
    }

    public function actionDone() {
        //$this->sendWelcomeText();

        $user = YumUser::model()->findByPk(Yii::app()->user->id);
        if ($user) {
            $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => $user->id));
            $contacts = Contact::model()->findAll('profile_id=:profile_id', array(':profile_id' => $profile->id));
            foreach ($contacts as $contact) {
                $url = "http://api.tropo.com/1.0/sessions?action=create&token=" . Yii::app()->params->tropo['messagetoken'];
                $options = array(
                    'number' => $contact->phone,
                    'msg' => $profile->name . ' has added you to their myaNUMBER. Visit myaNUMBER.com and contact ' . $profile->name . ' for details.',
                    'type' => 'welcomesignup',
                    'orig' => $profile->provisioned_number,
                );
                foreach ($options as $key => $value) {
                    $url .= "&$key=" . $value;
                }
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_exec($ch);
                curl_close($ch);
            }
        }

        $this->render('done');
    }

    public function sendWelcomeText() {
        $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->user->id));

        $msg = "Hi " . $profile->name . ". Thank you for signing up for myaNUMBER! As you add contacts we will send the SMS enabled numbers a welcome text with your name & our website for info.";

        $url = "http://api.tropo.com/1.0/sessions?action=create" .
                "&token=" . Yii::app()->params->tropo['admin_messagetoken'] .
                "&number=" . urlencode($profile->phone) .
                "&msg=" . urlencode($msg) .
                "&type=welcome" .
                '&orig=' . $profile->provisioned_number;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }

    public function actionAtt() {
        if (isset($_GET['success'])) {
            die("There was an error processing your subscription with at&t. Please go back and try again.");
        }
        if (isset($_GET['SubscriptionAuthCode'])) {
            $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => Yii::app()->session['user_id']));
            $profile->subscription_auth_code = $_GET['SubscriptionAuthCode'];
            $profile->validate();
            if (!$profile->hasErrors()) {
                $profile->step = 'six';
                $profile->update();
                $this->redirect('/register/stepsix');
            }
        }
    }

    public function getActivationUrl($user) {
        /**
         * Quick check for a enabled Registration Module
         */
        if (Yum::module('registration')) {
            $activationUrl = array('/register/activation');
            if (is_array($activationUrl) && isset($user->profile)) {
                $activationUrl = $activationUrl[0];
                $params['key'] = $user->activationKey;
                $params['email'] = $user->profile->email;
                return Yii::app()->controller->createAbsoluteUrl($activationUrl, $params);
            }
        }

        return Yum::t('Activation Url cannot be retrieved');
    }

    public function sendRegistrationEmail($user) {
        if (!isset($user->profile->email))
            throw new CException(Yum::t('Email is not set when trying to send Registration Email'));

        $activation_url = $this->getActivationUrl($user);
        $profile = YumProfile::model()->find('user_id=:user_id', array(':user_id' => $user->id));

        /*
          $body = strtr(
          'Hello, {username}. Please activate your account with this url: {activation_url}', array(
          '{username}' => $user->username,
          '{activation_url}' => $activation_url));
         */

        $body = strtr(
                "Hello {name}<br />
                <br />
                Your myaNUMBER account is ready for activation. If you did not make this request, just ignore this email. Otherwise, please visit the link below to activate your account:<br />
                <br />
                <a href='{activation_url}'>Confirm Registration & Choose Your myaNUMBER!</a><br />
                <br />
                By activating your account, you are now able to:<br />
                <br />
                - Place calls to your myaNUMBER and set up your support network.<br />
                - Conveniently locate a family member with your Locator map.<br />
                - Send & Receive SMS Messages with your myaNUMBER.<br />
                <br />
                myaNUMBER works hard to provide safe, secure and convenient tools for families of all shapes and sizes. We look forward to helping your family stay in touch.<br />
                <br />
                The myaNUMBER Team -<br />
                <br />
                John & Kyle<br />
                <br />
                TM and Copyright &#169; 2013<br />
                myaNUMBER Inc. | <a href='mailto:support@myanumber.com'>Support@myaNUMBER.com</a><br />
                <a href='http://myanumber.com/terms'>Terms Of Service</a> | <a href='http://myanumber.com/privacy'>Privacy Policy</a>", array('{name}' => $profile->name, '{activation_url}' => $activation_url)
        );

        $mail = array(
            'from' => Yii::app()->modules['registration']['registrationEmail'],
            'to' => $user->profile->email,
            'subject' => 'Activate your myaNUMBER account',
            'body' => $body,
        );

        $sent = YumMailer::send($mail);

        return $sent;
    }

    public function actionActivation($email, $key) {
        // If already logged in, we dont activate anymore
        if (!Yii::app()->user->isGuest) {
            Yii::app()->user->logout();
            //Yum::setFlash('You are already logged in, please log out to activate your account');
            //$this->redirect(Yii::app()->user->returnUrl);
        }

        // If everything is set properly, let the model handle the Validation
        // and do the Activation
        $status = YumUser::activate($email, $key);

        if ($status instanceof YumUser) {
            $login = new YumUserIdentity($status->username, false);
            $login->authenticate(true);
            Yii::app()->user->login($login);

            //$this->render(Yum::module('registration')->activationSuccessView);
            $this->redirect('/me');
        }
        else
            $this->render('/register/failure', array('error' => 'Could not activate account at this time. Please contact support'));
    }

    public function getAvailableNumbersForPrefix($prefix) {
        $url = "https://api.tropo.com/addresses?type=number&prefix=1$prefix&available=true";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $username = Yii::app()->params->tropo['username'];
        $password = Yii::app()->params->tropo['password'];
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_HTTPGET, true);
        curl_setopt($ch, CURLOPT_POST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        if ($info['http_code'] >= 200 && $info['http_code'] < 300) {
            $data = json_decode($result);
            return $data;
        } else {
            return false;
        }
    }

}

?>
