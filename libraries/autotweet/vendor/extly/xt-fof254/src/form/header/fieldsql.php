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
 * Generic field header, with drop down filters based on a SQL query
 *
 * @since    2.0
 */
class XTF0FFormHeaderFieldsql extends XTF0FFormHeaderFieldselectable
{
    /**
     * Create objects for the options
     *
     * @return array The array of option objects
     */
    protected function getOptions()
    {
        $options = [];

        // Initialize some field attributes.
        $key = $this->element['key_field'] ? (string) $this->element['key_field'] : 'value';
        $value = $this->element['value_field'] ? (string) $this->element['value_field'] : (string) $this->element['name'];
        $translate = $this->element['translate'] ? (string) $this->element['translate'] : false;
        $query = (string) $this->element['query'];

        // Get the database object.
        $xtf0FDatabaseDriver = XTF0FPlatform::getInstance()->getDbo();

        // Set the query and get the result list.
        $xtf0FDatabaseDriver->setQuery($query);

        $items = $xtf0FDatabaseDriver->loadObjectlist();

        // Build the field options.
        if (!empty($items)) {
            foreach ($items as $item) {
                if (true == $translate) {
                    $options[] = JHtml::_('select.option', $item->$key, JText::_($item->$value));
                } else {
                    $options[] = JHtml::_('select.option', $item->$key, $item->$value);
                }
            }
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}
