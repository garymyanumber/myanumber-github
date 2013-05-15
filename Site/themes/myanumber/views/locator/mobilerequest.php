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
            <p class="loc-desc">The myaNUMBER <span class="number"><?php echo $number; ?></span> is requesting access to your location.</p>
            <input type="button" class="allow" />
            <a href="/locator/mobiledeny?h=<?php echo $_GET['h']; ?>" id="locator-mobile-deny">Deny Access</a>
            <div id="locator-mobile-border-light"></div>
            <p id="lower">AT&amp;T Subscribers click here to allow access to your location with myaNUMBER <span class="number"><?php echo $number; ?></span></p>
            <div id="att-allow">
                <div id="att-logo-small"></div>
                <input type="button" id="allow-att" />
            </div>
            <div class="clear"></div>
            <div id="mobile-lower-border"></div>
            <a href="/locator/mobileprivacy" id="mobile-privacy-policy">Privacy Policy</a>
            <a href="/locator/mobilelearn" id="mobile-learn-more">Learn More</a>
            <div class="clear"></div>
            <div class="hidden">
                <form action="/locator/reportlocation" method="POST">
                    <input type="text" id="lat" name="lat" />
                    <input type="text" id="lon" name="lon" />
                    <input type="text" id="hash" name="hash" />
                    <input type="text" id="accuracy" name="accuracy" />
                </form>
            </div>
        </div>
        <script type="text/javascript">
            var positions = [];
            $(function() {
                if(window.navigator.geolocation) {
                    $(".allow").click(function(e) {
                        e.preventDefault();
                        $(".allow").hide();
                        $(".loc-desc").text("Getting location...");
                        //navigator.geolocation.getCurrentPosition(GetLocation, errorLocation, { maximumAge: 0, enableHighAccuracy: true});
                        navigator.geolocation.watchPosition(GetLocation, errorLocation, {enableHighAccuracy: true, timout: 10000, maximumAge: 0});
                        function GetLocation(location) {
                           positions.push(location);
                           if(positions.length > 5) {
                               var best;
                               for(var i in positions) {
                                   if(best) {
                                       if(positions[i].coords.accuracy < best.coords.accuracy)
                                           best = positions[i];
                                   }
                                   else {
                                       best = positions[i];
                                   }
                               }
                               
                               $("#lat").val(best.coords.latitude);
                               $("#lon").val(best.coords.longitude);
                               $("#hash").val('<?php echo $hash; ?>');
                               $("#accuracy").val(best.coords.accuracy);
                               $("form").submit();
                           }
                        }
                        function errorLocation(error) {
                            alert("There was an error. Please confirm that your location services are turned on and your browser can request your location. ");
                        }
                    });
                    $("#allow-att").click(function(e) {
                        e.preventDefault();
                        location.href = "<?php echo $att; ?>";
                    });
                }
                else {
                    alert("Location API not available on this device.");
                    location.href = "/locator/mobiledeny";
                }
            });
        </script>
    </body>
</html>