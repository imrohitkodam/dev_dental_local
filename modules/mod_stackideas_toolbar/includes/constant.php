<?php
/**
* @package      StackIdeas
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* StackIdeas Toolbar is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

define('FDT_JOOMLA', JPATH_ROOT);
define('FDT_JOOMLA_URI', rtrim(JURI::root(), '/'));

define('FDT_ROOT', FDT_JOOMLA . '/modules/mod_stackideas_toolbar');
define('FDT_URI', FDT_JOOMLA_URI . '/modules/mod_stackideas_toolbar');
define('FDT_INCLUDES', FDT_ROOT . '/includes');
define('FDT_ADAPTER', FDT_ROOT . '/includes/adapter');

// Scripts
define('FDT_SCRIPTS', FDT_ROOT . '/assets/scripts');
define('FDT_SCRIPTS_URI', FDT_URI . '/assets/scripts');
define('FDT_IMAGES', FDT_URI. '/assets/images');

// Theme path
define('FDT_THEMES', FDT_ROOT . '/tmpl');

// Environment
define('FDT_ENVIRONMENT', 'production');