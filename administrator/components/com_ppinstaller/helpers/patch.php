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

class PpinstallerHelperPatch
{
	/**
	 * if $patch already applied then return false otherwise return true.
	 */
	static function is_applicable($patch) 
	{
		$db= JFactory::getDbo();

		$query = " 
					 SELECT `value`
				     FROM   `#__payplans_support`
				     WHERE  `key`   = '$patch' 
				  ";

		$db->setQuery($query);
		$is_required = $db->loadResult();
		
		// false means, patch already applied
		return !(bool)$is_required ;
	}

	/** 
	 * insert patches into database.
	 * @param $patch = Array('patch_name'=>value)
	 * @param $value of patch when patch is 'patch_name' instead of patch array 
	 */
	static function insert($patches = Array(), $value = 1, $on_duplicate_update = true)
	 {
	 	if(empty($patches)){
	 		return true;
	 	}
		
	 	if(!is_array($patches)) { $patches = Array($patches => $value ); }
	 	
	 	$db = JFactory::getDbo();
	 	
	 	$ignore = '';
	 	// Update duplicate entry 
		$where = ' ON DUPLICATE KEY UPDATE `value`=VALUES(`value`) ';

	 	if(!$on_duplicate_update){
			// Dont update duplicate entry when key status(value) is 0
			$ignore = 'IGNORE';
			$where  = ''; 
	 	}
	 	
	 	$insert = "
	 				INSERT $ignore INTO `#__payplans_support`
	 					(`key`,`value`)
	 			 ";
	 	  
	 	$values = ' VALUES';
		foreach ($patches as $key => $value) {
			$values .= "('$key','$value'),";
		}
		// remove last character (,)
		$values = substr($values,0,strrpos($values,','));
		$db->setQuery($insert.$values.$where);

		return (bool) $db->query();
	}
	
	// XiTODO:: Move to Pyplans patch file
	static function update_previous_patches($last_patch)
	{
		$patches  = PayplansHelperPatch::getMapper();
		$key      = array_search($last_patch,$patches);
		for($i = 0 ; $i <= $key ;$i++) {
			$applied_patches[$patches[$i]] = 1;
		}
		
		$result = self::insert($applied_patches,1,false);
		$query = "DELETE FROM `#__payplans_support` WHERE `key` = 'lastDbPatch' ";
	
		$db = JFactory::getDbo();
		$db->setQuery($query);
		return  (bool)$db->query();
	}
	
	/**
	 * return applied patches
	 */
	static function applied_patches($value = 1)
	{
		$condition = " (`key` LIKE 'patch_%' OR `key` LIKE 'secondPatch') AND `value` = $value ";

		$query = '
					SELECT `key`
					FROM `#__payplans_support`
					WHERE '.$condition;

		$db = JFactory::getDbo();
		
		$db->setQuery($query);
		$result = $db->loadColumn();
		// Below three patches already applied by Installation
		array_unshift($result, 'patch_enable_plugins','patch_enable_modules');
		// value should be unique.
		return array_values(array_unique($result));
	}

	/**
	 * Set required patch into session
	 */
	static function set_into_session() 
	{
		$patches 			= PayplansHelperPatch::getMapper();			
		$applied_patches  	= self::applied_patches();
		//Index must be sequentially
		$required_patches	= array_values(array_diff($patches,$applied_patches));
		PpinstallerHelperUtils::set_session_value(PPINSTALLER_REQUIRED_PATCHES,$required_patches);
		
		return $required_patches;
	}
	
	/**
	 * Clear Patchs from session
	 */
	static function clear_session()
	{
		PpinstallerHelperUtils::clear_session_value(PPINSTALLER_REQUIRED_PATCHES);
	}
	
	
}
