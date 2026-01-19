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

use RegularLabs\Library\DB as RL_DB;
use RegularLabs\Library\Form\Form;
use RegularLabs\Library\Form\FormField as RL_FormField;

class FlexicontentTagsField extends RL_FormField
{
    public bool $is_select_list  = true;
    public bool $use_ajax        = true;
    public bool $use_tree_select = true;

    public function getNamesByIds(array $values, array $attributes): array
    {
        $query = $this->db->getQuery(true)
            ->select('t.name as id, t.name, t.published')
            ->from('#__flexicontent_tags AS t')
            ->where(RL_DB::is('t.name', $values))
            ->group('t.name')
            ->order('t.name');
        $this->db->setQuery($query);
        $tags = $this->db->loadObjectList();

        return Form::getNamesWithExtras($tags, ['unpublished']);
    }

    protected function getOptions()
    {
        $query = $this->db->getQuery(true)
            ->select('t.name as id, t.name, t.published')
            ->from('#__flexicontent_tags AS t')
            ->where('t.published > -1')
            ->where('t.name != ' . $this->db->quote(''))
            ->group('t.name')
            ->order('t.name');
        $this->db->setQuery($query);
        $tags = $this->db->loadObjectList();

        return $this->getOptionsByList($tags, ['unpublished']);
    }
}
