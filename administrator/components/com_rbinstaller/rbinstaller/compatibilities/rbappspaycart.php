<?php 

/**
* @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		Rbinstaller
* @subpackage	Back-end
* @contact		support@readybytes.in
*/
class RBInstallerCompatibilityRbappspaycart
{
	public function get_version($installed_extenstions)
	{		 
		$ext = 'component__com_paycart_1';
		if(!isset($installed_extenstions[$ext])){
			return '0.0.0';
		}
		
		$manifest_cache = json_decode($installed_extenstions[$ext]->manifest_cache);
		return $manifest_cache->version;
	}
	
	public function get_return_url()
	{
		return 'index.php?option=com_paycart';
	}
	
	public function get_token()
	{
		return 'paycart';
	}
	
	public function get_color_code()
	{
		return '#00ccff';
	}
}
