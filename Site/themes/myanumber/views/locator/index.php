<script type="text/javascript" src="/scripts/infobox.js"></script>
<div id="map-content">
    <div id="map"><p>Loading map...</p></div>
    <div id="overlay">
        <div id="overlay-top"></div>
        <div id="overlay-middle">
            <?php
            if (count($model->contacts) > 0) {
                for ($i = 0; $i < count($model->contacts); $i++) {
                    ?>
                    <div class="contact c<?php echo ($i + 1); ?>" cid="<?php echo $model->contacts[$i]->id; ?>">
                        <div class="cname"><?php echo $model->contacts[$i]->name; ?></div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <p>Contacts will appear here when they have SMS enabled.</p>
                <?php
            }
            ?>
            <div class="clear"></div>
        </div>
        <div id="overlay-bottom"></div>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        $(window).resize(function() {
            if(this.resizeTO) clearTimeout(this.resizeTO);
            this.resizeTO = setTimeout(function() {
                $(this).trigger('resizeEnd');
            }, 500);
        });
        $(window).bind('resizeEnd', function() {
            var height = $('body').height();
            var width = $('body').width();

            $("#map").height(height);
            $("#inner-content").width(width - 109);
            $("#inner-content").css("padding", "0");
            google.maps.event.trigger(map, 'resize');
        });
        $(".padd").click(function() {
            myanumber.showDialer(function(to) {
                
            });
        });
        
        $(".contact").click(function() {
            var id = $(this).attr("cid");
            var match;
            for(var i in markers) {
                if(markers[i].cid == id)
                    match = markers[i];
            }
            if(match) {
                map.setCenter(match.getPosition());
                if(map.getZoom() < 15)
                    map.setZoom(15);
                for(var box in boxes) {
                    boxes[box].close();
                }
                match.ibox.open(map, match);
            }
            else {
                myanumber.noty.info("Location Request Sent: When the user offers their location it will appear on the map.");
                $.ajax({
                    type: "GET",
                    url: "/locator/getcontactlocation",
                    data: {contact: id},
                    dataType: "json",
                    success: function(data) {
                        if(data) {
                            myanumber.noty.success("Request complete.");
                        }
                        else {
                            myanumber.noty.info("Can't send too many requests at once. Please wait at least 5 minutes to send another request.");
                        }
                    },
                    error: function(err) {
                        myanumber.noty.error("There was an error. Please try again later.");
                    }
                });
            }
        });
        loadMap();
        setInterval(refreshContacts, 10000);
    });
    var map;
    var markers = new Array();
    var boxes = new Array();
    var circles = new Array();
    function loadMap() {
        $('#map').children().remove();
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "/locator/getcontacts",
            success: function(data) {
                var mapOptions = {
                    center: new google.maps.LatLng(47.8205, -121.9619),
                    zoom: 3,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                    
                map = new google.maps.Map(document.getElementById("map"), mapOptions);
                    
                var height = $('body').height();
                var width = $('body').width();

                $("#map").height(height);
                $("#inner-content").width(width - 109);
                $("#inner-content").css("padding", "0");
                
                var bounds = new google.maps.LatLngBounds();
                var doBounds = false;
                
                for(var i = 0; i < data.length; i++) {
                    if(data[i].last_location != null && data[i].last_location != "") {
                        var image = "/images/pin_" + (i + 1) + ".png";
                        var locstring = data[i].last_location.replace("POINT(", "").replace(")", "");
                        var locarr = locstring.split(" ");
                        var marker = new google.maps.Marker({
                            position: new google.maps.LatLng(locarr[0], locarr[1]),
                            animation: google.maps.Animation.DROP,
                            map: map,
                            icon: image,
                            index: i,
                            cid: data[i].id
                        });
                        
                        bounds.extend(marker.getPosition());
                        doBounds = true;

                        var lastAcc = 500;
                    
                        if(parseInt(data[i]['last_accuracy']) != NaN) {
                            lastAcc = parseInt(data[i]['last_accuracy']);
                        }
                    
                        var circle = new google.maps.Circle({
                            map: map,
                            radius: lastAcc,
                            strokeColor: '#FF0000',
                            strokeWeight: 2
                        });
                    
                        circles.push(circle);
                    
                        circle.bindTo('center', marker, 'position');
                    
                        var boxText = document.createElement("div");
                        boxText.style.cssText = "";
                        boxText.innerHTML = "<div class='map-infobox-wrapper' cid='" + data[i].id + "'><div class='update-time'>Last Updated: " + myanumber.date.getShortDateString(data[i].last_location_update) + "</div><div class='map-infobox' cid='" + data[i].id + "'><div class='call'></div><div class='msg'></div><div class='refresh'></div><div class='refresh-loading'></div><div class='name'>" + data[i].name + "</div></div></div>";
                    
                        var myOptions = {
                            content: boxText,
                            disableAutoPan: false,
                            maxWidth: 0,
                            pixelOffset: new google.maps.Size(-120, -200),
                            zIndex: null,
                            boxStyle: {
                                width: "240px"
                            },
                            closeBoxMargin: "5px",
                            closeBoxURL: null,
                            infoBoxClearance: new google.maps.Size(1, 1),
                            isHidden: false,
                            pane: "floatPane",
                            enableEventPropogation: false
                        };
                    
                        var ib = new InfoBox(myOptions);
                        marker.ibox = ib;
                        
                        google.maps.event.addListener(marker, 'click', function() {
                            for(var box in boxes) {
                                boxes[box].close();
                            }
                            
                            this.ibox.open(map, this);
                        });
                        
                        boxes.push(ib);
                    
                        $(".refresh", boxText).click(function() {
                            var contact = $(this).parent().attr("cid");
                            var refreshButton = this;
                            $(refreshButton).hide();
                            $(refreshButton).parent().find(".refresh-loading").show();
                            $.ajax({
                                type: "GET",
                                url: "/locator/getcontactlocation",
                                data: {contact: contact},
                                dataType: "json",
                                success: function(data) {
                                    $(refreshButton).parent().find(".refresh-loading").hide();
                                    $(refreshButton).show();
                                    if(data) {
                                        myanumber.noty.success("Request complete.");
                                    }
                                    else {
                                        myanumber.noty.info("Can't send too many requests at once. Please wait at least 5 minutes to send another request.");
                                    }
                                }
                            });
                        });
                        
                        $(".msg", boxText).click(function() {
                            $.bbq.pushState({
                                url: "/messaging/message"
                            });
                        });
                        
                        $(".call", boxText).click(function() {
                            var cid = $(this).parent().parent().attr("cid");
                            $.bbq.pushState({
                                url: "/calls/status?ids=" + cid + ","
                            });
                        });
                    
                        markers.push(marker);
                    }
                }
                google.maps.event.trigger(map, 'resize');
                if(doBounds) {
                    if(data.length == 1) {
                        var locstring = data[0].last_location.replace("POINT(", "").replace(")", "");
                        var locarr = locstring.split(" ");
                        map.setCenter(new google.maps.LatLng(locarr[0], locarr[1]));
                        map.setZoom(15);
                    }
                    else {
                        map.fitBounds(bounds);
                    }
                }
                else {
                    map.setCenter(new google.maps.LatLng(47.8205, -121.9619));
                    map.setZoom(3);
                }
            },
            error: function(err) {
                myanumber.noty.error("There was an error. Please try again later.");
            }
        });
    }
    
    function refreshContacts() {
        $.ajax({
            type: "GET",
            dataType: "json",
            url: "/locator/getcontacts",
            success: function(data) {
                for(var i = 0; i < data.length; i++) {
                    var counter = 0;
                    for(var ix = 0; ix < markers.length; ix++) {
                        if(data[i].id == markers[ix].cid) {
                            if(data[i].last_location != null && data[i].last_location != "") {
                                var locstring = data[i].last_location.replace("POINT(", "").replace(")", "");
                                var locarr = locstring.split(" ");
                                markers[ix].setPosition(new google.maps.LatLng(locarr[0], locarr[1]));
                            }
                            if(data[i].last_accuracy != null && data[i].last_accuracy != "") {
                                var newRad = parseInt(data[i].last_accuracy);
                                if(newRad != NaN) {
                                    circles[ix].setRadius(newRad);
                                }
                            }
                            if(data[i].last_location_update != null && data[i].last_location_update != "") {
                                $(markers[ix].ibox.getContent()).find('.update-time').text("Last Updated: " + myanumber.date.getShortDateString(data[i].last_location_update));
                            }
                        }
                        else {
                            counter++;
                        }
                    }
                    if(counter == markers.length) {
                        if(data[i].last_location != null && data[i].last_location != "") {
                            var locstring = data[i].last_location.replace("POINT(", "").replace(")", "");
                            var locarr = locstring.split(" ");
                            var image = "/images/pin_" + (i + 1) + ".png";
                            var marker = new google.maps.Marker({
                                position: new google.maps.LatLng(locarr[0], locarr[1]),
                                animation: google.maps.Animation.DROP,
                                map: map,
                                icon: image,
                                index: i,
                                cid: data[i].id
                            });
                            
                            var lastAcc = 500;
                    
                            if(parseInt(data[i]['last_accuracy']) != NaN) {
                                lastAcc = parseInt(data[i]['last_accuracy']);
                            }
                    
                            var circle = new google.maps.Circle({
                                map: map,
                                radius: lastAcc,
                                strokeColor: '#FF0000',
                                strokeWeight: 2
                            });
                    
                            circles.push(circle);
                    
                            circle.bindTo('center', marker, 'position');
                    
                            var boxText = document.createElement("div");
                            boxText.style.cssText = "";
                            boxText.innerHTML = "<div class='map-infobox-wrapper'><div class='update-time'>Last Updated: " + myanumber.date.getShortDateString(data[i].last_location_update) + "</div><div class='map-infobox' cid='" + data[i].id + "'><div class='call'></div><div class='msg'></div><div class='refresh'></div><div class='refresh-loading'></div><div class='name'>" + data[i].name + "</div></div></div>";
                    
                            var myOptions = {
                                content: boxText,
                                disableAutoPan: false,
                                maxWidth: 0,
                                pixelOffset: new google.maps.Size(-120, -200),
                                zIndex: null,
                                boxStyle: {
                                    width: "240px"
                                },
                                closeBoxMargin: "5px",
                                closeBoxURL: null,
                                infoBoxClearance: new google.maps.Size(1, 1),
                                isHidden: false,
                                pane: "floatPane",
                                enableEventPropogation: false
                            };
                    
                            var ib = new InfoBox(myOptions);
                            marker.ibox = ib;
                        
                            google.maps.event.addListener(marker, 'click', function() {
                                for(var box in boxes) {
                                    boxes[box].close();
                                }
                            
                                this.ibox.open(map, this);
                            });
                        
                            boxes.push(ib);
                    
                            $(".refresh", boxText).click(function() {
                                var contact = $(this).parent().attr("cid");
                                var refreshButton = this;
                                $(refreshButton).hide();
                                $(refreshButton).parent().find(".refresh-loading").show();
                                $.ajax({
                                    type: "GET",
                                    url: "/locator/getcontactlocation",
                                    data: {contact: contact},
                                    dataType: "json",
                                    success: function(data) {
                                        $(refreshButton).parent().find(".refresh-loading").hide();
                                        $(refreshButton).show();
                                        if(data) {
                                            myanumber.noty.success("Request complete.");
                                        }
                                        else {
                                            myanumber.noty.info("Can't send too many requests at once. Please wait at least 5 minutes to send another request.");
                                        }
                                    }
                                });
                            });
                        
                            $(".msg", boxText).click(function() {
                                $.bbq.pushState({
                                    url: "/messaging/message"
                                });
                            });
                        
                            $(".call", boxText).click(function() {
                                var cid = $(this).parent().parent().attr("cid");
                                $.bbq.pushState({
                                    url: "/calls/status?ids=" + cid + ","
                                });
                            });
                    
                            markers.push(marker);
                        }
                    }
                }
            },
            error: function(err) {
                myanumber.noty.error("There was an error. Please try again later.");
            }
        });
    }
</script>