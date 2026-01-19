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

// Load toolbar engine
require_once(__DIR__ . '/includes/toolbar.php');

// Ensure that foundry exists and is enabled on the site.
if (!FDT::foundryExists()) {
	return JFactory::getApplication()->enqueueMessage('To utilize <b>StackIdeas Toolbar</b>, please ensure that the plugin <b>Foundry by StackIdeas</b> is enabled on the site.', 'error');
}

if (!FDT::componentExists()) {
	return;
}

// Initialize module params.
FDT::setConfig($params);

// Set the module id
FDT::setModuleId($module->id);

// Initialize scripts and stylesheets.
FDT::initialize();

if (!FDT::toolbarEnabled()) {
	return;
}

$adapter = FDT::getAdapter(FDT::getMainComponent());
$themes = FDT::themes();
$responsive = FH::responsive()->isMobile() || FH::responsive()->isTablet();

require JModuleHelper::getLayoutPath('mod_stackideas_toolbar', $params->get('layout', 'default'));