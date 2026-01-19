<?php
/**
 * @package         Advanced Module Manager
 * @version         10.4.8
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

namespace RegularLabs\Component\AdvancedModules\Administrator\View\Module;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Toolbar\ToolbarHelper;
use RegularLabs\Component\AdvancedModules\Administrator\Model\ModuleModel;
use RegularLabs\Library\Input as RL_Input;
use RegularLabs\Library\Language as RL_Language;
use RegularLabs\Library\Parameters as RL_Parameters;

/**
 * View to edit a module.
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The actions the user is authorised to perform
     *
     * @var    CMSObject
     */
    protected $canDo;
    /**
     * The Form object
     *
     * @var  Form
     */
    protected $form;
    /**
     * The active item
     *
     * @var  object
     */
    protected $item;
    /**
     * The model state
     *
     * @var  CMSObject
     */
    protected $state;

    /**
     * Display the view
     *
     * @param string $tpl The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */
    public function display($tpl = null)
    {
        RL_Language::load('com_modules', JPATH_ADMINISTRATOR);

        /** @var ModuleModel $model */
        $model = $this->getModel();

        $this->state = $model->getState();

        // Have to stop it earlier, because on cancel task for a new module we do not have an ID, and Model doing redirect on getItem()
        if ($this->getLayout() === 'modalreturn' && !$this->state->get('module.id')) {
            parent::display($tpl);

            return;
        }

        $this->form  = $model->getForm();
        $this->item  = $model->getItem();

        $this->canDo = ContentHelper::getActions('com_modules', 'module', $this->item->id);

        if ($this->getLayout() === 'modalreturn') {
            parent::display($tpl);

            return;
        }

        $this->getConfig();

        // Check for errors.
        $errors = $model->getErrors();

        if (count($errors))
        {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        $this->addToolbar();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     */
    protected function addToolbar()
    {
        RL_Input::set('hidemainmenu', true);

        $user       = Factory::getUser();
        $isNew      = ($this->item->id == 0);
        $checkedOut = ! (is_null($this->item->checked_out) || $this->item->checked_out == $user->get('id'));
        $canDo      = $this->canDo;

        $title = $this->item->title . ' [' . $this->item->module . ']';
        ToolbarHelper::title(JText::sprintf('AMM_MODULE_EDIT', $title), 'cube module');

        // For new records, check the create permission.
        if ($isNew && $canDo->get('core.create'))
        {
            ToolbarHelper::apply('module.apply');

            ToolbarHelper::saveGroup(
                [
                    ['save', 'module.save'],
                    ['save2new', 'module.save2new'],
                ],
                'btn-success'
            );

            ToolbarHelper::cancel('module.cancel');
        }
        else
        {
            $toolbarButtons = [];

            // Can't save the record if it's checked out.
            if ( ! $checkedOut)
            {
                // Since it's an existing record, check the edit permission.
                if ($canDo->get('core.edit'))
                {
                    ToolbarHelper::apply('module.apply');

                    $toolbarButtons[] = ['save', 'module.save'];

                    // We can save this record, but check the create permission to see if we can return to make a new one.
                    if ($canDo->get('core.create'))
                    {
                        $toolbarButtons[] = ['save2new', 'module.save2new'];
                    }
                }
            }

            // If checked out, we can still save
            if ($canDo->get('core.create'))
            {
                $toolbarButtons[] = ['save2copy', 'module.save2copy'];
            }

            ToolbarHelper::saveGroup(
                $toolbarButtons,
                'btn-success'
            );

            ToolbarHelper::cancel('module.cancel', 'JTOOLBAR_CLOSE');
        }

        // Get the help information for the menu item.
        $lang = Factory::getApplication()->getLanguage();

        $help = $this->get('Help');

        if ($lang->hasKey($help->url))
        {
            $debug = $lang->setDebug(false);
            $url   = Text::_($help->url);
            $lang->setDebug($debug);
        }
        else
        {
            $url = null;
        }

        ToolbarHelper::help($help->key, false, $url);
    }

    /**
     * Function that gets the config settings
     *
     * @return    Object
     */
    protected function getConfig()
    {
        if (isset($this->config))
        {
            return $this->config;
        }

        $this->config = RL_Parameters::getComponent('advancedmodules');

        return $this->config;
    }
}
