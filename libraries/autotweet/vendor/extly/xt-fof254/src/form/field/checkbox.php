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

JFormHelper::loadFieldClass('checkbox');

/**
 * Form Field class for the XTF0F framework
 * A single checkbox
 *
 * @since    2.0
 */
class XTF0FFormFieldCheckbox extends JFormFieldCheckbox implements XTF0FFormField
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
        $value = $this->element['value'] ? (string) $this->element['value'] : '1';
        $disabled = ('true' === (string) $this->element['disabled']) ? ' disabled="disabled"' : '';
        $onclick = $this->element['onclick'] ? ' onclick="'.(string) $this->element['onclick'].'"' : '';
        $required = $this->required ? ' required="required" aria-required="true"' : '';

        if (empty($this->value)) {
            $checked = (isset($this->element['checked'])) ? ' checked="checked"' : '';
        } else {
            $checked = ' checked="checked"';
        }

        return '<span id="'.$this->id.'" '.$class.'>'.
            '<input type="checkbox" name="'.$this->name.'" id="'.$this->id.'" value="'.htmlspecialchars($value, \ENT_COMPAT, 'UTF-8').'"'.$class.$checked.$disabled.$onclick.$required.' />'.
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
        $value = $this->element['value'] ? (string) $this->element['value'] : '1';
        $disabled = ('true' === (string) $this->element['disabled']) ? ' disabled="disabled"' : '';
        $onclick = $this->element['onclick'] ? ' onclick="'.(string) $this->element['onclick'].'"' : '';
        $required = $this->required ? ' required="required" aria-required="true"' : '';

        if (empty($this->value)) {
            $checked = (isset($this->element['checked'])) ? ' checked="checked"' : '';
        } else {
            $checked = ' checked="checked"';
        }

        return '<span class="'.$this->id.' '.$class.'">'.
            '<input type="checkbox" name="'.$this->name.'" class="'.$this->id.' '.$class.'" value="'.htmlspecialchars($value, \ENT_COMPAT, 'UTF-8').'"'.$checked.$disabled.$onclick.$required.' />'.
            '</span>';
    }
}
