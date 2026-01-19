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
 * FrameworkOnFramework model behavior class
 *
 * @since    2.1
 */
class XTF0FModelFieldBoolean extends XTF0FModelFieldNumber
{
    /**
     * Is it a null or otherwise empty value?
     *
     * @param mixed $value The value to test for emptiness
     *
     * @return bool
     */
    public function isEmpty($value)
    {
        return null === $value || ('' === $value);
    }
}
