<?
if(!isset($model)) 
	$model = new YumUserLogin();

$module = Yum::module();

$this->pageTitle = Yum::t('Login');
if(isset($this->title))
$this->title = Yum::t('Login');
$this->breadcrumbs=array(Yum::t('Login'));

Yum::renderFlash();
?>

<?php

if($module->loginType & UserModule::LOGIN_BY_FACEBOOK)
{
    $fbconfig = Yum::module()->facebookConfig;
    if(isset($fbconfig))
    {
        Yii::import('application.modules.user.vendors.facebook.*');
        require_once('Facebook.php');
        $facebook = new Facebook($fbconfig);
        if(!isset(Yii::app()->session['fb_state']))
            Yii::app()->session['fb_state'] = md5(uniqid(rand(), TRUE));
        $loginUrl = $facebook->getLoginUrl(array(
                'scope' => 'email',
                'redirect_uri' => YUM::module()->facebookConfig['domain'] . "/user/auth/redirect"
            ));
        echo "<a href='" . $loginUrl. "'>Login with Facebook</a><br />";
    }
}

?>

<div class="form">
<p>
<? 
echo Yum::t(
		'Please fill out the following form with your login credentials:'); ?>
</p>

<? echo CHtml::beginForm(array('//user/auth/login'));  ?>

<?
if(isset($_GET['action']))
	echo CHtml::hiddenField('returnUrl', urldecode($_GET['action']));
?>

<? echo CHtml::errorSummary($model); ?>
	
	<div class="row">
		<? 
		if($module->loginType & UserModule::LOGIN_BY_USERNAME 
				|| $module->loginType & UserModule::LOGIN_BY_LDAP)
		echo CHtml::activeLabelEx($model,'username'); 
		if($module->loginType & UserModule::LOGIN_BY_EMAIL)
			printf ('<label for="YumUserLogin_username">%s <span class="required">*</span></label>', Yum::t('E-Mail address')); 
		if($module->loginType & UserModule::LOGIN_BY_OPENID)
			printf ('<label for="YumUserLogin_username">%s <span class="required">*</span></label>', Yum::t('OpenID username'));  ?>

		<? echo CHtml::activeTextField($model,'username') ?>
	</div>
	
	<div class="row">
		<? echo CHtml::activeLabelEx($model,'password'); ?>
		<? echo CHtml::activePasswordField($model,'password');
		if($module->loginType & UserModule::LOGIN_BY_OPENID)
			echo '<br />'. Yum::t('When logging in with OpenID, password can be omitted');
 ?>
		
	</div>
	
	<div class="row">
	<p class="hint">
	<? 
	if(Yum::hasModule('registration') && Yum::module('registration')->enableRegistration)
	echo CHtml::link(Yum::t("Registration"),
			Yum::module('registration')->registrationUrl);
	if(Yum::hasModule('registration') 
			&& Yum::module('registration')->enableRegistration
			&& Yum::module('registration')->enableRecovery)
	echo ' | ';
	if(Yum::hasModule('registration') 
			&& Yum::module('registration')->enableRecovery) 
	echo CHtml::link(Yum::t("Lost password?"),
			Yum::module('registration')->recoveryUrl);
	?>
</p>
	</div>

<div class="row rememberMe">
<? echo CHtml::activeCheckBox($model,'rememberMe', array('style' => 'display: inline;')); ?>
<? echo CHtml::activeLabelEx($model,'rememberMe', array('style' => 'display: inline;')); ?>
</div>

<div class="row submit">
<? echo CHtml::submitButton(Yum::t('Login')); ?>
</div>

<? echo CHtml::endForm(); ?>
</div><!-- form -->

<?
$form = new CForm(array(
			'elements'=>array(
				'username'=>array(
					'type'=>'text',
					'maxlength'=>32,
					),
				'password'=>array(
					'type'=>'password',
					'maxlength'=>32,
					),
				'rememberMe'=>array(
					'type'=>'checkbox',
					)
				),

			'buttons'=>array(
				'login'=>array(
					'type'=>'submit',
					'label'=>'Login',
					),
				),
			), $model);
?>

