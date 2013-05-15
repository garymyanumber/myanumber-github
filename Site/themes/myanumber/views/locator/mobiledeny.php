<!DOCTYPE html>
<html>
    <head>
        <link type="text/css" rel="Stylesheet" href="/css/site.css" />
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
        <meta name="viewport" content="width=320, initial-scale=1, maximum-scale=1">
    </head>
    <body>
        <div id="locator-mobile">
            <div id="locator-mobile-header">
                <div id="mobile-logo"></div>
                <div id="mobile-pin"></div>
                <h1>Locator</h1>
            </div>
            <div id="locator-mobile-deny-box">
                <h1>Location Access Denied</h1>
                <p>We have notified the account owner for myaNUMBER <span class="number"><?php echo $number; ?></span></p>
                <p>They have not been given access to your location. To remove yourself from future requests please contact the account owner for <span class="number"><?php echo $number; ?></span></p>
                <a href="/locator/mobilerequest">Return to Previous Screen</a>
            </div>
        </div>
        <script type="text/javascript">
            $(function() {
                $("body").css("background", "#555555");
            });
        </script>
    </body>
</html>