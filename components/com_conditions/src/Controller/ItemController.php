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

namespace RegularLabs\Component\Conditions\Site\Controller;

use Joomla\CMS\MVC\Controller\FormController as JFormController;
use RegularLabs\Library\Input as RL_Input;

defined('_JEXEC') or die;

class ItemController extends JFormController
{
    /**
     * @var     string    The prefix to use with controller messages.
     */
    protected $text_prefix = 'RL';

    public function map()
    {
        RL_Input::set('tmpl', 'component');
        RL_Input::set('view', 'items');
        RL_Input::set('layout', 'modal_update_summary');

        return parent::display();
    }
}
