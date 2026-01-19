<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

PP::load('Logger');
PP::load('LogFormatter');

class PPLog
{
	/**
	 * Legacy log files are stored in .txt file extension.
	 * Anyone that is smart enough is able to browse it on the web.
	 *
	 * This fixes it and renames it to .php so nobody can view it.
	 *
	 * @since	4.0.12
	 * @access	public
	 */
	public static function fixLegacyFile($file)
	{
		$contents = file_get_contents($file);

		$prepend = "<" . "?" . "php defined('_JEXEC') or die('Unauthorized Access'); " . "?" . ">" . PHP_EOL;
		
		$contents = $prepend . $contents;

		// Generate a new file name
		$newFile = str_ireplace('.txt', '.php', $file);
			
		JFile::write($newFile, $contents);

		// Delete the old file
		JFile::delete($file);
	}

	/**
	 * Detects for legacy log files (Readybytes era .txt log files)
	 *
	 * @since	4.0.12
	 * @access	public
	 */
	public static function getLegacyLFiles()
	{
		$path = JPATH_ROOT . '/media/com_payplans/log';

		$files = array();
		if (JFolder::exists($path)) {
			$files = JFolder::files($path, '.txt', true, true);
		}

		return $files;
	}

	/**
	 * Detects for legacy log files (Readybytes era .txt log files)
	 *
	 * @since	4.0.12
	 * @access	public
	 */
	public static function addHTAccessFile()
	{
		$path = JPATH_ROOT . '/media/com_payplans/log';

		$file = $path . '/.htaccess';

		if (!JFile::exists($file)) {

			$content = '# Deny access to .htaccess
<Files .htaccess>
Order allow,deny
Deny from all
</Files>

# Deny access to files with extensions:
<FilesMatch "\.(php|txt|log|ini)$">
Order allow,deny
Deny from all
</FilesMatch>';

			JFile::write($file, $content);
		}

		return true;
	}

	/**
	 * Log messages into audit logs
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	static public function log($level, $message, $object = null, $content = null, $type = 'PayplansFormatter', $class = 'SYSTEM', $sendemail = false)
	{
		$token = md5(serialize($content));
		$block = false;

		// block logging for selected objects
		$config = PP::config();
		$blockLogging = $config->get('blockLogging', '');


		$userEmail = '';
		if ($type == 'PayplansPaymentFormatter' && $config->get('notify_user_payment_failure')) {
			$user = PP::user($object->getBuyer());
			$userEmail = $user->getEmail();
		}

		if (!empty($blockLogging)) {			

			// convert to array
			$blockLogging = json_decode($blockLogging);

			foreach ($blockLogging as $logObject) {
				$className = 'PP' . ucfirst($logObject);

				// IMP: for proper handling when config log needs to be blocked
				//since in config log we dont have any object to get class from
				if ($object == null && $class != 'PPConfig') {
					$className = 'Payplans_' . ucfirst($logObject);
				}

				if ((($object != null) && ($object instanceof $className)) || !strcasecmp($className, $class)) {
					$block = true;
					continue;
				}
			}
		}

		if ($block === true) {
			return true;
		}

		$objectId = 0;
		
		if ($object && is_object($object)) {
			$objectId = method_exists($object, 'getId') ? $object->getId() : 0;
			$class = get_class($object);
		}

		$logger = PP::logger();
		
		return $logger->log($level, $message, $objectId, $class, $content, $type, $token, $sendemail, $userEmail);
	}

	/**
	 * Generates the file name for the log file
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function getFilePath($logId, $random = '')
	{
		$maxFolderSize = 32768;
		$folderId = 1;

		$config = PP::config();
		$folderId = $config->get('logBucket') ? $config->get('logBucket') : 1;

		$folderName = 'log_bucket_' . $folderId;
		$folderPath = JPATH_ROOT . '/media/com_payplans/log/' . $folderName;

		// Ensure that the folder exists
		$exists = JFolder::exists($folderPath);

		if (!$exists) {
			JFolder::create($folderPath);
		}

		$folderSize = filesize($folderPath);

		// Once a folder exceeds the default maximum folder size, rotate the log folder
		if ($folderSize > PP_LOGS_FOLDER_MAXSIZE) {
			$folderId++;

			$folderName = 'log_bucket_' . $folderId;
			$folderPath = JPATH_ROOT . '/media/com_payplans/log/' . $folderName;

			$config = PP::model('Config');
			$config->save([
				'logBucket' => $folderId
			]);

			// Create the new folder
			JFolder::create($folderPath);
		}

		$id = $random ? $random : ($logId % 16);
		$fileName = 'log_' . $id . '.php';

		$file = $folderPath . '/' . $fileName;

		return $file;
	}

	/**
	 * Generates a folder name
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getFolderName()
	{

	}

	/**
	 * Given a token, determine if the log is a legacy log record
	 *
	 * @since	4.0.12
	 * @access	public
	 */
	public static function getLegacyStatus($token)
	{
		$db = PP::db();

		$query = [];
		$query[] = 'SELECT `legacy` FROM ' . $db->qn('#__payplans_log');
		$query[] = 'WHERE ' . $db->qn('current_token') . '=' . $db->Quote($token);
		
		$db->setQuery($query);
		$legacy = (bool) $db->loadResult();
		
		return $legacy;
	}

	//return log entries for the given object and mentioned log-level
	static public function getLog($object=null, $level=null)
	{
		if($object && is_object($object)){
			$object_id 	 = method_exists($object, 'getId') ? $object->getId() : 0 ;
			$record = PP::model('log')->loadRecords(array('object_id'=>$object_id, 'level'=>$level));

			return $record;
		}

		return false;
	}

	public static function getOwnerId($content)
	{
			$owner_id = '';
			$id = [
				'user_id' => '',
				'buyer_id' => ''
			];

			$compare = isset($content['current']) ? $content['current'] : $content;
			$owner_id = array_intersect_key($id, $compare);

			if (!empty($owner_id)){
				$owner_id = key($owner_id);
				$owner_id = $compare[$owner_id];
			}
			return $owner_id;
	}

	public static function calculatePreviousToken($object_id, $class,$content)
	{
		// when log for any app is created then $object_id == app_id
		// Always create an log when it is related to an app.
		preg_match('/^PayplansApp/', $class, $matches);
		
		if (count($matches) == 1  && !isset($content['current'])) {
			return '';
		}

		$previousToken = '';

		$db = PP::db();

		$query = [];
		$query[] = 'SELECT `current_token` FROM ' . $db->qn('#__payplans_log');
		$query[] = 'WHERE ' . $db->qn('object_id') . '=' . $db->Quote($object_id);
		$query[] = 'AND ' . $db->qn('class') . '=' . $db->Quote($class);
		$query[] = 'AND ' . $db->qn('class') . ' NOT IN("SYSTEM", "Payplans_Cron")';
		$query[] = 'ORDER BY `log_id` DESC';

		$db->setQuery($query);
		$previousToken = $db->loadResult();

		return $previousToken;
	}

	public static function calculatePreviousposition($previousToken, $class = "")
	{
		$db = PP::db();

		$query = [];
		$query[] = 'SELECT `position` FROM ' . $db->qn('#__payplans_log');
		$query[] = 'WHERE ' . $db->qn('current_token') . '=' . $db->Quote($previousToken);

		if (!empty($class)) {
			$query[] = 'AND ' . $db->qn('class') . '=' . $db->Quote($class);
		}

		$query[] = 'ORDER BY `log_id` DESC';

		$db->setQuery($query);
		$previousTokenPosition = $db->loadResult();
		
		return $previousTokenPosition;
	}

	/**
	 * Write contents in the log file
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function writeToFile($logId, $contents, $random = '')
	{
		$file = self::getFilePath($logId, $random);

		$fh = fopen($file, 'a+');
		fseek($fh, 0, SEEK_END);

		$pos = ftell($fh);

		fwrite($fh, $contents);
		fclose($fh);

		$position = json_encode([
			'location' => $pos, 
			'filePath' => urlencode($file)
		]);

		return $position;
	}

	/**
	 * Search for the given token from the file and return it
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function calculateLogData($position, $token, $legacy = false)
	{
		$file = urldecode($position->filePath);

		// #805 Under the new log structure, all files must be .php
		$file = str_ireplace('.txt', '.php', $file);

		// #805 Because we have prepended a php defined check on each file, we must now increment the counter
		// 58 characters = the standard defined header in joomla
		$location = $position->location;

		if ($legacy) {
			$location = $position->location + 58;
		}

		$contents = file_get_contents($file, false, null, $position->location);

		$pattern = '#\<'.$token.'>(.*?)\</'.$token.'>#m';

		preg_match($pattern, $contents, $data);

		if (isset($data[1])) {
			$data = json_decode($data[1]);
			$data = unserialize($data);
		}

		return $data;
	}

	public function readBaseEncodeLog($log)
	{
		$content['content'] = '';
		$logData = unserialize(base64_decode($log->content));
		$content['type'] = array_shift($logData);
		
		if (!empty($logData)) {
			$content['content'] = unserialize(base64_decode(array_shift($logData)));
		}

		return [$content['type'], $content['content']];
	}

	/**
	 * Reads a json encoded log data
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function readJsonEncodeLog($log)
	{
		$previousToken = $log->previous_token;
		$prevLogData = [];

		// If previous token is set, then calculate previous data
		if (isset($previousToken) && !empty($previousToken)) {

			// Determines if the previous token is a legacy
			$previousLegacy = self::getLegacyStatus($previousToken);

			$previousData = self::calculatePreviousposition($previousToken);
			$prevLog_position = json_decode($previousData);
			$prevLogData = self::calculateLogData($prevLog_position, $previousToken);
		}

		// Get the current data
		$position = json_decode($log->position);
		$currentLogData = self::calculateLogData($position, $log->current_token, $log->legacy);

		if (isset($prevLogData['content'])) {
			$content['previous'] = $prevLogData['content'];
		}

		$className = array_shift($currentLogData);
		
		if (!isset($currentLogData['content']['current']) && isset($currentLogData['content']['previous'])) {
			$content['previous'] = $currentLogData['content']['previous'];
		} else {
			$content['current'] = array_shift($currentLogData);
		}

		// this is done in order to read base64_encode data.
		if (isset($conten['current']) && is_array($content['current']) && count($content['current']) == 2) {
			$content = array_shift($content);
		}
		
		return [$className, $content];
	}

	/**
	 * return a list of class log
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getClassLog($retrieveMappingClass = false)
	{
		$classes = [
			'SYSTEM' => strtolower(JText::_('COM_PP_LOG_SYSTEM')), 
			'PayplansSubscription' => strtolower(JText::_('COM_PP_LOG_SUBSCRIPTION')), 
			'PayplansOrder' => strtolower(JText::_('COM_PP_LOG_ORDER')), 
			'PayplansConfig' => strtolower(JText::_('COM_PP_LOG_CONFIGURATION')), 
			'PayplansPayment' => strtolower(JText::_('COM_PP_LOG_PAYMENT')), 
			'PayplansUser' => strtolower(JText::_('COM_PP_LOG_USER')), 
			'PayplansPlan' => strtolower(JText::_('COM_PP_LOG_PLAN')), 
			'PayplansGroup' => strtolower(JText::_('COM_PP_LOG_GROUP')), 
			'PayplansInvoice' => strtolower(JText::_('COM_PP_LOG_INVOICE')), 
			'PayPlans_Cron' => strtolower(JText::_('COM_PP_LOG_CRON')), 
			'PayplansAppAssignplan' => strtolower(JText::_('COM_PP_LOG_APP_ASSIGNPLAN')), 
			'PayplansAppEmail' => strtolower(JText::_('COM_PP_LOG_APP_EMAIL')), 
			'PayplansApp2checkout' => strtolower(JText::_('COM_PP_LOG_APP_2CHECKOUT')), 
			'PayplansAppAuthorize' => strtolower(JText::_('COM_PP_LOG_APP_AUTHORIZE')), 
			'PayplansAppPaypal' => strtolower(JText::_('COM_PP_LOG_APP_PAYPAL')), 
			'PayplansAppOfflinepay' => strtolower(JText::_('COM_PP_LOG_APP_OFFLINEPAY')), 
			'PayplansAppContent' => strtolower(JText::_('COM_PP_LOG_APP_CONTENT')), 
			'PayplansAppContentacl' => strtolower(JText::_('COM_PP_LOG_APP_CONTENTACL')), 
			'PayplansAppJsmultiprofile' => strtolower(JText::_('COM_PP_LOG_APP_JOMSOCIAL_MULTIPROFILE')), 
			'PayplansAppJusertype' => strtolower(JText::_('COM_PP_LOG_APP_JOOMLA_USERTYPE')), 
			'PayplansAppXiprofiletype' => strtolower(JText::_('COM_PP_LOG_APP_JOOMLAXI_PROFILETYPE')), 
			'PayplansAppDiscount' => strtolower(JText::_('COM_PP_LOG_APP_DISCOUNT')), 
			'PayplansAppGanalytics' => strtolower(JText::_('COM_PP_LOG_APP_GANALYTICS')), 
			'PayplansAppJuga' => strtolower(JText::_('COM_PP_LOG_APP_JUGA')), 
			'PayplansAppK2category' => strtolower(JText::_('COM_PP_LOG_APP_K2_CATEGORY')), 
			'PayplansAppK2' => strtolower(JText::_('COM_PP_LOG_APP_K2')), 
			'PayplansAppMtree' => strtolower(JText::_('COM_PP_LOG_APP_MTREE')), 
			'PayplansProdiscount' => strtolower(JText::_('COM_PP_LOG_APP_PRODISCOUNT'))
		];

		if ($retrieveMappingClass) {

			$classes = [
				strtolower(JText::_('COM_PP_LOG_SYSTEM')) => ['SYSTEM'],
				strtolower(JText::_('COM_PP_LOG_SUBSCRIPTION')) => ['PayplansSubscription', 'PPSubscription'], 
				strtolower(JText::_('COM_PP_LOG_ORDER')) => ['PayplansOrder', 'PPOrder'], 
				strtolower(JText::_('COM_PP_LOG_CONFIGURATION')) => ['PayplansConfig', 'PPConfig'], 
				strtolower(JText::_('COM_PP_LOG_PAYMENT')) => ['PayplansPayment', 'PPPayment'], 
				strtolower(JText::_('COM_PP_LOG_USER')) => ['PayplansUser', 'PPUser'], 
				strtolower(JText::_('COM_PP_LOG_PLAN')) => ['PayplansPlan', 'PPPlan'], 
				strtolower(JText::_('COM_PP_LOG_GROUP')) => ['PayplansGROUP', 'PPGroup'], 
				strtolower(JText::_('COM_PP_LOG_INVOICE')) => ['PayplansInvoice', 'PPInvoice'], 
				strtolower(JText::_('COM_PP_LOG_CRON')) => ['PayPlans_Cron', 'PPCron'], 
				strtolower(JText::_('COM_PP_LOG_APP_ASSIGNPLAN')) => ['PayplansAppAssignplan'], 
				strtolower(JText::_('COM_PP_LOG_APP_EMAIL')) => ['PayplansAppEmail'], 
				strtolower(JText::_('COM_PP_LOG_APP_2CHECKOUT')) => ['PayplansApp2checkout'], 
				strtolower(JText::_('COM_PP_LOG_APP_AUTHORIZE')) => ['PayplansAppAuthorize'], 
				strtolower(JText::_('COM_PP_LOG_APP_PAYPAL')) => ['PayplansAppPaypal'], 
				strtolower(JText::_('COM_PP_LOG_APP_OFFLINEPAY')) => ['PayplansAppOfflinepay'], 
				strtolower(JText::_('COM_PP_LOG_APP_CONTENT')) => ['PayplansAppContent'], 
				strtolower(JText::_('COM_PP_LOG_APP_CONTENTACL')) => ['PayplansAppContentacl'], 
				strtolower(JText::_('COM_PP_LOG_APP_JOMSOCIAL_MULTIPROFILE')) => ['PayplansAppJsmultiprofile'], 
				strtolower(JText::_('COM_PP_LOG_APP_JOOMLA_USERTYPE')) => ['PayplansAppJusertype'], 
				strtolower(JText::_('COM_PP_LOG_APP_JOOMLAXI_PROFILETYPE')) => ['PayplansAppXiprofiletype'], 
				strtolower(JText::_('COM_PP_LOG_APP_DISCOUNT')) => ['PayplansAppDiscount'], 
				strtolower(JText::_('COM_PP_LOG_APP_GANALYTICS')) => ['PayplansAppGanalytics'], 
				strtolower(JText::_('COM_PP_LOG_APP_JUGA')) => ['PayplansAppJuga'], 
				strtolower(JText::_('COM_PP_LOG_APP_K2_CATEGORY')) => ['PayplansAppK2category'], 
				strtolower(JText::_('COM_PP_LOG_APP_K2')) => ['PayplansAppK2'], 
				strtolower(JText::_('COM_PP_LOG_APP_MTREE')) => ['PayplansAppMtree'], 
				strtolower(JText::_('COM_PP_LOG_APP_PRODISCOUNT')) => ['PayplansProdiscount']
			];
		}

		return $classes;
	}

	/**
	 * Retrieve the log owner name
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getOwner($userId)
	{
		if (!$userId) {
			$guest = new stdClass();
			$guest->owner_id = 0;
			$guest->username = JText::_('COM_PP_SYSTEM');
			
			return $guest;
		}

		static $users = [];

		if (!isset($users[$userId])) {
			$users[$userId] = PP::user($userId);
		}

		return $users[$userId];
	}	
}