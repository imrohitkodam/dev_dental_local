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
 * Supports a title field with an optional slug display below it.
 *
 * @since    2.0
 */
class XTF0FFormFieldTitle extends XTF0FFormFieldText implements XTF0FFormField
{
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
        $slug_format = '(%s)';
        $slug_class = 'small';

        // Get field parameters
        $slug_field = $this->element['slug_field'] ? (string) $this->element['slug_field'] : $this->item->getColumnAlias('slug');

        if ($this->element['slug_format']) {
            $slug_format = (string) $this->element['slug_format'];
        }

        if ($this->element['slug_class']) {
            $slug_class = (string) $this->element['slug_class'];
        }

        // Get the regular display
        $html = parent::getRepeatable();

        $slug = $this->item->$slug_field;

        $html .= '<br /><span class="'.$slug_class.'">';
        $html .= JText::sprintf($slug_format, $slug);
        $html .= '</span>';

        return $html;
    }
}
