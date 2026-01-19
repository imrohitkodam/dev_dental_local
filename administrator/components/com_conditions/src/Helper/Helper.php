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

use Joomla\CMS\Language\Text as JText;
use RegularLabs\Library\ArrayHelper as RL_Array;
use RegularLabs\Library\DB as RL_DB;
use RegularLabs\Library\Language as RL_Language;
use RegularLabs\Library\Parameters as RL_Parameters;
use RegularLabs\Library\StringHelper as RL_String;

class Helper
{
    public static function conditionIsActive(object $rule): bool
    {
        if (empty($rule->type))
        {
            return false;
        }

        $disabled_types = RL_Array::toArray(RL_Parameters::getComponent('conditions')->disabled_rule_types);

        return ! in_array($rule->type, $disabled_types);
    }

    public static function getConditionsClass(
        object            $rule,
        object|false|null $article,
        object|false|null $module,
        object|false|null $request = null,
        ?int              $category_id = null
    )
    {
        if (empty($rule->type))
        {
            return false;
        }

        if ( ! self::conditionIsActive($rule))
        {
            return false;
        }

        $field = self::getField($rule->type);

        if ( ! $field)
        {
            return false;
        }

        $classname = str_replace('_', '. ', $field->class);
        $classname = RL_String::toTitleCase($classname);
        $classname = str_replace(['. . ', '. '], ['\\', ''], $classname);

        $classname = 'RegularLabs\\Component\\Conditions\\Administrator\\Condition\\'
            . $classname;

        if ( ! class_exists($classname))
        {

            return false;
        }

        return new $classname($rule, $article, $module, $request, $category_id);
    }

    public static function getExtensionItemString(string $extension): string
    {
        RL_Language::load($extension);

        $no_com_prefix = strtoupper(preg_replace('#^com_#', '', $extension));

        $item_name = $no_com_prefix . '_ITEM_NAME_FOR_CONDITIONS';

        if (JText::_($item_name) !== $item_name)
        {
            return JText::_($item_name);
        }

        $extension = self::getExtensionName($extension);

        return JText::sprintf('CON_EXTENSION_ITEM', $extension);
    }

    public static function getExtensionName(string $extension): string
    {
        RL_Language::load($extension);

        $no_com_prefix = strtoupper(preg_replace('#^com_#', '', $extension));

        $extension_name = JText::_($no_com_prefix) !== $no_com_prefix
            ? JText::_($no_com_prefix)
            : JText::_($extension);

        return str_replace('Regular Labs - ', '', $extension_name);
    }

    public static function getField(string $key): ?object
    {
        $fields = Helper::getXmlFields();

        return $fields[$key] ?? null;
    }

    public static function getFieldClass(string $key): object|false
    {
        $field = self::getField($key);

        if ( ! $field)
        {
            return false;
        }

        $classname = 'RegularLabs\\Component\\Conditions\\Administrator\\Form\\Field\\' . $field->type . 'Field';

        if ( ! class_exists($classname))
        {
            $classname = 'RegularLabs\\Library\\Form\\Field\\' . $field->type . 'Field';
        }

        if ( ! class_exists($classname))
        {
            return false;
        }

        $class = new $classname;

        if ( ! method_exists($class, 'getNamesByIds'))
        {
            return false;
        }

        return $class;
    }

    public static function getForItemText(
        string $extension,
        int    $item_id,
        string $table,
        string $name_column = 'name'
    ): string
    {
        RL_Language::load('com_conditions');

        $extension_item = self::getExtensionItemString($extension);
        $item_name      = self::getItemNameFromDB($item_id, $table, $name_column);

        return JText::sprintf('CON_FOR_ITEM', $extension_item, $item_name);
    }

    public static function getIdValuesFromDB(string $table, string $name_column = 'name'): array
    {
        if ( ! $table || ! $name_column)
        {
            return [];
        }

        $cache = new Cache;

        if ($cache->exists())
        {
            return $cache->get();
        }

        $db = RL_DB::get();

        $query = $db->getQuery(true)
            ->select(RL_DB::quoteName('id'))
            ->select(RL_DB::quoteName($name_column, 'value'))
            ->from(RL_DB::quoteName('#__' . $table));

        $db->setQuery($query);

        return $cache->set($db->loadAssocList('id', 'value'));
    }

    public static function getItemNameFromDB(
        int    $item_id,
        string $table,
        string $name_column = 'name'
    ): string
    {
        $items = self::getIdValuesFromDB($table, $name_column);

        return $items[$item_id] ?? '';
    }

    public static function getItemPublishStateFromDB(int $item_id, string $table): int
    {
        $columns = RL_DB::getTableColumns('#__' . $table);

        $published_column = isset($columns['published'])
            ? 'published'
            : ($columns['state'] ? 'state' : '');

        if ( ! $published_column)
        {
            return 1;
        }

        $items = self::getIdValuesFromDB($table, $published_column);

        return $items[$item_id] ?? 0;
    }

    public static function getPublishStateString(int $state): string
    {
        $state = match ($state)
        {
            1       => 'PUBLISHED',
            0       => 'UNPUBLISHED',
            2       => 'ARCHIVED',
            -2      => 'TRASHED',
            default => '',
        };

        return JText::_($state);
    }

    public static function getXmlFields(bool $use_cache = true): array
    {
        $cache = new Cache('Conditions.getXmlFields');

        if ($use_cache && $cache->exists())
        {
            return $cache->get();
        }

        RL_Parameters::getObjectFromData('', 'administrator/components/com_conditions/forms/item_rule.xml');

        $file = file_get_contents(JPATH_ADMINISTRATOR . '/components/com_conditions/forms/item_rule.xml');

        $xml_parser = xml_parser_create();
        xml_parse_into_struct($xml_parser, $file, $xml_fields);

        $fields = [];

        $field_name = '';

        foreach ($xml_fields as $field)
        {
            if ( ! in_array($field['tag'], ['FIELD', 'OPTION'])
                || ! isset($field['attributes'])
                || isset($field['attributes']['DISABLED'])
            )
            {
                continue;
            }

            if ($field['tag'] === 'OPTION')
            {
                $option_name          = $field_name . '.' . RL_String::toUnderscoreCase($field['attributes']['VALUE'], true, true);
                $fields[$option_name] = (object) [
                    'type'       => '',
                    'name'       => $field['value'],
                    'class'      => $field['attributes']['CONDITION'] ?? '',
                    'attributes' => (array) RL_Array::changeKeyCase($field['attributes'], 'lowercase'),
                ];
                continue;
            }

            if (isset($field['attributes']['TYPE']) && $field['attributes']['TYPE'] === 'LoadLanguage')
            {
                RL_Language::load($field['attributes']['EXTENSION'], JPATH_ADMINISTRATOR);
                continue;
            }

            if ( ! isset($field['attributes']['NAME'])
                || ! isset($field['attributes']['TYPE'])
                || $field['attributes']['NAME'] == ''
                || $field['attributes']['NAME'][0] == '@'
                || $field['attributes']['TYPE'] == 'spacer'
            )
            {
                continue;
            }

            $field_name = $field['attributes']['NAME'];

            $fields[$field_name] = (object) [
                'type'       => $field['attributes']['TYPE'],
                'name'       => $field['attributes']['LABEL'] ?? $field['attributes']['NAME'],
                'class'      => $field['attributes']['CONDITION'] ?? $field['attributes']['NAME'],
                'attributes' => (array) RL_Array::changeKeyCase($field['attributes'], 'lowercase'),
            ];
        }

        return $cache->set($fields);
    }

    public static function thereAreConditions(): bool
    {
        $cache = new Cache;

        if ($cache->exists())
        {
            return $cache->get();
        }

        $db = RL_DB::get();

        $query = $db->getQuery(true)
            ->select('count(*)')
            ->from('#__conditions')
            ->where(RL_DB::is('published', 1));

        $db->setQuery($query);

        return $cache->set($db->loadResult() > 0);
    }
}
