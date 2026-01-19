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

namespace RegularLabs\Component\Conditions\Administrator\Condition\Hikashop;

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use RegularLabs\Component\Conditions\Administrator\Condition\HasArraySelection;
use RegularLabs\Library\Input as RL_Input;

class Category extends Hikashop
{
    use HasArraySelection;

    public function pass(): bool
    {
        if ($this->request->option !== 'com_hikashop')
        {
            return false;
        }

        if (empty($this->selection))
        {
            return false;
        }

        $app = JFactory::getApplication();

        $page_types  = $this->params->page_types ?? [];
        $is_category = $this->request->view == 'category' || $this->request->layout == 'listing';
        $is_item     = $this->request->view == 'product';

        if (
            ! (in_array('categories', $page_types) && $is_category)
            && ! (in_array('items', $page_types) && $is_item)
            && ! (RL_Input::getInt('rl_qp') && ! empty($this->getCategoryIds()))
        )
        {
            return false;
        }

        $category_ids = $this->getCategoryIds($is_category);

        $pass = $this->passSimple($category_ids);

        if ($pass)
        {
            // If passed, return false if assigned to only children
            // Else return true
            return (int) $this->params->include_children !== 2;
        }

        if ( ! $this->params->include_children)
        {
            return false;
        }

        $parent_ids = [];

        foreach ($category_ids as $category_id)
        {
            $parent_ids = [...$parent_ids, ...$this->getCategoryParentIds((int) $category_id)];
        }

        return $this->passSimple($parent_ids);
    }

    private function getCategoryIds(bool $is_category = false): array
    {
        if ($is_category)
        {
            return (array) $this->request->id;
        }

        switch (true)
        {
            case ($this->request->view == 'category' || $this->request->layout == 'listing'):
                include_once JPATH_ADMINISTRATOR . '/components/com_hikashop/helpers/helper.php';
                $menuClass = hikashop_get('class.menus');
                $menuData  = $menuClass->get($this->request->Itemid);

                return $this->makeArray($menuData->hikashop_params['selectparentlisting']);

            case ($this->request->id):
                $query = $this->db->getQuery(true)
                    ->select('c.category_id')
                    ->from('#__hikashop_product_category AS c')
                    ->where('c.product_id = ' . (int) $this->request->id);
                $this->db->setQuery($query);
                $cats = $this->db->loadColumn();

                return $this->makeArray($cats);

            default:
                return [];
        }
    }

    private function getCategoryParentIds(int $id = 0): array
    {
        return $this->getParentIds($id, 'hikashop_category', 'category_parent_id', 'category_id');
    }
}
