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

jimport('joomla.application.component.controller');

class JFBConnectController extends JControllerLegacy
{
    public function __construct($config = array())
    {
        if (!class_exists('JFBCFactory'))
        {
            $app = JFactory::getApplication();
            $app->redirect('index.php', "JFBCSystem plugin is not enabled.");
        }
        parent::__construct($config);
    }

    function display($cachable = false, $urlparams = false)
    {
        // If the request comes here, it means the URL was index.php?option=com_jfbconnect (with no other query params)
        // At this point, we need to check if it's a possible Twitter authentication attempt and set things up properly.
        $oauth_token = JRequest::getString('oauth_token');
        $oauth_verifier = JRequest::getString('oauth_verifier');
        if ($oauth_token != "" && $oauth_verifier != "")
        {
            // Assume it's a twitter login and redirect appropriately
            JFactory::getApplication()->redirect('index.php?option=com_jfbconnect&task=authenticate.callback&provider=twitter&state=' . JSession::getFormToken() . '&oauth_verifier=' . $oauth_verifier . '&oauth_token=' . $oauth_token );
        }

        parent::display();
    }

    function deauthorizeUser()
    {
        $fbClient = JFBCFactory::provider('facebook')->client;

        $signedRequest = JRequest::getString('signed_request', null, 'POST');
        if ($signedRequest)
        {
            $parsed = $fbClient->parseSignedRequest($signedRequest);
            $fbUserId = $parsed['user_id'];
            if ($fbUserId)
            {
                JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/' . 'models');
                $userModel = JModelLegacy::getInstance('UserMap', 'JFBConnectModel');
                $userModel->setAuthorized($fbUserId, '0');
            }
        }
        exit;
    }

    /*  Not ready for primetime yet. The setInitialRegistration causes issues.
    function updateProfile()
    {
        $jUser = JFactory::getUser();
        $jfbcLibrary = JFBConnectFacebookLibrary::getInstance();
        $jfbcLibrary->setInitialRegistration();
        $fbUserId = $jfbcLibrary->getMappedFbUserId();
        $args = array($jUser->get('id'), $fbUserId);

        $app = JFactory::getApplication();
        JPluginHelper::importPlugin('jfbcprofiles');
        $app->triggerEvent('scProfilesImportProfile', $args);
        JFBCFactory::log('Profile Imported!');
        $app->redirect('index.php');
    }*/
}
