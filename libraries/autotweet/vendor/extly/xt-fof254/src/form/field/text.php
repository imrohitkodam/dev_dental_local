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

JFormHelper::loadFieldClass('text');

/**
 * Form Field class for the XTF0F framework
 * Supports a one line text field.
 *
 * @since    2.0
 */
class XTF0FFormFieldText extends JFormFieldText implements XTF0FFormField
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
        $class = $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
        $empty_replacement = '';

        if ($this->element['empty_replacement']) {
            $empty_replacement = (string) $this->element['empty_replacement'];
        }

        if ($empty_replacement !== '' && $empty_replacement !== '0' && empty($this->value)) {
            $this->value = JText::_($empty_replacement);
        }

        return '<span id="'.$this->id.'" '.$class.'>'.
            htmlspecialchars($this->value, \ENT_COMPAT, 'UTF-8').
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
        // Initialise
        $class = $this->id;
        $format_string = '';
        $format_if_not_empty = false;
        $parse_value = false;
        $show_link = false;
        $link_url = '';
        $empty_replacement = '';

        // Get field parameters
        if ($this->element['class']) {
            $class = (string) $this->element['class'];
        }

        if ($this->element['format']) {
            $format_string = (string) $this->element['format'];
        }

        if ('true' == $this->element['show_link']) {
            $show_link = true;
        }

        if ('true' == $this->element['format_if_not_empty']) {
            $format_if_not_empty = true;
        }

        if ('true' == $this->element['parse_value']) {
            $parse_value = true;
        }

        if ($this->element['url']) {
            $link_url = $this->element['url'];
        } else {
            $show_link = false;
        }

        if ($show_link && ($this->item instanceof XTF0FTable)) {
            $link_url = $this->parseFieldTags($link_url);
        } else {
            $show_link = false;
        }

        if ($this->element['empty_replacement']) {
            $empty_replacement = (string) $this->element['empty_replacement'];
        }

        // Get the (optionally formatted) value
        $value = $this->value;

        if ($empty_replacement !== '' && $empty_replacement !== '0' && empty($this->value)) {
            $value = JText::_($empty_replacement);
        }

        if ($parse_value) {
            $value = $this->parseFieldTags($value);
        }

        if ($format_string !== '' && $format_string !== '0' && (!$format_if_not_empty || ($format_if_not_empty && !empty($this->value)))) {
            $format_string = $this->parseFieldTags($format_string);
            $value = sprintf($format_string, $value);
        } else {
            $value = htmlspecialchars($value, \ENT_COMPAT, 'UTF-8');
        }

        // Create the HTML
        $html = '<span class="'.$class.'">';

        if ($show_link) {
            $html .= '<a href="'.$link_url.'">';
        }

        $html .= $value;

        if ($show_link) {
            $html .= '</a>';
        }

        $html .= '</span>';

        return $html;
    }

    /**
     * Replace string with tags that reference fields
     *
     * @param string $text Text to process
     *
     * @return string Text with tags replace
     */
    protected function parseFieldTags($text)
    {
        $ret = $text;

        // Replace [ITEM:ID] in the URL with the item's key value (usually:
        // the auto-incrementing numeric ID)
        $keyfield = $this->item->getKeyName();
        $replace = $this->item->$keyfield;
        $ret = str_replace('[ITEM:ID]', $replace, $ret);

        // Replace the [ITEMID] in the URL with the current Itemid parameter
        $ret = str_replace('[ITEMID]', JFactory::getApplication()->input->getInt('Itemid', 0), $ret);

        // Replace other field variables in the URL
        $fields = $this->item->getTableFields();

        foreach ($fields as $field) {
            $fieldname = $field->Field;

            if (empty($fieldname)) {
                $fieldname = $field->column_name;
            }

            $search = '[ITEM:'.strtoupper($fieldname).']';
            $replace = $this->item->$fieldname;
            $ret = str_replace($search, $replace, $ret);
        }

        return $ret;
    }
}
