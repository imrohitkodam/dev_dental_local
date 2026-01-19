
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

class PpinstallerControllerMigrate extends PpinstallerController
{
	public function display($cachable = false, $urlparams = false)
	{
		$input 			= JFactory::getApplication()->input;
		$nextTask 		= $input->get('nextTask','backup');
		$nextView 		= $input->get('nextView','migrate');
		$migrationTask 	= $input->get('migrationTask',array(),'array');
		
		if(empty($migrationTask)) {
			$class_name 	= PpinstallerHelperMigrate::fetchMigrationFile();
			$obj			= new $class_name;
			$migrationTempTask = $obj->get('action');
			$migrationTask = array('backup' => array(0,'Backup',''));
			
			foreach ($migrationTempTask as $task => $title) {
				$migrationTask[$task] = array(-1,$title,'');
			}
		}
		
		$this->_display('default','display',array($nextView,$nextTask,$migrationTask));
	}

	public function backup($tablePrefix = null)
	{
		$nextTask 		= 'migrate';
		$nextView 		= 'migrate';
		
		//process for taking backup
		$success = PpinstallerHelperBackup::create($tablePrefix);
		
		$this->_display('default','changeTask',array('backup',$nextView,$nextTask,'before',$startLimit,'Backup Done',$success));
		return true;
	}
	
	public function migrate()
	{
		//process for taking migration		
		$nextTask 		= 'migrate';
		$nextView 		= 'migrate';
		$startLimit 		= 0;

		$migrateAction	= JFactory::getApplication()->input->get('action','before');

		try {
		
			$class_name 	= PpinstallerHelperMigrate::fetchMigrationFile();
			$obj			= new $class_name;
			$migrate_info 	= $obj->$migrateAction();
		
			if(!isset($migrate_info['migrateAction'])){
				list($migrate_info['migrateAction'],$startLimit) = $obj->nextMigrateAction($migrateAction);
			}
			
			//if next action is nothing to execute then execute next task
			if(empty($migrate_info['migrateAction'])){
				$nextTask 		= 'display';
				$nextView 		= 'install';	
			}
			
			// set next task. {migration or install}
			$migrate_info['currentAction']  	= $migrateAction;
			
			if($migrate_info['currentAction'] == 'noMoreAction') {
				$nextTask 		= 'display';
				$nextView 		= 'install';
			}
			$this->_display('default','changeTask',array($migrate_info['currentAction'],$nextView,$nextTask,$migrate_info['migrateAction'],$startLimit,$obj->get('msg'),$obj->get('is_success')));
		}
		//if there is some error in migration process then do not execute further
		catch (Exception $e) {
			
			$this->_display('default','changeTask',array($migrate_info['currentAction'],$nextView,$nextTask,$migrate_info['migrateAction'],$startLimit,$e->getMessage()));
			return true;
		}
		
		return true;
	}
	
	public function restore($version = 0)
	{
		$config 	= PpinstallerHelperUtils::getConfig();
		$success = PpinstallerHelperBackup::revertDbBackup($config->goingToInstall);
		
		$this->_display('default','changeTask',array(__FUNCTION__,'install','display',null,0,'Reverting Database done',$success));
		return true;
	}
	
	public function needForRevert()
	{
		$lastBackupPrefix = PpinstallerHelperBackup::get_backup_table_prefix();
		$success = PpinstallerHelperBackup::revertDbBackup(0,$lastBackupPrefix);
	
		$this->_display('default','changeTask',array(__FUNCTION__,'complete','revertComplete',null,0,'Reverting Database done',$success));
		return true;
	}
	
}
