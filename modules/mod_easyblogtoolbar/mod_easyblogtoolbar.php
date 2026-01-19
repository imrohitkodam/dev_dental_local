<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');

$file = JPATH_ADMINISTRATOR . '/components/com_easyblog/includes/easyblog.php';

if (!JFile::exists($file)) {
	return;
}

// Load up our library
$modules = EB::modules($module);

$modToolbar = array();
$modToolbar['showToolbar'] = true;
$modToolbar['showSearch'] = $params->get('showSearch', 1);
$modToolbar['showHeader'] = $params->get('showHeader', 0);
$modToolbar['showHome'] = $params->get('showHome', 1);
$modToolbar['showCategories'] = $params->get('showCategories', 1);
$modToolbar['showTags'] = $params->get('showTags', 1);
$modToolbar['showBloggers'] = $params->get('showBloggers', 1);
$modToolbar['showTeamblog'] = $params->get('showTeamblog', 1);
$modToolbar['showArchives'] = $params->get('showArchives', 1);
$modToolbar['showCalendar'] = $params->get('showCalendar', 1);
$modToolbar['renderToolbarModule'] = true;

// since we are loading frontend lib, we will need to load EasyBlog frontend language.
JFactory::getLanguage()->load('com_easyblog', JPATH_ROOT);

require(JModuleHelper::getLayoutPath('mod_easyblogtoolbar'));

