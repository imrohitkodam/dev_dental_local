<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     Edocman
 * @author         Dang Thuc Dam
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die();

// Import common required classes
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');
require_once JPATH_ADMINISTRATOR . '/components/com_edocman/libraries/rad/loader.php';

// Turn off error reporting to prevent notices (if have)
error_reporting(E_ERROR | E_CORE_ERROR | E_PARSE | E_COMPILE_ERROR);

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_edocman'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Initialize controller config
$config = array(
	'default_controller_class' => 'EDocmanController',
	'default_view'             => 'dashboard',
	'class_prefix'             => 'EDocman'

);

//Init and Pre-process the input
$input  = new OSInput();
if ($input->has('cid') && !$input->has('id'))
{
	$cid = $input->get('cid', array(), 'array');
	$input->set('id', $cid[0]);
}
//Initialize the controller, execute the task and perform redirect if needed
OSController::getInstance('com_edocman', $input, $config)
	->execute()
	->redirect();