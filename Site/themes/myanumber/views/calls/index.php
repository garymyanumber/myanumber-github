<div id="inner-content-wrap">
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
    <div id="calls-mid">
        <h2>Select Users To Call</h2>
        <div id="calls-start-button"></div>
        <div class="clear"></div>
    </div>
    <div class="list-item">
        <div class="list-item-top">
            <h2>Call History</h2>
        </div>
        <div class="list-item-middle">
            <div class="list-item-items">
            </div>
        </div>
        <div class="list-item-bottom"></div>
    </div>
    <!--
    <div class="load-more-button">
    </div>
    -->
</div>
<script id="call-item-template" type="text/x-jquery-tmpl">
    <div class="list-item-item">
        <h2>Call from: ${originator}</h2>
        <p>
            {{if audio == null}}
            Recordings and transcripts may take a few minutes to generate or the call was incomplete.
            {{else}}
            <a href="javascript:doGetFile('${audio}')" target="_blank">Recording</a>
            {{if transcription == null}}
                Transcripts may take a few minutes to generate.
            {{else}}
            - <a href="javascript:doGetTranscription('${id}')" target="_blank">Transcript</a>
            {{/if}}
            {{/if}}
        </p>
        <div class="time">${myanumber.date.getShortDateString(start)}</div>
    </div>
</script>
<iframe id="downloadIframe" style="display: none"></iframe>
<script type="text/javascript">
    $(function() {
        $(".cadd").click(function(e) {
            e.preventDefault();
            myanumber.showDialer(function(number) {
            });
        });
        $("#calls-start-button").click(function() {
            var selected = $(".contacts .selected");
            var ids = "";
            $(selected).each(function() {
                ids += $(this).attr("cid") + ",";
            });
            if(ids != "") {
                thread = null;
                $.bbq.pushState({
                    url: "/calls/status?ids=" + ids
                });
                
                //start phono call
                
            }
            else {
                myanumber.noty.info("You must select one or more contacts.");
            }
        });
        $(".contact").click(function(e) {
            e.preventDefault();
            if($(this).hasClass("selected")) {
                $(this).removeClass("selected");
            }
            else {
                $(this).addClass("selected");
            }
        });
        
        $.ajax({
            type: "POST",
            url: "/calls/getcalls",
            dataType: "json",
            success: function(data) {
                if(data.length > 0) {
                    $("#call-item-template").tmpl(data).appendTo(".list-item-items");
                }
                else {
                    $(".list-item-items").append("<p>Place your first call.</p>");
                }
            }
        });
    });
    function doGetFile(file) {
        var frm = document.getElementById('downloadIframe');
        frm.src = "/calls/getfile?f=" + file;
        console.log(frm);
    }
    function doGetTranscription(log) {
        window.open('/calls/transcription?log=' + log, 'transcript', 'height=600,location=no,menubar=no,status=no,toolbar=no,width=350');
    }
</script>