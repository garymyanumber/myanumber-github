<?php
function GetFirstLastClass($contacts, $contact) {
    if ($contact == $contacts[0]) {
        return "first";
    }
    if ($contact == $contacts[(count($contacts) - 1)]) {
        return "last";
    }

    return "";
}

function GetNumberParts($number, $inx) {
    $area = substr($number, 0, 3);
    $prefix = substr($number, 3, 3);
    $line = substr($number, 6, 4);
    $parts = array($area, $prefix, $line);
    return $parts[$inx];
}
?>
<div id="inner-settings">
    <div id="settings-menu">
        <div id="upgrade-button"></div>
        <div id="support-button"></div>
        <div id="contacts-button" class="selected"></div>
        <div class="clear"></div>
    </div>
    <h1>Settings</h1>
    <div class="clear"></div>
    <div id="settings-contacts">
        <h2>Support Network Contacts</h2>
        <p>myaNUMBER will ring your contacts from top to bottom.</p>
        <div class="clear"></div>
        <div id="settings-contacts-list">
            <div id="add-contact-button">ADD CONTACT</div>
            <div id="settings-contacts-items">
                <?php
                foreach ($contacts as $contact) {
                    ?>
                    <div class="contact-row <?php echo GetFirstLastClass($contacts, $contact); ?>">
                        <input class="id" type="hidden" value="<?php echo $contact->id; ?>" />
                        <div class="nickname">
                            <h3>nickname</h3>
                            <input type="text" name="name" value="<?php echo $contact->name; ?>" />
                        </div>
                        <div class="email">
                            <h3>email</h3>
                            <input type="text" name="email" value="<?php echo $contact->email; ?>" />
                        </div>
                        <div class="phone">
                            <h3>phone number</h3>
                            <span class="plus">+1</span>
                            <input type="text" name="area" class="three" maxlength = "3" value="<?php echo GetNumberParts($contact->phone, 0); ?>" />
                            <input type = "text" name="prefix" class="three" maxlength = "3" value="<?php echo GetNumberParts($contact->phone, 1); ?>" />
                            <input type = "text" name="line" class="four" maxlength = "4" value="<?php echo GetNumberParts($contact->phone, 2); ?>" />
                        </div>
                        <div class="order-change">
                            <div class="down-order"></div>
                            <div class="up-order"></div>
                            <h3>change call ring order</h3>
                        </div>
                        <div class="delete-contact">delete contact</div>
                        <div class="enable-sms <?php echo $contact->enable_sms ? 'enabled' : ''; ?>">enable SMS messaging to this number.</div>
                    </div>
                    <?php
                }
                ?>
            </div>

        </div>
        <div class="clear"></div>
        <div class="save-changes"></div>
        <div class="clear"></div>
        <div id="account-owner">
            
            <h2>Update Account Owner</h2>
            <p>The account owner is required to be included on the Support Network call list. Learn more <a href="#">here</a>.</p>
            <div class="clear"></div>            
            <div id="account-owner-box">
                <h3>nickname</h3>
                <input type="text" value="<?php echo $profile->name; ?>" name="nickname" />
                <?php if(!isset($profile->facebook_id)) { ?>
                <h3>email</h3>
                <input type="text" value="<?php echo $profile->email; ?>" name="email" />
                <h3>password</h3>
                <input type="password" name="password" />
                <h3>confirm password</h3>
                <input type="password" name="password-confirm" />
                <?php } ?>
            </div>
        </div>
        <div id="secondary-lines">
            <h2>Secondary Call Lines</h2>
            <p>If none of your contacts are reached myaNUMBER will begin ringing all secondary lines. Learn more <a href="#">here</a>.</p>
            <div class="clear"></div>
            <div id="secondary-lines-box">
                <?php
                for ($i = 0; $i < 3; $i++) {
                    if ($secondary) {
                        if ($i <= (count($secondary) - 1)) {
                            ?>
                            <div class="secondary-line">
                                <h3>phone number</h3>
                                <span class="plus">+1</span>
                                <input type="text" class="three" maxlength="3" value="<?php echo GetNumberParts($secondary[$i]->phone, 0); ?>" />
                                <input type="text" class="three" maxlength="3" value="<?php echo GetNumberParts($secondary[$i]->phone, 1); ?>" />
                                <input type="text" class="four" maxlength="4" value="<?php echo GetNumberParts($secondary[$i]->phone, 2); ?>" />
                            </div>
                            <?php
                        } else {
                            ?>
                            <div class="secondary-line">
                                <h3>phone number</h3>
                                <span class="plus">+1</span>
                                <input type="text" class="three" maxlength="3" />
                                <input type="text" class="three" maxlength="3" />
                                <input type="text" class="four" maxlength="4" />
                            </div>
                            <?php
                        }
                    } else {
                        ?>
                        <div class="secondary-line">
                            <h3>phone number</h3>
                            <span class="plus">+1</span>
                            <input type="text" class="three" maxlength="3" />
                            <input type="text" class="three" maxlength="3" />
                            <input type="text" class="four" maxlength="4" />
                        </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
        <div class="clear"></div>
        <div class="secondary-contacts"></div>
    </div>
</div>
<script id="contact-template" type="text/x-jquery-tmpl">
    <div class="contact-row">
        <input class="id" type="hidden" />
        <div class="nickname">
            <h3>nickname</h3>
            <input type="text" name="name" />
        </div>
        <div class="email">
            <h3>email</h3>
            <input type="text" name="email" />
        </div>
        <div class="phone">
            <h3>phone number</h3>
            <span class="plus">+1</span>
            <input type="text" name="area" class="three" maxlength = "3" />
            <input type = "text" name="prefix" class="three" maxlength = "3" />
            <input type = "text" name="line" class="four" maxlength = "4" />
        </div>
        <div class="order-change">
            <div class="down-order"></div>
            <div class="up-order"></div>
            <h3>change call ring order</h3>
        </div>
        <div class="delete-contact">delete contact</div>
        <div class="enable-sms enabled">enable SMS messaging to this number.</div>
    </div>
</script>
<script type="text/javascript">
    $(function() {
        var saving = false;
        $(".save-changes").click(function() {
            if(!saving) {
                saving = true;
                var contacts = new Array();
                $(".contact-row").each(function() {
                    var contact = {};
                    contact.name = $(".nickname input", this).val();
                    contact.email = $(".email input", this).val();
                    contact.id = $(".id", this).val();
                    contact.phone = '';
                    $(".phone input", this).each(function() {
                        contact.phone += $(this).val();
                    });
                    contact.enable_sms = $(".enable-sms", this).hasClass("enabled");
                    contacts.push(contact);
                });
            
                $.ajax({
                    type: "POST",
                    data: {contacts: contacts},
                    url: "/settings/savecontacts",
                    dataType: "json",
                    success: function(data) {
                        saving = false;
                        if(data.success) {
                            myanumber.noty.success("Your contacts have been saved.");
                        }
                        else {
                            var msg = "";
                            for(var i = 0; i < data.message.length; i++) {
                                msg += data.message[i] + "<br />";
                            }
                            myanumber.noty.error(msg);
                        }
                    }
                });
            }
        });
        $("#add-contact-button").click(function() {
            var ccount = $(".contact-row").size();
            if(ccount < 5) {
                var newContact = $("#contact-template").tmpl(null).appendTo("#settings-contacts-items");
                SetupContact(newContact);
                $(".contact-row").removeClass("last").removeClass("first");
                $(".contact-row").first().addClass("first");
                $(".contact-row").last().addClass("last");
            }
            else {
                myanumber.noty.info("You can only have up to five contacts.");
            }
        });
        
        $(".contact-row").each(function() {
            SetupContact(this);
        });
        
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
        
        $(".secondary-contacts").click(function() {
            
            var data = {};
            data.account = {};
            data.errors = [];
            data.secondary = [];
            
            var nickname = $("#account-owner input[name='nickname']").val().trim();
            if(nickname != "") {
                data.account.nickname = nickname;
            }
            else {
                data.errors.push("You must specify your nickname.");
            }
            
            var email = $("#account-owner input[name='email']").val().trim();
            if(email != "") {
                data.account.email = email;
            }
            else {
                data.errors.push("You must have an email on this account.");
            }
            
            if($("#account-owner input[name='password']").val() != '') {
                var password = $("#account-owner input[name='password']").val().trim();
                var passwordConfirm = $("#account-owner input[name='password-confirm']").val().trim();
                if(password == passwordConfirm) {
                    data.account.password = password;
                }
                else {
                    data.errors.push("Your passwords do not match.");
                }
            }
            
            $(".secondary-line").each(function() {
                var number = "";
                $("input", this).each(function() {
                    number += $(this).val();
                });
                if(number.length == 10) {
                    data.secondary.push(number);
                }
            });
            
            if(data.errors.length == 0) {
                $.ajax({
                    type: "POST",
                    url: "/settings/savesecondary",
                    data: data,
                    dataType: "json",
                    success: function(suc) {
                        myanumber.noty.success("Saved account owner information and secondary contacts.");
                        $("#account-owner input[name='password']").val('');
                        $("#account-owner input[name='password-confirm']").val('');
                    },
                    error: function() {
                        myanumber.noty.error("There was an error. Please try again.");
                    }
                });
            }
            else {
                var errors = "";
                for(var i in data.errors) {
                    errors += data.errors[i] + "<br />";
                }
                myanumber.noty.error(errors);
            }
        });
    });
    function SetupContact(contact) {
        $(".enable-sms", contact).click(function() {
            if($(this).hasClass("enabled")) {
                $(this).removeClass("enabled");
            }
            else {
                $(this).addClass("enabled");
            }
        });
        $(".delete-contact", contact).click(function() {
            var name = $(this).parent().find(".nickname input").val();
            var ccount = $(".contact-row").size();
            if(ccount > 1) {
                var deleteContact = confirm("Are you sure you want to delete " + name + "?");
                if(deleteContact) {
                    $(this).parent().remove();
                    $(".contact-row").removeClass("last").removeClass("first");
                    $(".contact-row").first().addClass("first");
                    $(".contact-row").last().addClass("last");
                }
            }
            else {
                myanumber.noty.info("You must have at least one contact.");
            }
        });
        $(".up-order", contact).click(function() {
            var item = $(this).parent().parent();
            item.prev().before(item);
            $(".contact-row").removeClass("last").removeClass("first");
            $(".contact-row").first().addClass("first");
            $(".contact-row").last().addClass("last");
        });
        $(".down-order", contact).click(function() {
            var item = $(this).parent().parent();
            item.next().after(item);
            $(".contact-row").removeClass("last").removeClass("first");
            $(".contact-row").first().addClass("first");
            $(".contact-row").last().addClass("last");
        });
    }
</script>