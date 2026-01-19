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

JFormHelper::loadFieldClass('timezone');

/**
 * Form Field class for XTF0F
 * Supports a generic list of options.
 *
 * @since    2.0
 */
class XTF0FFormFieldTimezone extends JFormFieldTimezone implements XTF0FFormField
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

        $selected = XTF0FFormFieldGroupedlist::getOptionName($this->getOptions(), $this->value);

        if (null === $selected) {
            $selected = [
                'group'	 => '',
                'item'	 => '',
            ];
        }

        return '<span id="'.$this->id.'-group" class="fof-groupedlist-group '.$class.'>'.
            htmlspecialchars($selected['group'], \ENT_COMPAT, 'UTF-8').
            '</span>'.
            '<span id="'.$this->id.'-item" class="fof-groupedlist-item '.$class.'>'.
            htmlspecialchars($selected['item'], \ENT_COMPAT, 'UTF-8').
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
        return $this->getStatic();
    }
}
