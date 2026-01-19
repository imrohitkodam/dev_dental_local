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

namespace RegularLabs\Component\Snippets\Administrator\Controller;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

/**
 * Snippets master display controller.
 */
class DisplayController extends BaseController
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
        $view   = $this->input->get('view', 'items');
        $layout = $this->input->get('layout', 'default');
        $id     = $this->input->getInt('id');

        // Check for edit form.
        if ($view == 'item' && $layout == 'edit' && ! $this->checkEditId('com_snippets.edit.item', $id))
        {
            // Somehow the person just went to the form - we don't allow that.
            $this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id), 'error');
            $this->setRedirect(Route::_('index.php?option=com_snippets&view=items', false));

            return false;
        }

        return parent::display();
    }
}
