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

class JFormFieldChannelAutopostObjects extends JFormFieldList
{
    public $type = 'ChannelAutopostObjects';

    protected function getOptions()
    {
        $options = array();

        //Get enabled plugins
        $app = JFactory::getApplication();
        JPluginHelper::importPlugin('opengraph');
        $ogPlugins = $app->triggerEvent('openGraphProfilesGetPlugins');

        //Build list of all objects that support autoposting
        $allSupportedObjects = array();
        foreach ($ogPlugins as $plugin)
        {
            if($plugin->supportedAutopostTypes)
                $allSupportedObjects = array_merge($allSupportedObjects, $plugin->supportedAutopostTypes);
        }


        //Get published open graph objects
        JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/models', 'JFBConnectAdminModel');
        $this->ogObjectModel = JModelLegacy::getInstance('OpenGraphObject', 'JFBConnectAdminModel');
        $ogObjects = $this->ogObjectModel->getObjects(true);

        $tmp = JHtml::_('select.option', 0, JText::_('COM_JFBCONNECT_CHANNEL_AUTOPOST_SELECT_OBJECT_DESC'), 'value', 'text');
        $tmp->checked = false;
        $options[] = $tmp;

        //Add open graph objects that are supported
        foreach ($ogObjects as $ogObject)
        {
            if(in_array($ogObject->system_name, $allSupportedObjects))
            {
                $tmp = JHtml::_('select.option', $ogObject->id, JText::sprintf('COM_JFBCONNECT_CHANNEL_AUTOPOST_OG_CONTENT_DESC',$ogObject->display_name), 'value', 'text');
                $tmp->checked = false;
                $options[] = $tmp;
            }
        }

        return $options;
    }
}