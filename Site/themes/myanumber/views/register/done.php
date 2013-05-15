<div class="register area-code">
    <h1>You're All Set Up!</h1>
    <div class="register-top"></div>
    <div class="register-content">
        <h3>Get started using your myaNUMBER</h3>
        <div class="clear"></div>
        <div class="done-calls">
            <p>Calls</p>
        </div>
        <div class="done-messages">
            <p>Messages</p>
        </div>
        <div class="done-locator">
            <p>Locator</p>
        </div>
        <div class="clear"></div>
        <div class="done-text">
            Choose from the options above or <a href="#">click here to Learn More</a> about your account.
        </div>
    </div>
    <div class="register-bottom"></div>
</div>
<script type="text/javascript">
    $(function() {
        $(".done-calls").click(function() {
            location.href = "/me#url=/calls";
        });
        $(".done-messages").click(function() {
            location.href = "/me#url=/messaging";
        });
        $(".done-locator").click(function() {
            location.href= "/me#url=/locator";
        });
    });
</script>