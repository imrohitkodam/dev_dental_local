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

namespace RegularLabs\Component\Conditions\Site\View\Item;

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
        $this->model  = $this->getModel();
        $this->form   = $this->get('Form');
        $this->item   = $this->get('Item');
        $this->state  = $this->get('State');
        $this->config = RL_Parameters::getComponent('conditions', $this->state->params);

        $errors = $this->get('Errors');

        // Check for errors.
        if (count($errors))
        {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        parent::display($tpl);
    }
}
