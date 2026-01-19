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

JFormHelper::loadFieldClass('media');

/**
 * Form Field class for the XTF0F framework
 * Media selection field.
 *
 * @since    2.0
 */
class XTF0FFormFieldMedia extends JFormFieldMedia implements XTF0FFormField
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
        $imgattr = [
            'id' => $this->id,
        ];

        if ($this->element['class']) {
            $imgattr['class'] = (string) $this->element['class'];
        }

        if ($this->element['style']) {
            $imgattr['style'] = (string) $this->element['style'];
        }

        if ($this->element['width']) {
            $imgattr['width'] = (string) $this->element['width'];
        }

        if ($this->element['height']) {
            $imgattr['height'] = (string) $this->element['height'];
        }

        if ($this->element['align']) {
            $imgattr['align'] = (string) $this->element['align'];
        }

        if ($this->element['rel']) {
            $imgattr['rel'] = (string) $this->element['rel'];
        }

        $alt = $this->element['alt'] ? JText::_((string) $this->element['alt']) : null;

        if ($this->element['title']) {
            $imgattr['title'] = JText::_((string) $this->element['title']);
        }

        if ($this->value && file_exists(JPATH_ROOT.'/'.$this->value)) {
            $src = XTF0FPlatform::getInstance()->URIroot().$this->value;
        } else {
            $src = '';
        }

        return JHtml::image($src, $alt, $imgattr);
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
