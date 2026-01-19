<?php
/**
 * @package         SourceCoast Extensions
 * @copyright (c)   2009-2019 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v8.3.1
 * @build-date      2019/11/19
 */
// Check to ensure this file is included in Joomla!
if (!(defined('_JEXEC') || defined('ABSPATH'))) {     die('Restricted access'); };

class JFBConnectProviderWindowsLive extends JFBConnectProvider
{
    function __construct()
    {
        $this->name = "WindowsLive";
        $this->usernamePrefix = "wl_";
        $this->loginPrefix = "wl";

        parent::__construct();
    }

    function setupAuthentication()
    {
        $options = new JRegistry();
        $options->set('authurl', 'https://login.live.com/oauth20_authorize.srf');
        $options->set('tokenurl', 'https://login.live.com/oauth20_token.srf');
        $options->set('authmethod', 'get');

        $options->set('scope', 'wl.signin wl.basic wl.emails wl.birthday');

        $this->client = new JFBConnectAuthenticationOauth2($options);

        $token = JFactory::getApplication()->getUserState('com_jfbconnect.' . $this->systemName . '.token', null);
        if ($token)
        {
            $token = (array)json_decode($token);
            $this->client->setToken($token);
        }
        $this->client->initialize($this);
    }

    /* getProviderUserId
    * Gets the provider User Id from the provider. This is regardless of whether they are mapped to an
    *  existing Joomla account.
    */
    function getProviderUserId()
    {
        if ($this->get('providerUserId', null) == null)
        {
            $profile = $this->profile->fetchProfile('me', 'id');
            $id = $profile->get('id');
            if (!empty($id))
                $this->set('providerUserId', $id);
            else
                $this->set('providerUserId', null);
        }
        return $this->get('providerUserId');
    }

    /*function setTimestampOffset($offset)
    {
        $this->timestampOffset = $offset;
    }*/

    function getHeadData()
    {
        $head = '';
        if ($this->needsJavascript)
        {
            // Add any Javascript files here
        }

        return $head;
    }

}