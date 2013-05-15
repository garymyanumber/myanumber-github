<!DOCTYPE html>
<html>
    <head>
        <title>myanumber</title>
        <link rel="Stylesheet" type="text/css" href="/css/reset.css" />
        <link rel="Stylesheet" type="text/css" href="/css/site.css" />
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
        <script type="text/javascript" src="/scripts/noty/jquery.noty.js"></script>
        <script type="text/javascript" src="/scripts/noty/layouts/bottom.js?q=<?php echo time(); ?>"></script>
        <script type="text/javascript" src="/scripts/noty/layouts/center.js?q=<?php echo time(); ?>"></script>
        <script type="text/javascript" src="/scripts/noty/layouts/topRight.js?q=<?php echo time(); ?>"></script>
        <script type="text/javascript" src="/scripts/noty/themes/myanumber.js"></script>
    </head>
    <body>
        <div class="page-overlay">
            <div class="contact-overlay">
                <h2>Contact Us</h2>
                <form action="/site/contactform" method="POST">
                    <div class="contact-overlay-content">
                        <h3>Mail Us</h3>
                        <p>2522 N Proctor St. #278<br />
                            Tacoma, WA 98406-5338</p>
                        <h3>Press Inquiries</h3>
                        <p>Please Contact: <br />
                            <a href="mailto:press@myanumber.com">press@myanumber.com</a></p>
                        <h3>Partnership Opportunities</h3>
                        <p>Please Contact:<br />
                            <a href="mailto:partnership@myanumber.com">partnership@myanumber.com</a></p>
                        <h3>Need Help?</h3>
                        <div class="contact-launch-support"></div>
                    </div>
                    <div class="contact-overlay-label" style="margin-top: 0;">Name</div>
                    <div class="contact-overlay-input">
                        <input type="text" name="name" />
                    </div>
                    <div class="contact-overlay-label">Email</div>
                    <div class="contact-overlay-input">
                        <input type="text" name="email" />
                    </div>
                    <div class="contact-overlay-label">Message</div>
                    <div class="contact-overlay-input">
                        <textarea name="body" cols="20" rows="20"></textarea>
                    </div>
                    <input type="hidden" name="subject" value="Contact Form" />
                    <input class="contact-submit" type="submit" value="" />
                </form>
            </div>
            <div class="about-overlay">
                <h2>About Us</h2>
                <div class="about-logo-small"></div>
                <div class="clear"></div>
                <p class="about-content-main">myaNUMBER was built by dads for dads, moms, grandparents and most importantly, kids. myaNUMBER ensures 
                    children are always able to reach a trusted adult by offering a local phone number that rings up to five people in a 
                    family's network of caregivers. The phones ring consecutively in a predefined order and continue ringing until 
                    answered. After each call, an email and text message report is sent to each caregiver. myaNUMBER further 
                    supports family communication through rich features like Family Locator, Group Texting, and In-App Calling \
                    services that function across carriers, phone plans, and devices.</p>
                <div class="about-names"></div>
                <div class="about-in-touch">
                    <h2>Get In Touch</h2>
                    <h3>Press Inquiries</h3>
                    <p>Please Contact:<br />
                        <a href="mailto:johanna@myanumber.com">johanna@myanumber.com</a></p>
                    <h3>Partnership Opportunities</h3>
                    <p>Please Contact:<br />
                        <a href="mailto:partnership@myanumber.com">partnership@myanumber.com</a></p>
                    <div class="about-john">
                        <div class="john-image"></div>
                        <div class="about-name">John Wantz | Chief-Dad</div>
                        <div class="about-job">Product &amp; Design</div>
                        <div class="social-links"><a target="_blank" href="http://twitter.com/wantzjt">Twitter</a> | <a class="lnkd" target="_blank" href="http://www.linkedin.com/in/johntwantz">LinkedIn</a></div>
                    </div>
                    <div class="about-kyle">
                        <div class="kyle-image"></div>
                        <div class="about-name">Kyle Schei | Ops-Dad</div>
                        <div class="about-job">Operations &amp; Finance</div>
                        <div class="social-links"><a target="_blank" href="http://twitter.com/myanumber">Twitter</a> | <a class="lnkd" target="_blank" href="http://www.linkedin.com/in/kyleschei/">LinkedIn</a></div>
                    </div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            $(function() {
                $(".page-overlay").click(function(e) {
                    if($(e.target).attr("class") == "page-overlay") {
                        $(this).hide();
                        $(".contact-overlay").hide();
                        $(".about-overlay").hide();
                    }
                });
                $("#contact-link").click(function(e) {
                    e.preventDefault();
                    $(".page-overlay").show();
                    $(".contact-overlay").show();
                    $(".contact-launch-support").click(function() {
                        $(".contact-overlay").hide();
                        $(".about-overlay").hide();
                        $(".page-overlay").hide();
                        GSFN.loadWidget(3595,{
                            "containerId":""
                        }, false, function(api, tab, container) {
                            api.showModal();
                        });
                    });
                });
                $("#about-link").click(function(e) {
                    e.preventDefault();
                    $(".page-overlay").show();
                    $(".about-overlay").show();
                });
                $(".contact-submit").click(function(e) {
                    e.preventDefault();
                    var action = $(this).parent().attr("action");
                    $("input[name='name']").css("border", "none");
                    $("input[name='email']").css("border", "none");
                    $("textarea[name='body']").css("border", "none");
                    $.ajax({
                        type: "POST",
                        data: $(this).parent().serialize(),
                        url: action,
                        dataType: 'json',
                        success: function(rsp) {
                            console.log(rsp);
                            if(rsp.success) {
                                $(".contact-overlay").hide();
                                $(".about-overlay").hide();
                                $(".page-overlay").hide();
                                var msg = "Thank you. Your email will be reviewed.";
                                noty({
                                    layout: 'center',
                                    text: '',
                                    theme: 'myanumber',
                                    type: 'success',
                                    closeWith: ['click'],
                                    timeout: 5000,
                                    template: '<div class="noty-box"><div class="grey-top"></div><div class="grey-body"><div class="white-top"></div><div class="white-middle"><div class="success"></div><div class="noty-text">' + msg + '</div></div><div class="white-bottom"></div></div><div class="grey-bottom"></div></div>'
                                });
                            }
                            else {
                                if(rsp.errors.name) {
                                    $("input[name='name']").css("border", "solid 1px #FF0000");
                                }
                                if(rsp.errors.email) {
                                    $("input[name='email']").css("border", "solid 1px #FF0000");
                                }
                                if(rsp.errors.body) {
                                    $("textarea[name='body']").css("border", "solid 1px #FF0000");
                                }
                                if(rsp.msg) {
                                    noty({
                                        layout: 'center',
                                        text: '',
                                        theme: 'myanumber',
                                        type: 'error',
                                        closeWith: ['click'],
                                        timeout: false,
                                        template: '<div class="noty-box"><div class="grey-top"></div><div class="grey-body"><div class="white-top"></div><div class="white-middle"><div class="error"></div><div class="noty-text">' + rsp.msg + '</div></div><div class="white-bottom"></div></div><div class="grey-bottom"></div></div>'
                                    });
                                }
                            }
                        }
                    });
                });
            });
        </script>
        <div id="home-content">
            <div id="home-header">
                <div id="home-header-wrapper">
                    <ul>
                        <li class="login">
                            <a href="/login">
                                <img alt="Login" src="/images/login_button.png" />
                            </a>
                        </li>
                        <li class="signup">
                            <a href="/register">
                                <img alt="Signup" src="/images/signup_button.png" />
                            </a>
                        </li>
                    </ul>
                    <div class="clear"></div>
                </div>
            </div>
            <div id="home-feature-background">
                <div id="home-feature">
                    <div id="home-feature-two"></div>
                    <h1>One phone number for your family.</h1>
                    <ul>
                        <li>Give your kids a single ten-digit number to reach a trusted adult.</li>
                        <li>Ensure children can always reach a trusted adult when they need to.</li>
                        <li>Use everyone's existing phones - landlines & cellular devices on all carriers.</li>
                        <li>Your family stays updated with text and email reports after each call.</li>
                    </ul>
                    <div id="home-feature-get-started"></div>
                </div>
            </div>
            <div class="clear"></div>
            <div id="home-slider">
                <div id="home-slider-feature">
                    <div id="home-slider-content">
                        <div class="home-slider-item">
                            <img alt="" src="/images/homepage/slider/slider1.png" />
                        </div>
                        <div class="home-slider-item">
                            <img alt="" src="/images/homepage/slider/slider2.png" />
                        </div>
                        <div class="home-slider-item">
                            <img alt="" src="/images/homepage/slider/slider3.png" />
                        </div>
                        <div class="home-slider-item">
                            <img alt="" src="/images/homepage/slider/slider4.png" />
                        </div>
                    </div>
                    <div id="home-slider-left"></div>
                    <div id="home-slider-right"></div>
                </div>
                <script type="text/javascript">
                    $(function() {
                        var items = $(".home-slider-item");
                        var animating = false;
                        var interval;
                        $("#home-slider-left").click(function(e) {
                            e.preventDefault();
                            clearInterval(interval);
                            if(!animating) {
                                $("#home-slider-content").width(2000);
                                $("#home-slider-content").css("left", "-1000px");
                                var last = $(".home-slider-item").last();
                                $(".home-slider-item").last().remove();
                                animating = true;
                                $("#home-slider-content").prepend(last).animate({"left": 0}, "slow", function() {
                                    animating = false;
                                    $("#home-slider-content").width(1000);
                                    interval = setInterval("rightClick()", 5000);
                                });
                            }
                        });
                        $("#home-slider-right").click(function(e) {
                            e.preventDefault();
                            clearInterval(interval);
                            if(!animating) {
                                animating = true;
                                $("#home-slider-content").width(2000).animate({"left": -1000}, "slow", function() {
                                    animating = false;
                                    var first = $(".home-slider-item").first();
                                    $(".home-slider-item").first().remove();
                                    $("#home-slider-content").width(1000).css("left", 0).append(first);
                                    interval = setInterval("rightClick()", 5000);
                                });
                            }
                        });
                        interval = setInterval("rightClick()", 5000);
                    });
                    function rightClick() {
                        $("#home-slider-right").click();
                    }
                </script>
            </div>
            <div class="homepage-feature-5">
                <h1>$9.99/Month - No Contract Required</h1>
                <div class="sign-up-button"></div>
                <br/>
                <br/>
                <br/>
                <div class="homepage-separator"></div>
            </div>
            <div id="homepage-feature-2">
                <div id="homepage-locator-icon"></div>
                <div id="homepage-phone"></div>
                <h1>Locator</h1>
                <p>Coordinating the family is a lot easier when you can see everyone on a map. Send a text message or initiate a phone call right from the Locator map.</p>
            </div>
            <div class="homepage-separator"></div>
            <div id="homepage-feature-3">
                <div id="homepage-computer"></div>
                <div id="homepage-phone-icon"></div>
                <h1>Calls</h1>
                <p>Everyone calls from a phone, now try calling from the web. Log into the myaNUMBER website to dial one of the caregivers through your computer or kick off a call to your myaNUMBER. </p>
            </div>
            <div class="homepage-separator"></div>
            <div id="homepage-feature-4">
                <div id="homepage-message-icon"></div>
                <div id="homepage-computer-messages"></div>
                <h1>Messages</h1>
                <p>myaNUMBER also provides group texting for all the caregivers on your account. You can even start messages from the website and receive them on your mobile phone.</p>
            </div>
            <div class="homepage-feature-5">
                <h1>$9.99/Month - No Contract Required</h1>
                <div class="sign-up-button"></div>
            </div>
            <div id="homepage-feature-6">
                <p>What people are saying about <span class="orange">mya</span><span class="blue">NUMBER</span></p>
                <div id="homepage-feature-6-border"></div>
                <div id="homepage-feature-6-shouts">
                    <div class="shout-item">
                        <div class="shout-top"></div>
                        <div class="shout-content">
                            I love the Locator! It is so much easier to coordinate picking up and dropping off children when you can see everyone on a map.
                        </div>
                        <div class="shout-bottom"></div>
                        <div class="shout-image">
                            <div class="shout-image-top"></div>
                            <div class="shout-image-middle">
                                <img alt="" src="/images/homepage/shout/shout_image2.png" />
                            </div>
                            <div class="shout-image-bottom"></div>
                        </div>
                        <div class="shout-name">Marisa Solis</div>
                        <div class="shout-description">Los Angeles, CA.</div>
                    </div>
                    <div class="shout-item">
                        <div class="shout-top"></div>
                        <div class="shout-content">
                            myaNumber is a great for the whole family. Both my kids and parents will have one number to call when they need to reach my wife and I.
                        </div>
                        <div class="shout-bottom"></div>
                        <div class="shout-image">
                            <div class="shout-image-top"></div>
                            <div class="shout-image-middle">
                                <img alt="" src="/images/homepage/shout/shout_image3.png" />
                            </div>
                            <div class="shout-image-bottom"></div>
                        </div>
                        <div class="shout-name">Scott Erickson</div>
                        <div class="shout-description">Tacoma, WA.</div>
                    </div>
                    <div class="shout-item last">
                        <div class="shout-top"></div>
                        <div class="shout-content">
                            My husband and I are very busy people. My day is more peaceful knowing that will NEVER miss an important call from our children.
                        </div>
                        <div class="shout-bottom"></div>
                        <div class="shout-image">
                            <div class="shout-image-top"></div>
                            <div class="shout-image-middle">
                                <img alt="" src="/images/homepage/shout/shout_image5.png" />
                            </div>
                            <div class="shout-image-bottom"></div>
                        </div>
                        <div class="shout-name">Nicole Greer</div>
                        <div class="shout-description">Fresno, CA.</div>
                    </div>
                    <div class="shout-item hidden">
                        <div class="shout-top"></div>
                        <div class="shout-content">
                            As the family quarterback I use the group text messaging feature to keep a few people on the same page regardless of whether they use new phones or old phones.
                        </div>
                        <div class="shout-bottom"></div>
                        <div class="shout-image">
                            <div class="shout-image-top"></div>
                            <div class="shout-image-middle">
                                <img alt="" src="/images/homepage/shout/shout_image7.png" />
                            </div>
                            <div class="shout-image-bottom"></div>
                        </div>
                        <div class="shout-name">Lindsay Rogers</div>
                        <div class="shout-description">Seattle, WA.</div>
                    </div>
                    <div class="shout-item hidden">
                        <div class="shout-top"></div>
                        <div class="shout-content">
                            I've really enjoyed staying in touch with my busy family through the Locator map. We've used it almost everyday to updated on our family's location.
                        </div>
                        <div class="shout-bottom"></div>
                        <div class="shout-image">
                            <div class="shout-image-top"></div>
                            <div class="shout-image-middle">
                                <img alt="" src="/images/homepage/shout/shout_image6.png" />
                            </div>
                            <div class="shout-image-bottom"></div>
                        </div>
                        <div class="shout-name">Adam Dow</div>
                        <div class="shout-description">Lynden, WA.</div>
                    </div>
                    <div class="shout-item hidden last">
                        <div class="shout-top"></div>
                        <div class="shout-content">
                            myaNUMBER is an elegant solution for my family to have one phone number. Now my family and friends just remember this one number to stay connected.
                        </div>
                        <div class="shout-bottom"></div>
                        <div class="shout-image">
                            <div class="shout-image-top"></div>
                            <div class="shout-image-middle">
                                <img alt="" src="/images/homepage/shout/miggy_mya.jpg" />
                            </div>
                            <div class="shout-image-bottom"></div>
                        </div>
                        <div class="shout-name">Juan Miguel</div>
                        <div class="shout-description">Los Angeles, CA.</div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div id="shout-selector">
                    <div class="shout-dot selected"></div>
                    <!--<div class="shout-dot"></div>-->
                    <div class="shout-dot last prev"></div>
                </div>
            </div>
            <script type="text/javascript">
                $(function() {
                    $("#shout-selector div").click(function() {
                        if($(this).hasClass("last")) {
                            $($("#homepage-feature-6-shouts .shout-item")[0]).addClass("hidden");
                            $($("#homepage-feature-6-shouts .shout-item")[1]).addClass("hidden");
                            $($("#homepage-feature-6-shouts .shout-item")[2]).addClass("hidden");
                            $($("#homepage-feature-6-shouts .shout-item")[3]).removeClass("hidden");
                            $($("#homepage-feature-6-shouts .shout-item")[4]).removeClass("hidden");
                            $($("#homepage-feature-6-shouts .shout-item")[5]).removeClass("hidden");
                            $("#shout-selector div.selected").addClass("prev");
                            $("#shout-selector div.selected").removeClass("selected");
                            $(this).removeClass("prev");
                            $(this).addClass("selected");
                        }
                        else {
                            $($("#homepage-feature-6-shouts .shout-item")[0]).removeClass("hidden");
                            $($("#homepage-feature-6-shouts .shout-item")[1]).removeClass("hidden");
                            $($("#homepage-feature-6-shouts .shout-item")[2]).removeClass("hidden");
                            $($("#homepage-feature-6-shouts .shout-item")[3]).addClass("hidden");
                            $($("#homepage-feature-6-shouts .shout-item")[4]).addClass("hidden");
                            $($("#homepage-feature-6-shouts .shout-item")[5]).addClass("hidden");
                            $("#shout-selector div.selected").addClass("prev");
                            $("#shout-selector div.selected").removeClass("selected");
                            $(this).removeClass("prev");
                            $(this).addClass("selected");
                        }
                    }) ;
                });
                setInterval(rotateShout, 10000);
                function rotateShout() {
                    $("#shout-selector div.prev").click();
                }
            </script>
            <div id="homepage-footer">
                <div id="homepage-footer-content">
                    <div id="homepage-twitter">
                        <script type="text/javascript">
                            $(function() {
                                $("#homepage-twitter").click(function() {
                                    window.open("http://www.twitter.com/myanumber");
                                });
                            });
                        </script>
                    </div>      
                    <div id="homepage-facebook">
                        <script type="text/javascript">
                            $("#homepage-facebook").click(function() {
                                window.open("http://www.facebook.com/myanumber");
                            });
                        </script>
                    </div>
                    <div id="homepage-pinterest">
                        <script type="text/javascript">
                            $("#homepage-pinterest").click(function() {
                                window.open("https://pinterest.com/myanumber/");
                            });
                        </script>
                    </div>
                    <div id="homepage-tumblr">
                        <script type="text/javascript">
                            $("#homepage-tumblr").click(function() {
                                window.open("http://myanumber.tumblr.com/");
                            });
                        </script>
                    </div>
                    <div id="homepage-logo-dark"></div>	
                    <ul>
                        <li>
                            <a id="about-link" href="/site/about">About Us</a>
                        </li>
                        <li class="light" id="support-link"></li>
                        <li>
                            <a href="/privacy">Privacy</a>
                        </li>
                        <li>
                            <a href="/terms">Terms</a>
                        </li>
                        <li>
                            <a id="contact-link" href="/site/contact">Contact Us</a>
                        </li>
                        <li>
                            <a href="#">Facebook</a>
                        </li>
                        <li>
                            <a href="#">Twitter</a>
                        </li>
                    </ul>
                    <div id="homepage-copyright">Copyright &#169; 2012 myaNUMBER. All Rights Reserved</div>
                </div>
            </div>
        </div>
        <script type="text/javascript">
            $(function() {
                $(".sign-up-button").click(function(e){
                    e.preventDefault();
                    location.href = "/register";
                });
                $("#home-feature-get-started").click(function(e){
                    e.preventDefault();
                    location.href = "/register";
                });
            });
        </script>
        <div id="getsat-widget-3595"></div>
        <script type="text/javascript" src="https://loader.engage.gsfn.us/loader.js"></script>
        <script type="text/javascript">
            if (typeof GSFN !== "undefined") { GSFN.loadWidget(3595,{"containerId":"support-link"}); }
        </script>
    </body>
</html>