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
 * Form Field class for XTF0F
 * Renders the checkbox in browse views which allows you to select rows
 *
 * @since    2.0
 */
class XTF0FFormFieldSelectrow extends JFormField implements XTF0FFormField
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
        throw new Exception(self::class.' cannot be used in single item display forms');
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
        if (!($this->item instanceof XTF0FTable)) {
            throw new Exception(self::class.' needs a XTF0FTable to act upon');
        }

        // Is this record checked out?
        $checked_out = false;
        $locked_by_field = $this->item->getColumnAlias('locked_by');
        $myId = JFactory::getUser()->get('id', 0);

        if (property_exists($this->item, $locked_by_field)) {
            $locked_by = $this->item->$locked_by_field;
            $checked_out = (0 != $locked_by && $locked_by != $myId);
        }

        // Get the key id for this record
        $key_field = $this->item->getKeyName();
        $key_id = $this->item->$key_field;

        // Get the HTML
        return JHtml::_('grid.id', $this->rowid, $key_id, $checked_out);
    }

    /**
     * Method to get the field input markup for this field type.
     *
     * @since 2.0
     *
     * @return string the field input markup
     */
    protected function getInput()
    {
        throw new Exception(self::class.' cannot be used in input forms');
    }
}
