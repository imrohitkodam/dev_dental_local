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
 * Config Table
 * @author Gaurav Jain
 */
class RBInstallerTableConfig extends RBInstallerTable
{
	public function __construct($table = '#__rbinstaller_config', $key = 'key', $db = null)
	{
		parent::__construct($table, $key, $db);
	}
}