<div id="inner-settings">
    <div id="settings-menu">
        <div id="upgrade-button" class="selected"></div>
        <div id="support-button"></div>
        <div id="contacts-button"></div>
        <div class="clear"></div>
    </div>
    <h1>Settings</h1>
    <div id="settings-plan-usage">
        <h2>Current Plan Usage</h2>
        <!--<p>Dec. 5th - Jan. 6th</p>-->
        <div class="clear"></div>
        <div id="settings-current-plan">
            <div id="current-subscription">
                <h3>Monthly Subscription</h3>
                <div id="subscription-price">
                    &#36;<?php echo $membership->price; ?>
                </div>
                <p>Up to 200 minutes<br />Includes up to 100 SMS</p>
            </div>
            <div id="voice-usage-box">
                <h3>Voice Usage</h3><span id="voice-usage"><?php echo $profile->available_voice_minutes; ?>/200</span><!--<p>Plan Refill: <span class="bold" id="voice-refill">1</span></p>-->
            </div>
            <div id="sms-usage-box">
                <h3>SMS Usage</h3><span id="sms-usage"><?php echo $profile->available_sms_minutes; ?>/100</span><!--<p>Plan Refill: <span class="bold" id="sms-refill">2</span></p>-->
            </div>
            <p>Account Refills: SMS Messages: +100: <span class="bold">&#36;1.99</span> / Voice Minutes +200: <span class="bold">&#36;4.99</span></p>
        </div>
        <div class="clear"></div>
        <div id="billing">
            <h2>Billing Preferences</h2>
            <p>Update the funding source attached to your account. <a id="deactivate-account" style="float: right; color: #666; font-style: italic" href="#">deactivate account</a></p>
            <div id="paypal">
                <h2>Subscribe with <span class="bold">Paypal</span></h2>
                <div id="paypal-button"></div>
            </div>
            <div id="att">
                <h2>Subscribe with <span class="bold">AT&amp;T</span></h2>
                <div id="att-button"></div>
                <p>Coming soon users can add their subscription directly to their AT&amp;T bill. Learn more about it in myaNUMBER support.</p>
            </div>
            <div id="or"></div>
            <div id="credit-card">
                <div id="card-types">
                    <p>card type</p>
                    <div id="visa"></div>
                    <div id="mastercard"></div>
                    <div id="amex"></div>
                    <div id="discover"></div>
                </div>
                <div class="clear"></div>
                <div id="numbers">
                    <div id="card-number">
                        credit card number
                        <input type="text" />
                    </div>
                    <div id="cvv-code">
                        cvv-code
                        <input type="text" />
                    </div>
                    <div id="billing-zip">
                        billing ZIP
                        <input type="text" />
                    </div>
                </div>
                <div id="expiration">
                    <div class="setting-label">experation date</div>
                    <select name="month" class="default">
                        <option selected="selected" value="">month</option>
                        <?php
                        for ($i = 1; $i <= 12; $i++) {
                            echo "<option value='" . $i . "'>" . $i . "</option>";
                        }
                        ?>
                    </select>
                    <select name="year" class="default">
                        <option selected="selected" value="">year</option>
                        <?php
                        $year = date("Y");
                        for ($i = 0; $i <= 10; $i++) {
                            echo "<option value='" . $year . "'>" . $year . "</option>";
                            $year++;
                        }
                        ?>
                    </select>
                </div>
                <div id="update-billing-button"></div>
            </div>
        </div>
        <div class="clear"></div>
        <div id="att-enhancements">
            <h2>AT&amp;T Subscriber Enhancements</h2>
            <p>AT&amp;T Mobile users have access to enhanced features, connect your AT&amp;T Mobile Number to your myaNUMBER experience.</p>
            <div id="att-enhancements-box">
                <div id="att-logo-small"></div>
                <?php
                if (!$profile->attauth) {
                    ?>
                    <div id="att-authorize-button"></div>
                    <?php
                }
                ?>

                <div class="clear"></div>
                <p>AT&amp;T customers can elect to use AT&amp;T network tools within myaNUMBER. There is no additional fee. You will be required to authorize your AT&amp;T mobile phone number through your cell phone. All information sent and received from AT&amp;T will be subject to myaNUMBER's privacy policy.</p>
                <div class="clear"></div>
                <table>
                    <tbody>
                        <tr>
                            <td></td>
                            <td class="att-setting-toggle-text">On / Off</td>
                        </tr>
                        <tr>
                            <td class="att-setting-text"><h3>Messages:</h3> Send SMS Messages as your AT&amp;T Mobile Number</td>
                            <td><div class="att-setting-toggle messages<?php echo $profile->att_messages == 1 ? " on" : "" ?>"></div></td>
                        </tr>
                        <tr>
                            <td class="att-setting-text"><h3>Locator:</h3> Use the AT&amp;T Network to locate AT&amp;T Handsets</td>
                            <td><div class="att-setting-toggle locator<?php echo $profile->att_locator == 1 ? " on" : "" ?>"></div></td>
                        </tr>
                        <tr>
                            <td class="att-setting-text"><span style="text-decoration: line-through"><h3>Calls:</h3> Send &amp; Receive Calls with your AT&amp;T Mobile Number within myaNUMBER</span> - <span style="font-weight: bold">Coming soon.</span></td>
                            <td><div class="att-setting-toggle calls disabled"></div></td>
                        </tr>
                        <tr>
                            <td class="att-setting-text"><span style="text-decoration: line-through"><h3>Billing:</h3> Add Subscription Billing to your AT&amp;T Bill</span> - <span style="font-weight: bold">Coming soon.</span></td>
                            <td><div class="att-setting-toggle billing disabled"></div></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        $(".default").dropkick();
        $("#upgrade-button").click(function() {
            $.bbq.pushState({
                url: "/settings"
            });
        });
        $("#support-button").click(function() {
            myanumber.doSupport();
        });
        $("#contacts-button").click(function() {
            $.bbq.pushState({
                url: "/settings/contacts"
            });
        });
        
        $(".att-setting-toggle").click(function() {
            if($("#att-authorize-button").length == 0) {
                if(!$(this).hasClass("disabled")) {
                    if($(this).hasClass("on")) {
                        $(this).removeClass("on");
                        var disable = $(this).attr("class").replace("att-setting-toggle ", "");
                        var msgText = $(".att-setting-text", $(this).parent().parent()).text();
                        $.ajax({
                            type: "POST",
                            url: "/settings/disableatt",
                            data: {setting: disable},
                            success: function(data) {
                                myanumber.noty.error(msgText + "<br /><br />Disabled");
                            }
                        });
                    }
                    else {
                        var enable = $(this).attr("class").replace("att-setting-toggle ", "");
                        $(this).addClass("on");
                        var msgText = $(".att-setting-text", $(this).parent().parent()).text();
                        $.ajax({
                            type: "POST",
                            url: "/settings/enableatt",
                            data: {setting: enable},
                            success: function(data) {
                                myanumber.noty.success(msgText + "<br /><br />Enabled");
                            }
                        });
                    }
                }
            }
            else {
                myanumber.noty.error("You must authorize at&t before you can activate these features.");
            }
        });
        
        $("#att-authorize-button").click(function(e) {
            e.preventDefault();
            window.open('<?php echo $att; ?>', 'attauthwindow', 'height=600,location=no,menubar=no,status=no,toolbar=no,width=350');
        });

        $("#update-billing-button").click(function(e) {
            e.preventDefault();
            var cardNumber = $("#card-number input[type='text']").val();
            var expMonth = $("select[name='month']").val();
            var expYear = $("select[name='year']").val();
            $.ajax({
                type: "POST",
                url: "/settings/updatecreditcard",
                dataType: "json",
                data: {number: cardNumber, month: expMonth, year: expYear },
                success: function(data) {
                    if(data.success) {
                        myanumber.noty.success("You have successfully updated your credit card information.");
                    }
                    else {
                        myanumber.noty.error(data.msg);
                    }
                },
                error: function(xhr, status, error) {
                }
            });
        });
        
        $("#deactivate-account").click(function(e) {
            e.preventDefault();
            var note = myanumber.noty.deactivate($("#deactivate-template").html());
            $(".deactivate-confirm").click(function() {
                alert("Your myaNUMBER will function through the end of the current bill cycle and will not be renewed. You will be required to pay for any refills you use before the end of your billing cycle.")
                //hit cancel method here
                $.get("/settings/deactivateaccount", function() {
                    note.close();
                    location.href = "/";
                });
            });
            $(".deactivate-cancel").click(function() {
                note.close();
            });
        });
    });
    
    function refreshMe() {
        $.bbq.pushState({
            url: "/settings?r=1"
        });
    }
</script>
<script id="deactivate-template" type="text/x-jquery-tmpl">
    <div class="noty-box">
        <div class="grey-top"></div>
        <div class="grey-body">
            <div class="info"></div>
            <div class="noty-text">
                Are you sure you want to deactivate your account? Once deactivated we'll not be able to offer future service with your existing myaNUMBER. You will have to choose a new phone number to use.
                <div class="deactivate-confirm">I want to deactivate my myaNUMBER</div>
                <div class="deactivate-cancel">Never mind, I would like to keep my myaNUMBER</div>
                <div class="clear"></div>
            </div>
        </div>
        <div class="grey-bottom"></div>
    </div>
</script>