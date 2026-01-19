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

JFormHelper::loadFieldClass('accesslevel');

/**
 * Form Field class for XTF0F
 * Joomla! access levels
 *
 * @since    2.0
 */
class XTF0FFormFieldAccesslevel extends JFormFieldAccessLevel implements XTF0FFormField
{
    /** @var int A monotonically increasing number, denoting the row number in a repeatable view */
    public $rowid;

    /** @var XTF0FTable The item being rendered in a repeatable form field */
    public $item;

    protected $static;

    protected $repeatable;

    /**
     * Method to get certain otherwise inaccessible properties from the form field object.
     *
     * @param string $name the property name for which to the the value
     *
     * @return mixed the property value or null
     *
     * @since   2.0
     */
    public function __get($name)
    {
        switch ($name) {
            case 'static':
                if (empty($this->static)) {
                    $this->static = $this->getStatic();
                }

                return $this->static;
                break;

            case 'repeatable':
                if (empty($this->repeatable)) {
                    $this->repeatable = $this->getRepeatable();
                }

                return $this->repeatable;
                break;

            default:
                return parent::__get($name);
        }
    }

    /**
     * Get the rendering of this field type for static display, e.g. in a single
     * item view (typically a "read" task).
     *
     * @since 2.0
     *
     * @return string The field HTML
     */
    public function getStatic()
    {
        $class = $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';

        $params = $this->getOptions();

        $xtf0FDatabaseDriver = XTF0FPlatform::getInstance()->getDbo();
        $xtf0FDatabaseQuery = $xtf0FDatabaseDriver->getQuery(true);

        $xtf0FDatabaseQuery->select('a.id AS value, a.title AS text');
        $xtf0FDatabaseQuery->from('#__viewlevels AS a');
        $xtf0FDatabaseQuery->group('a.id, a.title, a.ordering');
        $xtf0FDatabaseQuery->order('a.ordering ASC');
        $xtf0FDatabaseQuery->order($xtf0FDatabaseQuery->qn('title').' ASC');

        // Get the options.
        $xtf0FDatabaseDriver->setQuery($xtf0FDatabaseQuery);
        $options = $xtf0FDatabaseDriver->loadObjectList();

        // If params is an array, push these options to the array
        if (is_array($params)) {
            $options = array_merge($params, $options);
        }

        // If all levels is allowed, push it into the array.
        elseif ($params) {
            array_unshift($options, JHtml::_('select.option', '', JText::_('JOPTION_ACCESS_SHOW_ALL_LEVELS')));
        }

        return '<span id="'.$this->id.'" '.$class.'>'.
            htmlspecialchars(XTF0FFormFieldList::getOptionName($options, $this->value), \ENT_COMPAT, 'UTF-8').
            '</span>';
    }

    /**
     * Get the rendering of this field type for a repeatable (grid) display,
     * e.g. in a view listing many item (typically a "browse" task)
     *
     * @since 2.0
     *
     * @return string The field HTML
     */
    public function getRepeatable()
    {
        $class = $this->element['class'] ? (string) $this->element['class'] : '';

        $params = $this->getOptions();

        $xtf0FDatabaseDriver = XTF0FPlatform::getInstance()->getDbo();
        $xtf0FDatabaseQuery = $xtf0FDatabaseDriver->getQuery(true);

        $xtf0FDatabaseQuery->select('a.id AS value, a.title AS text');
        $xtf0FDatabaseQuery->from('#__viewlevels AS a');
        $xtf0FDatabaseQuery->group('a.id, a.title, a.ordering');
        $xtf0FDatabaseQuery->order('a.ordering ASC');
        $xtf0FDatabaseQuery->order($xtf0FDatabaseQuery->qn('title').' ASC');

        // Get the options.
        $xtf0FDatabaseDriver->setQuery($xtf0FDatabaseQuery);
        $options = $xtf0FDatabaseDriver->loadObjectList();

        // If params is an array, push these options to the array
        if (is_array($params)) {
            $options = array_merge($params, $options);
        }

        // If all levels is allowed, push it into the array.
        elseif ($params) {
            array_unshift($options, JHtml::_('select.option', '', JText::_('JOPTION_ACCESS_SHOW_ALL_LEVELS')));
        }

        return '<span class="'.$this->id.' '.$class.'">'.
            htmlspecialchars(XTF0FFormFieldList::getOptionName($options, $this->value), \ENT_COMPAT, 'UTF-8').
            '</span>';
    }
}
