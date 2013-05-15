<h2 style="color: #FFF"> <? echo Yum::t('Your account has been activated'); ?> </h2>

<p style="color: #FFF">Please wait while you are redirected.</p>

<script type="text/javascript">
    setTimeout("redirectToLogin()", 3000);
    function redirectToLogin() {
        location.href = '<?php echo Yii::app()->getBaseUrl(true); ?>/login';
    }
</script>