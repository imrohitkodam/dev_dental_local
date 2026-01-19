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

class JFBConnectViewOpengraph extends JFBConnectAdminView
{
    function display($tpl = null)
    {
        $title = "JFBConnect: Social Meta";
        $app = JFactory::getApplication();

        $layout = $this->getLayout();
        if ($layout != 'display' && $layout != 'default')
        {
            JToolBarHelper::custom('display', 'opengraph.png', 'index.php?option=com_jfbconnect&view=opengraph', 'Open Graph Home', false);
            JToolBarHelper::divider();
        }

        switch ($this->getLayout())
        {
            case 'objects':
                $title .= " - Objects";
                $bar = JToolBar::getInstance('toolbar');
                $bar->appendButton('Popup', 'new', 'New', 'index.php?option=com_jfbconnect&view=opengraph&task=objectcreate&tmpl=component', '550', '400', '0', '0', '');

                JToolBarHelper::publishList();
                JToolBarHelper::unpublishList();
                JToolBarHelper::deleteList('Deleting these objects will delete all activity associated with them. Are you sure?');

                $objectModel = $this->getModel('OpenGraphObject', 'JFBConnectAdminModel');
                $objects = $objectModel->getObjects();
                $this->assignRef('objects', $objects);
                break;

            // Modal popups for selecting the action/object to create
            case 'objectcreate':
                JPluginHelper::importPlugin('opengraph');
                $plugins = $app->triggerEvent('onOpenGraphGetPlugins');
                $this->assignRef('plugins', $plugins);
                break;
            case 'objectedit':
                $title .= " - Edit Object";

                $model = $this->getModel('OpenGraphObject', 'JFBConnectAdminModel');
                $id = JRequest::getInt('id', 0);
                if ($id != 0)
                    $object = $model->getObject($id);
                else
                {
                    $plugin = JRequest::getString('plugin');
                    $name = JRequest::getString('name');

                    $object = new ogObject();
                    $object->loadDefaultObject($plugin, $name);
                }
                $this->assignRef('object', $object);

                /* Get the current list of objects fetched from Autotune */
                $objectTypes = JFBCFactory::provider('facebook')->networkSettings->get('open_graph.objects');
                if (empty($objectTypes))
                {
                    $none = new stdClass;
                    $none->name = "none";
                    $none->title = "No Objects Available - Please Run Autotune";
                    $objectTypes = [$none];
                    JFactory::getApplication()->enqueueMessage(JText::_('COM_JFBCONNECT_AUTOTUNE_NETWORKSETTINGS_NOT_LOADED'), 'error');
                }
                else
                {
                    JToolBarHelper::apply('apply', 'Save');
                    JToolBarHelper::save('save', 'Save & Close');
                }
                JToolBarHelper::cancel('cancel', 'Cancel');

                $this->assignRef('object_types', $objectTypes);

                // Load the params for this specific object
                jimport('joomla.filesystem.file');
                JFormHelper::addFieldPath(JPATH_SITE . '/plugins/opengraph/' . $object->plugin . '/objects');
                $xml = JPATH_SITE . '/plugins/opengraph/' . $object->plugin . '/objects/' . $object->system_name . '.xml';
                if (JFile::exists($xml))
                {
                    $form = JForm::getInstance('opengraph.' . $object->plugin . '.' . $object->system_name, $xml);
                    $form->bind(array('params' => $object->params->toArray()));
                }
                else
                    $form = null;
                $this->assignRef('params', $form);
                break;

            case 'settings':
                $title .= " - Settings";
                JToolBarHelper::apply('apply', 'Save');
                JToolBarHelper::save('save', 'Save & Close');
                JToolBarHelper::cancel('cancel', 'Cancel');

                JForm::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/models/fields');
                $this->formLoad('meta', JPATH_ADMINISTRATOR . '/components/com_jfbconnect/models/forms/opengraph_settings.xml');
                $this->formBind('meta', JFBCFactory::config()->getGraphSettings());

                break;
        }
        JToolBarHelper::title($title, 'jfbconnect.png');

        SCAdminHelper::addAutotuneToolbarItem();

        parent::display($tpl);
    }
}
