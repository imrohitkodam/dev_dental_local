<?php
/**
* @copyright	Copyright (C) 2009 - 2012 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package		PayPlans
* @subpackage	PayPlans-Installer
* @contact 		payplans@readybytes.in
*/

// No direct access.
defined('_JEXEC') or die;
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class PpinstallerHelperUtils 
{
	static public function migrationOrder() 
	{
		return array (13=>14,14=>20,20=>21);
	}
	
	static function remove_dir($dir= array())
	{
		if(empty($dir)){
			$dir = JFolder::folders(PPINSTALLER_EXTENSION_PATH);
		}
		
		foreach ($dir as $path){
			JFolder::delete(PPINSTALLER_EXTENSION_PATH.DS.$path);
		}
	}
	
	
	static function get_session_value($name,$default=null,$name_space='payplans_installer') 
	{
		return JFactory::getSession()->get($name,$default,$name_space);
	}
	
	static function set_session_value($name,$default=null,$name_space='payplans_installer')
	{
		JFactory::getSession()->set($name,$default,$name_space);
	}
	
	static function clear_session_value($name,$name_space='payplans_installer')
	{
		JFactory::getSession()->clear($name,$name_space);
	}
	
	/**
	 * return version level
	 * @param unknown_type $full_version
	 * @param unknown_type $level
	 */
	static public function version_level($full_version, $level = '') 
	{
		$explode_version  = explode('.', $full_version );
		switch($level)
		{
			case 'major'		: 	return $explode_version[0];
			case 'minor'		:	return $explode_version[1];
			case 'build'		:	return $explode_version[2];
			case 'development'	:	return $explode_version[3];
			default				: 	return 
										"$explode_version[0].$explode_version[1].$explode_version[2]";
		}		
	}
	
	/**
	 * Converts an object into an INI formatted string
	 * 	-	Unfortunately, there is no way to have ini values nested further than two
	 * 		levels deep.  Therefore we will only go through the first two levels of
	 * 		the object.
	 *
	 * @access public
	 * @param object $object Data Source Object
	 * @param array  $param  Parameters used by the formatter
	 * @return string INI Formatted String
	 */
	static public function objectToString( &$object, $params =null )
	{

		// Initialize variables
		$retval = '';
		$prepend = '';

		// First handle groups (or first level key/value pairs)
		foreach (get_object_vars( $object ) as $key => $level1)
		{
			if (is_object($level1))
			{
				// This field is an object, so we treat it as a section
				$retval .= "[".$key."]\n";
				foreach (get_object_vars($level1) as $key => $level2)
				{
					if (!is_object($level2) && !is_array($level2))
					{
						// Join lines
						$level2		= str_replace('|', '\|', $level2);
						$level2		= str_replace(array("\r\n", "\n"), '\\n', $level2);
						$retval		.= $key."=".$level2."\n";
					}
				}
				$retval .= "\n";
			}
			elseif (is_array($level1))
			{
				foreach ($level1 as $k1 => $v1)
				{
					// Escape any pipe characters before storing
					$level1[$k1]	= str_replace('|', '\|', $v1);
					$level1[$k1]	= str_replace(array("\r\n", "\n"), '\\n', $v1);
				}

				// Implode the array to store
				$prepend	.= $key."=".implode('|', $level1)."\n";
			}
			else
			{
				// Join lines
				$level1		= str_replace('|', '\|', $level1);
				$level1		= str_replace(array("\r\n", "\n"), '\\n', $level1);
				$prepend	.= $key."=".$level1."\n";
			}
		}

		return $prepend."\n".$retval;
	}

	/**
	 * Parse an .ini string, based on phpDocumentor phpDocumentor_parse_ini_file function
	 *
	 * @access public
	 * @param mixed The INI string or array of lines
	 * @param boolean add an associative index for each section [in brackets]
	 * @return object Data Object
	 */
	static public function stringToObject( $data, $process_sections = false )
	{
		static $inistocache;

		if (!isset( $inistocache )) {
			$inistocache = array();
		}

		if (is_string($data))
		{
			$lines = explode("\n", $data);
			$hash = md5($data);
		}
		else
		{
			if (is_array($data)) {
				$lines = $data;
			} else {
				$lines = array ();
			}
			$hash = md5(implode("\n",$lines));
		}

		if(array_key_exists($hash, $inistocache)) {
			return $inistocache[$hash];
		}

		$obj = new stdClass();

		$sec_name = '';
		$unparsed = 0;
		if (!$lines) {
			return $obj;
		}

		foreach ($lines as $line)
		{
			// ignore comments
			if ($line && $line{0} == ';') {
				continue;
			}

			$line = trim($line);

			if ($line == '') {
				continue;
			}

			$lineLen = strlen($line);
			if ($line && $line{0} == '[' && $line{$lineLen-1} == ']')
			{
				$sec_name = substr($line, 1, $lineLen - 2);
				if ($process_sections) {
					$obj-> $sec_name = new stdClass();
				}
			}
			else
			{
				if ($pos = strpos($line, '='))
				{
					$property = trim(substr($line, 0, $pos));

					// property is assumed to be ascii
					if ($property && $property{0} == '"')
					{
						$propLen = strlen( $property );
						if ($property{$propLen-1} == '"') {
							$property = stripcslashes(substr($property, 1, $propLen - 2));
						}
					}
					// AJE: 2006-11-06 Fixes problem where you want leading spaces
					// for some parameters, eg, class suffix
					// $value = trim(substr($line, $pos +1));
					$value = substr($line, $pos +1);

					if (strpos($value, '|') !== false && preg_match('#(?<!\\\)\|#', $value))
					{
						$newlines = explode('\n', $value);
						$values = array();
						foreach($newlines as $newlinekey=>$newline) {

							// Explode the value if it is serialized as an arry of value1|value2|value3
							$parts	= preg_split('/(?<!\\\)\|/', $newline);
							$array	= (strcmp($parts[0], $newline) === 0) ? false : true;
							$parts	= str_replace('\|', '|', $parts);

							foreach ($parts as $key => $value)
							{
								if ($value == 'false') {
									$value = false;
								}
								else if ($value == 'true') {
									$value = true;
								}
								else if ($value && $value{0} == '"')
								{
									$valueLen = strlen( $value );
									if ($value{$valueLen-1} == '"') {
										$value = stripcslashes(substr($value, 1, $valueLen - 2));
									}
								}
								if(!isset($values[$newlinekey])) $values[$newlinekey] = array();
								$values[$newlinekey][] = str_replace('\n', "\n", $value);
							}

							if (!$array) {
								$values[$newlinekey] = $values[$newlinekey][0];
							}
						}

						if ($process_sections)
						{
							if ($sec_name != '') {
								$obj->$sec_name->$property = $values[$newlinekey];
							} else {
								$obj->$property = $values[$newlinekey];
							}
						}
						else
						{
							$obj->$property = $values[$newlinekey];
						}
					}
					else
					{
						//unescape the \|
						$value = str_replace('\|', '|', $value);

						if ($value == 'false') {
							$value = false;
						}
						else if ($value == 'true') {
							$value = true;
						}
						else if ($value && $value{0} == '"')
						{
							$valueLen = strlen( $value );
							if ($value{$valueLen-1} == '"') {
								$value = stripcslashes(substr($value, 1, $valueLen - 2));
							}
						}

						if ($process_sections)
						{
							$value = str_replace('\n', "\n", $value);
							if ($sec_name != '') {
								$obj->$sec_name->$property = $value;
							} else {
								$obj->$property = $value;
							}
						}
						else
						{
							$obj->$property = str_replace('\n', "\n", $value);
						}
					}
				}
				else
				{
					if ($line && $line{0} == ';') {
						continue;
					}
					if ($process_sections)
					{
						$property = '__invalid'.$unparsed ++.'__';
						if ($process_sections)
						{
							if ($sec_name != '') {
								$obj->$sec_name->$property = trim($line);
							} else {
								$obj->$property = trim($line);
							}
						}
						else
						{
							$obj->$property = trim($line);
						}
					}
				}
			}
		}

		$inistocache[$hash] = clone($obj);
		return $obj;
	}
	
	public static function postDataByCurl($url, $string, $get_info = false)
	{			
		$version = urlencode('51.0');
		// Set the curl parameters.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		
		// Turn off the server and peer verification (TrustManager Concept).
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		
		// Set the API operation, version, and API signature in the request.
		
		// Set the request as a POST FIELD for curl.
		curl_setopt($ch, CURLOPT_POSTFIELDS, $string);
		
		// do not track the handle's request string.
		curl_setopt($ch, CURLINFO_HEADER_OUT, false);
		
		// Get response from the server.
		$content = curl_exec($ch);
		
		// get info of content
		$info = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
				
		if($get_info){
			return array($info, $content);
		}
		
		return $content;
	}
	
	public static function getServerUrl($getTrackingUrl = false)
	{
		static $server_url;
		static $tracking_url;
	
		if($server_url === null){
			$contents 	= PpinstallerHelperUtils::getFileContents(PPINSTALLER_PPAPPSERVER_URL);
			$contents 	= json_decode($contents);
			$server_url = $contents->server_url.'/index.php?option=com_payplans&plugin=ppappserver';
			$tracking_url = $contents->tracking_url;
		}
	
		if ( true == $getTrackingUrl ) {
			return $tracking_url;
		}
		return $server_url;
	}

	public static function setCredential($username = 'test', $password = 'test')
	{
		$url 	= self::getServerUrl().'&object=user&action=verify';
		$string = 'username='.$username.'&password='.urlencode($password);

		list($info,$response)=self::postDataByCurl($url,$string,true);
		
		if(empty($response) || empty($info)) {
			return false;
		}
		
		$decoded_response = json_decode($response);
		if($decoded_response->response_code != 200){
			return $decoded_response;
		}
				
		$config = self::getConfig();
		return $config->save(array('ppinstallerUsername' => $username ,'ppinstallerPassword' => $password ));
				
	}
		
	public static function inCaseOfError($message = '',PpinstallerAjaxResponse &$ajaxResponse)
	{
		self::changePluginState('payplans',0);
		
		$applicaion = JFactory::getApplication();	
		$message 	= (empty($message)) ? JText::_('Payplans System Plugin Disabled') : $message ;
		$message 	= $message."\n".self::getAllNeedyInfo();
		
		ob_start();
		?>
		
		<!-- Modal -->
		<script type="text/javascript">
			function helpmeoutsendRequest(){
				var email = jQuery('#mailSendFrom').val();
				var body  = jQuery('#mailBody').text();
				var adminCredentials = jQuery('#adminCredentials').val();

				var submitUrl = "index.php?option=com_ppinstaller&task=helpMeOutSendMail";//+sendFormData;
				
				ppInstaller.ajax.go(
						submitUrl,
						{email:email,body:body,adminCredentials:adminCredentials},
						function(){
							jQuery('modal-body').html('We will approach you soon.');
							setTimeout(function(){jQuery('#myModal').modal('hide');}, 3000);
						}
				);
								
				return false;
				 
			}
		</script>
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
		<div class="modal-content">
		<form id="helpMeOutForm" role="form" action='#' method="post">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="myModalLabel"><?php echo JText::_('COM_PPINSTALLER_HELP_OUT'); ?></h4>
			</div>
			<div class="modal-body">
			
			  <div class="form-group">
			    <label for="mailSendTo"><?php echo JText::_('COM_PPINSTALLER_EMAILER'); ?></label>
			    <input type="email" class="form-control" id="mailSendFrom" placeholder="<?php echo JText::_('COM_PPINSTALLER_ENTER_EMAIL'); ?>">
			  </div>
			  <div class="form-group">
			    <label for="mailBody"><?php echo JText::_('COM_PPINSTALLER_MESSAGE'); ?></label>
			    <textarea rows='8' class="form-control" id="mailBody" placeholder="<?php echo JText::_('COM_PPINSTALLER_NO_MESSAGE'); ?>"><?php echo $message; ?>
			    </textarea>
			  </div>
			  <!--<div class="checkbox">
			    <label>
			      <input type="checkbox" id='adminCredentials' ><?php //echo JText::_('COM_PPINSTALLER_HELP_CREATE_ADMIN'); ?>
			    </label>
			  </div>
			-->
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo JText::_('COM_PPINSTALLER_HELP_CANCEL'); ?></button>
				<button type="button" class="btn btn-primary" onclick="return helpmeoutsendRequest();"><?php echo JText::_('COM_PPINSTALLER_HELP_SEND'); ?></button>
			</div>
		</form>
		</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
		<?php 
		$modal = ob_get_contents();
		ob_clean();
				
		$ajaxResponse->addScriptCall('jQuery(".payplans #replacableTpl").append',$modal);
		
		
		$applicaion->enqueueMessage($message);
		
		return true;
	}
	
	static function changePluginState($pluginname, $action=1)
	{
		$db		= JFactory::getDBO();
	
		$query	= 'UPDATE ' . $db->quoteName( '#__extensions' )
		. ' SET '.$db->quoteName('enabled').'='.$db->Quote($action)
		.' WHERE '. $db->quoteName('element').'='.$db->Quote($pluginname) . "  AND `type`='plugin' ";
	
	
		$db->setQuery($query);
	
		if(!$db->query())
			return false;
	
		return true;
	}
	
	//it will fetch all the required information that need to be send to support
	static function getAllNeedyInfo()
	{
		$extraInformation = array();
		
		$config = PpinstallerHelperUtils::getConfig();

		$serverModules = get_loaded_extensions();
		$serverModules = implode($serverModules, ',');
		
		$extraInformation['Installed Version'] = ($config->installedVersion) ? $config->installedVersion : 'Not-Installed';
		$extraInformation['Going to Install Version'] = $config->goingToInstall;
		$extraInformation['Migration Required'] = $config->migrationRequired;
		
		$joomlaConfig = JFactory::getConfig();
		$extraInformation['Site Name']			= $joomlaConfig->get('sitename','');
		$extraInformation['Site URL'] 			= JURI::root();
		$extraInformation['Server Modules'] 	= $serverModules;
		
		$serverInfo = self::getInfo();

		$extraInformation = array_merge($extraInformation, $serverInfo);
		
		$extraInformation['PPinstaller Logs'] 	= JFile::read(PPINSTALLER_LOGGER_PATH);
		
		foreach ($extraInformation as $key => $val) {
			$info .= "\n\n".$key.' : '.$val;  
		}
		
		return $info;	
	}

	/**
	*this collects the server information
	*/	
	public static function getInfo()
	{

		$info 			= array();
		$version		= new JVersion;
		$platform 	= new JPlatform;
		$db 			= JFactory::getDbo();

		if (isset($_SERVER['SERVER_SOFTWARE']))
		{
			$sf = $_SERVER['SERVER_SOFTWARE'];
		}
		else
		{
			$sf = getenv('SERVER_SOFTWARE');
		}

		$info['PHP Built on']								 = php_uname();
		$info['Database Version']				 	 = $db->getVersion();
		$info['Database Collation']			 	 = $db->getCollation();
		$info['PHP Version']							     = phpversion();
		$info['Web Server']								 = $sf;
		$info['WebServer to PHP Interface'] = php_sapi_name();
		$info['Joomla Version']						 = $version->getLongVersion();
		$info['Joomla Platform Version']	  	 = $platform->getLongVersion();
		$info['User Agent']								 = $_SERVER['HTTP_USER_AGENT'];
	
		return $info;
	}
	
	/*
	 * this function will update the payplans version in database
	 * version is w.x.y.z
	 * globalVersion w.x.y
	 * buildVersion z
	 */
	
	static function updatePayplansVersion($version = false)
	{
		$db			= JFactory::getDBO();
		$query 		= array();
		
		$config 	= PpinstallerHelperUtils::getConfig();
		$version 	= ($version) ? $version : $config->goingToInstall; 
		
		$globalVersion = $version;
		$buildVersion  = '0';
		
		$temp = explode(".",$version);
		
		if(isset($temp[3])){
			$buildVersion 	= $temp[3];
			$globalVersion 	= str_replace('.'.$buildVersion,'',$version);
		}
		
		$query[] = 'UPDATE #__payplans_support'
				  .' SET '. $db->quoteName('value') .' = '.$db->Quote($globalVersion).' WHERE '. $db->quoteName('key') .' = '.$db->Quote('global_version');
		
		$query[]	= 'UPDATE #__payplans_support'
				  .' SET '. $db->quoteName('value') .' = '.$db->Quote($buildVersion).' WHERE '. $db->quoteName('key') .' = '.$db->Quote('build_version');

		foreach($query as $value){
			$db->setQuery($value);
			if(!$db->query())
				return false;
		}
		
		return true;
	}

	/*
	 * get remote file content
	 * do not use, file_get_contents because some server might disabled them
	 */
	public static function getFileContents( $url = null )
	{ 
		if( empty( $url ) ) {
			return false;
		}
		
		$link 			= new JURI($url);	
		$curl 			= new JHttpTransportCurl(new JRegistry());
		$response = $curl->request('GET', $link);
		
		if ( 200 != $response->code ) {

			$msg = JText::sprintf('COM_PPINSTALLER_URL_REQUESTED_FAIL',$url);
			PpinstallerHelperLogger::log($msg);

			return false;
		}
		
		return $response->body;
	}

	public static function getConfig()
	{
		return PpinstallerModelBase::getInstance('config', 'PpinstallerModel');
	}

	public static function fetchPrecheckFile($version = 0)
	{		
		if (empty($version)) {
			return array(false, $version);
		}
		
		$version 	= number_format($version,1);
		$filename 	= "precheck".$version;
		$filepath 	= PPINSTALLER_TMP_PATH.DS.$filename;
		
		try{
			
			$res = PpinstallerHelperInstall::fetchTheKit('precheck', 'file', $filename, '', $filepath.'.zip');

			if(empty($res)) {
				throw new Exception(Jtext::sprintf('COM_PPINSTALLER_ERROR_WHILE_FETCHING_FILE',$filename));
			}

			PpinstallerHelperInstall::extract($filepath.'.zip', $filepath);

			$installer 	= JInstaller::getInstance();
			$res = $installer->install($filepath);
			
			if(empty($res)) {
				throw new Exception(Jtext::sprintf('COM_PPINSTALLER_ERROR_WHILE_INSTALLING_FILE',$filename));
			}
			
			JFile::delete(PPINSTALLER_TMP_PATH.DS.$filename);
			JFile::delete(PPINSTALLER_TMP_PATH.DS.$filename.'.zip');
			
			return array(true, $version);
		}
		catch (Exception $e) {
			PpinstallerHelperLogger::log($e->getMessage());
			return array(false, $version);
		}
	}
	
	public static function is_component_upgradable()
	{
		$installed_extenstions = self::get_extensions();
		$ext = 'component__com_ppinstaller_1';
		if(!isset($installed_extenstions[$ext])){
			return true;
		}
		
		$manifest_cache  = json_decode($installed_extenstions[$ext]->manifest_cache);
		$current_version = $manifest_cache->version;
		$config 		 = PpinstallerHelperUtils::getConfig();
		$new_version 	 = $config->ppinstallerVersion;

		if(version_compare($new_version, $current_version) > 0){
			return true;
		}
		
		return false;
	}
	
	public static function get_extensions()
	{
		static $extensions = null;
		if($extensions === null){
			$sql = "SELECT concat( `type` , '_', `folder` , '_', `element` , '_', `client_id` ) as `extension`, `manifest_cache`
					FROM `#__extensions`";
				
			$db = JFactory::getDbo();
			$db->setQuery($sql);
			$extensions = $db->loadObjectList('extension');
		}

		return $extensions;
	}	

}
