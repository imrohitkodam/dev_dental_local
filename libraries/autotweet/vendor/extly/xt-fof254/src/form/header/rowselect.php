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
 * Row selection checkbox
 *
 * @since    2.0
 */
class XTF0FFormHeaderRowselect extends XTF0FFormHeader
{
    /**
     * Get the header
     *
     * @return string The header HTML
     */
    protected function getHeader()
    {
        return '<input type="checkbox" name="checkall-toggle" value="" title="'
            .JText::_('JGLOBAL_CHECK_ALL')
            .'" onclick="Joomla.checkAll(this)" />';
    }
}
