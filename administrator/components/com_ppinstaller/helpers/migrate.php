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
define('skipCache', true);

class PpinstallerHelperMigrate {
	
	static public function isRequired($oldVersion = false, $latestVersion = false)
	{
		
		/*
		 * if REVERT 		+1
		 * if DONOTHING  	 0
		 * if MIGRATE 		-1
		 */
		
		if(!$oldVersion) {
			
			//only major and minor version are considerable to check for migration
			$oldVersion = PpinstallerHelperMigrate::oldVersion('major');
			$oldVersion = $oldVersion.".".PpinstallerHelperMigrate::oldVersion('minor');
		}
		
		if(!$latestVersion) {
			//better to fetch from s3
			$config = PpinstallerHelperUtils::getConfig();
			$latestVersion = $config->latestRelease;
		}

		$oldVersion 	= number_format($oldVersion,1);
		$latestVersion 	= number_format($latestVersion,1);
		$migration 		= version_compare($oldVersion,$latestVersion);
		$fileAvailable 	= self::fetchMigrationFile($oldVersion, skipCache);
		
		if( 0 != $migration && $fileAvailable ) {
			return $migration;
		}
				
		return 0;
		
	}
	

	static public function oldVersion($level = '')
	{
		$version = self::payplans_version();
		
		if($version != false) {
			return PpinstallerHelperUtils::version_level($version,$level);
		}
		return $version;		
	}
	
	/**
	 *  Return Exist PayPlans Version
	 *  we can remove this build version query to select only global version
	 * */
	static public function payplans_version($table_prefix = '#__') 
	{
		
		$table_name = $table_prefix.'payplans_support';
		
		$db 	= JFactory::getDbo();

		$query  = "SHOW TABLES LIKE '{$db->getPrefix()}payplans_support'";
		$db->setQuery($query);
		$tableExist = $db->loadResult();
		
		if(!empty($tableExist)) {
			$db 	= JFactory::getDbo();
			$query  = " SELECT GROUP_CONCAT(`value` ORDER BY `key` DESC SEPARATOR '.') FROM `$table_name`
						WHERE `key`IN ('global_version', 'build_version')";
			
			$db->setQuery($query);
			return $db->loadResult();
		} else {
			return false;
		}
		
	}
	
	static public function requiredVersion($folder_path = null , $level= 'default')
	{
		static $version_level =Array();
		
		// Ticket number #1564
		if(!empty($version_level)){
			return $version_level[$level];
		}
		
		$full_version = PPINSTALLER_PAYPLANS_VERSION.'.'.PPINSTALLER_PAYPLANS_REVISION;

		$explode_version  = explode('.', $full_version );
		
		$version_level['major']		  = $explode_version[0];
		$version_level['minor']		  = $explode_version[1];
		$version_level['build']		  = $explode_version[2];
		$version_level['development'] = $explode_version[3];
		$version_level['default']     = PPINSTALLER_PAYPLANS_VERSION ;		
		
		return $version_level[$level];;	
	}
	
	static function isTableExist($tableName, $prefix='#__')
	{
		$db		 	= JFactory::getDBO();
		$tables		= $db->getTableList();
		
		//if table name consist #__ replace it.
		$tableName = str_replace($prefix, $db->getPrefix(), $tableName);

		//check if table exist
		return in_array($tableName, $tables ) ? true : false;
	}
	
	static public function fileName($version){
		$version = explode('.', $version);
		return "$version[0].$version[1].php"; 
	}
	
	static public function className($version){
		$version = explode('.', $version);
		return "Migrate$version[0]$version[1]"; 
	}	
	
	//XiTODO :: use utilts function
	static public function sessionValue($fun = 'get', $var ='payplans_version' ,$value= '20',$name_space = 'payplans_installer')
	{
		//get new version 
		return JFactory::getSession()->$fun($var, $value, $name_space );
	}
	
	static public function clear_session($variables =Array('payplans_version'))
	{ 
		if(!is_array($variables)){	$variables = Array($variables);	}
		
		foreach($variables as $var){
			PpinstallerHelperUtils::clear_session_value($var);
		}
	}
	
	static public function getFolderPath() 
	{
		$extractdir		 = constant('PPINSTALLER_TMP_PAYPLANS'.PPINSTALLER_PAYPLANS_KIT_SUFFIX);
	
		return $extractdir;
		
	}
	
	static function importSql($fileName)
	{
		$is_success = true;
		$db	= JFactory::getDBO();
		//read file
		if(!($sql = JFile::read($fileName))){
			return false;
		}

		//clean comments from files
		$sql = self::_filterComments($sql);

		//break into queries
		$queries	= $db->splitSql($sql);

		//run queries
		foreach($queries as $query)
		{
			//filter whitespace
			$query = self::_filterWhitespace($query);

			//if query is blank
			if(empty($query)){ continue; }

			//run our query now
			$db->setQuery($query);
			$is_success &= $db->query();

			//if error add it
			PpinstallerHelperLogger::log(JText::_('COM_PPINSTALLER_EXECUTE_QUERY'),$db->getErrorMsg());
		}
		return $is_success;
	}
	
	static function _filterComments($sql)
	{
		return preg_replace("!/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/!s","",$sql);
	}
	
	/*
	 * Filter Unneccessary characters from a query to identify empty query
	 */
	static function _filterWhitespace($sql)
	{
		//query need trimming
		$sql	=  trim($sql,"\n\r\t");

		//remove leading, trailing and "more than one" space in between words
		$pat[0] = "/^\s+/";
		$pat[1] = "/\s+\$/";
		$rep[0] = "";
		$rep[1] = "";
		$sql = preg_replace($pat,$rep,$sql);

		return $sql;
	}
	/**
	 * 
	 * Enter description here ...
	 */
	static public function getKeyValue($key = 'migration_status') 
	{
		try{
			$db		=  JFactory::getDbo();
			$query  = " SELECT `value` FROM `#__payplans_support` WHERE `key` = '$key' ";
			$db->setQuery($query);
			$key_value = $db->loadResult();
			return ($key_value) ? $key_value : false;
		}
		catch(Exception $e) {
			return false;
		}
	}
	
	static public function setKeyValue($key='migration_status',$value='') 
	{	
		if(empty($value)){
			//XITODO:: Handle it
			return false;
		}
		
		$current_status = self::getKeyValue($key);
		if(!$current_status){
			$query = " INSERT INTO `#__payplans_support` 
						   (`key`,`value`)VALUES
						   ('$key','$value')
					  ";
		}else {
			$query = " UPDATE `#__payplans_support`
							SET `value` = '$value'
							WHERE `key` = '$key'
					  ";
		}
		$db		=  JFactory::getDbo();
		$db->setQuery($query);
		$db->query();
		
		PpinstallerHelperLogger::log(JText::sprintf('COM_PPINSTALLER_UPDATE_MIGRATION_STATUS',$value),$db->errorMsg());
	}

	static public function stringToObject( $data, $process_sections = false )
	{
		static $inistocache;

		if (!isset( $inistocache )) {
			$inistocache = array();
		}

		if (is_string($data))
		{
			$lines = explode("\n", $data);
			$hash = md5($data);
		}
		else
		{
			if (is_array($data)) {
				$lines = $data;
			} else {
				$lines = array ();
			}
			$hash = md5(implode("\n",$lines));
		}

		if(array_key_exists($hash, $inistocache)) {
			return $inistocache[$hash];
		}

		$obj = new stdClass();

		$sec_name = '';
		$unparsed = 0;
		if (!$lines) {
			return $obj;
		}

		foreach ($lines as $line)
		{
			// ignore comments
			if ($line && $line{0} == ';') {
				continue;
			}

			$line = trim($line);

			if ($line == '') {
				continue;
			}

			$lineLen = strlen($line);
			if ($line && $line{0} == '[' && $line{$lineLen-1} == ']')
			{
				$sec_name = substr($line, 1, $lineLen - 2);
				if ($process_sections) {
					$obj-> $sec_name = new stdClass();
				}
			}
			else
			{
				if ($pos = strpos($line, '='))
				{
					$property = trim(substr($line, 0, $pos));

					// property is assumed to be ascii
					if ($property && $property{0} == '"')
					{
						$propLen = strlen( $property );
						if ($property{$propLen-1} == '"') {
							$property = stripcslashes(substr($property, 1, $propLen - 2));
						}
					}
					// AJE: 2006-11-06 Fixes problem where you want leading spaces
					// for some parameters, eg, class suffix
					// $value = trim(substr($line, $pos +1));
					$value = substr($line, $pos +1);

					if (strpos($value, '|') !== false && preg_match('#(?<!\\\)\|#', $value))
					{
						$newlines = explode('\n', $value);
						$values = array();
						foreach($newlines as $newlinekey=>$newline) {

							// Explode the value if it is serialized as an arry of value1|value2|value3
							$parts	= preg_split('/(?<!\\\)\|/', $newline);
							$array	= (strcmp($parts[0], $newline) === 0) ? false : true;
							$parts	= str_replace('\|', '|', $parts);

							foreach ($parts as $key => $value)
							{
								if ($value == 'false') {
									$value = false;
								}
								else if ($value == 'true') {
									$value = true;
								}
								else if ($value && $value{0} == '"')
								{
									$valueLen = strlen( $value );
									if ($value{$valueLen-1} == '"') {
										$value = stripcslashes(substr($value, 1, $valueLen - 2));
									}
								}
								if(!isset($values[$newlinekey])) $values[$newlinekey] = array();
								$values[$newlinekey][] = str_replace('\n', "\n", $value);
							}

							if (!$array) {
								$values[$newlinekey] = $values[$newlinekey][0];
							}
						}

						if ($process_sections)
						{
							if ($sec_name != '') {
								$obj->$sec_name->$property = $values[$newlinekey];
							} else {
								$obj->$property = $values[$newlinekey];
							}
						}
						else
						{
							$obj->$property = $values[$newlinekey];
						}
					}
					else
					{
						//unescape the \|
						$value = str_replace('\|', '|', $value);

						if ($value == 'false') {
							$value = false;
						}
						else if ($value == 'true') {
							$value = true;
						}
						else if ($value && $value{0} == '"')
						{
							$valueLen = strlen( $value );
							if ($value{$valueLen-1} == '"') {
								$value = stripcslashes(substr($value, 1, $valueLen - 2));
							}
						}

						if ($process_sections)
						{
							$value = str_replace('\n', "\n", $value);
							if ($sec_name != '') {
								$obj->$sec_name->$property = $value;
							} else {
								$obj->$property = $value;
							}
						}
						else
						{
							$obj->$property = str_replace('\n', "\n", $value);
						}
					}
				}
				else
				{
					if ($line && $line{0} == ';') {
						continue;
					}
					if ($process_sections)
					{
						$property = '__invalid'.$unparsed ++.'__';
						if ($process_sections)
						{
							if ($sec_name != '') {
								$obj->$sec_name->$property = trim($line);
							} else {
								$obj->$property = trim($line);
							}
						}
						else
						{
							$obj->$property = trim($line);
						}
					}
				}
			}
		}

		$inistocache[$hash] = clone($obj);
		return $obj;
	}
	
	//fetch migration file and return it's object
	//may be usefull when fetching data from S3
	public static function fetchMigrationFile($version = 0, $skipCache = false)
	{
		//this checking would be need cause it would be called many times in case of migration, it may timesaver
		$config = PpinstallerHelperUtils::getConfig();
		if($config->migrationClass && true != $skipCache) {
			include_once $config->migrationFile;
			return $config->migrationClass;
		}
	
		$version 		= (empty($version))?PpinstallerHelperMigrate::oldVersion():$version;
		$file       	= PpinstallerHelperMigrate::fileName($version);
		$class_name 	= PpinstallerHelperMigrate::className($version);
		$file_path 		= PPINSTALLER_MIGRATER_PATH.DS.$file;
	
		if(!JFile::exists($file_path)){
	
			try{
				$version 	= number_format($version,1);	
				$filename 	= "migration".$version;
				
				$res = PpinstallerHelperInstall::fetchTheKit('migration','file',$filename,'',PPINSTALLER_TMP_PATH.DS.$filename.'.zip');
				
				if(empty($res)) {
					throw new Exception('Error in fetching file');
				}

				PpinstallerHelperInstall::extract(PPINSTALLER_TMP_PATH.DS.$filename.'.zip', PPINSTALLER_TMP_PATH.DS.$filename);
				
				$installer 	= JInstaller::getInstance();
				$success	= $installer->install(PPINSTALLER_TMP_PATH.DS.$filename);
				
				JFile::delete(PPINSTALLER_TMP_PATH.DS.$filename);
				JFile::delete(PPINSTALLER_TMP_PATH.DS.$filename.'.zip');
			}	
			catch (Exception $e) {	
				PpinstallerHelperLogger::log(Jtext::sprintf('COM_PPINSTALLER_MISSING_MIGRATION_FILE',$file_path));
			
				return false;
			}
		}
	
		$config->save(array('migrationFile'=>$file_path,'migrationClass'=>$class_name));
		
		include_once $file_path;
	
		return $class_name;
	}
	
	public static function checkForRestorePoint()
	{
		$restoringPoints 	= array();
		$database = JFactory::getConfig()->get('db');
		$db 				= JFactory::getDbo();
		$query   			= "show tables where `Tables_in_".$database."` like 'bk___payplans_support'";
		$db->setQuery($query);
		$backuptables 	= $db->loadColumn();
		
		if( !empty($backuptables) ) {
			foreach ($backuptables as $table) {
				$tmp =  strtok($table,'_');
				$tmp = (int)str_replace('bk', '' , $tmp);
				$tmp = number_format($tmp/10,1);
				$restoringPoints["$tmp"] = false;
			}
		}
		
		return $restoringPoints;
	}
}
