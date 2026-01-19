<?php
/**
* @copyright	Copyright (C) 2009 - 2013 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		RBInstaller
* @subpackage	Back-end
* @contact		support@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class RBInstallerAdminControllerItem extends RBInstallerController
{
	public function getModel($name = '', $prefix = '', $config = array())
	{
		return null;
	}

	public function display($cachable = false, $urlparams = array())
	{
		$product_tag = $this->input->get('product_tag');
		// set $product_tag
		RBInstallerHelperItem::productTag($product_tag);
		
		$this->getView()->assign('product_tag', $product_tag);
		return true;
	}
	
	public function addToCart()
	{
		$args = $this->_getArgs();
		
		$item_id = isset($args['item_id']) ? $args['item_id'] : 0;
		
		// no item-id provided to add in the cart
		if (!$item_id){
			// XITODO :
			return ;
		}

		$cart_items = $this->_helper->get('cart_items');
		
		$cart = $item_id;
		if(!empty($cart_items)){
			$cart = explode(',', $cart_items);
			
			if (!in_array($item_id, $cart)){
				$cart[] = $item_id;
			}
			
			$cart = implode(',', $cart);
		}
		
		$this->_helper->set(array('cart_items' => $cart));
		
		$response = Rb_Factory::getAjaxResponse();
		$response->addScriptCall('rbinstaller.cart.add_success', explode(',', $cart));
		$response->sendResponse();
	}
	
	public function removeFromCart()
	{
		$args = $this->_getArgs();
		
		$item_id = isset($args['item_id']) ? $args['item_id'] : 0;
		
		// 	no item-id provided to remove from the cart
		if (!$item_id){
			return ;
		}

		$added_items = $this->_helper->get('cart_items');
		$added_items = explode(",", $added_items);
		
		if(in_array($item_id, $added_items)){
			$added_items = array_diff($added_items, array($item_id));
			$this->_helper->set(array('cart_items' => implode(",", $added_items)));
		}
		
		$response = Rb_Factory::getAjaxResponse();
		$response->addScriptCall('rbinstaller.cart.remove_success', $added_items);
		$response->sendResponse();
	}
	
	public function checkout()
	{		
		$product_tag = $this->input->get('product_tag');
		// set $product_tag
		RBInstallerHelperItem::productTag($product_tag);
		
		$ajax_response = Rb_Factory::getAjaxResponse();
		$args = $this->_getArgs();

		$data = array();
		$data['paymart']['items'] = isset($args['items']) ? $args['items'] : array();
		$data['paymart']['buyer_email']	= $this->_helper->get('email');
		
		//XITODO : need to discuss, if we can send buyer email
		try{
			$user = (array) $this->_helper->get_user($data['paymart']['buyer_email']);
			$user = array_shift($user);
		}
		catch (Exception $e){
			//XITODO : what to do
			$ajax_response->sendResponse();
		}
		
		$data['paymart']['buyer_id']	= $user['buyer_id'];
		$data['paymart']['currency']	= 'USD';
				
		$data['paymart']['domain']	= JUri::root();
		
		try{
			$invoice = $this->_helper->create_invoice($data);
		}
		catch (Exception $e){
			//XITODO : what to do
			$ajax_response->sendResponse();
		}
		
		// IMP :: as invoice has been created than clean the cart items
		// XITODO : cartitems should be deleted after invoice is paid
		$this->_helper->set(array('cart_items' => ''));
		$invoice = (array) $invoice;
		$invoice = array_shift($invoice);
		
		try{
			$url = $this->_helper->get_pay_url($invoice['invoice_id']);
		}
		catch (Exception $e){
			//XITODO : what to do
			$ajax_response->sendResponse();
		}
		
		if(isset($args['return_url'])){
			$url .= '&return_url='.base64_encode(urldecode($args['return_url']));
		}
		
		$ajax_response->addScriptCall('rbinstaller.cart.redirect_to_pay',$url);
		$ajax_response->sendResponse();
		
	}
	
	public function install()
	{ 
		$product_tag = $this->input->get('product_tag');
		// set $product_tag
		RBInstallerHelperItem::productTag($product_tag);
		
		$ajax_response  = Rb_Factory::getAjaxResponse();
		$args  = $this->_getArgs();
	
		if(!isset($args['item_id']) || !isset($args['version_id'])){
			//XITODO :send error response
			$ajax_response->sendResponse();
		}
		
		try {
			$product_tag = isset($args['product_tag']) ? $args['product_tag'] : '';
			$file = $this->_helper->get_version_file($args['item_id'], $args['version_id'], $product_tag);
			$response = $this->_helper->install($file, $args['item_id'], $args['version_id']);
			
			$decoded_reponse = json_decode($response, true);
			if(isset($decoded_reponse['response_code']) && $decoded_reponse['response_code'] == 200){
				$this->_helper->changeStatus($args['item_id']);
			}			
		}
		catch (Exception $e){
			//XITODO : error handling
			$response = json_encode(array('response_code' => 400, 'reason' => $e->getMessage()));
		}		
		
		$ajax_response->addScriptCall('rbinstaller.item.install_response',$response);
		$ajax_response->sendResponse();
	}

	public function credential()
	{
		$product_tag = $this->input->get('product_tag');
		// set $product_tag
		RBInstallerHelperItem::productTag($product_tag);
		
		$action = $this->input->get('action', 'check');
		$this->getView()->assign('action', $action);
		
		if($action == 'check'){
			$credential['email'] 		= $this->_helper->get('email');
			$credential['password'] 	= $this->_helper->get('password');			
		}
		
		if($action == 'verify'){
			$credential = $this->_getArgs();					
			$credential['password'] = isset($credential['password']) ? urldecode($credential['password']) : ''; // encoded by javascript 
		}
		
		$verified = true;
		if(isset($credential['email']) && isset($credential['password'])
			&& !empty($credential['email']) && !empty($credential['password'])){		
			// verify the credential from app server
			$response = $this->_helper->verify_crendetial($credential['email'], $credential['password']);
			if($response && !(is_array($response) && isset($response['error']) && $response['error']== true)){
				$verified = true;
			}
		}
		
		$this->getView()->assign('credential_verified', $verified);
		$this->getView()->assign('credential', $credential);
		
		return true;
	}
	
	public function registration()
	{
		$product_tag = $this->input->get('product_tag');
		// set $product_tag
		RBInstallerHelperItem::productTag($product_tag);
		
		$action = $this->input->get('action', 'check');
		$this->getView()->assign('action', $action);
		
		if($action == 'form'){
			return true;
		}
		
		if($action == 'register'){
			$args 		= $this->_getArgs();
			$email 		= $args['email'];			
			$password 	= urldecode($args['password']); // encoded by javascript 
			$registered = false;
			
			$response = $this->_helper->register($email, $password);
			if(!(is_array($response) && isset($response['error']) && $response['error']== true)){
				$registered = true;
			}
			
			$view = $this->getView();
			$view->assign('registered', $registered);
			$view->assign('email', $email);
		}
	}	
	
	public function component_upgrade()
	{ 
		$ajax_response  = Rb_Factory::getAjaxResponse();		
		
		try {
			$file = $this->_helper->get_component_file();
			$config = $this->_helper->get_update_configuration('rbinstaller');
			$response = $this->_helper->install($file, 'rbinstaller', $config['version']);
		}
		catch (Exception $e){
			//XITODO : error handling
			$response = json_encode(array('response_code' => 400, 'error_code' => 'FILE_NOT_FOUND'));
		}		
		
		$ajax_response->addScriptCall('rbinstaller.upgrade.response',$response);
		$ajax_response->sendResponse();
	}
	
	public function update_account()
	{
		$product_tag = $this->input->get('product_tag');
		// set $product_tag
		RBInstallerHelperItem::productTag($product_tag);
		
		$action = $this->input->get('action', '');
		$this->getView()->assign('action', $action);
		
		if($action == 'verify'){	
			$credential = $this->_getArgs();
			$credential['password'] = isset($credential['password']) ? urldecode($credential['password']) : ''; // encoded by javascript 
			
			$verified = false;
			if(isset($credential['email']) && isset($credential['password'])
				&& !empty($credential['email']) && !empty($credential['password'])){		
				// verify the credential from app server
				$response = $this->_helper->verify_crendetial($credential['email'], $credential['password']);
				if($response && !(is_array($response) && isset($response['error']) && $response['error']== true)){
					$verified = true;
				}
			}
			
			$this->getView()->assign('credential_verified', $verified);					
		}
		else{
			$credential['email'] = $this->_helper->get('email');
		}		
		
		$this->getView()->assign('credential', $credential);
		return true;
	}

	public function show_login_popup()
	{
		$credential['email'] 		= $this->_helper->get('email');
		$this->getView()->assign('credential', $credential);
		return true;
	}
}
