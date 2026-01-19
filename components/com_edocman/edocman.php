<?php
/**
 * @version        1.7.6
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die();
error_reporting(E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR);
// Include dependencies
jimport('joomla.filesystem.file');
require_once JPATH_ADMINISTRATOR . '/components/com_edocman/libraries/rad/loader.php';
//Prepare controller input
EDocmanHelper::prepareRequestData();
$input  = new OSInput();

//Controller config
$config = array(
	'default_controller_class' => 'EDocmanController',
	'default_view'             => 'categories',
	'class_prefix'                   => 'EDocman'
);

//Initialize the controller, execute the task and perform redirect if needed
OSController::getInstance('com_edocman', $input, $config)
	->execute()
	->redirect();
