<?php echo CHtml::form(); ?>
<div class="register">
    <h1>Choose your myaNUMBER</h1>
    <div class="register-top"></div>
    <div class="register-content">
        <div class="tab-content">
            <h3>Submit Area Code</h3>
            <?php echo CHtml::activeTextField($model, 'prefix', array('class' => 'number first', 'maxlength' => '3', 'placeholder' => '123')); ?>
            <?php if (isset($numbers)) {
                ?>
                <h3>Select your myaNUMBER below</h3>
                <?php
                foreach ($numbers as $key => $value) {
                    ?>
                    <p class="select-number" key="<?php echo $key; ?>"><?php echo $value; ?></p>
                    <?php
                }
                echo CHtml::activeHiddenField($model, 'number');
            }
            ?>
        </div>
    </div>
    <div class="register-bottom"></div>
    <div class="register-errors hidden">
        <?php echo CHtml::errorSummary($model); ?>
    </div>
    <?php
    if (!isset($numbers)) {
        echo CHtml::submitButton('');
    }
    ?>
</div>
<?php echo CHtml::endForm(); ?>
<script type="text/javascript">
    $(function() {
        $(".register input[type='submit']").click(function(e) {
            e.preventDefault();
            $(this).hide();
            $(".register").append("<div class='loading' style='margin: 0 auto'></div>");
            $("form").submit();
        });
        $(".select-number").click(function(e) {
            e.preventDefault();
            var form = $("form");
            var number = $(this).attr("key");
            $("#RegisterFormTwo_number", form).val(number);
            if($("#RegisterFormTwo_number", form).val()) {
                $(form).submit();
            }
        });
        if($(".register-errors").children().length > 0) {
            myanumber.noty.error($(".register-errors").html());
        }
    });
</script>