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
 * Generic filter, drop-down based on fixed options
 *
 * @since    2.0
 */
class XTF0FFormHeaderFilterselectable extends XTF0FFormHeaderFieldselectable
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
