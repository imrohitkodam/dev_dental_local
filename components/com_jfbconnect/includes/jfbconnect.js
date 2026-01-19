/**
 * @package JFBConnect
 * @copyright (C) 2009-2019 by Source Coast - All rights reserved
 * @website http://www.sourcecoast.com/
 * @website http://www.sourcecoast.com/joomla-facebook/
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

/**
 * Ensure global _gaq Google Anlaytics queue has be initialized.
 * @type {Array}
 */
;
var _gaq = _gaq || [];

var jfbc = {
    base: null,
    return_url: null,
    token: 't', // have a default, but this should be filled in
    login: {
        show_modal: false,
        scope: null, // Permissions JFBConnect requests
        logout_facebook: false,
        logged_in: true,
        login_started: false, // Prevents multiple concurrent login attempts, mainly caused by the auto-login feature enabled
        use_popup: true,

        provider: function (name)
        {
            if ((name == 'facebook') && jfbc.login.use_popup)
            {
                jfbc.debug.log('using popup' + jfbc.login.use_popup);
                jfbc.login.facebook();
            }
            else
                self.location = jfbc.base + 'index.php?option=com_jfbconnect&task=authenticate.login&provider=' + name + '&return=' + jfbc.return_url + '&' + jfbc.token + '=1';
        },

        // Special method for Facebook. Should not be called directly in case it changes. Use jfbc.login.provider('facebook') instead
        facebook: function ()
        {
            FB.login(function (response)
            {
                if (!jfbc.login.logged_in)
                    jfbc.login.facebook_onlogin();
                else
                    jfbc.permissions.fetch();
            }, {
                scope: jfbc.login.scope
            });
        },

        // v5.2 - Move to jfbc.login.provider(xyz)
        google: function ()
        {
            jfbc.login.provider('google');
        },

        // Deprecated. Use jfbc.login.login_custom();
        login_custom: function ()
        {
            jfbc.debug.log("jfbc.login.login_custom is deprecated. Please use jfbc.login.provider('facebook') instead");
            jfbc.login.provider('facebook');

        },

        // Action to perform after authentication on FB has occurred
        facebook_onlogin: function ()
        {
            if (!jfbc.login.login_started)
            {
                jfbc.login.login_started = true;
                FB.getLoginStatus(function (response)
                {
                    if (response.status === 'connected')
                    {
                        jfbcJQuery(document).one("jfbc-permissions-fetched", function ()
                        {
                            jfbc.debug.log("Login checks");
//                            if (jfbc.permissions.check('email'))
//                            {
                            jfbc.debug.log("Logging in");
                            if (jfbc.login.show_modal == '1')
                            {
                                // First, hide the SCLogin modal if it's there
                                jfbcJQuery('#login-modal').modal('hide');
                                jfbcJQuery("#jfbcLoginModal").css({"margin-left": function ()
                                {
                                    return -(jfbcJQuery("#jfbcLoginModal").width() / 2)
                                }});
                                jfbcJQuery("#jfbcLoginModal").modal();
                            }
                            self.location = jfbc.base + 'index.php?option=com_jfbconnect&task=authenticate.login&provider=facebook&return=' + jfbc.return_url + '&' + jfbc.token + '=1';
//                            }
                            jfbc.debug.log("Done with checks");
                        });
                        // Start the check_permissions asynchronous check. This will fire the code above.
                        jfbc.permissions.fetch();
                    }
                });
            }
            jfbc.login.login_started = false;
        },

        logout_button_click: function ()
        {
            if (jfbc.login.logout_facebook)
            {
                FB.getLoginStatus(function (response)
                {
                    if (response.status === 'connected')
                    {
                        FB.logout(function (response)
                        {
                            jfbcJQuery(document).trigger("jfbc-provider-logout-done");
                        });
                    }
                    else
                    {
                        jfbcJQuery(document).trigger("jfbc-provider-logout-done");
                    }
                });
            }
            else
            {
                jfbcJQuery(document).trigger("jfbc-provider-logout-done");
            }
        },
        logout: function (redirect)
        {
            jfbcJQuery(document).one("jfbc-provider-logout-done", function ()
            {
                window.location = jfbc.base + 'index.php?option=com_users&task=user.logout&return=' + redirect + '&' + jfbc.token + '=1';
            });
            jfbc.login.logout_button_click();
        }
    },
    permissions: {
        // scope: comma-separated list of permissions to check
        check: function (scope)
        {
            var c = jfbc.cookie.get('jfbconnect_permissions_granted');
            if (c === null)
                return false;

            var permissions = [];
            permissions = jfbcJQuery.parseJSON(c);

            var checkScope = scope.split(',');
            var scopeFound = true;
            jfbcJQuery.each(checkScope, function (k, v)
            {
                if (jfbcJQuery.inArray(v, permissions) == -1)
                    scopeFound = false;
            });
            return scopeFound;
        },

        // Called at initialization
        // If called independently, use the jfbcJQuery.one("jfbc-permissions-fetched", ..) code to check new values
        fetch: function ()
        {
            jfbc.debug.log("permissions_fetch");

            FB.api('/me/permissions', function (response)
            {
                var permissions = "";
                if (response.data !== undefined && jfbcJQuery.isArray(response.data))
                {
                    jfbcJQuery.each(response.data, function (k, v)
                    {
                        // Check for v2.0 of Graph API
                        if ('permission' in v)
                        {
                            if (v.status == "granted")
                                permissions = permissions + '","' + v.permission;
                        }
                        else
                        {
                            jfbcJQuery.each(v, function (perm, value)
                            {
                                permissions = permissions + '","' + perm;
                            });
                        }
                    });
                    // Can't use JSON.stringify as it's incompatible with IE7 :(
                    permissions = permissions + '"';
                    permissions = permissions.substring(2, permissions.length);

                    jfbc.cookie.set('jfbconnect_permissions_granted', "[" + permissions + "]");
                }
                jfbcJQuery(document).trigger("jfbc-permissions-fetched");
            });
        },
        // newScope = comma-separated list of scope to request
        update_scope: function (newScope)
        {
            var jfbcScope = jfbc.login.scope.split(',');
            newScope = newScope.split(',');
            newScope = jfbcJQuery.merge(jfbcScope, newScope);
            newScope = jfbcJQuery.grep(newScope, function (v, k)
            { // Create a new scope array with no duplicates
                return jfbcJQuery.inArray(v, newScope) === k;
            });
            jfbc.login.scope = newScope.join(',');
            jfbc.debug.log("update_scope: Now set to " + jfbc.login.scope);
        }
    },

    social: {
        facebook: {
            comment: {
                create: function (response)
                {
                    var title = window.btoa(document.title);
                    var url = 'option=com_jfbconnect&task=social.comment&type=create&href=' + encodeURIComponent(escape(response.href)) + '&commentID=' + response.commentID + '&title=' + title;
                    jfbc.util.ajax(url, null);
                },
                remove: function (response)
                {
                    var title = window.btoa(document.title);
                    var url = 'option=com_jfbconnect&task=social.comment&type=remove&href=' + encodeURIComponent(escape(response.href)) + '&commentID=' + response.commentID + '&title=' + title;
                    jfbc.util.ajax(url, null);
                }
            },
        },
        linkedin: {
            share: function ()
            {
                var href = document.location.href;
                var url = 'option=com_jfbconnect&task=social.share&provider=linkedin&share=share&type=create&href=' + encodeURIComponent(escape(href)) + '&title=' + document.title;
                jfbc.util.ajax(url, null);
            }
        },
        twitter: {
            tweet: function (intentEvent)
            {
                if (!intentEvent) return;
//            var id = intentEvent.tweet_id;
//            var username = intentEvent.screen_name;
                var href = document.location.href;
                var url = 'option=com_jfbconnect&task=social.share&provider=twitter&share=tweet&type=create&href=' + encodeURIComponent(escape(href)) + '&title=' + document.title;
                jfbc.util.ajax(url, null);
            }
        },
        /**
         * Tracks Facebook likes, unlikes and sends by suscribing to the Facebook
         * JSAPI event model. Note: This will not track facebook buttons using the
         * iFrame method.
         */
        googleAnalytics: {
            trackFacebook: function ()
            {
                var opt_pageUrl = window.location;
                try
                {
                    if (FB && FB.Event && FB.Event.subscribe)
                    {
                        FB.Event.subscribe('message.send', function (targetUrl)
                        {
                            _gaq.push(['_trackSocial', 'facebook', 'send',
                                targetUrl, opt_pageUrl]);
                        });
                        FB.Event.subscribe('comment.create', function (targetUrl)
                        {
                            _gaq.push(['_trackSocial', 'facebook', 'comment',
                                targetUrl, opt_pageUrl]);
                        });
                        FB.Event.subscribe('comment.remove', function (targetUrl)
                        {
                            _gaq.push(['_trackSocial', 'facebook', 'uncomment',
                                targetUrl, opt_pageUrl]);
                        });
                    }
                }
                catch (e)
                {
                }
            }
        },

        // Not published yet. Need to figure out the best way to incorporate this into pages
        feedPost: function (title, caption, description, url, picture)
        {
//            javascript:jfbc.social.feedPost('SourceCoast JE', 'My caption', 'Great extensions!', 'http://www.sourcecoast.com/', 'https://www.sourcecoast.com/images/stories/extensions/jfbconnect/home_jfbconn.png');
            var obj = {
                method: 'feed',
                link: url,
                picture: picture,
                name: title, // page title?
                caption: caption,
                description: description
            };

            function callback(response)
            {
            }

            FB.ui(obj, callback);
        },

        share: function ()
        {
            var element = jfbcJQuery('.jfbcsocialshare');
            if (!element.length)
                element = jfbcJQuery('<div class="jfbcsocialshare"></div>').appendTo('body');
            jfbcJQuery('.jfbcsocialshare').fadeIn(1000);
        }
    },

    canvas: {
        checkFrame: function ()
        {
            if (top == window)
            { // crude check for any frame
                if (window.location.search == "")
                    top.location.href = window.location.href + '?jfbcCanvasBreakout=1';
                else
                    top.location.href = window.location.href + '&jfbcCanvasBreakout=1';
            }
        }
    },

    request: {
        currentId: null,
        popup: function (jfbcReqId)
        {
            jfbc.request.currentId = jfbcReqId;
            data = jfbcRequests[jfbcReqId];
            FB.ui({method: 'apprequests', display: 'popup',
                message: data.message,
                title: data.title,
                data: jfbcReqId
            }, jfbc.request.fbCallback);

        },

        fbCallback: function (response)
        {
            if (response != null)
            {
                var rId = response.request;
                var to = response.to;

                var toQuery = "";
                for (var i = 0; i < to.length; i++)
                    toQuery += "&to[]=" + to[i];

                var query = 'option=com_jfbconnect&task=request.requestSent&requestId=' + rId + toQuery + '&jfbcId=' + jfbc.request.currentId;
                jfbc.util.ajax(query, jfbc.request.redirectToThanks);
            }
        },

        redirectToThanks: function ()
        {
            data = jfbcRequests[jfbc.request.currentId];
            if (data.thanksUrl != "" && (window.location.href != data.thanksUrl))
                window.location.href = data.thanksUrl;
        }
    },
    popup: {
        display: function (ret)
        {
            var deferred = jfbcJQuery.Deferred();
            var data = jfbcJQuery.parseJSON(ret);
            jfbcJQuery(data.target).html(data.html);

            var buttons = {};
            if (data.buttons)
            {
                for (var i = 0; i < data.buttons.length; i++)
                {
                    var button = data.buttons[i];
                    buttons[button.name] = {
                        text: button.name,
                        id: button.id,
                        click: function ()
                        {
                            var action = jfbc.get(button.action);
                            action();
                        }
                    };
                }
            }
            buttons["Cancel"] = {
                text: "Cancel",
                id: 'jfbc-popup-close',
                click: function ()
                {
                    jfbcJQuery(this).dialog("close");
                }
            };
            jfbcJQuery(data.target).dialog({buttons: buttons, title: data.title,
                width: '50%',
                close: function ()
                {
                    jfbcJQuery(data.target).html("");
                }
            });
            jfbcJQuery(data.target).css('display', 'block');
            // Return our promise. This is always resolved (no fail), but need the promise so it can be done after AJAX calls
            deferred.resolve();
            return deferred;
        }
    },

    get: function (prop)
    {
        var path = prop.split('.');
        var value = window;
        for (var i = 0; i < path.length; i++)
        {
            if (value[path[i]])
                value = value[path[i]];
        }
        return value;
    },

    util: {
        thousands_separator: ",",
        // format an integer such as 1234 to a string with a thousands separator: 1,234
        format_number: function (num)
        {
            return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1" + jfbc.util.thousands_separator);
        },
        encode_data: function (data)
        {
            return encodeURIComponent(data).replace(/\-/g, "%2D").replace(/\_/g, "%5F").replace(/\./g, "%2E").replace(/\!/g, "%21").replace(/\~/g, "%7E").replace(/\*/g, "%2A").replace(/\'/g, "%27").replace(/\(/g, "%28").replace(/\)/g, "%29");
        },
        // Abstracted originally for use in different Mootools libraries. Remains in case future jfbcJQuery changes introduced
        ajax: function (url, callback)
        {
            url = url + '&' + jfbc.token + '=1';
            return jfbcJQuery.ajax({url: jfbc.base + 'index.php', data: url}).done(callback);
        },

        jqueryUiLoaded: false,
        loadJQueryUi: function ()
        {
            if (!jfbc.util.jqueryUiLoaded)
            {
                jfbcJQuery.getScript(jfbc.base + "media/sourcecoast/js/jquery-ui.min.js").done(function ()
                {
                    jfbc.util.jqueryUiLoaded = true;
                });
            }
        }
    },
    debug: {
        enable: 0,
        log: function (string)
        {
            if (jfbc.debug.enable == 1)
                console.log("JFBConnect logger: " + string);
        },
        stats: function ()
        {
            var element = jfbcJQuery('#jfbcAdminStats');
            if (!element.length)
                element = jfbcJQuery('<div id="jfbcAdminStats"></div>').appendTo('body');
            jfbcJQuery('#jfbcAdminStats').fadeIn(1000);
        }
    },
    cookie: {
        get: function (sKey)
        {
            return unescape(document.cookie.replace(new RegExp("(?:(?:^|.*;)\\s*" + escape(sKey).replace(/[\-\.\+\*]/g, "\\$&") + "\\s*\\=\\s*([^;]*).*$)|^.*$"), "$1")) || null;
        },
        set: function (sKey, sValue)
        {
            if (!sKey || /^(?:expires|max\-age|path|domain|secure)$/i.test(sKey))
            {
                return false;
            }
            document.cookie = escape(sKey) + "=" + escape(sValue) + "; path=/";
            return true;
        }
    },


    init: function ()
    {
        if (typeof jfbcJQuery == "undefined")
        {
            jfbcJQuery = jQuery;
            jfbc.jqcompat = false;
        }
        else
            jfbc.jqcompat = true;

        if (jfbc.login.logged_in && jfbc.login.logout_facebook)
        {
            jfbcJQuery(document).ready(function ()
            {
                jfbcJQuery(".sclogout-button form").submit(function (e)
                {
                    e.preventDefault();
                    jfbcJQuery(document).one("jfbc-provider-logout-done", null, {caller: this }, function (e)
                    {
                        e.data.caller.submit();
                    });
                    jfbc.login.logout_button_click();
                    return false;
                });
            });
        }


        jfbcJQuery(document).ready(function ()
        {
            jfbc.social.share();
            if (jfbcJQuery('#social-toolbar').length)
            {
                jfbc.util.loadJQueryUi();
                jfbcJQuery('#social-toolbar button').click(function ()
                {
                    var method = jfbcJQuery(this).attr("name");
                    jfbc.toolbar[method].display();
                });
            }
        });
    }

};