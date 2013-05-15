<h2>Please wait while you're redirected.</h2>
<script type="text/javascript">
function redirect() {
    location.href = '<?php echo YUM::module()->facebookConfig['domain'] . '/user/auth?state=' . $_REQUEST['state']; ?>';
}
window.setTimeout("redirect()", 3000);
</script>