<?php

/**
* @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		RBInstaller
* @contact		support@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

if(!defined('RB_FRAMEWORK_LOADED')){
	JLog::add('RB Frameowork not loaded',JLog::ERROR);
}

require_once dirname(__FILE__).'/rbinstaller/includes.php';

// find the controller to handle the request
$option	= 'com_rbinstaller';
$view	= 'item';
$task	= 'list';
$format	= 'html';

$controllerClass = RbInstallerHelper::findController($option,$view, $task,$format);


$controller = RbInstallerFactory::getInstance($controllerClass, 'controller', 'RbInstallerAdmin');

// execute task
try{
	$controller->execute($task);
}catch(Exception $e){
	RbInstallerHelper::handleException($e);
}

// lets complete the task by taking post-action
$controller->redirect();
