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
 * Generic field header, without any filters
 *
 * @since    2.0
 */
class XTF0FFormHeaderField extends XTF0FFormHeader
{
    /**
     * Get the header
     *
     * @return string The header HTML
     */
    protected function getHeader()
    {
        $sortable = ('false' != $this->element['sortable']);

        $label = $this->getLabel();

        if ($sortable) {
            $view = $this->form->getView();

            return JHtml::_('grid.sort', $label, $this->name,
                $view->getLists()->order_Dir, $view->getLists()->order,
                $this->form->getModel()->task
            );
        } else {
            return JText::_($label);
        }
    }
}
