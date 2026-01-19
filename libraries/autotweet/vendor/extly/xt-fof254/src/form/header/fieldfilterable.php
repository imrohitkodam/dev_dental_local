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
 * Generic field header, with text input (search) filter
 *
 * @since    2.0
 */
class XTF0FFormHeaderFieldfilterable extends XTF0FFormHeaderFieldsearchable
{
    /**
     * Get the filter field
     *
     * @return string The HTML
     */
    protected function getFilter()
    {
        $valide = ['yes', 'true', '1'];

        // Initialize some field(s) attributes.
        $size = $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
        $maxLength = $this->element['maxlength'] ? ' maxlength="'.(int) $this->element['maxlength'].'"' : '';
        $filterclass = $this->element['filterclass'] ? ' class="'.(string) $this->element['filterclass'].'"' : '';
        $placeholder = $this->element['placeholder'] ?: $this->getLabel();
        $name = $this->element['searchfieldname'] ?: $this->name;
        $placeholder = ' placeholder="'.JText::_($placeholder).'"';

        $single = in_array($this->element['single'], $valide);
        $showMethod = in_array($this->element['showmethod'], $valide);
        $method = $this->element['method'] ?: 'between';
        $fromName = $this->element['fromname'] ?: 'from';
        $toName = $this->element['toname'] ?: 'to';

        $values = $this->form->getModel()->getState($name);
        $fromValue = $values[$fromName];
        $toValue = $values[$toName];

        // Initialize JavaScript field attributes.
        if ($this->element['onchange']) {
            $onchange = ' onchange="'.(string) $this->element['onchange'].'"';
        } else {
            $onchange = ' onchange="document.adminForm.submit();"';
        }

        if ($showMethod) {
            $html = '<input type="text" name="'.$name.'[method]" value="'.$method.'" />';
        } else {
            $html = '<input type="hidden" name="'.$name.'[method]" value="'.$method.'" />';
        }

        $html .= '<input type="text" name="'.$name.'[from]" id="'.$this->id.'_'.$fromName.'" value="'.htmlspecialchars($fromValue, \ENT_COMPAT, 'UTF-8').'"'.$filterclass.$size.$placeholder.$onchange.$maxLength.'/>';

        if (!$single) {
            $html .= '<input type="text" name="'.$name.'[to]" id="'.$this->id.'_'.$toName.'" value="'.htmlspecialchars($toValue, \ENT_COMPAT, 'UTF-8').'"'.$filterclass.$size.$placeholder.$onchange.$maxLength.'/>';
        }

        return $html;
    }
}
