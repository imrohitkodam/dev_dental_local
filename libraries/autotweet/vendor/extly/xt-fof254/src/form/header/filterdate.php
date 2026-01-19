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
 * Generic filter, text box entry with calendar button
 *
 * @since    2.3.3
 */
class XTF0FFormHeaderFilterdate extends XTF0FFormHeaderFielddate
{
    /**
     * Get the header
     *
     * @return string The header HTML
     */
    protected function getHeader()
    {
        return '';
    }
}
