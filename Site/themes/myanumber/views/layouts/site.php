<?php
$fbconfig = Yum::module()->facebookConfig;
if (isset($fbconfig)) {
    Yii::import('application.modules.user.vendors.facebook.*');
    require_once('Facebook.php');
    $facebook = new Facebook($fbconfig);
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>myanumber</title>
        <link rel="Stylesheet" type="text/css" href="/css/reset.css" />
        <link rel="Stylesheet" type="text/css" href="/css/site.css" />
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
        <script type="text/javascript" src="http://ajax.microsoft.com/ajax/jquery.templates/beta1/jquery.tmpl.min.js"></script>
        <script type="text/javascript" src="/scripts/noty/jquery.noty.js"></script>
        <script type="text/javascript" src="/scripts/noty/layouts/bottom.js?q=<?php echo time(); ?>"></script>
        <script type="text/javascript" src="/scripts/noty/layouts/center.js?q=<?php echo time(); ?>"></script>
        <script type="text/javascript" src="/scripts/noty/layouts/topRight.js?q=<?php echo time(); ?>"></script>
        <script type="text/javascript" src="/scripts/noty/themes/myanumber.js"></script>
        <script type="text/javascript" src="/scripts/myanumber.js"></script>
        <link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/favicon.ico" type="image/x-icon" />
    </head>
    <body>
        <?php if (isset($fbconfig)): ?>
            <div id="fb-root"></div>
            <script>
                window.fbAsyncInit = function() {
                    FB.init({
                        appId   : '<?php echo $facebook->getAppId(); ?>',
                        status  : <?php echo $fbconfig['status']; ?>, // check login status
                        cookie  : <?php echo $fbconfig['cookie']; ?>, // enable cookies to allow the server to access the session
                        xfbml   : <?php echo $fbconfig['xfbml']; ?>,
                        oauth: true// parse XFBML
                    });

                    // whenever the user logs in, we refresh the page
                    FB.Event.subscribe('auth.login', function() {
                        window.location.reload();
                    });
                };

                (function() {
                    var e = document.createElement('script');
                    e.src = document.location.protocol + '//connect.facebook.net/<?php echo $fbconfig['lang']; ?>/all.js';
                    e.async = true;
                    document.getElementById('fb-root').appendChild(e);
                }());
            </script>
        <?php endif; ?>
        <div id="site-wrap">
            <div id="site-content">
                <?php echo $content; ?>
            </div>
        </div>
        <div id="site-footer">
            <div class="copyright">
                <a href="/">myaNUMBER &#169;</a>
            </div>
            <ul>
                <li>
                    <a href="#" id="support-link"></a>
                </li>
                <li>
                    <a href="/site/contact">Contact&nbsp;Us</a>
                </li>
            </ul>
        </div>
        <script type="text/javascript">
            $(function() {
                $(".number").keyup(function(e) {
                    if($(this).val().length == $(this).attr("maxlength")) {
                        $(this).next().focus();
                    }
                    if($(this).val().length == 0) {
                        $(this).prev().focus();
                    }
                });
            });
        </script>
        <script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-30081675-1']);
  _gaq.push(['_setDomainName', 'myanumber.com']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
    </body>
</html>