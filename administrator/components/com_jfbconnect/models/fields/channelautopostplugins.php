<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2019 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v8.3.1
 * @build-date      2019/11/19
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldChannelAutopostPlugins extends JFormFieldList
{
    public $type = 'ChannelAutopostPlugins';

    protected function getOptions()
    {
        $options = array();

        //Add all content from plugins
        $app = JFactory::getApplication();
        JPluginHelper::importPlugin('opengraph');
        $ogPlugins = $app->triggerEvent('openGraphProfilesGetPlugins');

        $tmp = JHtml::_('select.option', 0, JText::_('COM_JFBCONNECT_CHANNEL_AUTOPOST_SELECT_PLUGIN_DESC'), 'value', 'text');
        $tmp->checked = false;
        $options[] = $tmp;

        foreach ($ogPlugins as $ogPlugin)
        {
            if($ogPlugin->supportedAutopostLabel)
            {
                $tmp = JHtml::_('select.option', $ogPlugin->pluginName, JText::sprintf('COM_JFBCONNECT_CHANNEL_AUTOPOST_ALL_CONTENT_DESC', $ogPlugin->supportedAutopostLabel, $ogPlugin->extensionName), 'value', 'text');
                $tmp->checked = false;
                $options[] = $tmp;
            }
        }

        return $options;
    }
}