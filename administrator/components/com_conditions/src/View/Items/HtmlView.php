<?php
/**
 * @package         Conditions
 * @version         25.11.2254
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

namespace RegularLabs\Component\Conditions\Administrator\View\Items;

use Joomla\CMS\Form\Form as JForm;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Registry\Registry;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Parameters as RL_Parameters;
use RegularLabs\Library\User as RL_User;

defined('_JEXEC') or die;

class HtmlView extends BaseHtmlView
{
    public array         $activeFilters;
    public JForm         $filterForm;
    protected bool       $collect_urls_enabled;
    protected bool       $enabled;
    protected array      $items;
    protected Pagination $pagination;
    protected Registry   $params;
    protected object     $state;

    public function display($tpl = null)
    {
        $model = $this->getModel();

        $this->items         = $model->getItems();
        $this->pagination    = $model->getPagination();
        $this->state         = $model->getState();
        $this->config        = RL_Parameters::getComponent('conditions');
        $this->filterForm    = $model->getFilterForm();
        $this->activeFilters = $model->getActiveFilters();
        $this->hasCategories = $model->getHasCategories();

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
     * @return  void
     */
    protected function addToolbar()
    {
        $canDo = ContentHelper::getActions('com_conditions');

        $toolbar = RL_Document::getToolbar();

        ToolbarHelper::title(Text::_('CONDITIONS') . ': ' . Text::_('RL_Items'), 'conditions icon-reglab');

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

            if (
                $canDo->get('core.edit.state')
                && $this->state->get('filter.state') !== ''
                && $this->state->get('filter.state') !== '1'
            )
            {
                $childBar->publish('items.publish')->listCheck(true);
            }

            if ($canDo->get('core.edit.state') && $this->state->get('filter.state') !== '-2')
            {
                $childBar->trash('items.trash')->listCheck(true);
            }

            if ($canDo->get('core.delete') && $this->state->get('filter.state') === '-2')
            {
                $childBar->delete('items.delete')->listCheck(true);
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

            //            if ($canDo->get('core.create'))
            //            {
            //                $childBar->standardButton('file-export')
            //                    ->text('RL_EXPORT')
            //                    ->task('items.export')
            //                    ->listCheck(true);
            //            }
        }

        //        if ($canDo->get('core.create'))
        //        {
        //            $toolbar->popupButton('file-import')
        //                ->icon('icon-file-import')
        //                ->text('RL_IMPORT')
        //                ->selector('importModal');
        //        }

        if ($canDo->get('core.admin'))
        {
            $toolbar->preferences('com_conditions');
        }
    }
}
