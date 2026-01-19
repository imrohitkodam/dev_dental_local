<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class PpInstallerViewRequirements extends PpinstallerViewAdapt
{

	function display($nextView,$nextTask)
	{	
		list($precheks,$errorExist) = $this->preRequirements();
		
		$config = PpinstallerHelperUtils::getConfig();
		
		$needPrecheck = false;	
		$this->assign('ppinstallerUsername',$config->ppinstallerUsername);
		$this->assign('ppinstallerPassword',$config->ppinstallerPassword);
		$this->assign('results',$precheks);
		$this->assign('errorExist',$errorExist);
		$this->assign('ltsReleases',$config->ltsReleases);
		$this->assign('latestRelease',$config->latestRelease);
		
		$installedVersion = PpinstallerHelperMigrate::oldVersion();
		$this->assign('installedVersion',$installedVersion);
		if ($installedVersion) {
			$restorePoints = PpinstallerHelperMigrate::checkForRestorePoint();
			
			foreach ($restorePoints as $point => $support) {
				if( in_array( $point, $config->downgradeVersion ) ) {
					unset($restorePoints[$point]);
					if(version_compare($installedVersion , $config->ltsReleases[$point]['version']) <= 0  )
						continue;
					$restorePoints[$config->ltsReleases[$point]['version']] = true; 
				}
			}
			$this->assign('restorePoints',$restorePoints);

			//detect if previous-one was already in error during database migration
			$migrationStatus = PpinstallerHelperMigrate::getKeyValue();
		if( ! empty( $migrationStatus ) && PPINSTALLER_BACKUP_CREATED < $migrationStatus && $migrationStatus < PPINSTALLER_MIGRATION_SUCCESS) {
				$nextView = 'requirements';
				$nextTask = 'needForRevert';
				$this->assign('needForRevert',true);
			}
		}

		$ltsReleases  = $config->ltsReleases;
		$freshInstall = array_pop($ltsReleases);
		$freshInstall = $freshInstall['version'];
		$goingToInstall = (!$installedVersion) ? $freshInstall : false;
		
		if($installedVersion) {
			foreach ($config->ltsReleases as $release) {
			 	if(version_compare($installedVersion,$release['version']) < 0) {		
					$goingToInstall = ($goingToInstall)?$goingToInstall:$release['version'];
				}
			}
			
			if( version_compare($installedVersion,$release['version']) >= 0 
				&& version_compare($installedVersion,$config->latestRelease['version']) < 0) {
				
				$goingToInstall = $config->latestRelease['version'];
			}
			
			$tmpInstalled 	 = number_format($installedVersion,1);
			$tmpGoingInstall = number_format($goingToInstall,1);
			$tmpPrecheck 	 = version_compare($tmpGoingInstall, $tmpInstalled);
			
			if($tmpPrecheck > 0) {
						
				list($needPrecheck, $version) = PpinstallerHelperUtils::fetchPrecheckFile($goingToInstall);
			
				if($needPrecheck) {
					include_once (PPINSTALLER_PRECHECK_PATH.DS.'precheck'.$version.'.php');
					$version = explode('.', $version);
					$classname =  "Precheck$version[0]$version[1]";
					ob_start();
					$object = new $classname;
					$object->start();
					$precheckTerms = ob_get_contents();
					ob_clean();
					$this->assign('precheckTerms', $precheckTerms);
				}
			}
		}
		
		//case that payplans is installed at latest version then unfortunatly uninstalled it then 
		//then database value says it installed at latest so that it not allowed to install payplans
		//so need to check folder is existed or not 
		$state 			= JPluginHelper::isEnabled('system','payplans');
		
		if(empty($goingToInstall) && empty($state)) {
			$goingToInstall = $freshInstall;
		}
		
		$this->assign('needPrecheck', $needPrecheck);
		
		$this->assign('goingToInstall',$goingToInstall);

		$this->assign('nextView',$nextView);
		$this->assign('nextTask',$nextTask);
		
		$ajaxResponse = PpinstallerAjaxResponse::getInstance();
		$ajaxResponse->addScriptCall('ppInstaller.replaceTpl',$this->loadTemplate());
		$ajaxResponse->addScriptCall($this->getDynamicJavascript());
		$ajaxResponse->sendResponse();
	}

	public function preRequirements()
	{
		$errorExist = false;
		$preReq = get_class_methods('PpinstallerHelperPrecheck');
	
		foreach ($preReq as $req){
			$result = PpinstallerHelperPrecheck::$req();
			if(empty($result)){	continue;}
			// If you don't have all minimum requirements then you can't install payplans
			if(PPINSTALLER_ERROR_LEVEL <= $result['status']){
				$errorExist = true;
			}
			$results[] = $result;
		}
	
		return array($results,$errorExist);
	}
	
	public function getDynamicJavascript()
	{
		ob_start();
		?>

		$('input:radio[name="restore"]').live().change(function(){
			var message = "Restore to "+$("input[name='restore']:checked").val();
			$("#ppInstaller_submit_button").addClass('btn-danger').html(message);
		});
		
		<?php
		$script = ob_get_contents();
		ob_clean();
		
		return $script;
	}

	//In case of error of file permission or content of Config file is not proper;
	public function error()
	{
		$ajaxResponse = PpinstallerAjaxResponse::getInstance();
		$ajaxResponse->addScriptCall('ppInstaller.replaceTpl',$this->loadTemplate('error'));
		$ajaxResponse->addScriptCall($this->getDynamicJavascript());
		$ajaxResponse->sendResponse();
	}	
	
}

