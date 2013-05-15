<h2 style="color: #FFF">Please wait while you're redirected.</h2>
<?php
if (isset($_GET['complete'])) {
    ?>
    <script type="text/javascript">
        window.opener.location.href = "/register/steponefacebook";
        close();
    </script>
    <?php
} else {
    ?>
    <script type="text/javascript">
        function redirect() {
            location.href = '<?php echo Yii::app()->getBaseUrl(true) . '/register/registerfacebook?state=' . $_GET['state']; ?>';
        }
        window.setTimeout("redirect()", 1000);
    </script>
    <?php
}
?>
