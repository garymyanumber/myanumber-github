<?php echo CHtml::form(); ?>
<div class="register">
    <h1>Select Plan</h1>
    <div class="register-top"></div>
    <div class="register-content">
        <div id="register-monthly"></div>
        <p class="billing">
            <span class="bold">200</span> Monthly Voice Minutes<br />
            <span class="bold">100</span> Monthly SMS Messages<br />
            (Including Rollover of Minutes and SMS)<br />
            <span class="bold">Family Locator</span><br />
            <span class="bold">In-Website Calling and SMS</span>
        </p>
        <div class="clear"></div>
        <div id="register-annually"></div>
        <p class="billing" style="margin-top: 20px;">
            Save 25&#37; off monthly plan<br />
            RECEIVE ALL OF THE ABOVE +<br />
            <span class="bold">Stickers</span> with your myaNUMBER<br />
            <span class="bold">Bag Tags</span> with your myaNUMBER<br />
            Save $29.89 per year
        </p>
        <div class="clear"></div>
        <h3 style="margin: 20px 0; padding: 0; text-align: center">Or Use A Promo Code</h3>
        <?php echo CHtml::textField('promo', '', array('class' => 'text', 'placeholder' => 'Promo Code')); ?>
    </div>
    <div class="register-bottom"></div>
    <?php echo CHtml::submitButton(''); ?>
    <div class="register-errors hidden">
        <?php echo CHtml::errorSummary($model); ?>
    </div>
</div>
<?php echo CHtml::hiddenField('type'); ?>
<?php echo CHtml::endForm(); ?>
<script type="text/javascript">
    $(function() {
        if($(".register-errors").children().length > 0) {
            myanumber.noty.error($(".register-errors").html());
        }
        $("#register-monthly").click(function(e) {
            e.preventDefault();
            $("#register-annually").removeClass("selected");
            $(this).addClass("selected");
            $('#type').val('monthly');
        });
        $("#register-annually").click(function(e) {
            e.preventDefault();
            $("#register-monthly").removeClass("selected");
            $(this).addClass("selected");
            $('#type').val('annual');
        });
    });
</script>