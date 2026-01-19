<?php

/**
 * @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * @package 	RBInstaller
 * @subpackage	Front-end
 * @contact		support@readybytes.in
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/** 
 * Base Model Form
 * @author Gaurav Jain
 */
class RBInstallerModelform extends Rb_Modelform
{
	public	$_component			= RBINSTALLER_COMPONENT_NAME;	
	protected $_forms_path 		= RBINSTALLER_PATH_CORE_FORMS;
	protected $_fields_path 	= RBINSTALLER_PATH_CORE_FIELDS;
}