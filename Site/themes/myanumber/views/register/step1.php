<?php echo CHtml::form('/register/stepone'); ?>
<div class="register">
    <h1>Create Account</h1>
    <div class="register-top"></div>
    <div class="register-content">
        <div class="facebook-login"></div>
        <script type="text/javascript">
            $(function() {
                $(".facebook-login").click(function(e) {
                    e.preventDefault();
                    window.open('<?php echo $model->facebookLogin; ?>', 'facebook_window', 'width=1000,height=600,menubar=0,resizable=0,scrollbars=0,status=0,toolbar=0');
                });
            });
        </script>
        <br />
        <p class="white heavy">By connecting with Facebook you agree to our <a class="teal" target="_blank" href="/terms">Terms of Service</a></p>
        <p class="center white heavy">or</p>
        <h3>Name</h3>
        <?php echo CHtml::activeTextField($model, 'name', array('class' => 'text', 'placeholder' => 'Susan Smith', 'autocomplete' => 'off')); ?>
        <h3>Email</h3>
        <?php echo CHtml::activeTextField($model, 'email', array('class' => 'text', 'placeholder' => 'yourname@mail.com', 'autocomplete' => 'off')); ?>
        <h3>Phone Number</h3>
        <?php echo CHtml::activeTextField($model, 'areaCode', array('class' => 'number first', 'maxlength' => '3', 'placeholder' => '121', 'autocomplete' => 'off')); ?>
        <?php echo CHtml::activeTextField($model, 'prefix', array('class' => 'number', 'maxlength' => '3', 'placeholder' => '555', 'autocomplete' => 'off')); ?>
        <?php echo CHtml::activeTextField($model, 'line', array('class' => 'number', 'maxlength' => '4', 'placeholder' => '1212', 'autocomplete' => 'off')); ?>
        <h3>Password</h3>
        <?php echo CHtml::activePasswordField($model, 'password', array('class' => 'text', 'autocomplete' => 'off')); ?>
        <h3>Confirm Password</h3>
        <?php echo CHtml::activePasswordField($model, 'confirmPassword', array('class' => 'text', 'autocomplete' => 'off')); ?>
        <p class="white heavy">By clicking Submit, you agree to our <a class="teal" target="_blank" href="/terms">Terms of Service</a></p>
    </div>
    <div class="register-bottom"></div>
    <div class="register-errors hidden">
        <?php echo CHtml::errorSummary($model); ?>
    </div>
    <?php echo CHtml::submitButton(''); ?>
</div>
<?php echo CHtml::endForm(); ?>
<script type="text/javascript">
    $(function() {
        if($(".register-errors").children().length > 0) {
            myanumber.noty.error($(".register-errors").html());
        }
    });
</script>