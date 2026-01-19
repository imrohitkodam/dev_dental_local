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
use RegularLabs\Library\Language as RL_Language;

class ZooCategoriesField extends RL_FormField
{
    public bool $is_select_list  = true;
    public bool $use_ajax        = true;
    public bool $use_tree_select = true;

    public function getNamesByIds(array $values, array $attributes): array
    {
        RL_Language::load('com_conditions');

        $app_values      = [];
        $category_values = [];

        foreach ($values as $value)
        {
            if (str_starts_with($value, 'app'))
            {
                $app_values[] = (int) str_replace('app', '', $value);
                continue;
            }

            $category_values[] = (int) $value;
        }

        $query = $this->db->getQuery(true)
            ->select('CONCAT("[",a.name,"]") as name, 1 as published')
            ->from('#__zoo_application AS a')
            ->where(RL_DB::is('a.id', $app_values))
            ->order('a.id');
        $this->db->setQuery($query);
        $apps = $this->db->loadObjectList();

        $query = $this->db->getQuery(true)
            ->select('c.name, c.published')
            ->from('#__zoo_category AS c')
            ->where(RL_DB::is('c.id', $category_values))
            ->order('c.ordering');
        $this->db->setQuery($query);
        $categories = $this->db->loadObjectList();

        $items = [...$apps, ...$categories];

        return Form::getNamesWithExtras($items, ['unpublished']);
    }

    protected function getOptions()
    {
        RL_Language::load('com_conditions');

        $query = $this->db->getQuery(true)
            ->select('COUNT(*)')
            ->from('#__zoo_category AS c')
            ->where('c.published > -1');
        $this->db->setQuery($query);
        $total = $this->db->loadResult();

        if ($total > $this->max_list_count)
        {
            return -1;
        }

        $this->value = RL_Array::toArray($this->value);

        $query->clear()
            ->select('CONCAT("app",a.id) as id, CONCAT("[",a.name,"]") as name, 1 as published, 0 as parent_id')
            ->from('#__zoo_application AS a')
            ->order('a.name, a.id');
        $this->db->setQuery($query);
        $apps = $this->db->loadObjectList();

        $query->clear()
            ->select('c.id, c.name, c.published, c.parent as parent_id, c.application_id as app_id')
            ->from('#__zoo_category AS c')
            ->where('c.published > -1')
            ->order('c.ordering, c.name');
        $this->db->setQuery($query);
        $categories = $this->db->loadObjectList();

        foreach ($categories as $category)
        {
            if ($category->parent_id == 0)
            {
                $category->parent_id = 'app' . $category->app_id;
            }
        }

        $items = [...$apps, ...$categories];

        $items = RL_Array::setLevelsByParentIds($items);

        return $this->getOptionsByList($items, ['unpublished']);
    }
}
