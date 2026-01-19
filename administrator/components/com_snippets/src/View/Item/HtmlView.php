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

namespace RegularLabs\Component\Snippets\Administrator\View\Item;

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
    /**
     * @var    object
     */
    protected $config;
    /**
     * @var  JForm
     */
    protected $form;
    /**
     * @var  object
     */
    protected $item;
    /**
     * @var    object
     */
    protected $state;

    /**
     * @param string $tpl The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  mixed  False if unsuccessful, otherwise void.
     */
    public function display($tpl = null)
    {
        $this->form   = $this->get('Form');
        $this->item   = $this->get('Item');
        $this->state  = $this->get('State');
        $this->config = RL_Parameters::getComponent('snippets', $this->state->params);

        // Check for errors.
        if (count($errors = $this->get('Errors')))
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
        $isNew = ($this->item->id == 0);
        $canDo = ContentHelper::getActions('com_snippets');

        RL_Input::set('hidemainmenu', true);

        ToolbarHelper::title(Text::_('SNIPPETS') . ': ' . Text::_('RL_ITEM'), 'snippets icon-reglab');

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
