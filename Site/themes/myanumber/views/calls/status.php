<div id="inner-content-wrap">
    <div class="contacts">
        <div class="contacts-top"></div>
        <div class="contacts-middle">
            <?php
            if (isset($contacts)) {
                for ($i = 0; $i < count($contacts); $i++) {
                    ?>
                    <div class="contact c<?php echo $i + 1; ?>">
                        <div class="cname"><?php echo $contacts[$i]->name; ?></div>
                    </div>
                    <?php
                }
            }
            ?>
            <div class="clear"></div>
        </div>
        <div class="contacts-bottom"></div>
    </div>
    <div id="call-window">
        <div id="call-window-top"><h3>Calls</h3></div>
        <div id="call-window-buttons">
            <div id="add-call"></div>
            <div id="keypad"></div>
            <div id="end-call"></div>
            <div id="push-call"></div>
            <div id="mute"></div>
            <div id="hold"></div>
        </div>
        <div id="call-window-status">
            <div id="status-name"></div>
            <div id="status-length">Call Length <div id="status-time">00:00</div></div>
        </div>
        <div id="call-window-names">
            <?php
            for ($i = 0; $i < 7; $i++) {
                if (isset($contacts)) {
                    if ($i < count($contacts)) {
                        ?>
                        <div class="call-name">
                            <h4 class="selected"><?php echo $contacts[$i]->name; ?></h4>
                        </div>
                        <?php
                    } else {
                        ?>
                        <div class="call-name"></div>
                        <?php
                    }
                } else {
                    ?>
                    <div class="call-name"></div>
                    <?php
                }
            }
            ?>
        </div>
        <div class="clear"></div>
        <div id="call-window-overlay">
            <h4>Pushing call...</h4>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function() {
<?php if (isset($sip)) {
    ?>
                myanumber.phono.phone.dial('<?php echo trim($sip); ?>', {
                    headers: [
                        {
                            name: "x-type",
                            value: "web"
                        },
                        {
                            name: "x-contacts",
                            value: "<?php echo trim($cids); ?>"
                        }
                    ],
                    onRing: function(event) {
                        myanumber.currentCall = event.source;
                        //myanumber.noty.info("It's ringing...");
                    },
                    onAnswer: function() {
                        //myanumber.noty.success("Answered...");
                    },
                    onHangup: function() {
                        //myanumber.noty.info("It's hanguped...");
                    },
                    volume: 100,
                    gain: 100
                });     
    <?php
}
?>
                
        
        $("#end-call").click(function() {
            stopTimer();
            myanumber.currentCall.hangup();
            $.bbq.pushState({
                url: "/calls"
            });
        });
        $("#mute").click(function() {
            if($(this).hasClass("active")) {
                myanumber.currentCall.mute(false);
                $(this).removeClass("active");
            }
            else {
                myanumber.currentCall.mute(true);
                $(this).addClass("active");
            }
        });
        
        $("#hold").click(function() {
            if($(this).hasClass("active")) {
                myanumber.currentCall.hold(false);
                $(this).removeClass("active");
            }
            else {
                myanumber.currentCall.hold(true);
                $(this).addClass("active");
            }
        });
        
        $("#push-call").click(function() {
            $.ajax({
                type: "POST",
                url: "/tropo/callpush",
                data: { pid: '<?php echo $profile->id; ?>' },
                success: function() {
                    stopTimer();
                    myanumber.currentCall.hangup();
                    $.bbq.pushState({
                        url: "/calls"
                    });
                }
            });
        });
        
        var startTime = new Date();
        
        var timer = setInterval(updateTimer, 1000);
    
        function updateTimer() {
            var stopTime = new Date();
            var result = stopTime - startTime;
            var seconds = parseInt(result / 1000);
            var minutes = 0;
            if(seconds >= 60) {
                minutes = parseInt(seconds / 60);
                seconds = seconds - (minutes * 60);
            }
            $("#status-time").text(("0" + minutes).slice(-2) + ":" + ("0" + seconds).slice(-2));
        }
        function stopTimer() {
            window.clearInterval(timer);
        }
    });
    
    
</script>