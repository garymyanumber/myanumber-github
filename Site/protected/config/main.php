<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');
// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'Myanumber',
    // preloading 'log' component
    'preload' => array('log'),
    // autoloading model and component classes
    'import' => array(
        'application.modules.user.models.*',
        'application.models.*',
        'application.components.*',
        'application.components.att.*'
    ),
    'theme' => 'myanumber',
    'modules' => array(
        // uncomment the following to enable the Gii tool
        /*
          'gii'=>array(
          'class'=>'system.gii.GiiModule',
          'password'=>'Enter Your Password Here',
          // If removed, Gii defaults to localhost only. Edit carefully to taste.
          'ipFilters'=>array('127.0.0.1','::1'),
          ),
         */
        'user' => array(
            'debug' => false,
            'facebookConfig' => array(
                'userTable' => 'user',
                'translationTable' => 'translation',
                'appId' => '134417903411819',
                'secret' => 'cfb9f8ebbab6d24d14fb3eaf18b6adcb',
                'domain' => 'http://myanumber.hazardbrick.com',
                'status' => true,
                'xfbml' => true,
                'cookie' => true,
                'lang' => 'en_US'
            ),
            'loginType' => 9,
            'passwordRequirements' => array(
                'minLowerCase' => 0,
                'minUpperCase' => 0,
                'minDigits' => 0,
                'maxLen' => 128
            ),
            'usernameRequirements' => array(
                'maxLen' => '255',
                'minLen' => '6',
                'match' => '*.*',
                'dontMatchMessage' => 'Username does not match the required pattern.'
            ),
            'mailer' => 'PHPMailer',
            'phpmailer' => array(
                'transport' => 'smtp',
                'properties' => array(
                    'Host' => 'smtp.sendgrid.net',
                    'SMTPAuth' => true,
                    'Username' => 'myanumber',
                    'Password' => 'keepspinning'
                ),
                'html' => true,
                'msgOptions' => array(
                    'fromName' => 'myaNUMBER Registration (no-reply)',
                    'toName' => 'Myanumber User'
                )
            )
        ),
        'usergroup' => array(
            'usergroupTable' => 'usergroup',
            'usergroupMessageTable' => 'user_group_message',
        ),
        'membership' => array(
            'membershipTable' => 'membership',
            'paymentTable' => 'payment'
        ),
        'friendship' => array(
            'friendshipTable' => 'friendship',
        ),
        'profile' => array(
            'privacySettingTable' => 'privacysetting',
            'profileFieldTable' => 'profile_field',
            'profileTable' => 'profile',
            'profileCommentTable' => 'profile_comment',
            'profileVisitTable' => 'profile_visit',
        ),
        'role' => array(
            'roleTable' => 'role',
            'userRoleTable' => 'user_role',
            'actionTable' => 'action',
            'permissionTable' => 'permission',
        ),
        'message' => array(
            'messageTable' => 'message',
            'adminEmail' => 'support@myanumber.com'
        ),
        'registration' => array(
            'enableRegistration' => true,
            'registrationEmail' => 'register@myanumber.com',
            'enableCaptcha' => false,
            'loginAfterSuccessfulActivation' => true
        )
    ),
    // application components
    'components' => array(
        'user' => array(
            'class' => 'application.modules.user.components.YumWebUser',
            'allowAutoLogin' => true,
            'loginUrl' => array('/login/')
        ),
        // uncomment the following to enable URLs in path-format
        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false,
            'rules' => array(
                '' => 'site/index',
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ),
        ),
        'db' => array(
            'connectionString' => 'mysql:host=localhost;dbname=myanumber',
            'emulatePrepare' => true,
            'username' => 'myanumber',
            'password' => 'myanumber',
            'charset' => 'utf8',
            'tablePrefix' => ''
        ),
        /**/
        'errorHandler' => array(
            // use 'site/error' action to display errors
            'errorAction' => 'site/error',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning, info',
                ),
            // uncomment the following to show log messages on web pages
            /*
              array(
              'class'=>'CWebLogRoute',
              ),
             */
            ),
        ),
        'cache' => array(
            'class' => 'system.caching.CDummyCache'
        )
    ),
    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(
        // this is used in contact page
        'adminEmail' => 'support@myanumber.com',
        'tropo' => array(
            'username' => 'myanumberdev',
            'password' => 'fog.pretzel18',
            'appname' => 'Myanumber Test',
            'voicetoken' => '1695653fc7ac064aa50b58a289c6f79c0616d6e31c6b778e9cadf9bd02d8d5165fb9d9e02028bc6d76847477',
            'messagetoken' => '1695a81fad241841876cae8aa4487fe683fbe9d3fd426081457754f5f60bbec14176e63f5a3507a26fe36677',
            'sip_address' => 'sip:9996184203@sip.tropo.com'
        ),
        'att' => array(
            'client_id' => 'c09a3ab78e45e8bde48df920805b4a76',
            'secret_id' => '30eb7586675ec913',
        ),
        'attfoundry' => array(
            'client_id' => '5d9ea82d71767367d03a2c14c4a857ec',
            'secret_id' => '7514aecd48602e1a'
        ),
        'paypal' => array(
            'sandbox' => true,
            'username' => 'merchant_api1.hazardbrick.com',
            'password' => '1368653816',
            'signature' => 'AvZD91B6xNcYfAo6E63utM6s2XhlALHheM3W.QVjf2GdoQOs6o0u3gwj'
        ),
        'braintree' => array(
            'sandbox' => true,
            'merchantid' => 'wzwz5kqmtf38fqp6',
            'publickey' => 'zvjbfrpms75rbf8w',
            'privatekey' => 'fa72cc6bccd76d194e517417a7cfa88f',
            'clientside_encryptionkey' => 'MIIBCgKCAQEAnOplOruw6vUJEacWbjhBUjRstEXL8IPxQgw18ylkGe2JbkhELIH5Kzp8wWwORwYgBObofx01n2cCbqA8RPuuGyFSYE/XNaXhWMvJ6HZzCmANk5DgLRDgkmVbdxyMb1PFxh0EEk+RJeqDxidqkBMnPESzc+55Qr6FSg49oZmeX/JQc20DiqPSc+e4GyYGDyEGmflJlaFg2lECxl5Ft4BcYdKKJjD+tcRrNn0HQ8R5h6CqYffan3VEnpNf+ymaIsb0FOil9cqIS5jQW/khgcr737rQwVvTAom9aN1XXFQReJJA9S1G/FeYFkvhKDB4ab1NRWLCf1VLj/u/1WctGZ1qKQIDAQAB'
        )
    ),
);