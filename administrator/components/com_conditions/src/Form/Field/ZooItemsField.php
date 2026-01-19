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

class ZooItemsField extends RL_FormField
{
    public bool $is_select_list  = true;
    public bool $use_ajax        = true;
    public bool $use_tree_select = true;

    public function getNamesByIds(array $values, array $attributes): array
    {
        $query = $this->db->getQuery(true)
            ->select('i.id, i.name, a.name as category, i.state as published')
            ->from('#__zoo_item AS i')
            ->join('LEFT', '#__zoo_application AS a ON a.id = i.application_id')
            ->where(RL_DB::is('i.id', $values))
            ->order('i.name');
        $this->db->setQuery($query);
        $items = $this->db->loadObjectList();

        return Form::getNamesWithExtras($items, ['category', 'unpublished']);
    }

    protected function getOptions()
    {
        $query = $this->db->getQuery(true)
            ->select('COUNT(*)')
            ->from('#__zoo_item AS i')
            ->where('i.state > -1');
        $this->db->setQuery($query);
        $total = $this->db->loadResult();

        if ($total > $this->max_list_count)
        {
            return -1;
        }

        $this->value = RL_Array::toArray($this->value);

        $query->clear('select')
            ->select('i.id, i.name, a.name as category, i.state as published')
            ->join('LEFT', '#__zoo_application AS a ON a.id = i.application_id')
            ->group('i.id')
            ->order('i.name, i.priority, i.id');
        $this->db->setQuery($query);
        $list = $this->db->loadObjectList();

        return $this->getOptionsByList($list, ['category', 'id']);
    }
}
