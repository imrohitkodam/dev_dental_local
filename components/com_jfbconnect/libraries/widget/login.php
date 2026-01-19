<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2019 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v8.3.1
 * @build-date      2019/11/19
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class JFBConnectProviderWidgetLogin extends JFBConnectWidget
{
    var $name = 'Login';
    var $systemName = "login";

    var $providerLoginInfo;

    function __construct($provider, $fields, $className)
    {
        parent::__construct($provider, $fields);

        $this->className = $className;

        $this->providerLoginInfo = array();

        $providers = JFBCFactory::getAllProviders();
        foreach($providers as $p)
            $this->providerLoginInfo[$p->name] = $p->getLoginButtonInfo();
    }

    public function render()
    {
        return $this->getTagHtml();
    }

    protected function getTagHtml()
    {
        $user = JFactory::getUser();
        $buttonHtml = '';

        $providers = $this->getSelectedProviders($this->getParamValue('providers'));
        if ($user->guest) // Only show login button if user isn't logged in (no remapping for now)
        {
            $buttonHtml = $this->getLoginButtons($providers);
        }
        else // logged in. Show logout button and/or reconnect buttons as configured
        {
            $showReconnect = $this->getParamValueEx('show_reconnect', null, 'boolean', 'false');
            $showLogoutButton = $this->getParamValueEx('logout', null, 'boolean', 'false');
            $logoutUrl = $this->getParamValueEx('logout_url', null, null, JURI::root());

            if ($showLogoutButton == 'true')
            {
                $logoutUrl = base64_encode(JRoute::_($logoutUrl, false));
                $logoutButtonId = $this->providerLoginInfo[$this->provider->name]->logoutButtonId;
                $buttonHtml = '<input type="submit" name="Submit" id="' . $logoutButtonId . '" class="button btn btn-primary" value="'
                    . JText::_('JLOGOUT') . "\" onclick=\"javascript:jfbc.login.logout('" . $logoutUrl . "')\" />";
            }
            if ($showReconnect == "true")
            {
                // get all providers and check if mapping exists. Then, get the buttons for providers the user hasn't connected with
                $mapProviders = array();
                $userProviders = JFBCFactory::usermap()->getUserProviders(JFactory::getUser()->id);

                foreach ($providers as $provider)
                {
                    if (!in_array($provider->systemName, $userProviders))
                        $mapProviders[] = $provider;
                }
                $buttonHtml = $this->getLoginButtons($mapProviders, ' reconnect');
            }
        }
        return $buttonHtml;
    }

    private function getSelectedProviders($providerList)
    {
        if (!is_array($providerList))
        {
            $providerList = str_replace("\r\n", ",", $providerList);
            $providerList = explode(',', $providerList);
        }
        $providers = array();
        foreach ($providerList as $p)
        {
            if ($p)
                $providers[] = JFBCFactory::provider(trim($p));
        }

        //For backwards compatibility:
        // - For Facebook, if no providers are specified, then show all providers
        // - For Others, if no providers are specified, just show the login button for that provider
        if (empty($providers))
        {
            if ($this->provider->name == 'Facebook')
                $providers = JFBCFactory::getAllProviders();
            else
                $providers[] = $this->provider;
        }

        return $providers;
    }

    // The new function of this is to iterate through the login providers and echo their buttons
    // This allows automatically adding the FB/Google buttons to apps that would normally add only FB
    private function getLoginButtons($providers, $extraClass = null)
    {
        $html = "";

        if ($this->fields->get('loginbuttonstype', 'default') == 'custom')
            $customImages = $this->fields->get('loginbuttons');
        else
            $customImages = null;

        foreach ($providers as $p)
        {
            if (!$p->appId) // Don't load provider if AppId isn't set as it won't allow logins anyways
                continue;

            $pName = $p->systemName;

            if ($customImages && isset($customImages->$pName))
            {
                $this->fields->set('image', $customImages->$pName);
            }
            else
            {
                $name = $this->getParamValueEx('image', null, null, null);
                $this->fields->set('image', $name);
            }

            $html .= $p->getLoginButtonWithImage($this->fields, $this->providerLoginInfo[$p->name]->loginButtonClass . $extraClass, $this->providerLoginInfo[$p->name]->loginButtonId);
        }

        $text = $this->getParamValueEx('text', null, null, null);
        /*        if ($text == null)
                {
                    list(, $caller) = debug_backtrace(false);
                    // Check if this is the JomSocial homepage calling us, and if so, add the JText'ed Login with string
                    if (array_key_exists('class', $caller) && $caller['class'] == 'CommunityViewFrontpage')
                    {
                        JFBConnectUtilities::loadLanguage('com_jfbconnect');
                        $text = JText::_('COM_JFBCONNECT_LOGIN_WITH');
                    }
                }*/

        if(!empty($html))
        {
            $text = empty($text) ? "" : '<div class="pull-left intro">' . $text . '</div>';
            $html = '<span class="sourcecoast login"><div class="row-fluid">' . $text . $html . '</div></span>';
        }
        return $html;
    }

}

class LoginButtonInfo
{

    //Need to set these in widget render divs
    var $className;
    var $tagName;
    var $examples;

    var $loginButtonId;
    var $loginButtonText;
    var $loginButtonClass;
    var $logoutButtonId;

    function __construct($providerName, $autonamePrefix)
    {
        if($providerName == 'Facebook') //Older naming style for easy tags
        {
            $this->className = 'jfbcLogin';
            $tagName = 'JFBCLogin';
            $prefix = 'jfbc';
        }
        else if($providerName == 'LinkedIn') //Older naming style for easy tags
        {
            $this->className = 'jLinkedLogin';
            $tagName = 'JLinkedLogin';
            $prefix = 'scLinkedIn';
        }
        else
        {
            $this->className = 'sc' . $providerName . 'LoginTag';
            $tagName = 'SC' . $providerName . 'Login';
            $prefix = 'sc'.$providerName;
        }

        $this->tagName = strtolower($tagName);
        $this->loginButtonId = 'sc_'. $autonamePrefix .'login';
        $this->loginButtonClass = $prefix.'Login';
        $this->logoutButtonId = $prefix.'LogoutButton';

        JFBConnectUtilities::loadLanguage('com_jfbconnect');
        $this->loginButtonText = JText::sprintf('COM_JFBCONNECT_LOGIN_USING_SOCIAL', $providerName);

        $this->examples = array(
            '{' . $tagName . '}',
            '{' . $tagName . ' logout=true logout_url=http://www.sourcecoast.com}'
        );

    }
}
