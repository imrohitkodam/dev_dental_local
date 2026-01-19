<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Extension Manager Default View
 *
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * @since		1.5
 */

class PpInstallerViewMigrate extends PpinstallerViewAdapt
{
	
	//default task to display migration and backupview
	function display($nextView , $nextTask, $migrationTask)
	{
		$this->assign('nextView',$nextView);
		$this->assign('nextTask',$nextTask);
		$this->assign('migrationTask',$migrationTask);
		
		$ajaxResponse = PpinstallerAjaxResponse::getInstance();
		$ajaxResponse->addScriptCall('ppInstaller.replaceNextTpl',$this->loadTemplate());
		$ajaxResponse->sendResponse();
	}
		
	function changeTask($previousAction, $nextView , $nextTask, $nextAction, $startLimit = 0 , $message ="" ,$success = false)
	{
		$ajax_response = PpinstallerAjaxResponse::getInstance();
	
		if($success === true) {
			$ajax_response->addScriptCall("ppInstaller.changeTask('$previousAction','$nextAction',{'view':'$nextView','task':'$nextTask','action':'$nextAction','start':'$startLimit'},'$message');");
			$ajax_response->sendResponse();
		}

		PpinstallerHelperUtils::inCaseOfError(JText::sprintf("COM_PPINSTALLER_FAILED_TO_PERFORM_MIGRATION_TASK", $previousAction),$ajax_response);
	
		$ajax_response->addScriptCall("ppInstaller.stopTask('$previousAction','$success');");
		$ajax_response->sendResponse();
	}	
}
