<div id="inner-content-wrap">
    <div class="contacts">
        <div class="contacts-top"></div>
        <div class="contacts-middle">
            <?php
            if (count($model->contacts) > 0) {
                for ($i = 0; $i < count($model->contacts); $i++) {
                    ?>
                    <div class="contact c<?php echo ($i + 1); ?>" cid="<?php echo $model->contacts[$i]->id; ?>">
                        <div class="cname"><?php echo $model->contacts[$i]->name; ?></div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <p>Contacts will appear here when they have SMS enabled.</p>
                <?php
            }
            ?>
            <div class="clear"></div>
        </div>
        <div class="contacts-bottom"></div>
    </div>
    <div class="message-mid">
        <h2>Select User To Get Started</h2>
        <div class="message-button"></div>
        <div class="clear"></div>
    </div>
    <div class="list-item">
        <div class="list-item-top">
            <h2>Message History</h2>
        </div>
        <div class="list-item-items">
        </div>
        <div class="list-item-bottom">
        </div>
    </div>
    <?php
    if ($model->attauth == 0) {
        ?>

        <div id="att-auth">
            <div id="att-auth-logo"></div>
            <div id="att-auth-message">
                <h5>Authorize AT&amp;T</h5>
                <p>Send a SMS Message as Your AT&amp;T Mobile <a href="#">Learn More</a></p>
            </div>
            <div id="att-auth-button"></div>
        </div>
        <?php
    }
    ?>
</div>
<script id="message-thread-template" type="text/x-jquery-tmpl">
    <div class="list-item-item">
        <h2>${from}</h2>
        <p>${message}</p>
        <div class="time">${myanumber.date.getShortDateString(createtime)}</div>
    </div>
</script>
<script type="text/javascript">
    var tpage = 0;
    var thread;
    $(function() {
        $(".message-button").click(function() {
            $.bbq.pushState({
                url: "/messaging/message"
            });
        });
        getThreads();
    });
    function getThreads() {
        $.ajax({
            type: "POST",
            data: {page: tpage},
            url: "/messaging/getmessages",
            dataType: "json",
            success: function(data) {
                if(data.length > 0) {
                    for(var i = 0; i < data.length; i++) {
                        $("#message-thread-template").tmpl(data[i]).appendTo(".list-item-items");
                    }
                }
                else {
                    if(tpage == 0) {
                        $(".list-item-items").append("<p>You have no messages yet.</p>");
                    }
                }
            }
        });
    }
        
</script>