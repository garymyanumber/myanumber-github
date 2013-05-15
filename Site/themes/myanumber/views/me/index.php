<div id="inner-content-wrap">
    <div id="overview-status">
        <div id="myanumber-number-desc">
            Our myaNUMBER: <span id="myanumber-blue-big"><?php echo $model['myanumber']; ?></span>
        </div>
        <div id="overview-voice-usage">
            Voice Usage <span id="voice-usage-orange"><?php echo $model['voice']; ?>/200</span>
        </div>
        <div id="overview-sms-usage">
            SMS Usage <span id="sms-usage-blue"><?php echo $model['sms']; ?>/100</span>
        </div>
    </div>
    <div class="clear"></div>
    <div id="overview-ad"></div>
    <div id="overview-locator-history"></div>
    <div class="clear"></div>
    <div id="overview-recent-calls">
        <div class="list-item">
            <div class="list-item-top">
                <h2>Recent Calls</h2>
            </div>
            <div class="list-item-middle">
                <div class="list-item-items">
                    <?php
                    if (count($model['calls']) > 0) {
                        foreach ($model['calls'] as $call) {
                            ?>
                            <div class="list-item-item">
                                <h2>Call from: <?php echo strlen($call->originator) > 10 ? "Web" : $call->originator; ?></h2>
                                <p>
                                    <?php if ($call->audio) {
                                        ?>
                                        <a href="javascript:doGetFile('<?php echo $call->audio; ?>')" target="_blank">Recording</a> - <a href="javascript:doGetTranscription('<?php echo $call->id; ?>')" target="_blank">Transcript</a>
                                        <?php
                                    } else {
                                        echo "Incomplete call.";
                                    }
                                    ?>
                                </p>
                                <div class="time">
                                    <?php
                                    $dt = new DateTime("@$call->start");

                                    //echo $dt->format('m/d/y g:iA');
                                    echo $call->start;
                                    ?></div>
                                <div class="arrow"></div>
                            </div>
                            <?php
                        }
                    } else {
                        ?>
                        <p>Place your first call.</p>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <div class="list-item-bottom"></div>
        </div>
    </div>
    <div class="page-break"></div>
    <div id="overview-recent-messages">
        <div class="list-item">
            <div class="list-item-top">
                <h2>Recent Messages</h2>
            </div>
            <div class="list-item-middle">
                <div class="list-item-items">
                    <?php
                    if (count($model['threads']) > 0) {
                        foreach ($model['threads'] as $thread) {
                            ?>
                            <div class="list-item-item">
                                <h2><?php echo $thread->from; ?></h2>
                                <p><?php echo $thread->message; ?></p>
                                <div class="time">
                                    <?php
                                    $dt = new DateTime("@$thread->createtime");

                                    //echo $dt->format('m/d/y g:iA');
                                    echo $thread->createtime;
                                    ?></div>
                                <div class="arrow"></div>
                            </div>
                            <?php
                        }
                    } else {
                        ?>
                        <p>Send your first text message.</p>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <div class="list-item-bottom"></div>
        </div>
    </div>
</div>
<iframe id="downloadIframe" style="display: none"></iframe>
<script type="text/javascript">
    $(function() {
        $("#overview-ad").click(function() {
            myanumber.doSupport();
        });
        $("#overview-locator-history").click(function() {
            $.bbq.pushState({
                url: "/locator"
            });
        });
        
        $(".time").each(function() {
            var epoch = $(this).text();
            if(!isNaN(epoch)) {
                var d = myanumber.date.getShortDateString(epoch);
                $(this).text(d);
            }
        });
    });
    function doGetFile(file) {
        var frm = document.getElementById('downloadIframe');
        frm.src = "/calls/getfile?f=" + file;
    }
    function doGetTranscription(log) {
        window.open('/calls/transcription?log=' + log, 'transcript', 'height=600,location=no,menubar=no,status=no,toolbar=no,width=350');
    }
</script>