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

namespace RegularLabs\Component\Conditions\Administrator\Controller;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

/**
 * Conditions master display controller.
 */
class DisplayController extends FormController
{
    protected $default_view = 'items';

    /**
     * @param boolean $cachable  If true, the view output will be cached.
     * @param mixed   $urlparams An array of safe URL parameters and their variable types, for valid values see {@link \JFilterInput::clean()}.
     *
     * @return  static|boolean     This object to support chaining or false on failure.
     */
    public function display($cachable = false, $urlparams = false)
    {
        $view   = $this->input->get('view', $this->default_view);
        $layout = $this->input->get('layout', $view == 'item' ? 'edit' : 'default');
        $id     = $this->input->getInt('id');

        // Check for edit form.
        if ($view == 'item' && $layout == 'edit' && ! $this->checkEditId('com_conditions.edit.item', $id))
        {
            // Somehow the person just went to the form - we don't allow that.
            $this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id), 'error');
            $this->setRedirect(Route::_('index.php?option=com_conditions&view=items', false));

            return false;
        }

        $this->input->set('layout', $layout);

        return parent::display();
    }
}
