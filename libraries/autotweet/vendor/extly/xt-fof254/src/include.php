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

defined('_JEXEC') || exit();

if (!defined('XTF0F_INCLUDED')) {
    define('XTF0F_INCLUDED', '2.5.4');

    // Register a debug log
    if (defined('JDEBUG') && JDEBUG) {
        XTF0FPlatform::getInstance()->logAddLogger('xtfof.log.php');
    }
}
