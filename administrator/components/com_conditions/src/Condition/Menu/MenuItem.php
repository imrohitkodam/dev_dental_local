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

namespace RegularLabs\Component\Conditions\Administrator\Condition\Menu;

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use RegularLabs\Component\Conditions\Administrator\Condition\Condition;
use RegularLabs\Component\Conditions\Administrator\Condition\HasArraySelection;
use RegularLabs\Library\Document as RL_Document;

class MenuItem extends Condition
{
    use HasArraySelection;

    public function pass(): bool
    {
        // return if no Itemid or selection is set
        if ( ! $this->request->Itemid || empty($this->selection))
        {
            return false;
        }

        // return true if menu is in selection
        if (in_array($this->request->Itemid, $this->selection))
        {
            return (int) $this->params->include_children !== 2;
        }

        // return false if selection is 0 or empty
        if (count($this->selection) === 1 && empty($this->selection[0]))
        {
            return false;
        }

        $menutype = 'type.' . self::getMenuType();

        // return true if menu type is in selection
        if (in_array($menutype, $this->selection))
        {
            return true;
        }

        if ( ! $this->params->include_children)
        {
            return false;
        }

        $parent_ids = $this->getMenuParentIds((int) $this->request->Itemid);
        $parent_ids = array_diff($parent_ids, [1]);

        foreach ($parent_ids as $parent_id)
        {
            if (in_array($parent_id, $this->selection))
            {
                return true;
            }
        }

        return false;
    }

    private function getMenuParentIds(int $id = 0): array
    {
        return $this->getParentIds($id, 'menu');
    }

    private function getMenuType(): string
    {
        if (isset($this->request->menutype))
        {
            return $this->request->menutype;
        }

        if (empty($this->request->Itemid))
        {
            $this->request->menutype = '';

            return $this->request->menutype;
        }

        if (RL_Document::isClient('site'))
        {
            $menu = JFactory::getApplication()->getMenu()->getItem((int) $this->request->Itemid);

            $this->request->menutype = $menu->menutype ?? '';

            return $this->request->menutype;
        }

        $query = $this->db->getQuery(true)
            ->select('m.menutype')
            ->from('#__menu AS m')
            ->where('m.id = ' . (int) $this->request->Itemid);
        $this->db->setQuery($query);
        $this->request->menutype = $this->db->loadResult();

        return $this->request->menutype;
    }
}
