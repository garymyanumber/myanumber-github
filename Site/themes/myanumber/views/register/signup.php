<?php echo CHtml::form('/register/signup'); ?>
<div class="register">
    <h1>Signup</h1>
    <div class="register-top"></div>
    <div class="register-content">
        <h3>Email</h3>
        <input type="text" class="text" name="email" />
        <p style="font-size: 14px; font-weight: bold">myaNUMBER is currently invite only. Sign up here for an invite to the public release or enter your sign up code and press submit.</p>
    </div>
    <div class="register-bottom"></div>
    <?php echo CHtml::submitButton(''); ?>
</div>
<?php echo CHtml::endForm(); ?>