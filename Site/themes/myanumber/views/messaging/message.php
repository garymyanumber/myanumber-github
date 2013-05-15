<div id="inner-content-wrap">
    <div id="send-message-head">
        <!--<div id="back-button"></div>-->
        <?php if ($model->attauth == 0) {
            ?>
            <div id="att-auth-logo"></div>
            <p>Send a One-Way SMS to select users within your support.<br />Users will receive SMS Messages form your AT&amp;T Mobile Number. <a href="#">Learn More</a></p>
        <?php }
        ?>
        <div class="clear"></div>
    </div>
    <div class="contacts">
        <div class="contacts-top"></div>
        <div class="contacts-middle">
            <?php
            for ($i = 0; $i < count($model->contacts); $i++) {
                ?>
                <div class="contact c<?php echo ($i + 1); ?>" cid="<?php echo $model->contacts[$i]->id; ?>">
                    <div class="cname"><?php echo $model->contacts[$i]->name; ?></div>
                </div>
                <?php
            }
            ?>
            <div class="clear"></div>
        </div>
        <div class="contacts-bottom"></div>
    </div>
    <div class="message-window">
        <div class="message-window-top"></div>
        <div class="message-window-middle flexcroll" id="chat">
        </div>
        <div class="message-window-bottom"></div>
        <div class="message-input">
            <div class="message-loading"></div>
            <input type="text" class="message-text" />
            <div class="message-send"></div>
        </div>
    </div>
</div>
<script id="message-template" type="text/x-jquery-tmpl">
    <div class="message" id="msg-${id}">
        <div class="time">${myanumber.date.getShortDateString(createtime)}</div>
        <p>${message}</p>
    </div>
    <div class="clear"></div>
</script>
<script id="message-receive-template" type="text/x-jquery-tmpl">
    <div class="message-left" id="msg-${id}">
        <div class="message-name">${from}</div>
        <div class="time">${myanumber.date.getShortDateString(createtime)}</div>
        <p>${message}</p>
    </div>
    <div class="clear"></div>
</script>
<script type="text/javascript">
    var messageSending = false;
    var messages = [];
    var thread;
    var gettingMessages = false;
    $(function() {
        $("#back-button").click(function() {
            $.bbq.pushState({
                url: "/messaging/"
            });
        });
        fleXenv.initByClass("flexcroll");
        $(".message-send").click(function() {
            if(!messageSending) {
                var message = $(".message-text").val();
                if(message != "") {
                    $(".message-loading").show();
                    messageSending = true;
                    var cids = "";
                    $(".contacts .contact").each(function() {
                        cids += $(this).attr("cid") + ",";
                    });
                    $.ajax({
                        type: "POST",
                        data: {text: message, contacts: cids, sid: thread },
                        url: "/messaging/sendmessage",
                        dataType: "json",
                        success: function(data) {
                            $("#message-template").tmpl(data).appendTo("#chat_contentwrapper");
                            fleXenv.updateScrollBars();
                            fleXenv.scrollTo("msg-" + data.id);
                            $(".message-loading").hide();
                            $(".message-text").val("");
                            messageSending = false;
                            messages.push(data.id);
                            thread = data.thread_id;
                        }
                    });
                }
            }
        });
        $(".message-text").keyup(function(e) {
            if(e.keyCode == 13) {
                $(".message-send").click();
            }
        });
        
        setInterval(getNewMessages, 3000);
    });    
    function getNewMessages() {
        if(!gettingMessages) {
            gettingMessages = true;
            $.ajax({
                type: "POST",
                data: {sid: thread},
                dataType: "json",
                url: "/messaging/getpreviousmessages",
                success: function(data) {
                    if(data) {
                        for(var i = 0; i < data.length; i++) {
                            if(messages.indexOf(data[i].id) == -1) {
                                if(data[i].from == "web") {
                                    $("#message-template").tmpl(data[i]).appendTo("#chat_contentwrapper");
                                }
                                else {
                                    $("#message-receive-template").tmpl(data[i]).appendTo("#chat_contentwrapper");
                                }
                                messages.push(data[i].id);
                                fleXenv.updateScrollBars();
                                fleXenv.scrollTo("msg-" + data[i].id);
                            }
                            gettingMessages = false;
                        }
                    }
                }
            });
        }
        
    }
</script>