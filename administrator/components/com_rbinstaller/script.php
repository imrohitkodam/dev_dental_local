<?php
/**
* @copyright		Copyright (C) 2009 - 2013 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license			GNU/GPL, see LICENSE.php
* @package			Paymart
* @subpackage		Backend
*/
if(defined('_JEXEC')===false) die();

class Com_rbinstallerInstallerScript
{
	public function preflight($type, $parent)
	{
		if ($type != 'install' && $type != 'update'){
			return true;
		}

		$message = JText::_('ERROR_RB_NOT_FOUND : RB-Framework not found. Refer <a href="http://www.readybytes.net/support/forum/knowledge-base/201257-error-codes.html" target="_blank">Error Codes </a> to resolve this issue.');

		// get content for rbframework version
    	$file_url   = 'http://pub.readybytes.net/rbinstaller/update/live.json';
		$link 		= new JURI($file_url);	
		$curl 		= new JHttpTransportCurl(new JRegistry());
		$response 	= $curl->request('GET', $link);
			
		if($response->code != 200){
			JFactory::getApplication()->enqueueMessage($message, 'error');
			return false;
		}
								
		$content   =  json_decode($response->body, true);
		if(!isset($content['rbframework']) || !isset($content['rbframework']['file_path'])){
			JFactory::getApplication()->enqueueMessage($message, 'error');
			return false;
		}
			
		// check if already exists
     	$db	= JFactory::getDbo();
     	$query	= $db->getQuery(true);
     
     	$query->select('*')
	     	  ->from($db->quoteName('#__extensions'))
	     	  ->where('`type` = '.$db->quote('plugin'))
	     	  ->where('`folder` = '.$db->quote('system'))
	     	  ->where('`client_id` = 0')
	     	  ->where('`element` = '.$db->quote('rbsl'));
	     	  
    	$db->setQuery($query);
		$result = $db->loadObject();
		
		//when rbframework is not already installed
		if (!$result) {
			$this->installRBFramework($content['rbframework']);
			$this->changeExtensionState(1);
			return true;
		}
		
		$query	= $db->getQuery(true);
     	$query->select('*')
	     	  ->from($db->quoteName('#__extensions'))
	     	  ->where('`type` = '.$db->quote('component'). ' AND  `element` LIKE '.$db->quote('com_payinvoice'). ' OR `element` LIKE '.$db->quote('com_jxiforms'));
	     	  
     	$db->setQuery($query);
		$installed_extensions = $db->loadObjectList();
		
		//when no dependent extension is installed, install framework
		if (!$installed_extensions){
			$this->installRBFramework($content['rbframework']);
			$this->changeExtensionState(1);
			return true;
		}
		else {
			$params = json_decode($result->manifest_cache, true);
			
			$latest_rb_version 		=  explode('.', $content['rbframework']['version']);
			$installed_rb_version 	=  explode('.', $params['version']);
			
			//if there is no change in the major version of rbframework then install else show message
			if(version_compare($installed_rb_version[0].'.'.$installed_rb_version[1], $latest_rb_version[0].'.'.$latest_rb_version[1]) == 0){
				$this->installRBFramework($content['rbframework']);
				if(!$result->enabled){
					$this->changeExtensionState(1);
				}
				return true;
			}

			$message = JText::_('ERROR_RB_MAJOR_VERSION_CHANGE : Major version change in the RB-Framework. Refer <a href="http://www.readybytes.net/support/forum/knowledge-base/201257-error-codes.html" target="_blank">Error Codes </a> to resolve this issue.');
			JFactory::getApplication()->enqueueMessage($message, 'error');
			return false;
		}
		
		return true;
	}
    
	function changeExtensionState($state = 1)
	{
		$db	= JFactory::getDbo();
	     	$query	= $db->getQuery(true);
		$query->update($db->quoteName('#__extensions'))
			  ->set('`enabled` = '.$state)
     		  ->where('`type` = '.$db->quote('plugin'))
     		  ->where('`folder` = '.$db->quote('system'))
     		  ->where('`client_id` = 0')
     		  ->where('`element` = '.$db->quote('rbsl'));
     		$db->setQuery($query);
		return $db->query();		
	}
	
   	protected function installRBFramework($content)
 	{	
		// get file
   		$link 		=	new JUri($content['file_path']);
		$curl		= 	new JHttpTransportCurl(new JRegistry());
		$response 	=	$curl->request('GET', $link);
		
		$content_type = $response->headers['Content-Type'];
		
		if ($content_type != 'application/zip'){
			return false;
		}
		else {
			$response =  $response->body;
		}
		
		return $this->installExtension($response, 'rbframework', $content['version']);
    } 
    
	public function installExtension($file, $item_id, $version)
	{
		$random			 = rand(1000, 999999);
		$tmp_file_name 	 = JPATH_ROOT.'/tmp/'.$random.'item_'.$item_id.'_'.$version.'.zip';
		$tmp_folder_name = JPATH_ROOT.'/tmp/'.$random.'item_'.$item_id.'_'.$version;
		
		// create a file
		JFile::write($tmp_file_name, $file);	
		
		jimport('joomla.filesystem.archive');
		jimport( 'joomla.installer.installer' );
		jimport('joomla.installer.helper');
		
		JArchive::extract($tmp_file_name, $tmp_folder_name);
		$installer = new JInstaller;

		if($installer->install($tmp_folder_name)){
				$response = true;
		}
		else{
			$response = false;
		}
		
		if (JFolder::exists($tmp_folder_name)){
			JFolder::delete($tmp_folder_name);
		}
		
		if (JFile::exists($tmp_file_name)){
			JFile::delete($tmp_file_name);
		}
		
		return $response;
	}
}
