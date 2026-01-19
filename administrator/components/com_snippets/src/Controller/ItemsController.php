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

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

class ItemsController extends AdminController
{
    /**
     * @var     string    The prefix to use with controller messages.
     */
    protected $text_prefix = 'RL';

    /**
     * Duplicate Method
     * Duplicate all items specified by array id
     */
    public function duplicate()
    {
        $ids        = $this->input->get('cid', [], 'array');
        $model      = $this->getModel('Items');
        $item_model = $this->getModel('Item');

        $model->duplicate($ids, $item_model);

        $this->setRedirect(Route::_('index.php?option=com_snippets&view=items', false));
    }

    /**
     * Export Method
     * Export the selected items specified by id
     */
    public function export()
    {
        $ids   = $this->input->get('cid', [], 'array');
        $model = $this->getModel('Items');

        $model->export($ids);
    }

    /**
     * Proxy for getModel.
     *
     * @param string $name   The name of the model.
     * @param string $prefix The prefix of the model.
     * @param array  $config An array of settings.
     *
     * @return  BaseDatabaseModel The model instance
     */
    public function getModel($name = 'Item', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }

    /**
     * Import Method
     * Set layout to import
     */
    public function import()
    {
        $file = $this->input->files->get('file', null, 'raw');

        // Get the model.
        $model      = $this->getModel('Items');
        $model_item = $this->getModel('Item');
        $model->import($file, $model_item);

        $this->setRedirect(Route::_('index.php?option=com_snippets&view=items', false));
    }
}
