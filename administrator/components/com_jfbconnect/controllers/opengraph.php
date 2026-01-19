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

class JFBConnectControllerOpenGraph extends JFBConnectController
{
    public function __construct()
    {
        parent::__construct();
        $document = JFactory::getDocument();
        $viewType = $document->getType();
        $viewName = 'opengraph';
        $this->view = $this->getView($viewName, $viewType);
    }

    function display($cachable = false, $urlparams = false)
    {
        $task = JRequest::getCmd('task', 'default');

        $ogObjectModel = $this->getModel('opengraphobject', 'JFBConnectAdminModel');
        $this->view->setModel($ogObjectModel, false);
        $configModel = $this->getModel('config', 'JFBConnectModel');
        $this->view->setModel($configModel, false);

        $this->view->setLayout($task);
        $this->view->display();
    }

    function objectcreate()
    {
        $this->display();
    }

    function cancel()
    {
        $type = JRequest::getString('formtype');
        if ($type == 'settings')
            $this->setRedirect('index.php?option=com_jfbconnect&view=opengraph');
        else
            $this->setRedirect('index.php?option=com_jfbconnect&view=opengraph&task=' . $type . "s");
    }

    function remove()
    {
        $type = JRequest::getString('formtype');
        $model = $this->getModel('opengraph' . $type, "JFBConnectAdminModel");

        $cids = JRequest::getVar('cid');
        $filter = JFilterInput::getInstance();

        foreach ($cids as $id)
        {
            $id = $filter->clean($id, 'INT');
            $model->delete($id);
        }

        $newType = $type . "s";

        $this->setRedirect('index.php?option=com_jfbconnect&view=opengraph&task=' . $newType);

    }

    function save()
    {
        $this->updateSettings();
        $type = JRequest::getString('formtype');
        if ($type == 'settings')
            $this->setRedirect('index.php?option=com_jfbconnect&view=opengraph');
        else
            $this->setRedirect('index.php?option=com_jfbconnect&view=opengraph&task=' . $type . "s");
        $this->redirect();
    }

    function apply()
    {
        $redirect = $this->updateSettings();
        $this->setRedirect($redirect);
        $this->redirect();
    }

    private function updateSettings()
    {
        $type = JRequest::getString('formtype');
        if ($type == "settings")
            return $this->saveSettings();
        else if ($this->checkAutotune())
        {
            $model = $this->getModel('opengraph' . $type, 'JFBConnectAdminModel');

            $id = $model->store();
            if ($id)
            {
                JFBCFactory::log(JText::_('COM_JFBCONNECT_MSG_SETTINGS_UPDATED'));
                return 'index.php?option=com_jfbconnect&view=opengraph&task=' . $type . "edit&id=" . $id;
            }
            else
                return 'index.php?option=com_jfbconnect&view=opengraph&task=' . $type . "s";
        }

        JFBCFactory::log(JText::sprintf('COM_JFBCONNECT_MSG_RUN_AUTOTUNE_FOR_OPENGRAPH', '<a href="index.php?option=com_jfbconnect&view=autotune">Autotune configuration tool</a>.'),
            'error');
        return 'index.php?option=com_jfbconnect&view=opengraph&task=' . $type . "s";
    }

    private function checkAutotune()
    {
        // Saving an object or action
        $appConfig = JFBCFactory::config()->getSetting('autotune_app_config', array());
        $namespace = $appConfig['namespace'];
        if ($namespace == '')
            return false;
        else
            return true;
    }

    function publish()
    {
        $this->changeObjectState(1);
    }

    function unpublish()
    {
        $this->changeObjectState(0);
    }

    private function changeObjectState($newState)
    {
        $model = $this->getModel('opengraphobject', "JFBConnectAdminModel");

        $cids = JRequest::getVar('cid');
        $filter = JFilterInput::getInstance();

        foreach ($cids as $cid)
        {
            $id = $filter->clean($cid, 'INT');
            $object = $model->getObject($id);
            $object->published = $newState;
            $model->store($object);
        }
        $this->setRedirect('index.php?option=com_jfbconnect&view=opengraph&task=objects');
        $this->redirect();
    }

    function saveSettings()
    {
        $configs = JRequest::get('POST', 4);
        $model = $this->getModel('config');
        $model->saveSettings($configs);
        JFBCFactory::log(JText::_('COM_JFBCONNECT_MSG_SETTINGS_UPDATED'));
        return 'index.php?option=com_jfbconnect&view=opengraph&task=settings';
    }
}