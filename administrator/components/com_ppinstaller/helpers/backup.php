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

class PpinstallerHelperBackup {
	
	/**
	 * Return true if back up required
	 */
	static public function is_required() 
	{
		$installed_version = PpinstallerHelperMigrate::oldVersion('major');
		$new_version 	   = PpinstallerHelperMigrate::requiredVersion(null, 'major');
		
		$migration_status = PpinstallerHelperMigrate::getKeyValue();
		//if upgrading from major version to next major version then always do migration
		if($new_version > $installed_version){
			if($migration_status >= PPINSTALLER_MIGRATION_START && $migration_status < PPINSTALLER_MIGRATION_SUCCESS){
				return false;
			}
			return true;
		}
		
		// if installed version and required version same then no need to back up
		if($new_version <= $installed_version){
			return false;
		}
		

		//XiTODO Stop it:: If I have already backup... still Installer will create back up (after revert and come back on home screen.) 
		if(false == $migration_status){
			return true;
		}
		
		// May be issue occure on migration time or after migration
		if( $migration_status >= PPINSTALLER_MIGRATION_START){
			return false;
		}
		
	
		return true;
	}
	
	static public function create($bk_table_prefix = '') 
	{
		try {
			if(!$bk_table_prefix){
				$bk_table_prefix = self::table_prefix();
			}
			//XiTODO::need to files backup
			self::createDbBackup($bk_table_prefix);
			PpinstallerHelperMigrate::setKeyValue('migration_status',PPINSTALLER_BACKUP_CREATED);
			// manage prefix of backup table
			self::set_backup_table_prefix($bk_table_prefix);
		}
		catch(Exception $e) { 
			$msg = JText::sprintf('COM_PPINSTALLER_QUERY_NOT_EXECUTING_PROPERLY');
			PpinstallerHelperLogger::log($msg, $e->getMessage());
			return false;
		}
		
		return true;
	}
	
	/**
	 * Return table prefix for backup table
	 * 'bk{MAJOR AND MINOR VERSION OF INSTTALED PAYPLANS}_'
	 * like: bk14_ , bk20_ etc
	 */
	static public function table_prefix($version = 0)
	{
		$installed_version 	= (empty($version)) ? PpinstallerHelperMigrate::oldVersion() : $version;
		$major_version 		= PpinstallerHelperUtils::version_level($installed_version , 'major');
		$minor_version 		= PpinstallerHelperUtils::version_level($installed_version , 'minor');
		return "bk{$major_version}{$minor_version}_";
		
	}
	static public function createDbBackup($backup_prefix = '')
	{
		// get DB name
		//$dbName			= JFactory::getConfig()->get('db');
		//Get all Payplans table name
		$payPlansTables = self::getTables();
		$db 			= JFactory::getDbo();
		
		// XiTODO:: Dump all tables in our extension folder
		//$backup_prefix = PpinstallerHelperMigrate::sessionValue('get','backup_prefix',JRequest::getVar('bakup_prefix','bk_'));
		if(!$backup_prefix) {
			$backup_prefix = self::table_prefix();	
		}
		// drop all backup tables if already exist
		self::dropTables($backup_prefix.'payplans%');

		//create back up 
		foreach ($payPlansTables as $table) {
			$bkTable = str_replace($db->getPrefix(),$backup_prefix, $table);
			// Create table with SCHEMA
			$db->setQuery("CREATE TABLE $bkTable LIKE $table");
			$isSuccess = $db->query();
			if(empty($isSuccess)) {
				 throw new Exception("Query is not executing properly : ".mysql_error());
			}
			
			$msg = JText::sprintf('COM_PPINSTALLER_CREATED_BACKUP_TABLE',$bkTable);
			PpinstallerHelperLogger::log($msg, $db->getErrorMsg());
				
			// backup data
			$db->setQuery("INSERT INTO $bkTable SELECT * FROM $table");
			$isSuccess = $db->query();
			
			if(empty($isSuccess)) {
				throw new Exception("Query is not executing properly : ".mysql_error());
			}
			
			$msg = JText::sprintf('COM_PPINSTALLER_INSERT_INTO_BACKUP_TABLE',$bkTable);
			PpinstallerHelperLogger::log($msg, $db->getErrorMsg());							
		}
		
		return true;
	}
	
	static public function dropTables($pattern, $drop_tables=Array())
	{
		//get all tables for drop
		if(empty($drop_tables)){
			$drop_tables = self::getTables($pattern);
		}
		// tables doesn't exist
		if(empty($drop_tables)) {return false;}
		//Drop all tables
		$query = "DROP TABLE IF EXISTS ".implode(',', $drop_tables);
		$db = JFactory::getDbo();
		$db->setQuery($query);
		$is_success = $db->query();
		
		if(empty($is_success)) {
			throw new Exception("Query is not executing properly : ".mysql_error());
		}
		
		PpinstallerHelperLogger::log(JText::sprintf('COM_PPINSTALLER_DROP_TABLES',$pattern),$db->getErrorMsg());

		return $is_success;
	}
	
	/**
	 * return array with all available payplans tables. 
	 * false if table does not exist 
	 */
	static public function getTables($pattern = "")
	{
		$db      = JFactory::getDbo();
		$pattern = empty($pattern)?$db->getPrefix().'payplans%':$pattern;
		$db->setQuery("SHOW TABLES LIKE '$pattern'");
		return $db->loadColumn();
	}
	
	/**
	 * return array with all available payplans tables. 
	 * false if table does not exist 
	 */
	static public function revertDbBackup($version = 0, $oldPrefix = false)
	{
		if( ! $version && ! $oldPrefix) {
			return false;
		}

		$db 				= JFactory::getDbo();
		$oldPrefix 		= ( $oldPrefix ) ? $oldPrefix : self::table_prefix($version);
		$currentPrefix 	= $db->getPrefix();
		$is_success 		= true;


		try {		
			$is_success = self::dropTables($currentPrefix.'payplans%');
			if(empty($is_success)) {
				throw new Exception(JText::_('COM_PPINSTALLER_FAILER_IN_DROPPING_DATABASE'));
			}

			$database 		= JFactory::getConfig()->get('db');
			$query   		= "show tables where Tables_in_".$database." like '".$oldPrefix."payplans_%'";
			$db->setQuery($query);
			$oldtables 		= $db->loadColumn();
		
			foreach ($oldtables as $table) {
				$newName = str_ireplace($oldPrefix, $currentPrefix, $table);
				$query = "ALTER TABLE `$table` RENAME `$newName`";
				$db->setQuery($query);
				$result = $db->execute();
				if (empty($result)) {
					throw new Exception(JText::_('COM_PPINSTALLER_FAILER_IN_REMANING_DATABASE'));
				}
			}
		}
		catch (Exception $e) {
			$msg = "COM_PPINSTALLER_FAILURE_RESTORED_BACKUP";
			PpinstallerHelperLogger::log(JText::_($msg));
			return false;
		}

		$msg = "COM_PPINSTALLER_SUCCESSFULLY_RESTORED_BACKUP";
		PpinstallerHelperLogger::log(JText::_($msg));	
		return true;
		
	}
	/**
	 * 
	 * Copy tables data with structure
	 * @param $table = Array(Olda_tbale_name=>New_table_name)
	 */
	static public function cloneTable($tables=Array())
	{
		$db      	= JFactory::getDbo();
		$is_success = true;
		if(empty($tables)) { return false;}
		foreach ($tables as $old_table => $new_table){
			// create table structure
			$db->setQuery("CREATE TABLE $new_table LIKE $old_table");
			$is_success &= $db->query();
			// insert table content
			$db->setQuery("INSERT INTO $new_table SELECT * FROM $old_table");
			$is_success &= $db->query();
						
			PpinstallerHelperLogger::log(JText::_('COM_PPINSTALLER_CREATE_CLONE'),"$old_table => $new_table".$db->getErrorMsg());
		}
		return $is_success;
	}
	
	/**
	 * return prefix of last created backup table
	 */
	static public function get_backup_table_prefix()
	{
		$prefix = PpinstallerHelperMigrate::getKeyValue('last_backup_prefix');
		return ($prefix) ? $prefix :'bk_';
	}
	
	/**
	 * store prefix of last created backup table into PayPlans support table 
	 */
	static public function set_backup_table_prefix($value= 'bk_')
	{
		PpinstallerHelperMigrate::setKeyValue('last_backup_prefix', $value);
	}
	
}