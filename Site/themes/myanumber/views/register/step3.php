<div class="register">
    <h1>Add Secondary Lines</h1>    
    <?php echo CHtml::form(); ?>
    <div class="register-top"></div>
    <div class="register-content">
        <h3>Secondary Back Up Call List</h3>
        <p>You can add a &quot;safety net&quot; of secondary lines that will all ring in the event that none of your contacts answer a call from myaNUMBER.</p>
        <h3>Phone Number</h3>
        <span class="plus">+1</span> <?php echo CHtml::activeTextField($model, 'areaCode1', array('class' => 'number', 'maxlength' => 3, 'placeholder' => '121')); ?>
        <?php echo CHtml::activeTextField($model, 'prefix1', array('class' => 'number', 'maxlength' => 3, 'placeholder' => '555')); ?>
        <?php echo CHtml::activeTextField($model, 'line1', array('class' => 'number', 'maxlength' => 4, 'placeholder' => '1212')); ?>
        <h3>Phone Number</h3>
        <span class="plus">+1</span> <?php echo CHtml::activeTextField($model, 'areaCode2', array('class' => 'number', 'maxlength' => 3, 'placeholder' => '121')); ?>
        <?php echo CHtml::activeTextField($model, 'prefix2', array('class' => 'number', 'maxlength' => 3, 'placeholder' => '555')); ?>
        <?php echo CHtml::activeTextField($model, 'line2', array('class' => 'number', 'maxlength' => 4, 'placeholder' => '1212')); ?>
        <h3>Phone Number</h3>
        <span class="plus">+1</span> <?php echo CHtml::activeTextField($model, 'areaCode3', array('class' => 'number', 'maxlength' => 3, 'placeholder' => '121')); ?>
        <?php echo CHtml::activeTextField($model, 'prefix3', array('class' => 'number', 'maxlength' => 3, 'placeholder' => '555')); ?>
        <?php echo CHtml::activeTextField($model, 'line3', array('class' => 'number', 'maxlength' => 4, 'placeholder' => '1212')); ?>
    </div>
    <div class="register-bottom"></div>
    <div class="register-errors hidden">
        <?php echo CHtml::errorSummary($model); ?>
    </div>
</div>
<div class="register-buttons-center">
    <?php echo CHtml::submitButton('', array('class' => 'save secondary-numbers')); ?>
    <a href="/register/stepfour">Skip Secondary Lines</a>
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