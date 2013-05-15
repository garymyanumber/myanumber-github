var myanumber = {
    call: {},
    note: {},
    phono: {},
    currentCall: {},
    dialerCallback: {},
    msgCount: 0,
    load: function() {
        $("#left-nav a").click(function(e) {
            e.preventDefault();
            var href = $(this).attr( "href" );		    
            if(href=="#")
                return false;	
 
            $.bbq.pushState({
                url: href
            });
        });
        
        $(window).bind( "hashchange", function(e) {	    
            var url = $.bbq.getState( "url" );    		
 
            if(!url) {
                $("#left-nav a:first").click();
                return false;
            }
 
            $("a").each(function(){
                var href = $(this).attr( "href" );
 
                if ( url.indexOf(href) != -1 ) {
                    $(this).addClass( "current" );
                    $(this).parent("li").addClass( "active" );
 
                } else {
                    $(this).removeClass( "current" );
                    $(this).parent("li").removeClass( "active" );
                }
            });
            
            if(url.indexOf("locator") == -1) {
                $("#inner-content").removeAttr("style");
            }
 
            if(url.indexOf("logout") != -1) {
                location.href = url;
            }
            else {
                $("#inner-content").load(url, function() {
                
                    });
            }
 
        });
 
        $(window).trigger( "hashchange" );
        
        myanumber.phono = $.phono({
            apiKey: "31270a063a5f55f93fc40ef8d649b039",
            onReady: function() {
                $.ajax({
                    type: "POST",
                    data: {
                        sid: this.sessionId
                    },
                    url: "/calls/savephonosession",
                    success: function() {}
                });
            },
            phone: {
                onIncomingCall: function(event) {
                    myanumber.currentCall = event.call;
                    myanumber.showCall("Incoming Call");
                }
            }
        });
        
        this.initializeDialer();
        
        $.get("/messaging/getmessagecount", function(count) {
            myanumber.msgCount = count;
        });
        
        setInterval(myanumber.checkMessages, 5000);
        
        if (navigator.userAgent.toLowerCase().indexOf("chrome") >= 0) {
            $(window).load(function(){
                $('input:-webkit-autofill').each(function(){
                    var text = $(this).val();
                    var name = $(this).attr('name');
                    $(this).after(this.outerHTML).remove();
                    $('input[name=' + name + ']').val(text);
                });
            });
        }
    },
    checkMessages: function() {
        $.get("/messaging/getmessagecount", function(count) {
            if(myanumber.msgCount != count) {
                var bbqstate = $.bbq.getState();
                if(bbqstate.url != "/messaging/message")
                    myanumber.noty.textnoty();
            }
            myanumber.msgCount = count;
        });
    },
    initializeDialer: function() {
        $("#dialer-digits div").click(function(e) {
            e.preventDefault();
            var idarr = $(this).attr("id").split("");
            if(idarr[0] == "d") {
                var currNum = $("#dialer-number").text();
                if(currNum.length < 12) {
                    currNum += idarr[1];
                    $("#dialer-number").text(currNum.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3'));
                }
            }
            if($(this).attr("id") == "function") {
                $("#dialer-overlay").click();
                var to = $("#dialer-number").text();
                myanumber.dialerCallback(to);
                myanumber.dialerCallback = {};
            }
            if($(this).attr("id") == "backspace") {
                var currNum = $("#dialer-number").text();
                $("#dialer-number").text(currNum.substring(0, currNum.length - 1).replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3'));
            }
        });
            
        $("#dialer-overlay").click(function(e) {
            if(e.target == $("#dialer-overlay")[0]) {
                $("#dialer").stop().animate({
                    top: "-534px"
                }, 'slow', function() {
                    $("#dialer-overlay").hide();
                });
            }
        });
    },
    showDialer: function(callbackSubmit) {
        $("#dialer-overlay").show();
        $("#dialer-number").text("");
        $("#dialer").stop().animate({
            top: "50%"
        }, 'slow');
        myanumber.dialerCallback = callbackSubmit;
    },
    showCall: function (number) {
        myanumber.note = noty({
            layout: 'bottom',
            text: '',
            theme: 'myanumber',
            type: 'info',
            closeWith: [''],
            callback: {
                onShow: function() {
                    $("#noty-call-answer").click(function(e) {
                        myanumber.note.close();
                        $("#left-nav .calls a").click();
                        if(myanumber.currentCall) {
                            myanumber.currentCall.answer();
                            myanumber.currentCall.volume(100);
                            $.bbq.pushState({
                                url: "/calls/status"
                            });
                        }
                    });
                    $("#noty-call-mute").click(function(e) {
                        if(myanumber.currentCall) {
                            myanumber.currentCall.hangup();
                        }
                        myanumber.note.close();
                    });
                }
            },
            template: '<div id="noty-call-template"><ul><li id="noty-call-name">' + number + '</li><li id="noty-call-answer"></li><li id="noty-call-mute"></li></ul></div>'
        });
    },
    doSupport: function() {
        GSFN.loadWidget(3595,{
            "containerId":"support-link"
        }, false, function(api, tab, container) {
            api.showModal();
        });
    },
    date: {
        getDateString: function(epoch) {
            var date = new Date(epoch * 1000);
            return date.format("mmmm dd, yyyy @ HH:MM");
        },
        getShortDateString: function(epoch) {
            var date = new Date(0);
            date.setUTCSeconds(epoch);
            var hrs = myanumber.date.formatAMPM(date);
            return date.format("mm/dd/yy - ") + hrs;
        },
        formatAMPM: function(date) {
            var hours = date.getHours();
            var minutes = date.getMinutes();
            var ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            minutes = minutes < 10 ? '0'+minutes : minutes;
            var strTime = hours + ':' + minutes + '' + ampm;
            return strTime;
        }
    },
    noty: {
        success: function(msg) {
            noty({
                layout: 'center',
                text: '',
                theme: 'myanumber',
                type: 'success',
                closeWith: ['click'],
                timeout: 5000,
                template: '<div class="noty-box"><div class="grey-top"></div><div class="grey-body"><div class="white-top"></div><div class="white-middle"><div class="success"></div><div class="noty-text">' + msg + '</div></div><div class="white-bottom"></div></div><div class="grey-bottom"></div></div>'
            });
        },
        info: function(msg) {
            noty({
                layout: 'center',
                text: '',
                theme: 'myanumber',
                type: 'info',
                closeWith: ['click'],
                timeout: 30000,
                template: '<div class="noty-box"><div class="grey-top"></div><div class="grey-body"><div class="info"></div><div class="noty-text">' + msg + '</div></div><div class="grey-bottom"></div></div>'
            });
        },
        error: function(msg) {
            noty({
                layout: 'center',
                text: '',
                theme: 'myanumber',
                type: 'error',
                closeWith: ['click'],
                timeout: false,
                template: '<div class="noty-box"><div class="grey-top"></div><div class="grey-body"><div class="white-top"></div><div class="white-middle"><div class="error"></div><div class="noty-text">' + msg + '</div></div><div class="white-bottom"></div></div><div class="grey-bottom"></div></div>'
            });
        },
        textnoty: function() {
            noty({
                layout: 'topRight',
                text: '',
                theme: 'myanumber',
                type: 'info',
                closeWith: ['click'],
                timeout: 5000,
                template: '<div class="noty-info-box"><div class="noty-info-text">You have received a text message.</div></div>'
            });
        },
        deactivate: function(template) {
            return noty({
                layout: 'center',
                text: '',
                theme: 'myanumber',
                type: 'info',
                closeWith: [''],
                timeout: 0,
                template: template
            });
        }
    }
}
window.onbeforeunload = function() {
    $.ajax({
        type: "POST",
        async: false,
        url: "/calls/removephonosession",
        success: function() {}
    });
}