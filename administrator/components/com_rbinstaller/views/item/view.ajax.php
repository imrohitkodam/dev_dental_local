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
 * Item Html View
 * @author Gaurav Jain
 */
require_once dirname(__FILE__).'/view.php';
class RBInstallerAdminViewItem extends RBInstallerAdminBaseViewItem
{
	public function credential()
	{
		$ajax = Rb_Factory::getAjaxResponse();
		if($this->get('action') == 'check'){
			if($this->get('credential_verified') == false){				
				$ajax->addScriptCall('$("#rbinstaller-login-modal").html', $this->loadTemplate('set_credential'));
				$ajax->addScriptCall('rbinstaller.credential.show_popup();');
			}
		
			$ajax->sendResponse();
		}
		
		if($this->get('action') == 'verify'){
			// as credentials are verified
			// store them in database
			$credential = $this->get('credential');			
			$event_args = array('email'=>$credential['email']);
			
			if($this->get('credential_verified') == true){
				$this->_helper->set(array('email' 		=> $credential['email']));
				$this->_helper->set(array('password' 	=> $credential['password']));

				//track registration failed
				$ajax->addScriptCall('rbinstaller.tracking', 'verification_success', $event_args);
				
				$ajax->addScriptCall('$("#rbinstaller-login-modal .modal-body").html', JText::_('COM_RBINSTALLER_SET_CREDENTIAL_WINDOW_BODY_VERIFICATION_SUCCESS'));
				$ajax->addScriptCall('setTimeout(function(){rb.ui.dialog.close(); location.reload();}, 3000);');	
			}
			else{	
				//track registration failed
				$ajax->addScriptCall('rbinstaller.tracking', 'verification_failed', $event_args);
				
				$this->assign('verification_error', JText::_('COM_RBINSTALLER_CREDENTIAL_VERIFICATION_FAILED'));
				$ajax->addScriptCall('$("#rbinstaller-login-modal").html', $this->loadTemplate('set_credential'));
				$ajax->addScriptCall('$("#rbinstaller-credential-form").find("input").jqBootstrapValidation();');
			}
			
			$ajax->sendResponse();	
		}	
	}
	
	public function registration()
	{
		$ajax = Rb_Factory::getAjaxResponse();
		$action = $this->get('action');
		if($action == 'form'){			
			$ajax->addScriptCall('$("#rbinstaller-login-modal").html', $this->loadTemplate('registration_form'));			
			$ajax->addScriptCall('$("#rbinstaller-registration-form").find("input").jqBootstrapValidation();');
		}
		
		$email 		= $this->get('email', '');
		$event_args = array('email'=>$email);
		
		if($action == 'register'){
			if($this->get('registered') == true){				
				$ajax->addScriptCall('$("#rbinstaller-login-modal .modal-body").html', JText::_('COM_RBINSTALLER_REGISTRATION_WINDOW_BODY_REGISTRATION_SUCCESS'));	
				$ajax->addScriptCall('setTimeout(function(){rb.ui.dialog.close(); location.reload();}, 10000);');	
				
				//track registration success
				$ajax->addScriptCall('rbinstaller.tracking', 'register_success', $event_args);
			}
			else{	
				$this->assign('registration_error', JText::_('COM_RBINSTALLER_REGISTRATION_WINDOW_BODY_REGISTRATION_FAILED'));
				$ajax->addScriptCall('$("#rbinstaller-login-modal").html', $this->loadTemplate('registration_form'));				
				$ajax->addScriptCall('$("#rbinstaller-registration-form").find("input").jqBootstrapValidation();');
				//track registration failed
				$ajax->addScriptCall('rbinstaller.tracking', 'register_failed', $event_args);
			}
		}
		$ajax->sendResponse();
		
	}	
	
	public function update_account()
	{		
		$action = $this->get('action');
		$ajax = Rb_Factory::getAjaxResponse();
		
		if(empty($action)){
			$ajax->addScriptCall('$("#rbinstaller-login-modal").html', $this->loadTemplate('update_account'));
			$ajax->addScriptCall('rbinstaller.account.show_popup();');
		}
		else{			
			if($this->get('credential_verified') == true){
				// as credentials are verified
				// store them in database
				$credential = $this->get('credential');
				$this->_helper->set(array('email' 		=> $credential['email']));
				$this->_helper->set(array('password' 	=> $credential['password']));	
				
				$ajax->addScriptCall('$("#rbinstaller-login-modal .modal-body").html', JText::_('COM_RBINSTALLER_UPDATE_ACCOUNT_WINDOW_BODY_VERIFICATION_SUCCESS'));
				$ajax->addScriptCall('setTimeout(function(){rb.ui.dialog.close(); location.reload();}, 3000);');	
			}
			else{	
				$this->assign('verification_error', JText::_('COM_RBINSTALLER_CREDENTIAL_VERIFICATION_FAILED'));
				$ajax->addScriptCall('$("#rbinstaller-login-modal").html', $this->loadTemplate('update_account'));
				$ajax->addScriptCall('$("#rbinstaller-credential-form").find("input").jqBootstrapValidation();');
			}
		}
		
		$ajax->sendResponse();		
	}
	
	public function show_login_popup()
	{
		$ajax = Rb_Factory::getAjaxResponse();
		$ajax->addScriptCall('$("#rbinstaller-login-modal").html', $this->loadTemplate('set_credential'));
		$ajax->addScriptCall('$("#rbinstaller-credential-form").find("input").jqBootstrapValidation();');
		$ajax->sendResponse();
	}
}