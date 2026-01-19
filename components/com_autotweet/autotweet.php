<?php

/*
 * @package     Perfect Publisher
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2025 Extly, CB. All rights reserved.
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 *
 * @see         https://www.extly.com
 */

defined('_JEXEC') || exit;

// Check for PHP4
if (defined('PHP_VERSION')) {
    $version = \PHP_VERSION;
} elseif (function_exists('phpversion')) {
    $version = \PHP_VERSION;
} else {
    // No version info. I'll lie and hope for the best.
    $version = '5.0.0';
}

// Old PHP version detected. EJECT! EJECT! EJECT!
if (!version_compare($version, '7.2.0', '>=')) {
    \Joomla\CMS\Factory::getApplication()->enqueueMessage('PHP versions 4.x and 5.x are no longer supported by Perfect Publisher.', 'error');
}

require_once JPATH_ADMINISTRATOR.'/components/com_autotweet/api/autotweetapi.php';

$config = [];
$view = null;

// XTF0F app
XTF0FDispatcher::getTmpInstance('com_autotweet', $view, $config)->dispatch();
