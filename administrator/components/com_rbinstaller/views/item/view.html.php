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
 * App Manager Html View
 * @author Gaurav Jain
 */
require_once dirname(__FILE__).'/view.php';
class RBInstallerAdminViewItem extends RBInstallerAdminBaseViewItem
{
	protected function _adminToolbar()
	{
		$this->_adminToolbarTitle();
	}
	
	public function display($tpl = null)
	{
		$this->assign('is_uppgradable', $this->_helper->is_component_upgradable());
		
		$product_tag = $this->get('product_tag');
		if(empty($product_tag)){
			$this->setTpl('error');
			return true;
		}
		
		$email = $this->_helper->get('email');
		
		$user = null;
		if(!empty($email)){
			// get user from email
			try{
				$user 				= (array) $this->_helper->get_user($email);
				$user 				= array_shift($user);				
			}
			catch (Exception $e){
				$user = null;
			}
		}
		
		$added_items = $this->_helper->get('cart_items');		
		
		if(!empty($added_items)){
			$added_items = explode(",", $added_items);
			$added_items = array_combine($added_items, $added_items);
		}
		
		$app_data 	 = $this->_helper->get_items($product_tag, $added_items, $user);		

		$invoices = array();
		if(isset($user['buyer_id']) && $user['buyer_id']){
			$invoices = $this->_helper->get_invoices($user['buyer_id']);
		}
		
		$this->assign('helper', $this->_helper);
		$this->assign('added_items', $added_items);
		$this->assign('data', 		 $app_data);
		$this->assign('invoices', 	 array_reverse($invoices, true));
		
		// XITODO : get component name
		// IMP : Tag will not contain "_" so use "-"
		$this->assign('default_tag', $product_tag);
		
		$return_url = 'index.php';
		$obj 		= $this->_helper->get_compatibility_object($product_tag);
		if($obj && method_exists($obj, 'get_return_url')){
			$return_url = $obj->get_return_url();
		}
		
		$product_version = '0.0.0';
		if($obj && method_exists($obj, 'get_version')){
			$product_version = $obj->get_version($this->_helper->get_extensions());
		}
		
		$this->assign('product_version', $product_version);
		$this->assign('return_url', $return_url);
		$this->assign('config', $this->_helper->get_config());
		return true;
	} 
}