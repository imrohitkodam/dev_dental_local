<?php

/*
 * @package     XT Transitional Package from FrameworkOnFramework
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2024 Extly, CB. All rights reserved.
 *              Based on Akeeba's FrameworkOnFramework
 * @license     https://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * @see         https://www.extly.com
 */

// Protect from unauthorized access
defined('XTF0F_INCLUDED') || exit;

/**
 * FrameworkOnFramework model behavior class
 *
 * @since    2.1
 */
class XTF0FModelBehaviorFilters extends XTF0FModelBehavior
{
    /**
     * This event runs after we have built the query used to fetch a record
     * list in a model. It is used to apply automatic query filters.
     *
     * @param XTF0FModel         &$model The model which calls this event
     * @param XTF0FDatabaseQuery &$query The model which calls this event
     *
     * @return void
     */
    public function onAfterBuildQuery(&$model, &$query)
    {
        $xtf0FTable = $model->getTable();
        $tableName = $xtf0FTable->getTableName();
        $tableKey = $xtf0FTable->getKeyName();
        $xtf0FDatabaseDriver = $model->getDBO();

        $filterzero = $model->getState('_emptynonzero', null);

        $fields = $model->getTableFields();
        $backlist = $model->blacklistFilters();

        foreach ($fields as $fieldname => $fieldtype) {
            if (in_array($fieldname, $backlist)) {
                continue;
            }

            $field = new stdClass();
            $field->name = $fieldname;
            $field->type = $fieldtype;
            $field->filterzero = $filterzero;

            $filterName = ($field->name == $tableKey) ? 'id' : $field->name;
            $filterState = $model->getState($filterName, null);

            $field = XTF0FModelField::getField($field, ['dbo' => $xtf0FDatabaseDriver, 'table_alias' => $model->getTableAlias()]);

            if ((is_array($filterState) && (
                array_key_exists('value', $filterState) ||
                array_key_exists('from', $filterState) ||
                array_key_exists('to', $filterState)
            )) || is_object($filterState)) {
                $options = new JRegistry($filterState);
            } else {
                $options = new JRegistry();
                $options->set('value', $filterState);
            }

            $methods = $field->getSearchMethods();
            $method = $options->get('method', $field->getDefaultSearchMethod());

            if (!in_array($method, $methods)) {
                $method = 'exact';
            }

            switch ($method) {
                case 'between':
                case 'outside':
                case 'range' :
                    $sql = $field->$method($options->get('from', null), $options->get('to'));
                    break;

                case 'interval':
                case 'modulo':
                    $sql = $field->$method($options->get('value', null), $options->get('interval'));
                    break;

                case 'exact':
                case 'partial':
                case 'search':
                default:
                    $sql = $field->$method($options->get('value', null));
                    break;
            }

            if ($sql) {
                $query->where($sql);
            }
        }
    }
}
