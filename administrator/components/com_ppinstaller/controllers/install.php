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
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class PpinstallerControllerInstall extends PpinstallerController
{ 
	public function display($cachable = false, $urlparams = false)
	{
		$nextView =	"install";
		$nextTask =	"download";
		
		$installingTask = array(
				'download' => 0,
				'extract' => -1,
				'installation' => -1,
		);
		
		
		$this->_display(null,'display',array($nextView,$nextTask,$installingTask));
	}

	//Remove this
	public function uninstall()
	{
		$nextView =	"install";
		$nextTask =	"installation";
		$success  = PpinstallerHelperInstall::remove_component();
			
		$this->_display('default','changeTask',array(__FUNCTION__,$nextView,$nextTask,$success));
	}
	
	public function download()
	{
		$nextView =	"install";
		$nextTask =	"extract";
		
		$config = PpinstallerHelperUtils::getConfig();
				
		$success = PpinstallerHelperInstall::fetchTheKit($config->goingToInstall,'com','com_payplans');
		
		$this->_display('default','changeTask',array(__FUNCTION__,$nextView,$nextTask,$success));

	}
	
	public function extract()
	{
		$config 			= PpinstallerHelperUtils::getConfig();
		$success 			= PpinstallerHelperInstall::extract($config->installationZipPath.'.zip', $config->installationZipPath);
		$nextView 			= "install";
		$nextTask 			= "installation";
	
		$this->_display('default','changeTask', array(__FUNCTION__,$nextView,$nextTask,$success));
	}
	
	public function installation()
	{
		$config			= PpinstallerHelperUtils::getConfig();
		$installer 		= new JInstaller();
		$success 		= false;

		//if there is any error in installation it should be handled otherwise it send 500 erorr	
		try {
			if($installer->install($config->installationZipPath)){
				$success = true;
				PpinstallerHelperLogger::log(JText::_('COM_PPINSTALLER_PAYPLANS_INSTALLATION_TRUE'));
			}else {
				PpinstallerHelperLogger::log(JText::_('COM_PPINSTALLER_PAYPLANS_INSTALLATION_FALSE'));
			}
				
		}
		catch (Exception $e) {
			$success = false;
			PpinstallerHelperLogger::log(JText::_('COM_PPINSTALLER_PAYPLANS_INSTALLATION_FALSE'));
		}
			
		//delete folder from tmp after installation
		JFolder::delete($config->installationZipPath);
		JFile::delete($config->installationZipPath.'.zip');
			
		$nextView =	"complete";
		$nextTask =	"display";		
		
		$this->_display('default','changeTask',array(__FUNCTION__,$nextView,$nextTask,$success));

	}

}
