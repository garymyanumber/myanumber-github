<div class="register area-code">
    <h1>Choose Desired Area Code</h1>
    <div class="register-top"></div>
    <div class="register-content">
        <p>Note: Your myaNUMBER can begin with any area code you choose.</p>
        <h3>Submit Area Code</h3>
        <input type="text" class="number first" maxlength="3" placeholder="121" />
    </div>
    <div class="register-bottom"></div>
    <input type="submit" value="" />
</div>
<div class="register choose-number hidden">
    <h1>Choose Your myaNUMBER</h1>
    <div class="register-top"></div>
    <div class="register-content">
        <h3>Please choose a (desired prefix) number from the list below.</h3>
        <div class="loading" style="margin: 0 auto"></div>
    </div>
    <div class="register-bottom"></div>
    <input type="submit" value="" />
</div>
<div class="register-learn-more hidden">
    <div class="register-learn-more-logo"></div>
    <div class="learn-more-content">
        It may take us up to 2 minutes to secure the phone number you chose. While we work on that, please take a few moments to learn more about some of the key features you have access to with your myaNUMBER.
        <div class="lower">
            Get Started
            <div class="small-right-arrow"></div>
        </div>
    </div>
    <div class="learn-more-content hidden">
        Use the Locator to see the geographic location of any mobile phone. With a simple text message verification your contacts can offer their location. We track that location on a family map to help you coordinate better than ever before. 
        <div class="lower">
            <div class="learn-image-1"></div>
            Continue
            <div class="small-right-arrow"></div>
        </div>
    </div>
    <div class="learn-more-content hidden">
        Your Contacts are ordered in a Calling List that you can modify at any time. When myaNUMBER is dialed, your Calling List will be rung in this order. You are also able to kick off family conference calls and dial out to individuals right from your web browser!
        <div class="lower">
            <div class="learn-image-2"></div>
            Continue
            <div class="small-right-arrow"></div>
        </div>
    </div>
    <div class="learn-more-content hidden">
        If you send a text message to your myaNUMBER, it will create a group text for all of your registered contacts who have mobile phones. Their reply will be sent to everyone creating a convenient family group chat.
        <div class="lower">
            <div class="learn-image-3"></div>
            Continue
            <div class="small-right-arrow"></div>
        </div>
    </div>
    <div class="learn-more-pager">
        <div class="lpage selected"></div>
        <div class="lpage"></div>
        <div class="lpage"></div>
        <div class="lpage"></div>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        var lcount = 0;
        $(".learn-more-content .lower").click(function() {
            if(lcount != 3) {
                $(this).parent().hide();
                $(this).parent().next().show();
                $(".learn-more-pager .selected").removeClass("selected").next().addClass("selected");
                lcount++;
            }
            else {
                $(".register-learn-more").hide();
                $(".register.choose-number").show();
            }
        });
        
        $(".register.area-code input[type='submit']").click(function(e) {
            e.preventDefault();
            var prefix = $(".number").val();
            $(".register.area-code").hide();
            $(".register.choose-number").show();
            $.ajax({
                type: "GET",
                url: "/register/generatenumbers",
                data: {prefix: prefix},
                dataType: "json",
                success: function(data) {
                    if(data) {
                        $(".register.choose-number .loading").hide();
                        for(var i = 0; i < data.length; i++) {
                            var str = "<p class='select-number' key='" + data[i].number + "'>" + data[i].display + "</p>";
                            $(".register.choose-number .register-content").append(str);
                            $(".register.choose-number .select-number").click(function() {
                                $(".register.choose-number p").removeClass("nselected");
                                $(this).addClass("nselected");
                            });
                        }
                        $(".register.choose-number input[type='submit']").click(function(e) {
                            e.preventDefault();
                            var number = $(".register.choose-number p.nselected").attr("key");
                            if(number != undefined) {
                                $(".register.choose-number input[type='submit']").hide();
                                $(".register.choose-number").append("<div class='loading' style='margin: 0 auto'></div>");
                                $(".register.choose-number").append("<p class='please-wait-text' style='margin: 0 auto; text-align: center'>Please wait. It may take up to two minutes to secure your number.</p>");
                                $(".register.choose-number").hide();
                                $(".register-learn-more").show();
                                $.ajax({
                                    type: "GET",
                                    url: "/register/savenumber",
                                    data: {number: number},
                                    dataType: "json",
                                    success: function(success) {
                                        if(success) {
                                            if(success.href) {
                                                if($(".register-learn-more").is(":hidden")) {
                                                    location.href = '/register/done';
                                                }
                                                else {
                                                    $(".learn-image-3").parent().unbind('click');
                                                    $(".learn-image-3").parent().click(function() {
                                                        location.href = '/register/done';
                                                    });    
                                                }
                                            }
                                            else {
                                                myanumber.noty.info("Sorry, that number has become unavailabe. Please choose a different number.");
                                                $(".register.choose-number .loading").remove();
                                                $(".register.choose-number .please-wait-text").remove();
                                                $(".register.choose-number .nselected").remove();
                                                $(".register.choose-number input[type='submit']").show();
                                            }
                                        }
                                        else {
                                            myanumber.noty.info("Sorry, that number has become unavailabe. Please choose a different number.");
                                            $(".register.choose-number .loading").remove();
                                            $(".register.choose-number .please-wait-text").remove();
                                            $(".register.choose-number .nselected").remove();
                                            $(".register.choose-number input[type='submit']").show();
                                        }
                                    }
                            });
                    }
                    else {
                        myanumber.noty.error("You must select a number first.");
                    }
                });
            }
            else {
                $(".register-learn-more").hide();
                $(".register.choose-number").hide();
                $(".register.area-code").show();
                myanumber.noty.error("We could not find any numbers for that prefix. Please try again.");
            }
    }
});
});
});
</script>