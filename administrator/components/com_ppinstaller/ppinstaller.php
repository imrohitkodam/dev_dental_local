<?php
/**
* @copyright	Copyright (C) 2009 - 2012 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package		PayPlans
* @subpackage	payplans Installer
* @contact 		payplans@readybytes.in
*/

// no direct access
defined('_JEXEC') or die();

if(!defined('DS')){
	define('DS', DIRECTORY_SEPARATOR);
}


require_once dirname(__FILE__).DS.'defines.php';

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_ppinstaller')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

$input = JFactory::getApplication()->input;
$view = $input->get('view','');
$task = $input->get('task','display');

//only show instructions for first time
$config = PpinstallerHelperUtils::getConfig();

if( 1 != $config->shownInstruction || $task == 'instruction') {
	$task = 'instruction';
	$config->save(array('shownInstruction' => 1));
}

$controllerClass = "PpinstallerController".ucfirst($view);

$controller	= new $controllerClass();
$controller->execute($task);
$controller->redirect();
