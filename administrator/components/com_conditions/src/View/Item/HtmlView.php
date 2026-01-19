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

namespace RegularLabs\Component\Conditions\Administrator\View\Item;

use Joomla\CMS\Form\Form as JForm;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use RegularLabs\Library\Input as RL_Input;
use RegularLabs\Library\Parameters as RL_Parameters;

defined('_JEXEC') or die;

/**
 * Item View
 */
class HtmlView extends BaseHtmlView
{
    protected object $config;
    protected JForm $form;
    protected object $item;
    protected object $state;

    public function display($tpl = null)
    {
        $this->model  = $this->getModel();

        $this->form   = $this->model->getForm();
        $this->item   = $this->model->getItem();
        $this->state  = $this->model->getState();
        $this->config = RL_Parameters::getComponent('conditions', $this->state->params);

        $errors = $this->model->getErrors();

        if (count($errors))
        {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar()
    {
        $isNew = ($this->item->id == 0);
        $canDo = ContentHelper::getActions('com_conditions');

        RL_Input::set('hidemainmenu', true);

        ToolbarHelper::title(Text::_('CONDITIONS') . ': ' . Text::_('RL_ITEM'), 'conditions icon-reglab');

        $toolbarButtons = [];

        // If not checked out, can save the item.
        if ($canDo->get('core.edit'))
        {
            ToolbarHelper::apply('item.apply');
            $toolbarButtons[] = ['save', 'item.save'];
        }

        /**
         * This component does not support Save as Copy due to uniqueness checks.
         * While it can be done, it causes too much confusion if the user does
         * not change the Old URL.
         */
        if ($canDo->get('core.edit') && $canDo->get('core.create'))
        {
            $toolbarButtons[] = ['save2new', 'item.save2new'];
        }

        if ( ! $isNew && $canDo->get('core.create'))
        {
            $toolbarButtons[] = ['save2copy', 'item.save2copy'];
        }

        ToolbarHelper::saveGroup(
            $toolbarButtons,
            'btn-success'
        );

        if (empty($this->item->id))
        {
            ToolbarHelper::cancel('item.cancel');
        }
        else
        {
            ToolbarHelper::cancel('item.cancel', 'JTOOLBAR_CLOSE');
        }
    }
}
