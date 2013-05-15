<form id='payment-form' action='<?php echo $url; ?>' method='POST'>
    <div class="register">
        <h1>Credit Card</h1>
        <div class="register-top"></div>
        <div class="register-content">
            <div id="details" style="height: auto">
                <div class="tab-content">
                    <input type='hidden' name='tr_data' value='<?php echo htmlentities($trData) ?>' />
                    <div>
                        <h3>Customer Information</h3>
                        <h3>First Name</h3>
                        <input type='text' name='customer[first_name]' id='customer_first_name' class="text" />
                        <h3>Last Name</h3>
                        <input type='text' name='customer[last_name]' id='customer_last_name' class="text" />
                        <h3>Email</h3>
                        <input type='text' name='customer[email]' id='customer_email' class="text" value="<?php echo $email; ?>" />
                        <h3>Billing Address</h3>
                        <h3>Street Address</h3>
                        <input type='text' name='customer[credit_card][billing_address][street_address]' id='billing_street_address' class="text" />
                        <h3>Extended Address</h3>
                        <input type='text' name='customer[credit_card][billing_address][extended_address]' id='billing_extended_address' class="text" />
                        <h3>City</h3>
                        <input type='text' name='customer[credit_card][billing_address][locality]' id='billing_locality' class="text" />
                        <h3>State</h3>
                        <input type='text' name='customer[credit_card][billing_address][region]' id='billing_region' class="text" />
                        <h3>Postal Code</h3>
                        <input type='text' name='customer[credit_card][billing_address][postal_code]' id='billing_postal_code' class="text" />
                    </div>
                    <div>
                        <h3>Credit Card</h3>
                        <h3>Credit Card Number</h3>
                        <input type='text' name='customer[credit_card][number]' id='braintree_credit_card_number' class="text" />
                        <h3>Credit Card Expiry (mm/yyyy)</h3>
                        <input type='text' name='customer[credit_card][expiration_date]' id='braintree_credit_card_exp' class="text" />
                    </div>
                </div>
            </div>
        </div>
        <div class="register-bottom"></div>
        <input type='submit' value="" />
    </div>
</form>