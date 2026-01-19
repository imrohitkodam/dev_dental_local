<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class PpInstallerViewComplete extends PpinstallerViewAdapt
{

	function display($nextView , $nextTask, $completeTask)
	{
		$this->assign('nextView',$nextView);
		$this->assign('nextTask',$nextTask);
		$this->assign('completeTask',$completeTask);
		
		$ajaxResponse = PpinstallerAjaxResponse::getInstance();
		$ajaxResponse->addScriptCall('ppInstaller.replaceNextTpl',$this->loadTemplate());
		$ajaxResponse->sendResponse();
	}
		
	function finalize()
	{
		$ajaxResponse = PpinstallerAjaxResponse::getInstance();	
		$ajaxResponse->addScriptCall("ppInstaller.activateRound('finish');");
		$ajaxResponse->addScriptCall('ppInstaller.replaceTpl',$this->loadTemplate('finalize'));
		$ajaxResponse->sendResponse();
	}

	function revertComplete()
	{
		$ajaxResponse = PpinstallerAjaxResponse::getInstance();
		$ajaxResponse->addScriptCall("ppInstaller.activateRound('finish');");
		$ajaxResponse->addScriptCall('ppInstaller.replaceTpl',$this->loadTemplate('revert_complete'));

		//incaseoferror is append to template, so first replace then append otherwise it will not shown
		$msg = JText::_('COM_PPINSTALLER_UPGRADE_FAIL');
		PpinstallerHelperUtils::inCaseOfError($msg,$ajaxResponse);

		$ajaxResponse->sendResponse();
	}

	function changeTask($previousAction, $nextView , $nextTask, $nextAction, $startLimit = 0 , $message ="" ,$success = false)
	{
		$ajax_response = PpinstallerAjaxResponse::getInstance();
	
		if($success === true) {
			$ajax_response->addScriptCall("ppInstaller.changeTask('$previousAction','$nextAction',{'view':'$nextView','task':'$nextTask','action':'$nextAction','start':'$startLimit'},'$message');");
			$ajax_response->sendResponse();
		}
	
		PpinstallerHelperUtils::inCaseOfError(JText::sprintf("COM_PPINSTALLER_FAILED_TO_PERFORM_COMPLETE_TASK", $previousAction), $ajax_response);
		
		$ajax_response->addScriptCall("ppInstaller.stopTask('$previousAction','$success');");
		$ajax_response->sendResponse();
	}
}
