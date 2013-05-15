<? echo CHtml::beginForm(); ?>
<div class="register">
    <h1>Set Password</h1>
    <div class="register-top"></div>
    <div class="register-content">
        <div class="form">

            <? echo Yum::requiredFieldNote(); ?>
            <? echo CHtml::errorSummary($form); ?>

            <div class="row">
                <h3><? echo CHtml::activeLabelEx($form, 'password'); ?></h3>
                <? echo CHtml::activePasswordField($form, 'password', array('class' => 'text')); ?>
            </div>

            <div class="row">
                <h3><? echo CHtml::activeLabelEx($form, 'verifyPassword'); ?></h3>
                <? echo CHtml::activePasswordField($form, 'verifyPassword', array('class' => 'text')); ?>
            </div>

        </div><!-- form -->

    </div>
    <div class="register-bottom"></div>

    <div class="row submit">
        <? echo CHtml::submitButton(Yum::t("")); ?>
    </div>
</div>
<? echo CHtml::endForm(); ?>