<h2 style="color: #FFF">Please wait while you're redirected.</h2>
<?php
if (isset($_GET['complete'])) {
    ?>
    <script type="text/javascript">
        window.opener.location.href = "/me";
        close();
    </script>
    <?php
} else if (isset($_GET['register'])) {
    ?>
    <script type="text/javascript">
        window.opener.location.href = "/register/";
        close();
    </script>
    <?php
} else if(isset($_GET['step'])) {
    ?>
    <script type="text/javascript">
        window.opener.location.href = "/register/step" + '<?php echo $_GET['step']; ?>';
        close();
    </script>
    <?php
} else {
    ?>
    <script type="text/javascript">
        function redirect() {
            location.href = '<?php echo "/login/facebook?state=" . $_GET["state"] . "&code=" . $_GET["code"]?>';
        }
        window.setTimeout("redirect()", 1000);
    </script>
    <?php
}
?>
