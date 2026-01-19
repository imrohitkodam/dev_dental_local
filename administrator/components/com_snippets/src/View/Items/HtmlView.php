<?php
/**
 * @package         Snippets
 * @version         9.3.8
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

namespace RegularLabs\Component\Snippets\Administrator\View\Items;

use JObject;
use Joomla\CMS\Form\Form as JForm;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Registry\Registry;
use RegularLabs\Component\Snippets\Administrator\Helper\Helper;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Input as RL_Input;
use RegularLabs\Library\Parameters as RL_Parameters;
use RegularLabs\Library\User as RL_User;

defined('_JEXEC') or die;

/**
 * List View
 */
class HtmlView extends BaseHtmlView
{
    /**
     * @var    array
     */
    public $activeFilters;
    /**
     * @var    JForm
     */
    public $filterForm;
    /**
     * @var  boolean
     */
    protected $collect_urls_enabled;
    /**
     * @var  boolean
     */
    protected $enabled;
    /**
     * @var  array
     */
    protected $items;
    /**
     * @var    Pagination
     */
    protected $pagination;
    /**
     * @var  Registry
     */
    protected $params;
    /**
     * @var  JObject
     */
    protected $state;

    /**
     * @param string $tpl The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  mixed  False if unsuccessful, otherwise void.
     *
     * @throws  GenericDataException
     */
    public function display($tpl = null)
    {
        $this->enabled       = Helper::isEnabled();
        $this->items         = $this->get('Items');
        $this->pagination    = $this->get('Pagination');
        $this->state         = $this->get('State');
        $this->config        = RL_Parameters::getComponent('snippets');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        $this->hasCategories = $this->get('HasCategories');
        $this->form          = $this->get('Form');

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        $this->addToolbar();

        return parent::display($tpl);
    }

    /**
     * @return  void
     */
    protected function addToolbar()
    {
        $canDo = ContentHelper::getActions('com_snippets');

        $viewLayout = RL_Input::getCmd('layout', 'default');

        $toolbar = RL_Document::getToolbar();

        if ($viewLayout == 'import')
        {
            ToolbarHelper::title(Text::_('SNIPPETS') . ': ' . Text::_('RL_IMPORT_ITEMS'), 'snippets icon-reglab');
            ToolbarHelper::back();

            return;
        }

        ToolbarHelper::title(Text::_('SNIPPETS') . ': ' . Text::_('RL_Items'), 'snippets icon-reglab');

        if ($canDo->get('core.create'))
        {
            $toolbar->addNew('item.add');
        }

        if ($canDo->get('core.edit.state') || RL_User::isAdministrator())
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
                $childBar->publish('items.publish')->listCheck(true);
                $childBar->unpublish('items.unpublish')->listCheck(true);
            }

            if ($canDo->get('core.admin'))
            {
                $childBar->checkin('items.checkin')->listCheck(true);
            }

            if ($canDo->get('core.edit.state') && $this->state->get('filter.state') !== '-2')
            {
                $childBar->trash('items.trash')->listCheck(true);
            }

            if ($canDo->get('core.create'))
            {
                $childBar->standardButton('copy')
                    ->text('JTOOLBAR_DUPLICATE')
                    ->task('items.duplicate')
                    ->listCheck(true);
            }

            if ($canDo->get('core.delete') && $this->state->get('filter.state') === '-2')
            {
                $toolbar->delete('items.delete')
                    ->text('JTOOLBAR_EMPTY_TRASH')
                    ->message('JGLOBAL_CONFIRM_DELETE')
                    ->listCheck(true);
            }

            if ($canDo->get('core.create'))
            {
                $childBar->standardButton('file-export')
                    ->text('RL_EXPORT')
                    ->task('items.export')
                    ->listCheck(true);
            }
        }

        if ($canDo->get('core.create'))
        {
            $toolbar->popupButton('file-import')
                ->icon('icon-file-import')
                ->text('RL_IMPORT')
                ->selector('importModal');
        }

        if ($canDo->get('core.admin'))
        {
            $toolbar->preferences('com_snippets');
        }
    }
}
