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
 * Item Base View
 * @author Gaurav Jain
 */
class RBInstallerAdminBaseViewItem extends RBInstallerView
{	
	public function __construct($config = array())
	{
		parent::__construct($config);	
	}
		
	public function display($tpl = null)
	{
		return true;
	}
	
	public function _basicFormSetup($task)
	{
		return true;
	}
		
	public function _adminSubmenu($selMenu = 'dashboard')
	{
		
		return $this;
	}
}