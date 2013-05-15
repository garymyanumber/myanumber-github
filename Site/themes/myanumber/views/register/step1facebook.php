<?php echo CHtml::form('/register/registerfacebook'); ?>
<div class="register">
    <h1>Create Account</h1>
    <div class="register-top"></div>
    <div class="register-content">
        <p>Hello <?php echo $name; ?>, to complete your registration please enter your phone number.</p>
        <h3>Phone Number</h3>
        <?php echo CHtml::activeTextField($model, 'areaCode', array('class' => 'number first', 'maxlength' => '3', 'placeholder' => '121')); ?>
        <?php echo CHtml::activeTextField($model, 'prefix', array('class' => 'number', 'maxlength' => '3', 'placeholder' => '555')); ?>
        <?php echo CHtml::activeTextField($model, 'line', array('class' => 'number', 'maxlength' => '4', 'placeholder' => '1212')); ?>
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