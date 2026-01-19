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

use Joomla\CMS\Language\Text as JText;
use RegularLabs\Library\ArrayHelper as RL_Array;
use RegularLabs\Library\DB as RL_DB;
use RegularLabs\Library\Form\Form;
use RegularLabs\Library\Form\FormField as RL_FormField;
use RegularLabs\Library\Language as RL_Language;

class HikashopCategoriesField extends RL_FormField
{
    public bool $is_select_list  = true;
    public bool $use_ajax        = true;
    public bool $use_tree_select = true;

    public function getNamesByIds(array $values, array $attributes): array
    {
        RL_Language::load('com_conditions');

        $query = $this->db->getQuery(true)
            ->select('c.category_id as id, c.category_parent_id AS parent_id, c.category_name AS name, c.category_published as published')
            ->from('#__hikashop_category AS c')
            ->where(RL_DB::is('c.category_id', $values))
            ->order('c.category_left');
        $this->db->setQuery($query);
        $categories = $this->db->loadObjectList();

        foreach ($categories as $category)
        {
            if ($category->name == 'product category')
            {
                $category->name = JText::_('CON_PRODUCTS');
                break;
            }
        }

        return Form::getNamesWithExtras($categories, ['unpublished']);
    }

    protected function getOptions()
    {
        RL_Language::load('com_conditions');

        $query = $this->db->getQuery(true)
            ->select('COUNT(*)')
            ->from('#__hikashop_category AS c')
            ->where('c.category_type = ' . $this->db->quote('product'))
            ->where('c.category_published > -1')
            ->where('c.category_parent_id > 0');
        $this->db->setQuery($query);
        $total = $this->db->loadResult();

        if ($total > $this->max_list_count)
        {
            return -1;
        }

        $this->value = RL_Array::toArray($this->value);

        $query->clear('select')
            ->select('c.category_id as id, c.category_depth AS level, c.category_name AS name, c.category_published as published')
            ->order('c.category_left');
        $this->db->setQuery($query);
        $categories = $this->db->loadObjectList();

        foreach ($categories as $category)
        {
            if ($category->name == 'product category')
            {
                $category->name = JText::_('CON_PRODUCTS');
                break;
            }
        }

        return $this->getOptionsByList($categories, ['unpublished'], -1);
    }
}
