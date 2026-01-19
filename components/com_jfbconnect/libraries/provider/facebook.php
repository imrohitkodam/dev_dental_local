<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2019 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v8.3.1
 * @build-date      2019/11/19
 */

// Check to ensure this file is included in Joomla!
if (!(defined('_JEXEC') || defined('ABSPATH'))) {     die('Restricted access'); };

include_once(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/assets/facebook-api/facebook.php');

class JFBConnectProviderFacebook extends JFBConnectProvider
{
    // Override model's getInstance to really only get the instance
    private $apiError;

    function __construct()
    {
        $this->name = 'Facebook';
        $this->usernamePrefix = "fb_";
        $this->loginPrefix = "fb";

        parent::__construct();

        $this->client = new JFBConnectProviderFacebookClient(array(
                'appId' => $this->appId,
                'secret' => $this->secretKey,
                'cookie' => true)
        );
    }

    function api($api, $params = null, $callAsUser = true, $method = null, $suppressErrors = false)
    {
        $this->apiError = null;
        if (!$method)
        {
            if ($params)
                $method = "POST";
            else
                $method = "GET";
        }

        if (!$callAsUser)
            $params['access_token'] = $this->appId . "|" . $this->secretKey;
        /*        else if (!$params || (is_array($params) && (!array_key_exists('access_token', $params))))
        {
            // Get the access token for the current user
            $jUser = JFactory::getUser();
            $userMapModel = new JFBConnectModelUserMap();
            $userMapModel->getData($jUser->get('id'));
            $accessToken = $userMapModel->_data->access_token;
            if ($accessToken != '' && $accessToken != null)
                $params['access_token'] = $accessToken;
        }*/

        try
        {
            if ($params != null) // Graph API call with paramters (either App call or POST call)
                $apiData = $this->client->api($api, $method, $params);
            else // Graph API call to only get data
                $apiData = $this->client->api($api);
        }
        catch (JFBCFacebookApiException $e)
        {
            $this->apiError = $e->getMessage();
            // Only display errors on the front-end if the config is set to do so
            $app = JFactory::getApplication();
            if (!$suppressErrors && ($app->isAdmin() || $this->configModel->get('facebook_display_errors')))
            {
                JFBCFactory::log(JText::_('COM_JFBCONNECT_FB_API_ERROR') . $e->getMessage(), 'error');
            }
            $apiData = null;
        }

        return $apiData;
    }

    public function getLastError()
    {
        $return = $this->apiError;
        $this->apiError = null;
        return $return;
    }

    function getLogoutButton()
    {
        JFBConnectUtilities::loadLanguage('com_jfbconnect');
        $logoutStr = JText::_('COM_JFBCONNECT_LOGOUT');

        return '<input type="submit" name="Submit" id="jfbcLogoutButton" class="button btn btn-primary" value="' . $logoutStr . '" onclick="javascript:jfbc.login.logout_button_click()" />';
    }

    /* getProviderUserId
    * The new way to get the userId from Facebook as of v5.1. This is the ID from Facebook even if the user is not logged into Joomla.
    */
    function getProviderUserId()
    {
        return $this->client->getUser();
    }

    public function userIsConnected()
    {
        static $connected = null;
        if ($connected == null)
            $connected = $this->getMappedUserId() == null ? false : true;

        return $connected;
    }

    public function onBeforeLogin()
    {
        $this->client->setExtendedAccessToken();
        $this->client->getUser();

        // Ensure the cookie is deleted from the correct path
        setcookie('fbsr_' . $this->appId, '', time()-1, '/');

        parent::onBeforeLogin();
    }

    // Check that additional scope for a user exists. If not, reroute them through Facebook login to obtain it
    public function fetchNewScope($newScope)
    {
        $params = array();
        $params['scope'] = implode(',', $newScope);
        $params['redirect_uri'] = JUri::base() . 'index.php?option=com_jfbconnect&task=authenticate.callback&provider=facebook';
        $redirect = $this->client->getLoginUrl($params);
        JFactory::getApplication()->redirect($redirect);
    }

    /***
     * Return an array of valid scope
     *
     * $uid = Facebook user ID to look at scope for
     * $token = If provided, will be the token used to request scope.
     * If no token provided, the token stored in the DB will be used
     */
    public function getUserScope($uid, $token = null)
    {
        // get current scope for the user
        $return = array();
        $jid = JFBCFactory::usermap()->getJoomlaUserId($uid, 'facebook');
        if ($token)
            $params['access_token'] = $token;
        else
            $params['access_token'] = JFBCFactory::usermap()->getUserAccessToken($jid, 'facebook');
        $currentScope = $this->api('/' . $uid . '/permissions', $params, true, 'GET');
        if (isset($currentScope['data']) && isset($currentScope['data'][0]))
        {
            foreach ($currentScope['data'] as $scope)
            {
                // Check for v2.0 of Graph API
                if (array_key_exists('permission', $scope))
                {
                    if ($scope['status'] == 'granted')
                        $return[] = $scope['permission'];
                }
                else
                {
                    foreach ($scope as $permission => $val)
                    {
                        if ($val == 1)
                            $return[] = $permission;
                    }
                }
            }
        }
        return $return;
    }

    /**
     * Get an array of custom set scopes by the administrator
     * @return array
     */
    public function getCustomScopes()
    {
        $customScopes = array();
        $customPermsSetting = JFBCFactory::config()->getSetting('facebook_perm_custom');
        if ($customPermsSetting != '')
        {
            $customPermsSetting = str_replace("\r\n", ',', $customPermsSetting);
            //Separate into an array to be able to merge and then take out duplicates
            $customPerms = explode(',', $customPermsSetting);
            foreach ($customPerms as $customPerm)
                $customScopes[] = trim($customPerm);
        }
        return $customScopes;
    }

    /**
     * Get a comma-separated list of scopes to request from a normal user on login
     * @return string
     */
    public function getRequestScopeString()
    {
        // Gather the scopes required from profile settings and those set in the configuration by an admin
        $scopesArray =  $this->profile->getRequiredScope();
        $customPerms = $this->getCustomScopes();
        if (count($customPerms) > 0)
            $scopesArray = array_merge($scopesArray, $customPerms);

        $scopesArray = array_unique($scopesArray);
        return implode(',', $scopesArray);
    }

    public function getLocale()
    {
        $fbLocale = JFBCFactory::config()->get('facebook_language_locale');

        // Get the language to use
        if ($fbLocale == '')
        {
            $lang = JFactory::getLanguage();
            $locale = $lang->getTag();
        }
        else
        {
            $locale = $fbLocale;
        }

        $locale = str_replace("-", "_", $locale);

        $locale = ($locale == 'ar_AA') ? 'ar_AR' : $locale;
        return $locale;
    }

    /** System plugin triggers */
    public function onAfterInitialise()
    {
        $fbCanvas = JFBConnectProviderFacebookCanvas::getInstance();
        $fbCanvas->setupCanvas();

        // Check if this was a Notification/Request, and redirect the user appropriately
        // If so, a redirect WILL occur here!
        $jfbcRequestLibrary = JFBConnectProviderFacebookRequest::getInstance();
        $jfbcRequestLibrary->checkForNotification();
    }

    /**
     * Perform initialization of JFBConnect variables into the document. Currently adds:
     * ** The (dynamic) login/logout redirects, used by jfbconnect.js
     * ** The {scopengraphplaceholder} tag to be replaced/removed by the system plugin
     * @return none
     */
    function onAfterDispatch()
    {
        $doc = JFactory::getDocument();
        if ($doc->getType() != 'html')
            return; // Only insert javascript on HTML pages, not AJAX, RSS, etc

        $app = JFactory::getApplication();
        $state = $app->getUserState('users.login.form.data', null);
        $return = null;
        if (JRequest::getCmd('option') == 'com_users' && JRequest::getCmd('view') == 'login' && is_array($state) && isset($state['return']))
            $return = urldecode($state['return']);
        if (!$return)
            $return = urldecode(base64_decode($app->input->getBase64('return', '')));
        if (!$return)
        {
            $uri = JURI::getInstance();
            $return = $uri->toString(array('path', 'query'));
            if ($return == "")
                $return = 'index.php';
        }

        $requiredPerms = $this->getRequestScopeString();

        $fbUserId = $this->getMappedUserId();
        $logoutJoomlaOnly = $this->configModel->get('logout_joomla_only');

        if ($fbUserId && !$logoutJoomlaOnly)
            $logoutFacebookJavascript = "jfbc.login.logout_facebook = true;";
        else
            $logoutFacebookJavascript = "jfbc.login.logout_facebook = false;";

        $user = JFactory::getUser();
        $showLoginModal = $this->configModel->get('facebook_login_show_modal');

        if ($user->guest)
            setcookie('jfbconnect_permissions_granted', '', time() - 10000, "/"); // clear the granted permissions cookie

        $debugCmd = $this->configModel->get('facebook_display_errors') ? "jfbc.debug.enable = '1';\n" : '';

        $doc->addScript(JURI::base(true) . '/components/com_jfbconnect/includes/jfbconnect.js?v=6');
        $doc->addCustomTag('<script type="text/javascript">' .
            $logoutFacebookJavascript . "\n" .
            "jfbc.base = '" . JURI::base() . "';\n" .
            "jfbc.return_url = '" . base64_encode($return) . "';\n" .
            "jfbc.login.scope = '" . $requiredPerms . "';\n" .
            "jfbc.login.show_modal = '" . $showLoginModal . "';\n" .
            "jfbc.login.use_popup = " . ($this->configModel->get('login_use_popup') ? 'true' : 'false') . ";\n" .
            "jfbc.login.auto = '" . $this->configModel->get('facebook_auto_login') . "';\n" .
            "jfbc.login.logged_in = " . (!$user->guest && JFBCFactory::usermap()->getProviderUserId($user->id, 'facebook') ? 'true' : 'false') . ";\n" .
            "jfbc.token = '" . JSession::getFormToken() . "';\n" .
            $debugCmd .
            "jfbc.init();\n" .
            "</script>");

        if(!JFBCFactory::config()->get('social_graph_turn_off_all'))
            $doc->addCustomTag('<SCOpenGraphPlaceholder />');
    }

    public function onAfterRender()
    {
        $body = JResponse::getBody();

        // Add FB built-in and custom OG tag for namespace, if applicable
        $ogNamespaces = 'og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#';
        $appConfig = JFBCFactory::config()->get('autotune_app_config', array());
        if (array_key_exists('namespace', $appConfig))
        {
            $appNamespace = $appConfig['namespace'];
            if ($appNamespace != '')
                $ogNamespaces .= " " . $appNamespace . ': http://ogp.me/ns/fb/' . $appNamespace . '#';
        }
        $body = str_ireplace("<html ", '<html prefix="' . $ogNamespaces . '" ', $body);

        // Should the modal popup be displayed?
        $showLoginModal = JFBCFactory::config()->get('facebook_login_show_modal');
        if ($showLoginModal)
        {
            JFBConnectUtilities::loadLanguage('com_jfbconnect');
            $loginModalDiv = '<div id="jfbcLoginModal" class="sourcecoast modal" style="display:none"><div class="modal-body">' . JText::_('COM_JFBCONNECT_LOGIN_POPUP') . '</div></div>';
        }
        else
            $loginModalDiv = "";

        $body = str_ireplace("</body>", $loginModalDiv . "</body>", $body);

        $locale = $this->getLocale();
        // get Event Notification subscriptions
        $subs  = "\nFB.Event.subscribe('comment.create', jfbc.social.facebook.comment.create);";
        $subs .= "\nFB.Event.subscribe('comment.remove', jfbc.social.facebook.comment.remove);";
        if (JFBCFactory::config()->get('social_notification_google_analytics'))
            $subs .= "\njfbc.social.googleAnalytics.trackFacebook();";

        $fbCanvas = JFBConnectProviderFacebookCanvas::getInstance();
        if ($fbCanvas->get('resizeEnabled', false))
            $resizeCode = "window.setTimeout(function() {\n" .
                "  FB.Canvas.setAutoGrow();\n" .
                "}, 250);";
        else
            $resizeCode = "";

        if ($fbCanvas->get('canvasEnabled', false))
            $canvasCode = "jfbc.canvas.checkFrame();";
        else
            $canvasCode = "";

        // Figure out if status:true should be set. When false, makes page load faster
        $user = JFactory::getUser();
        $guest = $user->guest;
        // Check cookie to make sure autologin hasn't already occurred once. If so, and we try again, there will be loops.
        $autoLoginPerformed = JRequest::getInt('jfbconnect_autologin_disable', 0, 'COOKIE');
        if (JFBCFactory::config()->get('facebook_auto_login') && $guest && !$autoLoginPerformed)
        {
            $status = 'status: true,';
            // get Event Notification subscriptions
            $subs .= "\nFB.Event.subscribe('auth.authResponseChange', function(response) {jfbc.login.facebook_onlogin();});";
        }
        else
            $status = 'status: false,';

        $debugEnabled = $this->configModel->get('facebook_display_errors');
        $script = $debugEnabled ? "sdk/debug.js" : "sdk.js";

        if ($this->appId)
            $appIdCode = "appId: '" . $this->appId . "', ";
        else
            $appIdCode = "";

        $forceParse = JFBCFactory::config()->get('social_tags_always_parse');
        $xfbml = ($forceParse || $this->widgetRendered) ? 'true' : 'false';

        $social_quote_enabled = JFBCFactory::config()->get('social_quote_enabled');
        $social_k2_quote_enabled = JFBCFactory::config()->get('social_k2_quote_enabled');
        $option = JFactory::getApplication()->input->get('option');

        if( ($social_quote_enabled && $option == 'com_content') || ($social_k2_quote_enabled && $option == 'com_k2') )
            $xfbml = 'true';


        $messengerEnabled = JFBCFactory::config()->get('social_messenger_enabled');
        $messengerPageId = JFBCFactory::config()->get('social_messenger_page_id');

        if($messengerEnabled && !empty($messengerPageId))
        {
            $version = "version: 'v3.3',";
            $xfbml = 'true';
            $script = "sdk/xfbml.customerchat.js";

            $themeColor = JFBCFactory::config()->get('social_messenger_theme_color');
            $loggedInGreeting = JFBCFactory::config()->get('social_messenger_logged_in_greeting');
            $loggedOutGreeting = JFBCFactory::config()->get('social_messenger_logged_out_greeting');
            $dialogDelay = JFBCFactory::config()->get('social_messenger_greeting_dialog_delay');
            $dialogDisplay = JFBCFactory::config()->get('social_messenger_greeting_dialog_display');

            $messengerScript = '<div class="fb-customerchat" attribution=setup_tool page_id="'.$messengerPageId.'"' .
                (empty($themeColor)?'' : (' theme_color="' . $themeColor .'"')) .
                (empty($loggedInGreeting)? '' : (' logged_in_greeting="'. $loggedInGreeting .'"')) .
                (empty($loggedOutGreeting) ? '' : (' logged_out_greeting="'. $loggedOutGreeting . '"')) .
                (empty($dialogDelay) ? '' : (' greeting_dialog_delay="'. $dialogDelay .'"')) .
                (empty($dialogDisplay) ? '' : (' greeting_dialog_display="'. $dialogDisplay. '"')) .'></div>';
        }
        else
        {
            $version = "version: 'v3.3',";
            $messengerScript = '';
        }

        $javascript =
            <<<EOT
<script type="text/javascript">
  {$canvasCode}
  window.fbAsyncInit = function() {
    FB.init({{$version}{$appIdCode}{$status} cookie: true, xfbml: {$xfbml}});{$subs}{$resizeCode}
  };
  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/{$locale}/{$script}";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>

{$messengerScript}

EOT;
        if (preg_match('/\<body[\s\S]*?\>/i', $body, $matches))
        {
            $newBody = str_replace($matches[0], $matches[0] . $javascript, $body);
            JResponse::setBody($newBody);
        }
    }
    /** End System trigger calls */

}


class JFBConnectProviderFacebookClient extends JFBCFacebook
{
    var $provider;

    public function isAuthenticated()
    {
        return (bool)$this->getUser();
    }

    function getToken()
    {
        return $this->getAccessToken();
    }

    // We don't actually authenticate with OAuth here, we do it through the Javascript library, so nothing to do here.
    public function authenticate()
    {
        $input = JFactory::getApplication()->input;
        if ($input->getCmd('task') == 'login' && !JFBCFactory::config()->get('login_use_popup'))
        {
            $params = array();
            $params['scope'] = JFBCFactory::provider('facebook')->getRequestScopeString();
            $params['redirect_uri'] = JUri::base() . 'index.php?option=com_jfbconnect&task=authenticate.callback&provider=facebook';
            $redirect = $this->getLoginUrl($params);
            JFactory::getApplication()->redirect($redirect);
        }
        return;
    }
}