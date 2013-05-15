<!DOCTYPE html>
<html>
    <head>
        <title>myanumber</title>
        <link rel="Stylesheet" type="text/css" href="/css/reset.css" />
        <link rel="Stylesheet" type="text/css" href="/css/dropkick.css" />
        <link rel="Stylesheet" type="text/css" href="/css/blank_theme.css" />
        <link rel="Stylesheet" type="text/css" href="/css/itemtemplates.css" />
        <link rel="Stylesheet" type="text/css" href="/css/flexcrollstyles.css" />
        <link rel="Stylesheet" type="text/css" href="/css/site.css" />
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
        <script type="text/javascript" src="/scripts/scrollability.js"></script>
        <script type="text/javascript" src="/scripts/jquery.dropkick-1.0.0.js"></script>
        <script type="text/javascript" src="http://ajax.microsoft.com/ajax/jquery.templates/beta1/jquery.tmpl.min.js"></script>
        <script src="/scripts/jquery.phono.js"></script>
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA7BczkLqd2YX7ajhctOHj_-xzjCksmJM4&sensor=false"></script>
        <script type="text/javascript" src="http://google-maps-utility-library-v3.googlecode.com/svn/tags/infobox/1.1.5/src/infobox.js"></script>
        <script type="text/javascript" src="/scripts/noty/jquery.noty.js"></script>
        <script type="text/javascript" src="/scripts/noty/layouts/bottom.js?q=<?php echo time(); ?>"></script>
        <script type="text/javascript" src="/scripts/noty/layouts/center.js?q=<?php echo time(); ?>"></script>
        <script type="text/javascript" src="/scripts/noty/layouts/topRight.js?q=<?php echo time(); ?>"></script>
        <script type="text/javascript" src="/scripts/noty/themes/myanumber.js"></script>
        <script type="text/javascript" src="/scripts/jquery.ba-bbq.min.js"></script>
        <script type="text/javascript" src="/scripts/flexcroll.js"></script>
        <script type="text/javascript" src="/scripts/date.format.js"></script>
        <script type="text/javascript" src="/scripts/myanumber.js"></script>
        <link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/favicon.ico" type="image/x-icon" />
    </head>
    <body>
        <div id="content">
            <div id="left-nav">
                <ul>
                    <li class="main">
                    </li>
                    <li class="calls">
                        <a href="/calls">
                            <img src="/images2/nav/clickable.gif" />
                        </a>
                    </li>
                    <li class="messages">
                        <a href="/messaging/message">
                            <img src="/images2/nav/clickable.gif" />
                        </a>
                    </li>
                    <li class="locator">
                        <a href="/locator">
                            <img src="/images2/nav/clickable.gif" />
                        </a>
                    </li>
                    <li class="settings">
                        <a href="/settings">
                            <img src="/images2/nav/clickable.gif" />
                        </a>
                    </li>
                    <li class="logout">
                        <a href="/login/logout">
                            <img src="/images2/nav/clickable.gif" />
                        </a>
                    </li>
                </ul>
            </div>
            <div id="main">
                <div id="inner-content">
                    <!-- page content -->
                    <?php echo $content; ?>
                    <!-- end page content -->
                </div>
            </div>
        </div>
        <script type="text/javascript">

            var _gaq = _gaq || [];
            _gaq.push(['_setAccount', 'UA-37429255-1']);
            _gaq.push(['_trackPageview']);

            (function() {
                var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
                ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
                var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
            })();

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