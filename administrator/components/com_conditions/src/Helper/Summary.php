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

namespace RegularLabs\Component\Conditions\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text as JText;
use RegularLabs\Library\ArrayHelper as RL_Array;
use RegularLabs\Library\Date as RL_Date;
use RegularLabs\Library\Language as RL_Language;
use RegularLabs\Library\StringHelper as RL_String;

class Summary
{
    public static function render(
        ?object $condition,
        string  $extension = '',
        string  $message = ''
    ): string
    {
        if (empty($condition))
        {
            return '';
        }

        RL_Language::load('com_conditions', JPATH_ADMINISTRATOR);

        if ($extension && ! empty($condition->name))
        {
            RL_Language::load($extension);
        }

        $no_rules_set = '<div class="alert alert-warning">'
            . '<span class="icon-info-circle text-info" aria-hidden="true"></span> '
            . JText::_('CON_MESSAGE_NO_RULES_SELECTED')
            . ($message ? '<br><br>' . JText::_($message) : '')
            . '</div>';

        $html = [];

        if ($extension && ! empty($condition->name))
        {
            $html[] = '<h4>'
                . '<small class="text-uppercase text-muted">' . JText::_('CON_CONDITION_SET') . ':</small><br>'
                . RL_String::escape($condition->name)
                . '</h4>';
        }

        $output = self::getGroupsOutput($condition);

        if ($output === false)
        {
            $output = $no_rules_set;
        }

        $html[] = $output;

        return implode('', $html);
    }

    private static function getAttributeOutput(
        string       $key,
        string|array $value,
        string       $default = 'CON_NONE',
        bool         $output_as_list = false
    ): string
    {
        if (is_array($value) && $output_as_list)
        {
            $value = '<ul><li>' . RL_Array::implode($value, '</li><li>') . '</li></ul>';
        }

        if (is_array($value))
        {
            $value = RL_Array::implode($value, ', ');
        }

        if (empty($value) && $value !== '0')
        {
            $value = JText::_('<em>%s</em>,' . $default);
        }

        return '<strong>' . $key . '</strong>: ' . $value;
    }

    private static function getAttributesOutputByRule(object $rule): string
    {
        $attributes = [];

        foreach ($rule->params as $key => $value)
        {
            $output = self::getRuleLineOutput($rule, $key, $value);

            if ( ! $output)
            {
                continue;
            }

            $attributes[] = $output;
        }

        if (empty($attributes))
        {
            return '';
        }

        return '<div class="small">' . implode('<br>', $attributes) . '</div>';
    }

    private static function getDateTimeValue(
        string $value,
        string $type,
        bool   $recurring = false
    ): string
    {
        if ( ! $value)
        {
            return '<em>' . JText::_('CON_NOT_SPECIFIED') . '</em>';
        }

        if ($type == 'date__time')
        {
            return RL_Date::fixTime($value, false);
        }

        $format = $recurring
            ? 'd F H:i'
            : 'd F Y H:i';

        return Date::getString($value, $format, true);
    }

    private static function getGlue(bool $match_all = true): string
    {
        $text  = JText::_($match_all ? 'CON_AND' : 'CON_OR');
        $class = $match_all ? 'bg-info' : 'bg-warning text-black';

        return '<div class="text-center"><span class="badge px-4 ' . $class . '">' . $text . '</span></div>';
    }

    private static function getGroupsOutput(object $condition): string|false
    {
        $condition->groups = RL_Array::clean($condition->groups ?? []);

        if (empty($condition->groups))
        {
            return false;
        }

        $groups = [];

        foreach ($condition->groups as $group)
        {
            $groups[] = self::getOutputByGroup($group);
        }

        $groups = RL_Array::clean($groups);

        if (empty($groups))
        {
            return false;
        }

        if (count($groups) < 2)
        {
            $html[] = implode('', $groups);

            return implode('', $html);
        }

        foreach ($groups as &$group)
        {
            $group = '<div class="card border-dark rl-card"><div class="card-body">' . $group . '</div></div>';
        }

        $html[] = implode(self::getGlue($condition->match_all), $groups);

        return implode('', $html);
    }

    private static function getNameString(string $name, string $type = ''): string
    {
        $id = str_replace('-', '_', $name);

        $field = Helper::getField($type . '.' . $id) ?? Helper::getField($id) ?? null;

        if ( ! $field || ! isset($field->name))
        {
            return JText::_($name);
        }

        $name = trim($field->name);

        if (empty($field->attributes['group_name']))
        {
            return JText::_($name);
        }

        return JText::_($field->attributes['group_name']) . ': ' . JText::_($name);
    }

    private static function getOutputByGroup(object $group): string
    {
        $rules = [];

        foreach ($group->rules as $rule)
        {
            $rules[] = self::getOutputByRule($rule);
        }

        return implode(self::getGlue($group->match_all), $rules);
    }

    private static function getOutputByRule(object $rule): string
    {
        $html = [];

        $state       = self::getNameString('exclude.' . $rule->exclude);
        $state_class = $rule->exclude ? 'danger' : 'success';
        $type_name   = self::getNameString('type.' . $rule->type);

        $values_output     = self::getValuesOutputByRule($rule);
        $attributes_output = self::getAttributesOutputByRule($rule);

        $class = 'alert alert-' . $state_class;

        if (isset($rule->disabled) && $rule->disabled)
        {
            $class .= ' disabled ghosted';
        }

        $html[] = '<div class="' . $class . '">';

        if (isset($rule->disabled) && $rule->disabled)
        {
            $html[] = '<div class="alert alert-danger">'
                . JText::_('RL_ONLY_AVAILABLE_IN_PRO')
                . '</div>';
        }

        $html[] = '<h3>'
            . '<span class="badge bg-' . $state_class . ' align-text-bottom">' . $state . '</span> '
            . $type_name
            . '</h3>';

        $html[] = $values_output;
        $html[] = $attributes_output;

        $html[] = '</div>';

        return implode('', $html);
    }

    private static function getRuleLineOutput(
        object       $rule,
        string       $key,
        string|array $value
    ): string
    {
        switch ($rule->type)
        {
            case 'date__date':
            case 'date__time':
            case 'content__article__date':
            case 'content__article__field':
                return '';

            default:
                return self::getRuleLineOutputDefault($rule, $key, $value);
        }
    }

    private static function getRuleLineOutputDefault(
        object       $rule,
        string       $key,
        string|array $value
    ): string
    {
        if ($key === 'selection')
        {
            return '';
        }

        $key = $rule->type . '__' . $key;

        $name = self::getNameString($key);

        if ($name === $key)
        {
            return '';
        }

        $value = self::getValues($value, $key);

        return self::getAttributeOutput($name, $value);
    }

    private static function getValues(string|array $values, string $key): string|array
    {
        if (empty($values) && $values !== '0')
        {
            return [];
        }

        $field = Helper::getField($key);

        if ( ! $field)
        {
            return $values;
        }

        if ($field->type == 'Radio')
        {
            return self::getNameString($key . '.' . $values);
        }

        if ( ! is_array($values))
        {
            $values = [$values];
        }

        $class = Helper::getFieldClass($key);

        if ($class)
        {
            $values = $class->getNamesByIds($values, $field->attributes);
        }
        else
        {
            foreach ($values as &$value)
            {
                $value = self::getNameString($value, $key);
            }
        }

        if (count($values) < 6)
        {
            return $values;
        }

        $values   = array_slice($values, 0, 5);
        $values[] = '...';

        return $values;
    }

    private static function getValuesOutputByRule(object $rule): string
    {
        switch ($rule->type)
        {
            case 'date__date':
            case 'date__time':
            case 'content__article__date':
                return self::getValuesOutputByRuleDateTime($rule);

            case 'content__article__field':
                return self::getValuesOutputByRuleField($rule);

            default:
                return self::getValuesOutputByRuleDefault($rule);
        }
    }

    private static function getValuesOutputByRuleDateTime(object $rule): string
    {
        $name = isset($rule->params->selection)
            ? self::getNameString($rule->params->selection, $rule->type)
            : '';

        if ($rule->params->comparison == 'between')
        {
            return self::getValuesOutputByRuleDateTimeBetween($rule, $name);
        }

        return self::getValuesOutputByRuleDateTimeBeforeOrAfter($rule, $name);
    }

    private static function getValuesOutputByRuleDateTimeBeforeOrAfter(
        object $rule,
        string $name
    ): string
    {
        $value = $rule->params->date ?? $rule->params->time;
        $type  = $rule->params->type ?? 'specific';

        $value = $type == 'now'
            ? JText::_('CON_NOW')
            : self::getDateTimeValue($value, $rule->type, false);

        $comparison = JText::_('CON_DATE_' . $rule->params->comparison);

        if ($name)
        {
            $comparison = lcfirst($comparison);
        }

        return '<strong>' . $name . '</strong>'
            . ' ' . $comparison
            . ' <code>' . $value . '</code>';
    }

    private static function getValuesOutputByRuleDateTimeBetween(object $rule, string $name): string
    {
        $recurring = ($rule->params->recurring ?? false);

        $value_from = self::getDateTimeValue($rule->params->from, $rule->type, $recurring);
        $value_to   = self::getDateTimeValue($rule->params->to, $rule->type, $recurring);

        $string = $recurring ? 'CON_BETWEEN_A_AND_B_EVERY_YEAR' : 'CON_BETWEEN_A_AND_B';

        $value = JText::sprintf(
            $string,
            '<code class="text-nowrap">' . $value_from . '</code>',
            '<code class="text-nowrap">' . $value_to . '</code>'
        );

        if ($name)
        {
            $value = lcfirst($value);
        }

        return '<strong>' . $name . '</strong>'
            . ' ' . $value;
    }

    private static function getValuesOutputByRuleDefault(object $rule): string
    {
        $values = null;

        foreach ($rule->params as $key => $value)
        {
            if ($key !== 'selection')
            {
                continue;
            }

            $values = self::getValues($value, $rule->type);
        }

        if (is_null($values))
        {
            return '';
        }

        if (RL_Array::implode($values, '') === '')
        {
            return '<ul><li>' . JText::_('<em>%s</em>,CON_NONE') . '</li></ul>';
        }

        $state_class = $rule->exclude ? 'danger' : 'success';

        $fields = Helper::getField($rule->type);

        if ($fields && in_array($fields->type, ['Editor', 'Textarea']))
        {
            return '<pre class="p-2 my-1 border border-' . $state_class . ' rounded"><code>'
                . RL_String::escape(RL_Array::implode($values, '<br>'))
                . '</code></pre>';
        }

        return '<ul><li>' . RL_Array::implode($values, '</li><li>') . '</li></ul>';
    }

    private static function getValuesOutputByRuleField(object $rule): string
    {
        $id   = $rule->type . '__field';
        $name = $rule->params->field;

        $field = Helper::getField($id);
        $class = Helper::getFieldClass($id);

        if ($field && $class && method_exists($class, 'getNameById'))
        {
            $name = $class->getNameById($rule->params->field, $field->attributes);
        }

        $comparison = JText::_('CON_COMPARISON_' . strtoupper($rule->params->comparison));

        if ($rule->params->comparison == 'empty')
        {
            return '<strong>' . $name . '</strong>'
                . ' <code>' . lcfirst($comparison) . '</code>';
        }

        return '<strong>' . $name . '</strong>'
            . ' <code>' . lcfirst($comparison) . '</code> '
            . $rule->params->value;
    }
}
