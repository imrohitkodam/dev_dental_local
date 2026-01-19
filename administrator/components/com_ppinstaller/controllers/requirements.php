<?php
/**
* @copyright	Copyright (C) 2009 - 2012 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package		PayPlans
* @subpackage	PayPlans-Installer
* @contact 		payplans@readybytes.in
*/

// No direct access.
defined('_JEXEC') or die;

class PpinstallerControllerRequirements extends PpinstallerController
{ 
	public function display($cachable = false, $urlparams = false)
	{	
		$config = PpinstallerHelperUtils::getConfig();
		
		if( null == $config->ltsReleases || null == $config->latestRelease) {
			$this->_display(null,'error');
			return false;
		} 

		$this->_display(null,'display',array('requirements','checkForNext'));
	}
	
	public function checkForNext()
	{
		$ajax_response 	= PpinstallerAjaxResponse::getInstance();
		$input 			= JFactory::getApplication()->input;
		$restore 			= $input->get('restore',0);
		$goingToInstall 	= $input->get('goingToInstall',false);
		$config = PpinstallerHelperUtils::getConfig();
		
		if($restore){
			$config->save(array('goingToInstall' => $goingToInstall));
			$postData = array('view'=>'migrate','task'=>'display','nextView'=>'migrate','nextTask'=>'restore','restore'=>$restore,'migrationTask'=>array('restore' => array(0,'Restore','')));
			$ajax_response->addScriptCall("ppInstaller.executeTask(".json_encode($postData).");");
			$ajax_response->sendResponse();
		}
		
		//$tablePrefix 	= $input->get('revertDatabase','');
		
		
		
		//next task to executes
		//just switch to bakcup task if required to backup
		//if do not want to take backup in that case there is not migration
		//becase in any migration case it need to be take backup
		$nextTask 		= 'display';
		$nextView 		= 'install';
		
		$migrationRequired 	= PpinstallerHelperMigrate::isRequired(false,$goingToInstall);
		$installedVersion 	= PpinstallerHelperMigrate::oldVersion();
		
		//save what is going to be installed
		$config->save(array(	'goingToInstall'=>$goingToInstall,
												'migrationRequired'=>$migrationRequired,
												'installedVersion'=>$installedVersion
									));
		
		$ajax_response->addScriptCall("ppInstaller.activateRound('migrate');");
		
		if($migrationRequired != 0) {
			$nextTask 	= 'display';
			$nextView 	= 'migrate';
		}
		
		$ajax_response->addScriptCall("ppInstaller.executeTask({'view':'$nextView','task':'$nextTask'});");
		$ajax_response->sendResponse();
	}

	function credential()
	{
		$username = JFactory::getApplication()->input->get('ppinstallerUsername','test','@');
		//To validate the x+y@readybytes.in type of email
		
		$extendedEmail	= strripos($username, "+");
		
		if($extendedEmail) { 
			
			$username = strstr($username,"+");
		  	
		  	$username = str_replace("+","",$username);
		}
		  
		$password = JFactory::getApplication()->input->getString('ppinstallerPassword','test');
		
		$response = PpinstallerHelperUtils::setCredential($username,$password);

		$ajax_response = PpinstallerAjaxResponse::getInstance();
		
		if($response === true) {			

			//payplans need to uninstalled before doing anything
			//it requires redierction so do it in the end
			PpinstallerHelperInstall::remove_component();

			//think of not doing anything just submit
			$ajax_response->addScriptCall("ppInstaller.submitform();");
			$ajax_response->sendResponse();
		}
		
		if(empty($response)) {
			$response->error_code = 'ERROR';
		}
		
		$message = JText::_('COM_PPINSTALLER_CREDENTIAL_ERROR_CODE_'.$response->error_code).'<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>';
		$ajax_response->addScriptCall('jQuery("#ppinstallerCredentialError").removeClass',"hide");
		$ajax_response->addScriptCall('jQuery("#ppinstallerCredentialError").html', $message);
		$ajax_response->addScriptCall("jQuery('#ppInstaller_submit_button').attr('onclick','return ppInstaller.credentialCheck();');");
		$ajax_response->addScriptCall("jQuery('#ppInstaller_submit_button').button('complete');");
		$ajax_response->sendResponse();
		
		return true;
	}
	
	//if there is need to revert the database then show the loading for reverting database.
	public function needForRevert()
	{
		$ajax_response 	= PpinstallerAjaxResponse::getInstance();

		$postData = array('view'=>'migrate','task'=>'display','nextView'=>'migrate','nextTask'=>'needForRevert','migrationTask'=>array('needForRevert' => array(0,'Reverting your database','')));
		
		$ajax_response->addScriptCall("ppInstaller.executeTask(".json_encode($postData).");");
		$ajax_response->sendResponse();
	}
	
	
}
