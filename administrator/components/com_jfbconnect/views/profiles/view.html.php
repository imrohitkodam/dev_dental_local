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

jimport('joomla.application.component.view');
jimport('sourcecoast.adminHelper');

require_once(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/includes/views.php');

class JFBConnectViewProfiles extends JFBConnectAdminView
{
    function display($tpl = null)
    {
        $app = JFactory::getApplication();
        JPluginHelper::importPlugin('socialprofiles');
        $profilePlugins = $app->triggerEvent('socialProfilesGetPlugins');
        $this->assignRef('profilePlugins', $profilePlugins);

        foreach ($profilePlugins as $p)
        {
            $options = array();
            $options['control'] = "profiles_" . $p->getName();
            JFBConnectUtilities::loadLanguage('plg_socialprofiles_' . $p->getName(), JPATH_ADMINISTRATOR);
            $path = JPATH_SITE . '/plugins/socialprofiles/' . $p->getName();
            JForm::addFieldPath($path . '/fields/');
            $this->formLoad($p->getName(), $path . '/forms/config.xml', $options);

            $data = JFBCFactory::config()->get('profile_' . $p->getName());
            $reg = new JRegistry();
            $reg->loadString($data);
            $this->formBind($p->getName(), $reg);
        }
        parent::display($tpl);
    }

    function addToolbar()
    {
        JToolBarHelper::title('JFBConnect: Profiles', 'jfbconnect.png');
        /** @var JFBConnectProvider $p */
        $settingsFetched = true;
        foreach (JFBCFactory::getAllProviders() as $p)
        {
            if (count($p->profile->getProviderFields()) <= 1)
            {
                $settingsFetched = false;
                break;
            }
        }

        if (!$settingsFetched)
        {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_JFBCONNECT_AUTOTUNE_NETWORKSETTINGS_NOT_LOADED'), 'error');
        }
        else
            JToolBarHelper::apply('apply', JText::_('COM_JFBCONNECT_BUTTON_APPLY_CHANGES'));
        SCAdminHelper::addAutotuneToolbarItem();
    }
}
