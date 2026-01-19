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

namespace RegularLabs\Component\AdvancedModules\Administrator\View\Modules;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\ToolbarHelper;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Language as RL_Language;
use RegularLabs\Library\Parameters as RL_Parameters;

/**
 * View class for a list of modules.
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The active search filters
     *
     * @var    array
     * @since  4.0.0
     */
    public $activeFilters;
    /**
     * Form object for search filters
     *
     * @var    Form
     */
    public $filterForm;
    /**
     * An array of items
     *
     * @var  array
     */
    protected $items;
    /**
     * The pagination object
     *
     * @var  Pagination
     */
    protected $pagination;
    /**
     * The model state
     *
     * @var  CMSObject
     */
    protected $state;
    /**
     * Is this view an Empty State
     *
     * @var  boolean
     * @since 4.0.0
     */
    private $isEmptyState = false;

    /**
     * Display the view
     *
     * @param string $tpl The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  void
     */
    public function display($tpl = null)
    {
        //        $result = ConvertAssignments::convert('com_advancedmodules', 'advancedmodules', 'modules', 'title', 'moduleid');
        //        exit;
        RL_Language::load('com_modules', JPATH_ADMINISTRATOR);

        $this->items         = $this->get('Items');
        $this->pagination    = $this->get('Pagination');
        $this->state         = $this->get('State');
        $this->total         = $this->get('Total');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        $this->clientId      = $this->state->get('client_id');
        $this->hasCategories = $this->get('HasCategories');

        $this->getConfig();

        if ( ! count($this->items) && $this->isEmptyState = $this->get('IsEmptyState'))
        {
            $this->setLayout('emptystate');
        }

        /**
         * The code below make sure the remembered position will be available from filter dropdown even if there are no
         * modules available for this position. This will make the UI less confusing for users in case there is only one
         * module in the selected position and user:
         * 1. Edit the module, change it to new position, save it and come back to Modules Management Screen
         * 2. Or move that module to new position using Batch action
         */
        if (count($this->items) === 0 && $this->state->get('filter.position'))
        {
            $selectedPosition = $this->state->get('filter.position');
            $positionField    = $this->filterForm->getField('position', 'filter');

            $positionExists = false;

            foreach ($positionField->getOptions() as $option)
            {
                if ($option->value === $selectedPosition)
                {
                    $positionExists = true;
                    break;
                }
            }

            if ($positionExists === false)
            {
                $positionField->addOption($selectedPosition, ['value' => $selectedPosition]);
            }
        }

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        // We do not need the Language filter when modules are not filtered
        if ($this->clientId == 1 && ! ModuleHelper::isAdminMultilang())
        {
            unset($this->activeFilters['language']);
            $this->filterForm->removeField('language', 'filter');
        }

        // We don't need the toolbar in the modal window.
        if ($this->getLayout() !== 'modal')
        {
            $this->addToolbar();

            // We do not need to filter by language when multilingual is disabled
            if ( ! Multilanguage::isEnabled())
            {
                unset($this->activeFilters['language']);
                $this->filterForm->removeField('language', 'filter');
            }
        }
        // If in modal layout.
        else
        {
            // Client id selector should not exist.
            $this->filterForm->removeField('client_id', '');

            // If in the frontend state and language should not activate the search tools.
            if (Factory::getApplication()->isClient('site'))
            {
                unset($this->activeFilters['state']);
                unset($this->activeFilters['language']);
            }
        }

        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     */
    protected function addToolbar()
    {
        $state = $this->get('State');
        $canDo = ContentHelper::getActions('com_modules');
        $user  = Factory::getUser();

        // Get the toolbar object instance
        $toolbar = RL_Document::getToolbar();

        $title = JText::_('ADVANCEDMODULEMANAGER')
            . ' (' . JText::_($state->get('client_id') ? 'JADMINISTRATOR' : 'JSITE') . ')';

        if ($this->config->list_title)
        {
            $title = JText::_(
                $state->get('client_id')
                    ? 'COM_MODULES_MANAGER_MODULES_ADMIN'
                    : 'COM_MODULES_MANAGER_MODULES_SITE'
            );
        }

        ToolbarHelper::title($title, 'cube module');

        if ($canDo->get('core.create'))
        {
            $toolbar->standardButton('new', 'JTOOLBAR_NEW')
                ->onclick("location.href='index.php?option=com_advancedmodules&amp;view=select&amp;client_id=" . $this->state->get('client_id', 0) . "'");
        }

        if ( ! $this->isEmptyState && ($canDo->get('core.edit.state') || Factory::getUser()->authorise('core.admin')))
        {
            $dropdown = $toolbar->dropdownButton('status-group')
                ->text('JTOOLBAR_CHANGE_STATUS')
                ->toggleSplit(false)
                ->icon('icon-ellipsis-h')
                ->buttonClass('btn btn-action')
                ->listCheck(true);

            $childBar = $dropdown->getChildToolbar();

            if ($canDo->get('core.edit.state'))
            {
                $childBar->publish('modules.publish')->listCheck(true);

                $childBar->unpublish('modules.unpublish')->listCheck(true);
            }

            if (Factory::getUser()->authorise('core.admin'))
            {
                $childBar->checkin('modules.checkin')->listCheck(true);
            }

            if ($canDo->get('core.edit.state') && $this->state->get('filter.published') != -2)
            {
                $childBar->trash('modules.trash')->listCheck(true);
            }

            // Add a batch button
            if (
                $user->authorise('core.create', 'com_modules') && $user->authorise('core.edit', 'com_modules')
                && $user->authorise('core.edit.state', 'com_modules')
            )
            {
                $childBar->popupButton('batch')
                    ->text('JTOOLBAR_BATCH')
                    ->selector('collapseModal')
                    ->listCheck(true);
            }

            if ($canDo->get('core.create'))
            {
                $childBar->standardButton('copy')
                    ->text('JTOOLBAR_DUPLICATE')
                    ->task('modules.duplicate')
                    ->listCheck(true);
            }
        }

        if ( ! $this->isEmptyState && ($state->get('filter.state') == -2 && $canDo->get('core.delete')))
        {
            $toolbar->delete('modules.delete')
                ->text('JTOOLBAR_EMPTY_TRASH')
                ->message('JGLOBAL_CONFIRM_DELETE')
                ->listCheck(true);
        }

        if ($canDo->get('core.admin'))
        {
            $toolbar->preferences('com_advancedmodules');
        }

        $toolbar->help('Modules');
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
