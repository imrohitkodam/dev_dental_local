<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Extension Manager Default View
 *
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * @since		1.5
 */

class PpInstallerViewInstall extends PpinstallerViewAdapt
{
	
	//default task to display migration and backupview
	function display($nextView , $nextTask, $installingTask)
	{	
		$this->assign('nextView',$nextView);
		$this->assign('nextTask',$nextTask);
		$this->assign('installingTask',$installingTask);
		
		
		$ajaxResponse = PpinstallerAjaxResponse::getInstance();
		$ajaxResponse->addScriptCall('ppInstaller.replaceNextTpl',$this->loadTemplate());
		$ajaxResponse->sendResponse();
	}
	
	function changeTask($previousTask, $nextView , $nextTask, $success = false)
	{
		$ajax_response = PpinstallerAjaxResponse::getInstance();
		
		if($success === true) {
			$ajax_response->addScriptCall("ppInstaller.changeTask('$previousTask','$nextTask',{'view':'$nextView','task':'$nextTask'});");
			$ajax_response->sendResponse();
		}
		
		PpinstallerHelperUtils::inCaseOfError(JText::sprintf("COM_PPINSTALLER_FAILED_TO_PERFORM_TASK", $previousTask), $ajax_response);

		$ajax_response->addScriptCall("ppInstaller.stopTask('$previousTask','$success');");
		$ajax_response->sendResponse();
	}
}
