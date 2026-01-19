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

use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Joomla\CMS\Language\Text as JText;
use RegularLabs\Library\ArrayHelper as RL_Array;
use RegularLabs\Library\DB as RL_DB;
use RegularLabs\Library\Form\Form as RL_Form;
use RegularLabs\Library\Form\FormField as RL_FormField;
use RegularLabs\Library\Language as RL_Language;

class ConditionSelectionField extends RL_FormField
{
    public bool $is_select_list = true;
    public bool $use_ajax       = true;

    public function getNameById(string $value, array $attributes): string
    {
        return RL_Array::implode($this->getNamesByIds([$value], $attributes));
    }

    public function getNamesByIds(array $values, array $attributes): array
    {
        $db    = RL_DB::get();
        $query = $db->getQuery(true)
            ->select('DISTINCT a.id, a.alias, a.name')
            ->from('#__conditions AS a')
            ->where('a.published = 1')
            ->where(RL_DB::is('a.id', $values))
            ->order('a.name');

        $db->setQuery($query);
        $fields = $db->loadObjectList();

        return RL_Form::getNamesWithExtras($fields);
    }

    protected function getOptions()
    {
        RL_Language::load('com_conditions');

        $current_id = 0;

        if (($this->parent_request->option ?? '') === 'com_conditions')
        {
            $current_id = ($this->parent_request->id ?? 0);
        }

        $db    = RL_DB::get();
        $query = $db->getQuery(true)
            ->select('DISTINCT a.id, a.alias, a.name')
            ->from('#__conditions AS a')
            ->where('a.published = 1')
            ->where('a.id != ' . $current_id)
            ->order('a.name');

        $db->setQuery($query);

        $conditions = $db->loadObjectList();

        $options = [];

        $options[] = JHtml::_('select.option', '', '- ' . JText::_('CON_SELECT_A_CONDITION') . ' -');

        foreach ($conditions as $condition)
        {
            $options[] = JHtml::_('select.option', $condition->id, $condition->name);
        }

        return $options;
    }
}
