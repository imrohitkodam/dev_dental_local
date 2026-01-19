<?php
/**
* @copyright	Copyright (C) 2009 - 2013 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package		PayPlans
* @subpackage	PayPlans-Installer
* @contact 		payplans@readybytes.in
*/

// No direct access.
defined('_JEXEC') or die;
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
jimport('joomla.application.component.controller');

/**
 * PayPlans Installer Controller
 *
 * @package		Joomla.Administrator
 */

if(!class_exists('PpinstallerControllerAdapt')) {
	if(interface_exists('JController')) {
		abstract class PpinstallerControllerAdapt extends JControllerLegacy {}
	} else {
		class PpinstallerControllerAdapt extends JController {}
	}
}

class PpinstallerController extends PpinstallerControllerAdapt
{
	//The (0) Header Display
	public function display($cachable = false, $urlparams = false)
	{
		if(!$this->netConnection()) {
			$view  = $this->getView('default','html');
			$error = JText::_('COM_PPINSTALLER_INTERNET_CONNECTION_NOT_AVAILABLE');
			$view->assign('error',$error);
			$this->_display();
			exit();
		}
		
		// Clear previous data if exist in session
		PpinstallerHelperPatch::clear_session();
		PpinstallerHelperMigrate::clear_session();
		$result = $this->setupConfiguration();
		
		//nessaery if there is any permissions issue
		if(empty($result)) {
			$view  = $this->getView('default','html');
			$error = JText::_('COM_PPINSTALLER_CONFIGURATION_NOT_SAVING');
			$view->assign('error',$error);
		}
		
		$this->_display();
		
		//exit for nothing to load any further template thing
		exit();
	}
	
	//fetch the lts Releases from public json
	function setupConfiguration()
	{
		$content  = PpinstallerHelperUtils::getFileContents(PPINSTALLER_RELEASE_FILE_URL);
		$content  = json_decode($content);

		if ( empty( $content ) ) {
			throw new JException(JText::_('COM_PPINSTALLER_UNABLE_TO_GET_RELEASE_FILE'),500,E_ALL);
		} 

		$config = PpinstallerHelperUtils::getConfig();
		return $config->save($content);
	}
	
	public function _display($layout = 'default', $fun ='display', $args = array())
	{
		$input = JFactory::getApplication()->input;
		$vName = $input->get('view', 'default');
		$vType = $input->get('format', 'html');
		
		try {
			$view  = $this->getView($vName,$vType);
		}
		catch (Exception $e) {
			$view  = $this->getView('default',$vType);
		}
		$view->setLayout($layout);
		
		$args = (is_array($args))? $args : array($args);
		call_user_func_array(array($view,$fun),$args);
	}
	
	public function helpMeOutSendMail()
	{
		$input 			= JFactory::getApplication()->input;
		$sendFrom 		= $input->get('email',false,'string');
		$sendFrom 		= (empty($sendFrom))? JFactory::getUser()->email : $sendFrom;
		
		$body 			= $input->get('body','','string');
		$mail 			= JFactory::getMailer();
		$user 			= JFactory::getUser();
		$siteName 		= JFactory::getConfig()->get('sitename','testsite');
		$ajaxResponse 	= PpinstallerAjaxResponse::getInstance();
		
		if($mail->sendMail($sendFrom,$siteName, 'support+payplans@readybytes.in', JText::_('COM_PPINSTALLER_SEND_MAIL_SUBJECT'), $body) === true) {
			
			echo true;
			$ajaxResponse->sendResponse();
		
		}
		echo false;
		$ajaxResponse->sendResponse();
		
	}
	
	//this function is Verify availablity of Net connection 
	//for that, user can't access anything without net connection
	/*
	*	there is @ because fsocket open return warning of system error 
	*   this is also issue in OS 
	*/
	function netConnection()
	{
		@$connected = fsockopen("www.readybytes.net", 80);
		if ($connected){
			$is_conn = true; //action when connected
			fclose($connected);
		}else{
			$is_conn = false; //action in connection failure
		}
		return $is_conn;
	}

	public function instruction()
	{
		$this->_display('default','instruction');
		exit();
	}
	
	/**
	 * if installation is successful then sends 200 code otherwise send what's the error.
	 * installer is supposed to be only one for one joomla version nothing else.
	 * return json encoded ajax response
	 */
	public function installerUpgrade()
	{

		$ajaxResponse 	= PpinstallerAjaxResponse::getInstance();
	
 		try {

 			$filename = 'com_ppinstaller';
 			
 			$jVersion = new JVersion();
			$jVersion = 'j'.$jVersion->RELEASE;
 			
 			$config 	= PpinstallerHelperUtils::getConfig();
			$newVersion	= $config->ppinstallerVersion;

 			$installationPath = PPINSTALLER_TMP_PATH.DS.$filename;
 			
 			$kitUrl = PPINSTALLER_KITS_URL.DS.'com_ppinstaller-'.$newVersion.'.zip';

 			$res 		= new JRegistry();
 			$curl 		= new JHttpTransportCurl($res);
 			$uri  		= new JUri($kitUrl);
 			$response 	= $curl->request('get', $uri);
 						
 			if( !empty($response->body) && strtolower($response->headers['Content-Type']) === 'application/zip') {
 			
 				$tmp_file_name = $installationPath.'.zip';
 				
 				chmod(PPINSTALLER_TMP_PATH, 0755);

 				JFile::delete($tmp_file_name);

 				$res1 = JFile::write($tmp_file_name, $response->body);

 				$res2 = PpinstallerHelperInstall::extract($installationPath.'.zip', $installationPath);
 			}
 			
 			if (!$res1 || !$res2) {			
				throw new Exception('Error while upgrading Payplans-Installer', 500);
			}
	 			
	 		$installer 		= new JInstaller();
	 		
	 		//if there is any error in installation it should be handled otherwise it send 500 erorr
 			if ($installer->install($installationPath))
 			{
 				PpinstallerHelperLogger::log(JText::_('COM_PPINSTALLER_PAYPLANS_INSTALLATION_TRUE'));
 			}
 			else 
 			{
 				throw new Exception('Error while upgrading Payplans-Installer', 500);
 			}
 		}
 		catch (Exception $e) {
 			PpinstallerHelperLogger::log(JText::_('COM_PPINSTALLER_PAYPLANS_INSTALLATION_FALSE'));
 			$response = json_encode(array('response_code' => $e->getCode(), 'error_code' => $e->getMessage()));
 		}

 		//delete folder from tmp after installation
 		JFolder::delete($installationPath);
 		JFile::delete($installationPath.'.zip');

		$response = json_encode(array('response_code' => $response->code, 'error_code' => $response->code));
	
		$ajaxResponse->addScriptCall('ppInstaller.upgrade.response',$response);
		$ajaxResponse->sendResponse();
	}
}
