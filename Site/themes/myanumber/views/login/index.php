<div class="login">
    <div class="big-logo"></div>
    <?php echo CHtml::beginForm(); ?>
    <div class="login-form">
        <div class="login-form-top"></div>
        <div class="login-form-content">
            <?php if (isset($fblogin)) { ?>
                <div class="facebook-login"></div>
                <script type="text/javascript">
                    $(function() {
                        $(".facebook-login").click(function(e) {
                            e.preventDefault();
                            window.open('<?php echo $fblogin; ?>', 'facebook_window', 'width=1000,height=600,menubar=0,resizable=0,scrollbars=0,status=0,toolbar=0');
                        });
                    });
                </script>
                <h2>or</h2>
            <?php } ?>
            <h3>Email</h3>
            <?php echo CHtml::activeTextField($model, 'username', array('placeholder' => 'support@myanumber.com', 'autocomplete' => 'off')); ?>
            <h3>Password</h3>
            <?php echo CHtml::activePasswordField($model, 'password', array('placeholder' => '********', 'autocomplete' => 'off')); ?>
        </div>
        <div class="login-form-bottom"></div>
    </div>
    <input type="Submit" value="" />
    <div class="forgot-password">
        <a href="/login/lostpassword">Forgot Password?</a>
    </div>
    <div class="login-errors hidden">
        <?php echo CHtml::errorSummary($model); ?>
    </div>
    <?php echo CHtml::endForm(); ?>
</div>
<script type="text/javascript">
    $(function() {
        if($(".login-errors").children().length > 0) {
            myanumber.noty.error($(".login-errors").html());
        }
    });
</script>