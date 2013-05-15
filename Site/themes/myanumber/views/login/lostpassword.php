<? echo CHtml::beginForm(); ?>
<div class="register area-code">
    <h1>Recover Password</h1>
    <div class="register-top"></div>
    <div class="register-content">
        <?
        if (Yum::hasFlash()) {
            echo '<div class="success">';
            echo '<p>' . Yum::getFlash() . '</p>';
            echo '</div>';
        } else {
            ?>

            <div class="form">
                

    <p><? echo CHtml::errorSummary($form); ?></p>

                <div class="row">
                    <h3>Email</h3>
    <? echo CHtml::activeTextField($form, 'login_or_email', array('class' => 'text')) ?>
                    <p class="hint"><? echo Yum::t("Please enter your user name or email address."); ?></p>
                </div>

                <div class="row submit">
    
                </div>

            
            </div><!-- form -->
<? } ?>
    </div>
    <div class="register-bottom"></div>
    
    <? 
        echo CHtml::submitButton('');
     ?>
</div>
<? echo CHtml::endForm(); ?>