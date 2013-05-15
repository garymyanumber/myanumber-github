<div class="register">
    <h1>Add Contacts</h1>
    <?php echo CHtml::form(); ?>
    <div class="register-top"></div>
    <div class="register-content">
        <h3>Contact #<?php echo $model->numberOfContacts + 1; ?> (Up to 5)</h3>
        <p>Note: You can edit your contact details in settings any time.</p>
        <h3>Nickname</h3> 
        <?php echo CHtml::activeTextField($model, 'name', array('class' => 'text', 'placeholder' => 'Susan Smith', 'autocomplete' => 'off')); ?>
        <h3>Email</h3>
        <?php echo CHtml::activeTextField($model, 'email', array('class' => 'text', 'placeholder' => 'yourname@mail.com', 'autocomplete' => 'off')); ?>
        <h3>Phone Number</h3>
        <?php echo CHtml::activeTextField($model, 'areaCode', array('class' => 'number first', 'maxlength' => '3', 'placeholder' => '121', 'autocomplete' => 'off')); ?>
        <?php echo CHtml::activeTextField($model, 'prefix', array('class' => 'number', 'maxlength' => '3', 'placeholder' => '555', 'autocomplete' => 'off')); ?>
        <?php echo CHtml::activeTextField($model, 'line', array('class' => 'number', 'maxlength' => '4', 'placeholder' => '1212', 'autocomplete' => 'off')); ?>
        <br />
        <p class='bold white'><?php echo CHtml::activeCheckBox($model, 'enable_sms', array('class' => 'checkbox', 'autocomplete' => 'off')); ?><label for="ytRegisterContactForm_enable_sms"><span></span>Enable SMS messaging to this number</label></p>
        <script type="text/javascript">
        $(function() {
            $("p label span").click(function() {
                if($("p input[type='checkbox']").attr('checked')) {
                    $("p input[type='checkbox']").prop('checked', false);
                }
                else {
                    $("p input[type='checkbox']").prop('checked', true);
                }
            });
        });
        </script>
    </div>
    <div class="register-bottom"></div>
    <div class="register-errors hidden">
        <?php echo CHtml::errorSummary($model); ?>
    </div>
</div>
<div class="register-buttons-center">
    <?php echo CHtml::submitButton('', array('name' => 'add', 'class' => 'save add-line-button')); ?>
    <?php echo CHtml::submitButton('All done adding contacts', array('name' => 'done', 'class' => 'save done-button')); ?>
</div>
<div class="clear"></div>
<?php echo CHtml::endForm(); ?>
<script type="text/javascript">
    $(function() {
        if($(".register-errors").children().length > 0) {
            myanumber.noty.error($(".register-errors").html());
        }
    });
</script>