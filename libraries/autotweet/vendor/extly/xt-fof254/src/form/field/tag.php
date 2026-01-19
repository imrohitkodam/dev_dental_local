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

JFormHelper::loadFieldClass('tag');

/**
 * Form Field class for XTF0F
 * Tag Fields
 *
 * @since    2.1
 */
class XTF0FFormFieldTag extends JFormFieldTag implements XTF0FFormField
{
    /** @var XTF0FTable The item being rendered in a repeatable form field */
    public $item;

    /** @var int A monotonically increasing number, denoting the row number in a repeatable view */
    public $rowid;

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
        $class = $this->element['class'] ? (string) $this->element['class'] : '';
        $translate = $this->element['translate'] ? (string) $this->element['translate'] : false;

        $options = $this->getOptions();

        $html = '';

        foreach ($options as $option) {
            $html .= '<span>';

            if (true == $translate) {
                $html .= JText::_($option->text);
            } else {
                $html .= $option->text;
            }

            $html .= '</span>';
        }

        return '<span id="'.$this->id.'" class="'.$class.'">'.
            $html.
            '</span>';
    }

    /**
     * Get the rendering of this field type for a repeatable (grid) display,
     * e.g. in a view listing many item (typically a "browse" task)
     *
     * @since 2.1
     *
     * @return string The field HTML
     */
    public function getRepeatable()
    {
        $class = $this->element['class'] ? (string) $this->element['class'] : '';
        $translate = $this->element['translate'] ? (string) $this->element['translate'] : false;

        $options = $this->getOptions();

        $html = '';

        foreach ($options as $option) {
            $html .= '<span>';

            if (true == $translate) {
                $html .= JText::_($option->text);
            } else {
                $html .= $option->text;
            }

            $html .= '</span>';
        }

        return '<span class="'.$this->id.' '.$class.'">'.
            $html.
            '</span>';
    }

    /**
     * Method to get a list of tags
     *
     * @return array the field option objects
     *
     * @since   3.1
     */
    protected function getOptions()
    {
        $options = [];

        $published = $this->element['published'] ?: [0, 1];

        $xtf0FDatabaseDriver = XTF0FPlatform::getInstance()->getDbo();
        $xtf0FDatabaseQuery = $xtf0FDatabaseDriver->getQuery(true)
            ->select('DISTINCT a.id AS value, a.path, a.title AS text, a.level, a.published, a.lft')
            ->from('#__tags AS a')
            ->join('LEFT', $xtf0FDatabaseDriver->quoteName('#__tags').' AS b ON a.lft > b.lft AND a.rgt < b.rgt');
        $item = $this->item instanceof XTF0FTable ? $this->item : $this->form->getModel()->getItem();
        $keyfield = $item->getKeyName();
        $content_id = $item->$keyfield;
        $type = $item->getContentType();
        $selected_query = $xtf0FDatabaseDriver->getQuery(true);
        $selected_query
            ->select('tag_id')
            ->from('#__contentitem_tag_map')
            ->where('content_item_id = '.(int) $content_id)
            ->where('type_alias = '.$xtf0FDatabaseDriver->quote($type));
        $xtf0FDatabaseDriver->setQuery($selected_query);
        $this->value = $xtf0FDatabaseDriver->loadColumn();

        // Filter language
        if (!empty($this->element['language'])) {
            $xtf0FDatabaseQuery->where('a.language = '.$xtf0FDatabaseDriver->quote($this->element['language']));
        }

        $xtf0FDatabaseQuery->where($xtf0FDatabaseDriver->qn('a.lft').' > 0');

        // Filter to only load active items

        // Filter on the published state
        if (is_numeric($published)) {
            $xtf0FDatabaseQuery->where('a.published = '.(int) $published);
        } elseif (is_array($published)) {
            XTF0FUtilsArray::toInteger($published);
            $xtf0FDatabaseQuery->where('a.published IN ('.implode(',', $published).')');
        }

        $xtf0FDatabaseQuery->order('a.lft ASC');

        // Get the options.
        $xtf0FDatabaseDriver->setQuery($xtf0FDatabaseQuery);

        try {
            $options = $xtf0FDatabaseDriver->loadObjectList();
        } catch (RuntimeException $runtimeException) {
            return false;
        }

        // Prepare nested data
        if ($this->isNested()) {
            $this->prepareOptionsNested($options);
        } else {
            $options = JHelperTags::convertPathsToNames($options);
        }

        return $options;
    }
}
