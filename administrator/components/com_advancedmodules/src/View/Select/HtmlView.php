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

namespace RegularLabs\Component\AdvancedModules\Administrator\View\Select;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Toolbar\Button\CustomButton;
use Joomla\CMS\Toolbar\Button\PopupButton;
use Joomla\CMS\Toolbar\ToolbarHelper;
use RegularLabs\Library\Document as RL_Document;

/**
 * HTML View class for the Modules component
 */
class HtmlView extends BaseHtmlView
{
    /**
     * An array of items
     *
     * @var  array
     */
    protected $items;
    /**
     * A suffix for links for modal use
     *
     * @var  string
     */
    protected $modalLink;
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
        $this->state     = $this->get('State');
        $this->items     = $this->get('Items');
        $this->modalLink = '';

        // Check for errors.
        if (count($errors = $this->get('Errors')))
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
        $state    = $this->get('State');
        $clientId = (int) $state->get('client_id', 0);

        // Add page title
        ToolbarHelper::title(Text::_('COM_MODULES_MANAGER_MODULES_SITE'), 'cube module');

        if ($clientId === 1)
        {
            ToolbarHelper::title(Text::_('COM_MODULES_MANAGER_MODULES_ADMIN'), 'cube module');
        }

        // Get the toolbar object instance
        $toolbar = RL_Document::getToolbar();

        $layout = new FileLayout('toolbar.cancelselect');

        $toolbar->customButton('new')
            ->html($layout->render(['client_id' => $clientId]));
    }
}
