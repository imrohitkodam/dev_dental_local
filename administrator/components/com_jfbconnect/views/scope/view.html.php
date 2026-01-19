<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2018 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v7.2.6
 * @build-date      2018/05/24
 */

// Check to ensure this file is included in Joomla!
if (!(defined('_JEXEC') || defined('ABSPATH')))
{
    die('Restricted access');
};

jimport('joomla.application.component.view');
jimport('sourcecoast.adminHelper');

require_once(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/includes/views.php');

class JFBConnectViewScope extends JFBConnectAdminView
{
    function display($tpl = null)
    {
        $title = "JFBConnect: Scope & App Review";

        JToolBarHelper::title($title, 'jfbconnect.png');

        SCAdminHelper::addAutotuneToolbarItem();

        $autotuneSettingsLoaded = JFBCFactory::provider('facebook')->networkSettings->exists('scopes');
        $this->assignRef('autotuneSettingsLoaded', $autotuneSettingsLoaded);

        if (!$autotuneSettingsLoaded)
        {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_JFBCONNECT_SCOPE_REVIEW_AUTOTUNE_REQUIRED'), 'error');
        }
        else
        {
            // Only doing Facebook for now..
            $requiredScopes = new stdClass();
            $requiredScopes->profiles = JFBCFactory::provider('facebook')->profile->getRequiredScope();

            $requiredScopes->channels = [];
            $channels = JFBCFactory::model('channels')->getChannels(['provider' => 'facebook', 'published' => 1]);
            foreach ($channels as $c)
            {
                $options = new JRegistry();
                $options->loadObject($c);
                /** @var JFBConnectChannel $channel */
                $channel = JFBCFactory::provider($c->provider)->channel($c->type, $options);

                $requiredScopes->channels = array_merge($requiredScopes->channels, $channel->getScopeUsed());
            }
            $requiredScopes->channels = array_unique($requiredScopes->channels);

            $customScope = JFBCFactory::provider('facebook')->getCustomScopes();
            $requiredScopes->custom = [];
            foreach ($customScope as $cs)
                $requiredScopes->custom[] = $cs;

            $scopes = new stdClass();
            foreach (['channels', 'profiles', 'custom'] as $type)
            {
                $data = [];
                foreach ($requiredScopes->$type as $scope)
                {
                    $data[$scope] = JFBCFactory::provider('facebook')->getScopeDetails($scope);
                }
                $scopes->$type = $data;
            }

            $this->assignRef('scopes', $scopes);
        }
        parent::display($tpl);
    }
}