<?php

/**
* @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		RBInstaller
* @subpackage	Front-end
* @contact		support@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/** 
 * Item Helper
 * @author Gaurav Jain
 */
jimport('joomla.http.transport');
class RBInstallerHelperItem extends Rb_Helper
{	
	public $json = array();
	public $spectrum_color = array('#800080', '#FF00FF', '#25383C', '#98AFC7', '#0041C2', '#00FFFF', '#FFDB58', 
									'#FFCBA4', '#FFCBA4', '#E77471', '#C48189', '#F6358A', '#F6358A', '#E238EC', '#EBDDE2');
	
	public function set($data)
	{
		// $data should be like array('key' => 'any value');
		$model = RBInstallerFactory::getInstance('config', 'model');
		return $model->save($data);
	}
	
	public function get($what = null)
	{
		static $config = null;
		if($config === null){			
			$model 		= RBInstallerFactory::getInstance('config', 'model');
			$records 	= $model->loadRecords(array(), array(), false, 'key');
			
			$config = new stdClass();
			foreach($records as $record){
				$config->{$record->key} = $record->value;
			}
		}

		if($what == null){
			return $config;
		} 
		
		if(!isset($config->$what)){
			return '';
		}
		
		return $config->$what;
	}
	
	protected function get_server_configuration($what)
	{
		static $content = null;
		
		$tag = self::productTag();
		
		if($content === null){
			$file_url  = 'http://pub.readybytes.net/rbinstaller/configuration/live.json';
			$link 		= new JURI($file_url);	
			$curl 		= new JHttpTransportCurl(new Rb_Registry());
			$response 	= $curl->request('GET', $link);
			
			if($response->code != 200){
				throw new Exception('File URL is not correct or not accessible.');
			}
								
			$content   =  json_decode($response->body, true);		
		}
		
		if(!isset($content[$tag]) || !isset($content[$tag][$what])){
			throw new Exception('Requested data not found in the configuration file or configuration file is corrupted.');
		}
		
		return $content[$tag][$what];
	}
	
	protected function _copyFileFromServer()
	{
		// XITODO : apply some calculation for not to ask on each request
		// XITODO : proper error handling for curl response
		
		$tag = self::productTag();
		$local_fetch_time 	= $this->get('data_fetch_time');
		$now = new Rb_Date();
		if(!empty($local_fetch_time)){
			$server_cache_time = $this->get_server_configuration('app_cache_time');
			if(($now->toUnix() - $local_fetch_time) < $server_cache_time && file_exists(RBINSTALLER_PATH_CORE_MEDIA_DATA.'/'.$tag.'.json')){
				$content = file_get_contents(RBINSTALLER_PATH_CORE_MEDIA_DATA.'/'.$tag.'.json');
				return json_decode($content, true);
			}
		}
		
		$url 	 	= $this->get_server_configuration('app_content_url');
		$link 		= new JURI($url);
		$curl 		= new JHttpTransportCurl(new Rb_Registry());	
		$response 	= $curl->request('GET', $link);
		if($response->code != 200){
			 throw new Exception('File URL is not correct or not accessible.');		
		}
		
		$content = $response->body;
		JFile::write(RBINSTALLER_PATH_CORE_MEDIA_DATA.'/'.$tag.'.json', $content); 
		$this->set(array('data_fetch_time' => $now->toUnix()));
		
		return json_decode($content, true);
	}
	
	public function get_items($product_tag, $added_items, $user = null)
	{
		$this->json = $this->_copyFileFromServer();

		$installed_extenstions = $this->get_extensions();
		
		//component vewrsion
		$component_version = $this->get_version($product_tag, $installed_extenstions);
		
		// XITODO : joomla version
		
		$component_version = explode(".", $component_version);
		$component_version = array_slice($component_version, 0, 3); //VVV IMP : consider only first three part of a version
		$criterias 		   = $this->get_criteria($product_tag);
			
		$compatibility_object = $this->get_compatibility_object($product_tag);
		$product_token = '{product}';
		if($compatibility_object && method_exists($compatibility_object, 'get_token')){
			$product_token = $compatibility_object->get_token();
		}
		
		$purchased_item = array();
		if(isset($user['buyer_id'])){
			$purchased_item = $this->get_purchased_item($user['buyer_id']);
		}
			
		$product_tag_id = $this->json['tags'][$product_tag]['id'];
		
		$items = array();
		if(!isset($this->json['tag_items'][$product_tag_id])){
			return array();
		}
		
		foreach ($this->json['tag_items'][$product_tag_id] as $item_id){
			$item = $this->json['items'][$item_id];
			
			if($item['published'] == false){
				continue;
			}
			
			// find if this item is alread installed
			$entension_name = $item['type'].'_'.$item['folder'].'_'.$item['element'].'_'.$item['client_id'];
			
			if(isset($installed_extenstions[$entension_name])){
				$manifest_cache = json_decode($installed_extenstions[$entension_name]->manifest_cache, true);
				$item['installed_version'] = $manifest_cache['version'];
			}
			else{
				$item['installed_version'] = 0;
			}

			// Calculate last modified date
            $item['last_modified_date'] = $item['modified_date'];

			// find if this item is compatible with component name or not			
			if(isset($item['version'])){
				$item['compatible_file'] = $this->get_compatible_file($component_version, $item['version'], $criterias);

                // get last date
                $tmp_date = new JDate($item['last_modified_date']);
                $latest = $tmp_date->getTimestamp();
                foreach($item['version'] as $ver){
                    $new = new JDate($ver['modified_date']);
                    if($latest < $new->getTimestamp()){
                        $latest = $new->getTimestamp();
                        $item['last_modified_date']=$new->toSql();
                    }
                }
			}
						
			// set status of item, Like : active or expired or none
			$item['subscription_status'] = 'none';		
			if(isset($purchased_item[$item['item_id']])){
				$item['subscription_status'] = $purchased_item[$item['item_id']]; 
			}
			
			$item['status'] = $this->_get_item_status($item, $added_items);
			$item['previous_status'] 	= $item['status'];
			
			$item_reference = json_decode($item['item_reference'], true);
			
			if(isset($item_reference['documentation_link'])){
				$item_reference['documentation_link'] = str_replace('{product}', $product_token, $item_reference['documentation_link']);
			}
			
			if(isset($item_reference['support_link'])){
				$item_reference['support_link'] = str_replace('{product}', $product_token, $item_reference['support_link']);
			}
			
			$item['item_reference'] = json_encode($item_reference);
			
			$items[$item_id] = $item;
		}
		
		$this->json['items'] = $items;
		
		$this->json['color_codes'] = $this->get_compatibility_theme($this->json['compatibility']);
		$this->json['compatibilities'] = $this->get_arranged_compatibilities($this->json['compatibility']);
		return $this->json;
	}
	
	public function get_compatibility_theme($compatibilities)
	{
		$colors = array();
		foreach ($compatibilities as $prefix => $data){
			$obj = $this->get_compatibility_object($prefix);
			
			if($obj && method_exists($obj, 'get_color_code')){
				$colors[$prefix] = $obj->get_color_code();
			}
			else{
				$index = rand(0, count($this->spectrum_color)-1);
				$colors[$prefix] = $this->spectrum_color[$index];
				unset($this->spectrum_color[$index]);
				$this->spectrum_color = array_values($this->spectrum_color);
			}
		}
		return $colors;
	}
	
	public function _get_item_status($item, $cart_items)
	{
		if((bool)$item['pre_installed']){
			return 'active_installed';
		}
		
		// case : Not Available
		if(!isset($item['compatible_file']) || !$item['compatible_file']){
			return 'not_available';
		}	
		
		if(is_array($cart_items) && in_array($item['item_id'], $cart_items)){
			return 'none_addedtocart';
		}
				
		// if item is not installed
		if($item['installed_version']){
			
			// if item is upgradable
			if($this->compare_version($item['compatible_file']['version_number'], $item['installed_version']) > 0){
				if(floatval($item['price']) == floatval('0.00')){
					return 'active_upgradable';
				}
				
				// case :  
				if($item['subscription_status'] == 'none'){					
					return 'none_upgradable';
				}
				
				// case :  
				if($item['subscription_status'] == 'active'){
					return 'active_upgradable';
				}
				
				// case :  
				if($item['subscription_status'] == 'expired'){
					return 'expired_upgradable';
				}		
			}
			
			// if item is not upgradable
			if(floatval($item['price']) == floatval('0.00')){
				return 'active_installed';
			}
			
			// case :  
			if($item['subscription_status'] == 'none'){
				return 'none_installed';				
			}
			
			// case :  
			if($item['subscription_status'] == 'active'){
				return 'active_installed';
			}
			
			// case :  
			if($item['subscription_status'] == 'expired'){
				return 'expired_installed';
			}			
		}
		
		if(!$item['installed_version']){	
			// if item is not upgradable
			if(floatval($item['price']) == floatval('0.00')){
				return 'free_install';
			}
				
			// case :  
			if($item['subscription_status'] == 'none'){
				return 'none_buynow';				
			}
			
			// case :  
			if($item['subscription_status'] == 'active'){
				return 'active_install';
			}
			
			// case :
			if($item['subscription_status'] == 'expired'){
				return 'expired_install';
			}		
		}
	}
	
	public function create_invoice($data)
	{
		$url 		 = $this->get_server_configuration('app_server_url').'';
		$url 		.= '&resource=invoice';
		$link 		 = new JURI($url);
		$response    = $this->sendCurlRequest($link,$data);
        $response	 = json_decode($response->body, true);

		if($response['response_code'] != 201){
			$msg = "There are some errors in creating the invoice. Please report this issue to service provider.";
			if(isset($response['response_data']) && isset($response['response_data']['reason'])){
				$msg = $response['response_data']['reason'];
			}
			throw new Exception($msg);
		}
		
		return $response['response_data'];
	}
	
	public function get_invoices($userid)
	{
		static $invoices = null;
		if($invoices === null){
			$url 		 = $this->get_server_configuration('app_server_url').'';
			$url 		.= '&resource=invoice&filter=buyer&id='.$userid;
			
			$data = array();
			$data['paymart']['email'] 		= $this->get('email');
			$data['paymart']['password'] 	= $this->get('password');

			$link 		  = new JURI($url);
			$response     = $this->sendCurlRequest($link,$data);
			$response	  = json_decode($response->body, true);
			
			if($response['response_code'] != 200){				
				    $invoices = array();
			}else{
				$invoices = $response['response_data'];
			}
		}
		
		return $invoices;
	}
	
	public function get_purchased_item($userid)
	{
		// XITODO : Domain name checking
		$purchased_items = array();		
		
		try{
			$invoices = $this->get_invoices($userid);			
		}
		catch(Exception $e){			
			return $purchased_items;
		}
		
		$now = new Rb_Date();
		$now = $now->toUnix();
		
		foreach($invoices as $invoice_id => $invoice){
			// if invoice is paid
			if($invoice['status'] != 402){
				continue;
			}
			
			$paid_date  = $invoice['paid_date'];
			$paid_date = new Rb_Date($paid_date);				
			
			if(!isset($invoice['invoiceitems']) || !is_array($invoice['invoiceitems'])){
				continue;
			}
			
			$items = $invoice['invoiceitems'];
			
			foreach($items as $item){
				// if already active, then need not to do following
				if(isset($purchased_items[$item['item_id']]) && $purchased_items[$item['item_id']] == 'active'){
					continue;
				}
				
				$params 	= json_decode($item['params'], true);
				$item_data 	= $params['item'];
				$time 		= $item_data['time'];
				// for life time support
				if($time == '000000000000'){
					$purchased_items[$item_data['item_id']] = 'active';
					continue;
				}
				
				$date 	  = date_parse($paid_date->toString());
				$exp_time = $this->_addExpiration($date, $time);

				if($now <= $exp_time){
					$purchased_items[$item_data['item_id']] = 'active';
				}
				else{
					$purchased_items[$item_data['item_id']] = 'expired';
				}
			}
		}
		
		return $purchased_items;
	}
	
	protected function _addExpiration($date, $expirationTime)
  	{   
    	$timerElements = array('year', 'month', 'day', 'hour', 'minute', 'second');
    	   

    	$count = count($timerElements);
    	if($expirationTime != 0){
      		for($i=0; $i<$count ; $i++){
				$date[$timerElements[$i]] +=   intval(JString::substr($expirationTime, $i*2, 2), 10);
      		}
    	}
    	// XITODO : Remove this code if timezone support is given in rbframework
    	$tz = date_default_timezone_get();
    	date_default_timezone_set('UTC');
    	
		$time = mktime($date['hour'], $date['minute'], $date['second'], $date['month'], $date['day'], $date['year']);

	   	date_default_timezone_set($tz);
    	return $time;    	
  	}
  	
	public function get_version_file($item_id, $version_id, $product_tag)
	{	
		$url 	=	 $this->get_server_configuration('app_server_url').'';
		$url   .=	'&resource=item&filter=file&id='.$item_id;
		
		if(!empty($product_tag)){
			$url .='&product_tag='.$product_tag;
		}
		
		$data = array();
		$data['paymart']['email'] 			= trim($this->get('email'));
		$data['paymart']['password'] 		= $this->get('password');
		$data['paymart']['itemversion_id'] 	= $version_id;

		$link 		= new JURI($url);
		$response = $this->sendCurlRequest($link,$data);

		$content_type = $response->headers['Content-Type'];
		
		if ($content_type != 'application/zip'){
			$response	= 	json_decode($response->body, true);
			
			if($response['response_code'] != 200){
				$msg = "Unable to find the requested file. Please report this issue to service provider.";
				if(isset($response['response_data']) && isset($response['response_data']['reason'])){
					$msg = $response['response_data']['reason'];
				}
				throw new Exception($msg);
			}
		}
		else {
			$response =  $response->body;
		}
		
		return $response;
	}
	
	public function get_user($email)
	{
		$url 		 = $this->get_server_configuration('app_server_url').'';
		$url 		.= '&resource=buyer&email='.base64_encode(trim($email));
		
		$link 		= new JURI($url);		
		$curl 		= new JHttpTransportCurl(new Rb_Registry());
		$response 	= $curl->request('GET', $link);		
		$response	= json_decode($response->body, true);
		
		if($response['response_code'] != 200){
			$msg = "User Not Found";
			if(isset($response['response_data']) && !empty($response['response_data']) && isset($response['response_data']['reason'])){
				$msg = $response['response_data']['reason'];
			}
			
			throw new Exception($msg);
		}
		
		return $response['response_data'];
	}
	
	public function get_pay_url($invoice_id)
	{
		$url 		 = $this->get_server_configuration('app_server_url').'';
		$url 		.= '&resource=invoice&filter=payurl&id='.$invoice_id;

		$data = array();
		$data['paymart']['email'] 		= trim($this->get('email'));
		$data['paymart']['password'] 	= $this->get('password');

		$link 		= new JURI($url);
		$response = $this->sendCurlRequest($link,$data);
		$response	= json_decode($response->body, true);
		
		if($response['response_code'] != 200){
			$msg = "Invalid purchase url. Please report this issue to service provider.";
			if(isset($response['response_data']) && isset($response['response_data']['reason'])){
				$msg = $response['response_data']['reason'];
			}
			
			throw new Exception($msg);
		}
		
		return $response['response_data'];
	}
	
	public function compare_version($version1, $version2)
	{
		// equal 	= 0
		// smaller 	= -1
		// greater 	= 1
		$flag = 0;
				
		if(!is_array($version1)){
			$version1 = explode(".", $version1);
		}
		
		if(!is_array($version2)){
			$version2 = explode(".", $version2);
		}
		
		$version1 = array_slice($version1, 0, 3);
		$version2 = array_slice($version2, 0, 3);
		
		foreach($version1 as $key => $value){
			$version2[$key] = isset($version2[$key]) ? $version2[$key] : 'STAR';
			if(is_numeric($version2[$key])){
				// check for lower
				if($flag !== 1 && intval($value) < intval($version2[$key])){
					$flag = -1;
					break;
				}
				
				if($flag !== 1 && intval($value) === intval($version2[$key])){
					$flag = 0;
				}
			
				if(intval($value) > intval($version2[$key])){
					$flag = 1;
				}
			}
			else{
				// IMP : we are asuming 1.0 = 1.*, so return 0 
				if($version2[$key] === 'STAR'){
					if($flag === 0){
						$flag = 0;
					}
					else{
						$flag = -1;
					}
				}
				elseif($version2[$key] === 'PLUS'){
					$flag = 1;
				}
					
			}
		}
		
		return $flag;
	} 
	
 	public function get_compatible_file($component_version, $files, $criterias)
    {
    	// arrange file according to version
        $arranged_files = array();
        foreach($files as $file){
        	$arranged_files[$file['itemversion_id']] = $file['version_number'];
		}
           
        $return_file_id 	 = 0;
        $return_file_version = '0.0.0';
        $found = false;
        foreach($arranged_files as $file_id => $file_version){
        	foreach($criterias as $column => $version){
				// if item has the compatibility with this criteria value
        		if(!isset($files[$file_id][$column]) || empty($files[$file_id][$column])){
        			continue;
        		}
        
				// compare the component version with criteria value
				// if current file version is greater than previous found file version, then set values
        		if($this->compare_version($component_version, $version) >= 0 && $this->compare_version($file_version, $return_file_version) > 0){        			      
        			$return_file_version = $file_version;
        			$return_file_id 	 = $file_id;
        			$found = true;
        			break;
        		}
        	}
        }
           
        if($found){
        	return $files[$return_file_id];
        }
        
        return false;
     }
     
	
	public function get_criteria($component)
	{
		if(empty($this->json)){
			$this->json = $this->get_items($component);
		}
		
		if(!isset($this->json['compatibility']) || !isset($this->json['compatibility'][$component])){
			return array();
		}
		
		$component_compatibility = $this->json['compatibility'][$component];

		$columns 	= json_decode($component_compatibility['value'], true);
		$criterias 	= array();
		foreach ($columns as $key => $value){
			$criteria = str_replace($component, '', $key);
			$criteria = explode("_", $criteria);
			$criterias[$key] = $criteria;
		}
		
		return $criterias;
	}	
	
	public function get_extensions()
	{
		static $extensions = null;
		if($extensions === null){
			$sql = "SELECT concat( `type` , '_', `folder` , '_', `element` , '_', `client_id` ) as `extension`, `manifest_cache`
					FROM `#__extensions`";
			
			$db = Rb_Factory::getDbo();
			$db->setQuery($sql);
			$extensions = $db->loadObjectList('extension');
		}
		
		return $extensions;
	}
	
	public function get_config()
	{
		$params = array('email');
		$config = array();
		foreach($params as $param){
			$config[$param] = $this->get($param);
		}
		
		// add domain name also
		$config['current_domain'] = JUri::root();
		$config['tracking_url']   = $this->get_server_configuration('tracking_url', ''); 
		return $config;
	}
	
	public function install($file, $item_id, $version_id)
	{
		$random			 = rand(1000, 999999);
		$tmp_file_name 	 = JPATH_ROOT.'/tmp/'.$random.'item_'.$item_id.'_'.$version_id.'.zip';
		$tmp_folder_name = JPATH_ROOT.'/tmp/'.$random.'item_'.$item_id.'_'.$version_id;
		
		// create a file
		JFile::write($tmp_file_name, $file);	
		
		jimport('joomla.filesystem.archive');
		jimport( 'joomla.installer.installer' );
		jimport('joomla.installer.helper');
		
		JArchive::extract($tmp_file_name, $tmp_folder_name);
		$installer = JInstaller::getInstance();	

		if($installer->install($tmp_folder_name)){
				$response = json_encode(array('response_code' => 200, 'reason' => 'INSTALLATION_SUCCESS'));
		}
		else{
			$response = json_encode(array('response_code' => 400, 'reason' => 'There are some errors in installing the item'));
		}
		
		if (JFolder::exists($tmp_folder_name)){
			JFolder::delete($tmp_folder_name);
		}
		
		if (JFile::exists($tmp_file_name)){
			JFile::delete($tmp_file_name);
		}
		
		return $response;
	}

	public function verify_crendetial($email, $password)
	{		
		$data['paymart']['email'] 		= $email;
		$data['paymart']['password'] 	= $password;
		
		$url 		 = $this->get_server_configuration('app_server_url').'';
		$url 		.= '&resource=buyer&filter=verify';

		$response    = $this->sendCurlRequest($url,$data);
		$response	 = json_decode($response->body, true);

		return $response['response_data'];
	}
	
	public function register($email, $password)
	{
		$data['paymart']['email'] 		= $email;
		$data['paymart']['password'] 	= $password;
		
		$url 		 = $this->get_server_configuration('app_server_url').'';
		$url 		.= '&resource=buyer';
		$link 		 = new JURI($url);
		$response    = $this->sendCurlRequest($link,$data);
		$response	 = json_decode($response->body, true);
				
		return $response['response_data'];
	}
	
	public function get_version($tag, $installed_extenstions)
	{
		$obj = $this->get_compatibility_object($tag);
		if(!$obj){
			return '0.0.0';
		}		
		
		return $obj->get_version($installed_extenstions);
	}
	
	public function get_compatibility_object($tag)
	{
		$classname = 'RBInstallerCompatibility'.$tag;
		if(!class_exists($classname, true)){
			return false;
		}
		
		return new $classname();
	}
	
	public function get_arranged_compatibilities($compatibilities)
	{	
		$compatibilities = $this->json['compatibility'];
		$criterias 	= array();
		
		foreach ($compatibilities as $compatibility){
			$columns 	= json_decode($compatibility['value'], true);			
			foreach ($columns as $key => $value){				
				$criterias[$key] = array('prefix' => $compatibility['prefix'], 'value' => $value, 'name' => $compatibility['name']);
			}
		}
		
		return $criterias;
	}

	public function get_update_configuration($what)
	{
		static $content = null;
		
		if($content === null){
			$file_url  = 'http://pub.readybytes.net/rbinstaller/update/live.json';
			$link 		= new JURI($file_url);	
			$curl 		= new JHttpTransportCurl(new Rb_Registry());
			$response 	= $curl->request('GET', $link);
			
			if($response->code != 200){
				throw new Exception('File URL is not correct or not accessible.');
			}
								
			$content   =  json_decode($response->body, true);		
		}
		
		return $content[$what];
	}
	
	public function is_component_upgradable()
	{
		$installed_extenstions = $this->get_extensions();
		$ext = 'component__com_rbinstaller_1';
		if(!isset($installed_extenstions[$ext])){
			return true;
		}
		
		$manifest_cache 	= json_decode($installed_extenstions[$ext]->manifest_cache);
		$current_version	=  $manifest_cache->version;
		
		$config = $this->get_update_configuration('rbinstaller');
		$new_version = $config['version'];
		if(version_compare($new_version, $current_version) > 0){
			return true;
		}
		
		return false;
	}
	
	public function get_component_file()
	{
		$config = $this->get_update_configuration('rbinstaller');
		
		$link 		=	new JUri($config['file_path']);
		$curl		= 	new JHttpTransportCurl(new Rb_Registry());
		$response 	=	$curl->request('GET', $link);
		
		$content_type = $response->headers['Content-Type'];
		
		if ($content_type != 'application/zip'){
			$msg = "Unable to find the requested file. Please report this issue to service provider.";
			throw new Exception($msg);
		}
		else {
			$response =  $response->body;
		}
		
		return $response;
	}
	
	public function changeStatus($itemid, $status = 1)
	{
		$items = $this->_copyFileFromServer();
		if(!isset($items['items'][$itemid])){
			return false;
		}
				
		$item = $items['items'][$itemid];
		
		$db		= Rb_Factory::getDBO();
		$query	= 'UPDATE '. $db->quoteName( '#__extensions' )
				. ' SET   '. $db->quoteName('enabled').'='.$db->Quote($status)
				. ' WHERE '. $db->quoteName('element').'='.$db->Quote($item['element'])
				. ' AND ' . $db->quoteName('folder').'='.$db->Quote($item['folder'])
				. ' AND ' . $db->quoteName('client_id').'='.$db->Quote($item['client_id']) 
				. ' AND ' . $db->quoteName('type').'='.$db->Quote($item['type']);
		
		$db->setQuery($query);
		return $db->query();
	}
	
	public static function productTag($tag = null)
	{
		static $productTag = null;
		
		// if tag is empty, it means return the assigned tag
		if($tag === null){
			return $productTag;
		}
		
		// else assign the input tag to productTag
		$productTag = $tag;
	}

	public function sendCurlRequest($url,$data) {
		$ch 		 = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);

		// Turn off the server and peer verification (TrustManager Concept).
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch,CURLOPT_HEADER,true);

		//Handling for php.ini args separater issue. This will separate all
		//request parameters(query string) from & irrespective of the setting in php.ini
		$field_string = http_build_query($data,null, "&");

		// Set the request as a POST FIELD for curl.
		curl_setopt($ch, CURLOPT_POSTFIELDS, ($field_string));

		$httpResponse = curl_exec($ch);

		// Get the request information.
		$info = curl_getinfo($ch);

		// Close the connection.
		curl_close($ch);

		return $this->getCurlResponse($httpResponse, $info);

		//return $httpResponse;

	}

	protected function getCurlResponse($content, $info)
	{
		// Create the response object.
		$return = new JHttpResponse;

		// Get the number of redirects that occurred.
		$redirects = isset($info['redirect_count']) ? $info['redirect_count'] : 0;

		/*
		 * Split the response into headers and body. If cURL encountered redirects, the headers for the redirected requests will
		 * also be included. So we split the response into header + body + the number of redirects and only use the last two
		 * sections which should be the last set of headers and the actual body.
		 */
		$response = explode("\r\n\r\n", $content, 2 + $redirects);

		// Set the body for the response.
		$return->body = array_pop($response);

		// Get the last set of response headers as an array.
		$headers = explode("\r\n", array_pop($response));

		// Get the response code from the first offset of the response headers.
		preg_match('/[0-9]{3}/', array_shift($headers), $matches);

		$code = count($matches) ? $matches[0] : null;

		if (is_numeric($code))
		{
			$return->code = (int) $code;
		}

		// No valid response code was detected.
		else
		{
			throw new UnexpectedValueException('No HTTP response code found.');
		}

		// Add the response headers to the response object.
		foreach ($headers as $header)
		{
			$pos = strpos($header, ':');
			$return->headers[trim(substr($header, 0, $pos))] = trim(substr($header, ($pos + 1)));
		}

		return $return;
	}
}