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

namespace RegularLabs\Component\Conditions\Administrator\Condition\Zoo;

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Menu\MenuItem as JMenuItem;
use RegularLabs\Component\Conditions\Administrator\Condition\Condition;
use RegularLabs\Library\Input as RL_Input;

abstract class Zoo extends Condition
{
    protected $request_keys = ['controller'];

    public function beforePass(): void
    {
        if ( ! $this->request->option == 'com_zoo')
        {
            return;
        }

        $full_request = RL_Input::getArray();

        $this->request->view = $this->request->task ?: $this->request->view;

        $this->request->idname = match ($this->request->view)
        {
            'item'     => 'item_id',
            'category' => 'category_id',
            default    => 'id',
        };

        if ( ! isset($full_request[$this->request->idname]))
        {
            $this->request->idname = 'id';
        }

        $this->request->id = $this->request->{$this->request->idname}
            ?? RL_Input::getInt($this->request->idname, 0);

        if ($this->request->id)
        {
            return;
        }

        $app = JFactory::getApplication();

        /* @var JMenuItem $menu_item */
        $menu = empty(self::$_request->Itemid)
            ? $app->getMenu('site')->getActive()
            : $app->getMenu('site')->getItem(self::$_request->Itemid);

        if (empty($menu))
        {
            return;
        }

        $id_name = $this->request->view == 'category' ? 'category' : 'item_id';

        $this->request->id = $menu->getParams()->get($id_name, 0);
    }
}
