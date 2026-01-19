<?php

/**
* @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		RBINSTALLER
* @subpackage	Front-end
* @contact		support@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/** 
 * Base Controller
 * @author Gaurav Jain
 */
class RBInstallerController extends Rb_Controller
{
	public $_component = RBINSTALLER_COMPONENT_NAME;	
	
	function __construct($options = array())
	{
		parent::__construct();
		
		if(!isset($this->input)){
			$this->input = RBInstallerFactory::getApplication()->input; 
		}
		
		if(!isset($this->_helper)){
			$this->_helper = $this->getHelper();
		}
	}
	
	public function getHelper($name = '')
	{
		if(empty($name)){
			$name = $this->getName();
		}
		
		return RBInstallerFactory::getHelper($name);
	}
}
