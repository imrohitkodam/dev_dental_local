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

use Joomla\CMS\MVC\Controller\FormController as JFormController;
use Joomla\CMS\Router\Route as JRoute;
use RegularLabs\Component\Conditions\Administrator\Helper\Helper;
use RegularLabs\Component\Conditions\Administrator\Model\ItemModel;
use RegularLabs\Library\Input as RL_Input;

defined('_JEXEC') or die;

class ItemController extends JFormController
{
    /**
     * @var     string    The prefix to use with controller messages.
     */
    protected $text_prefix = 'RL';

    /**
     * Duplicate the item and then edit it
     */
    public function copy()
    {
        $extension   = $this->input->get('extension', '');
        $item_id     = $this->input->getInt('item_id');
        $table       = $this->input->get('table', '');
        $name_column = $this->input->get('name_column', '');
        $id          = $this->input->getInt('id');

        $name = '';

        /* @var ItemModel $item_model */
        $item_model = $this->getModel('Item');

        if ( ! $id)
        {
            $condition = $item_model->getConditionByExtensionItem($extension, $item_id);
            $id        = $condition->id;
        }

        if ($extension && $item_id)
        {
            $name = Helper::getForItemText($extension, $item_id, $table, $name_column);
        }

        $condition = $item_model->duplicate($id, true, $name);
        ItemModel::map($condition->id, $extension, $item_id);

        $this->setRedirect(
            JRoute::_(
                'index.php?option=' . $this->option . '&view=' . $this->view_item
                . '&task=item.edit'
                . $this->getRedirectToItemAppend($condition->id), false
            )
        );
    }

    /**
     * Map an existing condition to an extension item
     */
    public function map()
    {
        $condition_id = $this->input->getInt('id');
        $extension    = $this->input->get('extension', '');
        $item_id      = $this->input->getInt('item_id');

        $item_model = $this->getModel('Item');

        $item_model->map($condition_id, $extension, $item_id);

        RL_Input::set('tmpl', 'component');
        RL_Input::set('layout', 'modal_update_summary');

        return parent::display();
    }

    /**
     * ...
     */
    public function modaledit($data = [], $key = 'id')
    {
        $extension = $this->input->get('extension', '');
        $item_id   = $this->input->getInt('item_id');

        $this->input->set('tmpl', 'component');
        $this->input->set('layout', 'modal_edit');

        if ( ! $extension)
        {
            return parent::display();
        }

        $item_model = $this->getModel('Item');
        $condition  = $item_model->getConditionByExtensionItem($extension, $item_id);

        if ( ! $condition || $condition->nr_of_uses < 2)
        {
            return parent::display();
        }

        RL_Input::set('layout', 'modal_multiple_usage');

        return parent::display();
    }

    /**
     * Map an existing condition to an extension item
     */
    public function remove_mapping()
    {
        $extension = $this->input->get('extension', '');
        $item_id   = $this->input->getInt('item_id');
        $remove    = $this->input->get('remove', '');

        RL_Input::set('tmpl', 'component');
        RL_Input::set('layout', 'modal_update_summary');

        switch ($remove)
        {
            case 'all':
                $this->getModel('Item')->trashByExtension($extension, $item_id);
                break;

            case 1:
                ItemModel::removeMapping($extension, $item_id);
                break;

            default:
                if (ItemModel::hasOtherUsesByExtensionItem($extension, $item_id))
                {
                    ItemModel::removeMapping($extension, $item_id);
                    break;
                }

                RL_Input::set('layout', 'modal_remove_mapping');
                break;
        }

        return parent::display();
    }

    /**
     * Gets the URL arguments to append to an item redirect.
     *
     * @param integer $recordId The primary key id for the item.
     * @param string  $urlVar   The name of the URL variable for the id.
     *
     * @return  string  The arguments to append to the redirect URL.
     */
    protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
    {
        $task   = $this->getTask();
        $layout = $this->input->getString('layout');

        $is_modal = $this->input->get('tmpl', '') === 'component';

        if ($is_modal && ! str_starts_with($layout, 'modal'))
        {
            $layout = 'modal_' . ($layout ?: 'edit');
        }

        if ($is_modal && $task === 'save')
        {
            $this->view_list = 'item';
            $layout          = 'modal_update_summary';
        }

        $params = [
            $urlVar          => $recordId,
            'id'             => $this->input->getInt('id'),
            'extension'      => $this->input->get('extension', ''),
            'item_id'        => $this->input->getInt('item_id'),
            'table'          => $this->input->get('table', ''),
            'enabled_types'  => $this->input->getString('enabled_types'),
            'name_column'    => $this->input->get('name_column', ''),
            'message'        => $this->input->get('message', ''),
            'tmpl'           => $this->input->getString('tmpl'),
            'layout'         => $layout,
            'forcedLanguage' => $this->input->get('forcedLanguage', '', 'cmd'),
            'return'         => $this->input->getBase64('return'),
        ];

        $append = http_build_query($params);

        return $append ? '&' . $append : '';
    }

    /**
     * Gets the URL arguments to append to a list redirect.
     *
     * @return  string  The arguments to append to the redirect URL.
     *
     * @since   1.6
     */
    protected function getRedirectToListAppend()
    {
        $task = $this->getTask();

        $is_modal = $this->input->get('tmpl', '') === 'component';

        $params = [
            'id'             => $this->input->getInt('id'),
            'extension'      => $this->input->get('extension', ''),
            'item_id'        => $this->input->getInt('item_id'),
            'table'          => $this->input->get('table', ''),
            'name_column'    => $this->input->get('name_column', ''),
            'enabled_types'  => $this->input->getString('enabled_types'),
            'message'        => $this->input->get('message', ''),
            'tmpl'           => $this->input->getString('tmpl'),
            'forcedLanguage' => $this->input->get('forcedLanguage', '', 'cmd'),
        ];

        if ($is_modal && $task === 'save')
        {
            $params['layout'] = 'modal_update_summary';
        }

        $append = http_build_query($params);

        return $append ? '&' . $append : '';
    }
}
