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
 * This class is taken verbatim from:
 *
 * lessphp v0.3.9
 * http://leafo.net/lessphp
 *
 * LESS css compiler, adapted from http://lesscss.org
 *
 * Copyright 2012, Leaf Corcoran <leafot@gmail.com>
 * Licensed under MIT or GPLv3, see LICENSE
 *
 * @since    2.0
 */
class XTF0FLessFormatterCompressed extends XTF0FLessFormatterClassic
{
    public $disableSingle = true;

    public $open = '{';

    public $selectorSeparator = ',';

    public $assignSeparator = ':';

    public $break = '';

    public $compressColors = true;

    /**
     * Indent a string by $n positions
     *
     * @param int $n How many positions to indent
     *
     * @return string The indented string
     */
    public function indentStr($n = 0)
    {
        return '';
    }
}
