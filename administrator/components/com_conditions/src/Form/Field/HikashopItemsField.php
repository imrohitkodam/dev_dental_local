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

namespace RegularLabs\Component\Conditions\Administrator\Form\Field;

defined('_JEXEC') or die;

use RegularLabs\Library\ArrayHelper as RL_Array;
use RegularLabs\Library\DB as RL_DB;
use RegularLabs\Library\Form\Form;
use RegularLabs\Library\Form\FormField as RL_FormField;

class HikashopItemsField extends RL_FormField
{
    public bool $is_select_list  = true;
    public bool $use_ajax        = true;
    public bool $use_tree_select = true;

    public function getNamesByIds(array $values, array $attributes): array
    {
        $query = $this->db->getQuery(true)
            ->select('p.product_id as id, p.product_name AS name, p.product_published AS published, c.category_name AS category')
            ->from('#__hikashop_product AS p')
            ->join('LEFT', '#__hikashop_product_category AS x ON x.product_id = p.product_id')
            ->join('INNER', '#__hikashop_category AS c ON c.category_id = x.category_id')
            ->where(RL_DB::is('p.product_id', $values))
            ->order('p.product_name');
        $this->db->setQuery($query);
        $items = $this->db->loadObjectList();

        return Form::getNamesWithExtras($items, ['category', 'unpublished']);
    }

    protected function getOptions()
    {
        $query = $this->db->getQuery(true)
            ->select('COUNT(*)')
            ->from('#__hikashop_product AS p')
            ->where('p.product_published = 1')
            ->where('p.product_type = ' . $this->db->quote('main'));
        $this->db->setQuery($query);
        $total = $this->db->loadResult();

        if ($total > $this->max_list_count)
        {
            return -1;
        }

        $this->value = RL_Array::toArray($this->value);

        $query->clear('select')
            ->select('p.product_id as id, p.product_name AS name, p.product_published AS published, c.category_name AS category')
            ->join('LEFT', '#__hikashop_product_category AS x ON x.product_id = p.product_id')
            ->join('INNER', '#__hikashop_category AS c ON c.category_id = x.category_id')
            ->group('p.product_id')
            ->order('p.product_name');
        $this->db->setQuery($query);
        $items = $this->db->loadObjectList();

        return $this->getOptionsByList($items, ['category', 'id', 'unpublished'], -2);
    }
}
