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

if(RB_REQUEST_DOCUMENT_FORMAT === 'ajax'){
	class RBInstallerViewbase extends Rb_ViewAjax{}
}elseif(RB_REQUEST_DOCUMENT_FORMAT === 'json'){
	class RBInstallerViewbase extends Rb_ViewJson{}
}else{
	class RBInstallerViewbase extends Rb_ViewHtml{}
}


/** 
 * Base View
 * @author Gaurav Jain
 */
class RBInstallerView extends RBInstallerViewbase 
{
	public $_component = RBINSTALLER_COMPONENT_NAME;
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		
		// intialize input
		$this->input = RBInstallerFactory::getApplication()->input;		
		
		if(!isset($this->_helper)){
			$this->_helper = $this->getHelper();
		}
		
		return $this;
	}
	
	public function getHelper($name = '')
	{
		if(empty($name)){
			$name = $this->getName();
		}
		
		return RBInstallerFactory::getHelper($name);
	} 
	
	protected function _adminGridToolbar()
	{
		return true;	
	}
	
	protected function _adminEditToolbar()
	{
		return true;
	}
	
	protected function _showFooter()
	{
		// avoid ajax request
		if(RB_REQUEST_DOCUMENT_FORMAT=='ajax'){
			return '';
		}
		
		//always shown in admin
		if(RbInstallerFactory::getApplication()->isAdmin()==true){
			return $this->_showAdminFooter();
		}
		
		return '';
	}
}
