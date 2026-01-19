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

namespace RegularLabs\Plugin\EditorButton\Snippets;

defined('_JEXEC') or die;

use RegularLabs\Component\Snippets\Administrator\Model\ItemsModel as SNP_ItemsModel;
use RegularLabs\Library\Plugin\EditorButtonPopup as RL_EditorButtonPopup;
use RegularLabs\Library\Input as RL_Input;

class Popup extends RL_EditorButtonPopup
{
    public    $items;
    public    $filterForm;
    public    $pagination;
    public    $state;
    protected $extension         = 'snippets';
    protected $require_core_auth = false;

    public function init(): void
    {
        @define('JPATH_COMPONENT', JPATH_ADMINISTRATOR . '/components/com_snippets');
        @define('JPATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR . '/components/com_snippets');

        $model = new SNP_ItemsModel;

        $limitstart = RL_Input::getInt('limitstart', 0);

        $this->state = $model->getState();
        $model->setState('client_id', 0);

        if ($limitstart)
        {
            $model->setState('list.start', $limitstart);
        }

        $this->filterForm = $model->getFilterForm();
        $this->items      = $model->getItems(true);
        $this->pagination = $model->getPagination();
    }
}
