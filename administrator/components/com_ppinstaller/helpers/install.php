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

class PpinstallerHelperInstall {
	
	//upgrade is allowed to - from
	public $allowedUpgrades  = Array(
								'3'=>'2.4'
							   );
	/*
	 * Return html for Installer Steps bar
	 */
	public static function stepbar()
 	{
		$task = JString::strtolower(JRequest::getWord('task','display'));
		switch ($task) {
			case 'backup':
			case 'revert':
				$on = 2;
				break;
			case 'migrate':
				$on = 3;
				break;
			case 'install':
				$on = 4;
				break;
			case 'patch':
				$on = 5;
				break;
			case 'complete':
				$on = 6;
				break;
			case 'display':
			default:
				$on = 1;
		}

 		$html =

	 			'<div class="pp-steps">'.
					'<div class="pp-step '.($on == 1 ? ' pp-active' : '').'" id="payplans_version">'.JText::_('COM_PPINSTALLER_STEP_1_LABEL').'</div>' .
					'<div class="pp-step '.($on == 2 ? ' pp-active' : '').'" id="payplans_backup">'.JText::_('COM_PPINSTALLER_STEP_2_LABEL').'</div>' .
					'<div class="pp-step '.($on == 3 ? ' pp-active' : '').'" id="payplans_migration">'.JText::_('COM_PPINSTALLER_STEP_3_LABEL').'</div>' .
 					'<div class="pp-step '.($on == 4 ? ' pp-active' : '').'" id="payplans_install">'.JText::_('COM_PPINSTALLER_STEP_4_LABEL').'</div>' .
					'<div class="pp-step '.($on == 5 ? ' pp-active' : '').'" id="payplans_patch">'.JText::_('COM_PPINSTALLER_STEP_5_LABEL').'</div>' .
					'<div class="pp-step '.($on == 6 ? ' pp-active' : '').'" id="payplans_complete">'.JText::_('COM_PPINSTALLER_STEP_6_LABEL').'</div>'.
 				'</div>';
			return $html;
	}
	
	public static function powered_by() {
		
		ob_start();
			echo JText::_('COM_PPINSTALLER_POWERED_BY');?>
		 	<a id="payplans-powered-by" href="http://www.jpayplans.com" target="_blank" >PayPlans</a><sup>TM</sup> <?php echo '<strong>'. PPINSTALLER_VERSION .'</strong>'?>	        		
	    <?php 
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
		     
	}
	
	
	
	public static function task_description($next_task) 
	{
		$append_msg = '';
		
		$task =JString::strtolower(JRequest::getWord('task','display'));
		
		switch($next_task){
			case 'backup':
				$msg = 'BACKUP_DESC';
				break;
			case 'migrate':
				$msg = 'MIGRATION_DESC';
				break;
			case 'install':
						$msg = 'INSTALLATION_DESC';
						$append_msg = JText::sprintf('COM_PPINSTALLER_LATEST_PAYPLANS_VERSION',PpinstallerHelperMigrate::requiredVersion());
						
						//If new version is already installed 
						$isPayPlansExist = PpinstallerHelperBackup::getTables();			
						// if tables exist then create backup
						if(!empty($isPayPlansExist) && $task != 'migrate')
						{
							$migration_status = PpinstallerHelperMigrate::getKeyValue();
							$bk_table_prefix  = PpinstallerHelperBackup::get_backup_table_prefix();
							$is_backup_exist  = PpinstallerHelperBackup::getTables($bk_table_prefix.'payplans%');
							// Say about how can revert payPlans Back up because currently,migration staus is "in progress".
							// if back up doesn't exist then skip this stage 
							if($migration_status >= PPINSTALLER_BACKUP_CREATED)
							{ // XiTODO:: clean code
								if(empty($is_backup_exist)) {
									$msg = 'DELETED_PREVIOUS_BACKUP';
									break;
								}
								$msg = 'INSTALLATION_WITH_REVERT_LINK_DESC';
								// dont use hard coded prefix
								$privious_version = PpinstallerHelperUtils::version_level(PpinstallerHelperMigrate::payplans_version($bk_table_prefix));
								$append_msg = JText::sprintf('COM_PPINSTALLER_PREVIOUS_PAYPLANS_VERSION',$privious_version);	
							}
						}
						break;
			case 'patch':
				$msg = 'PATCH_DESC';
				break;
			case 'complete':
				$msg = 'COMPLETE_DESC';
				break;
			default:
				$msg = 'DONT_HAVE_ANY_TASK';
		}
		
		if($task  == 'complete'){
			$msg = 'GO_TO_PAYPLANS_PAGE';
		}
		return JTEXT::_('COM_PPINSTALLER_'.$msg).$append_msg;

	}
	
	public static function addStyle($file ='core.css') {
		
		echo "<link rel='stylesheet' href='".PPINSTALLER_STYLE.$file."' />";
		//JFactory::getDocument()->addStyleSheet(PPINSTALLER_STYLE.$file);
	}
	public static function addScript($content='template.js',$script = false)
	{
		if(!$script) {
			echo '<script type="text/javascript" src="'.PPINSTALLER_JS.$content.'"></script>';
		}
		else {
			echo '<script type="text/javascript">'.$content.'</script>';
		}
		//JFactory::getDocument()->addScript(PPINSTALLER_JS.$file);
	}
	
	
	// XiTODO:: move it into utils file
	static function extract($archive_name,$extract_dir)
	{
		//delete existing folder
		if(is_dir($extract_dir)){
			JFolder::delete($extract_dir);
		}
		
		$is_success = JArchive::extract($archive_name, $extract_dir);
		PpinstallerHelperLogger::log(JText::_('COM_PPINSTALLER_FILE_EXTRACT_'.(bool)$is_success).' Extract-Dir:'.$extract_dir);
		
		return $is_success;
	}
	

	static function set_extension($status = 0)
	{
		$query = "UPDATE `#__extensions` SET `enabled` = '$status' WHERE `type`='component' AND `element` ='com_ppinstaller'";
		$db = JFactory::getDbo();
		$db->setQuery($query);

		$msg ='Issue on change extension-status';
		if($db->query()){
			$msg ='Successfully changed extension-status.';	
		}
		PpinstallerHelperLogger::log($msg);
	}

	static function uninstall($uninstall_items=Array())
	{
		// Get an installer object for the extension type
		$installer  =  JInstaller::getInstance();
		$is_success = true;
		
		foreach($uninstall_items as $item=>$item_type){								
			$ext_id = self::extension_id($item_type,$item);
	
			if(empty($ext_id)){	continue;}
			
			$result	   =  $installer->uninstall($item_type, $ext_id );

			if ($result === false) {
				$is_success &= false;
				$msg = JText::sprintf('COM_PPINSTALLER_UNINSTALLATION_ISSUE', $item.' '.$item_type);
			}else{
				$msg = JText::sprintf('COM_PPINSTALLER_SUCCESSFULLY_UNINSTALLED', $item.' '.$item_type);
			}
			PpinstallerHelperLogger::log($msg);
		}	
		
		return $is_success;
	}
	
	static function remove_plugins() 
	{
		$plugins = array( 
						array('system',	  			'payplans'),
					 	array('system',   			'payplanslogincontroller'),
					 	array('system',   			'mtreepayplans'),
					 	array('payplansmigration',  'acctexp'),
					 	array('payplansmigration',  'ambrasubs'),
					 	array('payplansmigration',  'amember')	
					  );
		
		$is_success = true;
					  
		foreach($plugins as $plugin){
			$folder = $plugin[0];
			$name 	= $plugin[1];
			$plugin_id = self::plugin_id($name, $folder);
			
			if(empty($plugin_id)){
				continue;
			}
			
			$is_success &= self::uninstall_extension('plugin',$plugin_id);
		}
		return $is_success;
	}
	
	static function remove_modules() 
	{
		$modules = array( 
							'mod_payplans_subscription'
					  	);
		$is_success = true;
		foreach($modules as $module_name){
			$module_id = self::module_id($module_name);
			
			if(empty($module_id)){
				continue;
			}
			
			$is_success &= self::uninstall_extension('module',$module_id);
		}
		return $is_success;
	}
	
	static function remove_component() 
	{
		$component_id = self::component_id();
		
		if(empty($component_id)){
				return true;
		}
					
		return self::uninstall_extension('component',$component_id);
	}
	
	static function changePluginState($name, $newState = 1, $folder = 'system')
		{
			$db		= JFactory::getDBO();
		        
			$query	= 'UPDATE '. $db->quoteName( '#__extensions' )
					. ' SET   '. $db->quoteName('enabled').'='.$db->Quote($newState)
					. ' WHERE '. $db->quoteName('element').'='.$db->Quote($name)
					. ' AND ' . $db->quoteName('folder').'='.$db->Quote($folder) 
					. " AND `type`='plugin' ";
			
			$db->setQuery($query);
			if(!$db->query())
				return false;
	
			return true;
		}
	
	
	static function plugin_id($name, $folder)
	{
		$db		= JFactory::getDBO();

		$query	= ' SELECT  `extension_id` FROM  '. $db->quoteName( '#__extensions' )
				. ' WHERE '. $db->quoteName('element')."='$name' "
				. ' AND ' . $db->quoteName('folder')."='$folder'" 
				. " AND `type`='plugin' ";
		
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	static function module_id($name)
	{	
		$db		= JFactory::getDBO();
				
		$query	= ' SELECT `id` FROM ' . $db->quoteName( '#__modules' )
		        . ' WHERE '  . $db->quoteName('module')."='$name'";
		
		
		$db->setQuery($query);
		return $db->loadResult();
	}
	
	static function component_id($name='PayPlans')
	{	
		$db		= JFactory::getDBO();

		$query = "  SELECT `extension_id` 
			   		FROM #__extensions
			   		WHERE `type`    = 'component' 
			   		AND `element` = 'com_payplans'
			 	";
		$db->setQuery($query);
		return $db->loadResult();
	}

	static function uninstall_extension($ext_type,$ext_id,$c_id=0)
	{
		// Get an installer object for the extension type
		$installer  =  JInstaller::getInstance();
		
		$result = $installer->uninstall($ext_type, $ext_id);
		
		if (false === $result){
			$msg = JText::sprintf('COM_PPINSTALLER_UNINSTALLATION_ISSUE', $ext_id.' '.$ext_type);
		}
		else{
			$msg = JText::sprintf('COM_PPINSTALLER_SUCCESSFULLY_UNINSTALLED', $ext_id.' '.$ext_type);
		}
		
		PpinstallerHelperLogger::log($msg);
		return $result;
	}
	
	public function isUpgradeAllowed()
	{
		$upgradingVersion = PpinstallerHelperUtils::version_level(PPINSTALLER_PAYPLANS_VERSION , 'major');
		$prevMajor = PpinstallerHelperMigrate::oldVersion('major');
		$prevMinor = PpinstallerHelperMigrate::oldVersion('minor'); 
		
		// major to major upgradation (same version) then do nothing
		if($upgradingVersion == $prevMajor){
			return true;
		}
		
		// check if upgrade is allowed
		if(isset($this->allowedUpgrades[$upgradingVersion]) && 
		  ($this->allowedUpgrades[$upgradingVersion] == $prevMajor.'.'.$prevMinor)){
			return true;
		}
		
		JFactory::getApplication()
				->enqueueMessage(JText::_('COM_PPINSTALLER_INSTALL_PREVIOUS_LATEST_VERSION_'.$prevMajor),'error');
		
		return false;
	}

	public static function fetchTheKit($version = '2.4', $ext_type = 'plg', $app_element = '', $app_folder = '', $zipFilePath = false)
	{
		$config 	= PpinstallerHelperUtils::getConfig();
		
		if('migration' !== $version && 'precheck' !== $version) {
			$explode_version  = explode('.', $version );

			if(!isset($explode_version[1])) {
				$explode_version[1] = 0;
			}

			$version = $explode_version[0].".".$explode_version[1];
		}
	
		$string		= "username=".$config->ppinstallerUsername
						."&password=".urlencode($config->ppinstallerPassword)
						."&version=".$version
						."&extension_type=$ext_type"
						."&client_id=0"
						."&app_element=$app_element"
						."&app_folder=$app_folder";
							
		$url 		= PpinstallerHelperUtils::getServerUrl().'&object=release&action=fetch';

		$msg = JText::sprintf('COM_PPINSTALLER_GOING_TO_ACCESS_URL', $url.'&'.$string);
		PpinstallerHelperLogger::log($msg);

		list($info,$response)=PpinstallerHelperUtils::postDataByCurl($url, $string, true);
	
		if( ! empty($response) && strtolower($info) === 'application/zip') {

			$tmp_file_name 	 	= ($zipFilePath) ? $zipFilePath : JPATH_ROOT.DS.'tmp'.DS.'payplans'.$version.'.zip';
					
			$config->save(array('installationZipPath'=>JPATH_ROOT.DS.'tmp'.DS.'payplans'.$version));
	
			// create a file
			chmod(PPINSTALLER_TMP_PATH, 0755);
			JFile::delete($tmp_file_name);
			return JFile::write($tmp_file_name, $response);		
		}

		$msg = JText::sprintf('COM_PPINSTALLER_FAILED_TO_ACCESS_URL', $url.'&'.$string);
		PpinstallerHelperLogger::log($msg);

		return false;
	}

}