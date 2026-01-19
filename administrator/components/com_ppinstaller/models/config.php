<?php
/**
 * configuration model
 * @copyright Copyright (C) 2009 - 2014 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
 * @license GNU/GPL, see LICENSE.php
 * @package Payplans Installer
 * @version 3.1.2
 * @author Mohit Agrawal <mohit@readybytes.in>
 */
// no direct access
defined('_JEXEC') or die();
jimport('joomla.application.component.model');

if(interface_exists('JModel')) {
	abstract class PpinstallerModelBase extends JModelLegacy {}
} else {
	class PpinstallerModelBase extends JModel {}
}

class PpinstallerModelConfig extends PpinstallerModelBase
{
	public static $_config;
	
	public function __construct($config)
	{
		self::loadRecords();
		return parent::__construct($config);
	}
	
	public static function loadRecords()
	{
		if (!empty(self::$_config)) {
			return self::$_config;
		} 
		
		$db 		= JFactory::getDbo();
		$config   = array();
	
		$query 	= " SELECT `key`, `value` FROM `#__ppinstaller_config`" ;
		
		$tmpConfig 	= $db->setQuery($query)->loadObjectList();
		
		if ( empty($tmpConfig) ) {
			return self::$_config = $config	;
		}
		foreach ($tmpConfig as $c) {
			$config[$c->key] = json_decode($c->value, true); 
		}
		
		return self::$_config = $config	;
	}
	
	public function save($data =array(), $pk = NULL, $new = false)
	{		
		$oldConfig = $this->loadRecords();
		
		if ( is_object($data) ) {
			$data = (array) $data;
		}
			
		
		$config = array_merge($oldConfig, $data);
		
		if ( empty( $config) ) {
			return false;
		}
			
		
		self::$_config = $config;
		
		$keys 		= array_keys($config);
		$db   		= JFactory::getDbo();
		$delete 	= " DELETE FROM `#__ppinstaller_config` WHERE `key` IN ('".implode("', '", $keys)."')" ;
		
		$db->setQuery($delete)->query();
		
		$query  				=  "INSERT INTO `#__ppinstaller_config` (`key`, `value`) VALUES ";
		$queryValue 	= array();
		
		foreach ($config as $key => $value){
			
			$value  = json_encode($value);
			
			$queryValue[] = "(".$db->quote($key).",". $db->quote($value).")";
		}
		$query .= implode(",", $queryValue);
		
		return $db->setQuery($query)->query();
	}
	
	public function __get($variable)
	{	
		if (isset(self::$_config[$variable])) {
			return self::$_config[$variable];
		}
		else {
			return null;
		}
	} 
	
	public function __set($variable, $value)
	{
		if (isset(self::$_config[$variable])) {
			return self::$_config[$variable] = $value;
		}
		else {
			return null;
		}
	}
}
