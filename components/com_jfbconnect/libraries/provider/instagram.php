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

class JFBConnectProviderInstagram extends JFBConnectProvider
{
    function __construct()
    {
        $this->name = "Instagram";
        $this->usernamePrefix = "instagram_";
        $this->loginPrefix = "instagram";

        parent::__construct();
    }

    function setupAuthentication()
    {
        $options = new JRegistry();
        $options->set('authurl', 'https://api.instagram.com/oauth/authorize');
        $options->set('tokenurl', 'https://api.instagram.com/oauth/access_token');
        $options->set('authmethod', 'get');

        $headers = array();
        $headers['Content-Type'] = 'application/json';
        $options->set('headers', $headers);

        $options->set('scope', implode(',', $this->profile->getRequiredScope()));

        $this->client = new JFBConnectAuthenticationOauth2($options);

        if (JFBCFactory::config()->get('instagram_new_api_enabled'))
            $this->client->setClientFields('app_id', 'app_secret');

        $token = JFactory::getApplication()->getUserState('com_jfbconnect.' . $this->systemName . '.token', null);
        if ($token)
        {
            $token = (array)json_decode($token);
            $this->client->setToken($token);
        }
        $this->client->initialize($this);

        if (JFBCFactory::config()->get('instagram_new_api_enabled'))
        {
            $redirect = JUri::base() . 'index.php';
        }
        else //deprecated by instagram, remove 03/2020
        {
            $redirect = $this->client->getOption('redirecturi');
        }

        $redirect = str_replace('http://', 'https://', $redirect);

        $this->client->setOption('redirecturi', $redirect);

    }

    /* getProviderUserId
    * Gets the provider User Id from the provider. This is regardless of whether they are mapped to an
    *  existing Joomla account.
    */
    function getProviderUserId()
    {
        $token = $this->client->getToken();

        if (JFBCFactory::config()->get('instagram_new_api_enabled'))
        {
            $user = (array) $token['user_id'];
            $userId = isset($user[0]) ? $user[0] : null;
        }
        else //deprecated by instagram, remove 03/2020
        {
            $user = (array) $token['user'];
            $userId = isset($user['id']) ? $user['id'] : null;
        }



        return $userId;
    }

    function onAfterInitialise()
    {
        $app = JFactory::getApplication();
        $loginStarted = $app->getUserState('com_jfbconnect.authentication.started.' . $this->systemName, false);

        $app->setUserState('com_jfbconnect.authentication.started.' . $this->systemName, false);

        if ($loginStarted)
        {
            $code = $app->input->get('code', false, 'raw');
            if ($code)
            {
                $app->redirect('index.php?option=com_jfbconnect&task=authenticate.callback&provider=instagram&code=' . $code . '&state=' . JSession::getFormToken());
            }
        }

    }

}