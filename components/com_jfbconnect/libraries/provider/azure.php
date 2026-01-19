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

class JFBConnectProviderAzure extends JFBConnectProvider
{
    function __construct()
    {
        $this->name = "Azure";
        $this->usernamePrefix = "az_";
        $this->loginPrefix = "az";

        parent::__construct();
    }

    function setupAuthentication()
    {
        $options = new JRegistry();
        $options->set('authurl', 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize');
        $options->set('tokenurl', 'https://login.microsoftonline.com/common/oauth2/v2.0/token');
        $options->set('authmethod', 'bearer');
        $options->set('scope', 'openid user.read profile email');

        $this->client = new JFBConnectAuthenticationOauth2($options);

        $token = JFactory::getApplication()->getUserState('com_jfbconnect.' . $this->systemName . '.token', null);
        if ($token)
        {
            $token = (array)json_decode($token);
            $this->client->setToken($token);
        }
        $this->client->initialize($this);

        $redirect = JUri::base() . 'index.php';
        //$redirect = str_replace('http://', 'https://', $redirect);
        $this->client->setOption('redirecturi', $redirect);
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
                $app->redirect('index.php?option=com_jfbconnect&task=authenticate.callback&provider=azure&code=' . $code . '&state=' . JSession::getFormToken());
            }
        }

    }

}