<div class="register">
    <h1>Order Summary &amp; Payment</h1>
    <div class="register-top"></div>
    <div class="register-content">
        <span style="font-size: 21px; color: #FFF; margin: 10px 0 10px 40px; text-align: left; font-weight: bold">Plan &amp; Pricing</span>
        <p><?php echo $summary; ?></p>
        <h3>Pay with Credit or Debit</h3>
        <div id="register-pay-card"></div>
        <h3>Pay with PayPal</h3>
        <div id="real-paypal-button" class="hidden">
            <?php echo $paypalsub->print_buy_button(); ?>
        </div>
        <div id="register-pay-paypal"></div>
        <h3>Pay with AT&amp;T</h3>
        <div id="register-pay-att"></div>
    </div>
    <div class="register-bottom"></div>
</div>
<script type="text/javascript">
    $(function() {
        $("#register-pay-card").click(function() {
            location.href = '/register/braintree';
        });
        $("#register-pay-paypal").click(function() {
            var elem = document.getElementById("paypal-submit");
            if(document.dispatchEvent) {   // W3C
                var oEvent = document.createEvent( "MouseEvents" );
                oEvent.initMouseEvent("click", true, true,window, 1, 1, 1, 1, 1, false, false, false, false, 0, elem);
                elem.dispatchEvent( oEvent );
            }
            else if(document.fireEvent) {   // IE
                elem.click();
            }
        });
    });
</script>