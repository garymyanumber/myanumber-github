<div id="inner-settings">
    <div id="settings-menu">
        <div id="upgrade-button"></div>
        <div id="support-button" class="selected"></div>
        <div id="contacts-button"></div>
        <div class="clear"></div>
    </div>
    <h1>Settings</h1>
    <div class="clear"></div>
    <div id="settings-support">
        <div id="social-links">
            <div id="homepage-twitter"></div>
            <div id="homepage-facebook"></div>
            <div id="homepage-pinterest"></div>
            <div id="homepage-tumblr"></div>
        </div>
        <h2>Support Center</h2>
        <p>Stay updated with announcements, get answer from the support team.</p>
        <div class="clear"></div>
        <div id="settings-support-search">
            <input type="text" />
            <div id="settings-support-search-button"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        $("#upgrade-button").click(function() {
            $.bbq.pushState({
                url: "/settings"
            });
        });
        $("#support-button").click(function() {
            $.bbq.pushState({
                url: "/settings/support"
            });
        });
        $("#contacts-button").click(function() {
            $.bbq.pushState({
                url: "/settings/contacts"
            });
        });
    });
</script>