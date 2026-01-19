<?php

/**
* @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		RBInstaller
* @subpackage	Back-end
* @contact		support@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/** 
 * Config Model
 * @author Gaurav Jain
 */
class RBInstallerModelConfig extends RBInstallerModel
{
	function save($data = array(), $pk=null, $new=false)
	{		
		$keys 	= array_keys($data);
		$db 	= RBInstallerFactory::getDbo();
		$delete = " DELETE FROM `#__rbinstaller_config` WHERE `key` IN ('".implode("', '", $keys)."')" ;
		
		$db->setQuery($delete)
		   ->query();
		
		
		$query  =  "INSERT INTO `#__rbinstaller_config` (`key`, `value`) VALUES ";
		$queryValue = array();
		
		foreach ($data as $key => $value){
			if(is_array($value)){
				$value  = json_encode($value);
			}

			$queryValue[] = "(".$db->quote($key).",". $db->quote($value).")";
		}

		$query .= implode(",", $queryValue);
		
		return $db->setQuery($query)
		   		  ->query();
		
	}
}

class RBInstallerModelformConfig extends RBInstallerModelform
{
	
}