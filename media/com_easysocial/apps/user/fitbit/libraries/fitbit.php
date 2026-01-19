<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(JPATH_ROOT . '/media/com_easysocial/apps/user/fitbit/vendor/autoload.php');
require_once(__DIR__ . '/api.php');

use djchen\OAuth2\Client\Provider\Fitbit;

class FitBitHelper
{
	/**
	 * Creates a new activity stream to notify the world the user linked a fitbit device
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public static function createStream(SocialUser $user)
	{
		$stream	= ES::stream();
		$streamTemplate	= $stream->getTemplate();

		$streamTemplate->setActor($user->id, SOCIAL_TYPE_USER);
		$streamTemplate->setContext($user->id, 'fitbit');
		$streamTemplate->setVerb('link');
		$streamTemplate->setPublicStream('core.view');

		// Create the stream data.
		$stream->add($streamTemplate);
	}

	/**
	 * Retrieve a list of accounts that needs to be synchronized
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public static function getAccountsToSync($limit = 10)
	{
		$db = ES::db();
		$query = array();
		$query[] = 'SELECT * FROM `#__social_fitbit` WHERE `cron`=0 LIMIT 0,' . $limit;

		$db->setQuery($query);

		$result = $db->loadObjectList();

		if (!$result) {
			return $result;
		}

		$accounts = array();

		foreach ($result as $row) {
			$account = self::table('Fitbit');
			$account->bind($row);

			$accounts[] = $account;
		}
		return $accounts;
	}

	/**
	 * Generates the url to the controller to initialize
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public static function getInitializeDeviceUrl($appId)
	{
		$url = self::getControllerUrl($appId, 'fitbit', 'initialize', false);

		return $url;
	}

	/**
	 * Helper to quickly generate a url for the app
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public static function getControllerUrl($appId, $controller, $task, $xhtml = true, $options = array())
	{
		$options = array_merge(array(
			'appId' => $appId,
			'controller' => 'apps',
			'task' => 'controller',
			'appController' => $controller,
			'appTask' => $task,
			ES::token() => 1
		), $options);

		$url = ESR::apps($options, $xhtml);

		return $url;
	}

	/**
	 * Helper to quickly generate a url for the app
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public static function getProfileAppUrl($user, $appId, $xhtml = true, $options = array())
	{
		$options = array_merge(array(
			'id' => $user->getAlias(),
			'appId' => $appId,
			ES::token() => 1
		), $options);

		$url = ESR::profile($options, $xhtml);

		return $url;
	}

	/**
	 * Helper to quickly generate a url for the app
	 *
	 * @since	3.0.0
	 * @access	public
	 */
	public static function getViewUrl($appId, $options = array())
	{
		$options = array_merge(array(
			'appId' => $appId,
			ES::token() => 1
		), $options);

		dump($options);
	}

	/**
	 * Generate the callback url to be used with fitbit's oauth request
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public static function getCallbackUrl()
	{
		$url = rtrim(JURI::root(), '/') . '/index.php?option=com_easysocial&fitbitAuthorization=1';

		return $url;
	}

	/**
	 * Creates a new provider to connect to fitbit
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public static function getProvider($clientId, $clientSecret = null, $redirectUri = null)
	{
		static $provider = null;

		if (is_null($provider)) {
			$redirectUri = is_null($redirectUri) ? FitBitHelper::getCallbackUrl() : $redirectUri;

			if ($clientId instanceof SocialRegistry) {
				$clientSecret = $clientId->get('client_secret');
				$clientId = $clientId->get('client_id');
			}

			$provider = new FitBitProvider($clientId, $clientSecret, $redirectUri);
		}

		return $provider;
	}

	/**
	 * Retrieves the steps taken for the past number of days
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public static function getSteps($days, SocialUser $user)
	{
		$db = ES::db();

		$query = array();
		$query[] = 'SELECT * FROM `#__social_fitbit_data` AS a';
		$query[] = 'WHERE';
		$query[] = 'a.`user_id`=' . $db->Quote($user->id);
		$query[] = 'AND a.`date` >= DATE(NOW()) - INTERVAL ' . $days . ' DAY';
		$query[] = 'ORDER BY `date` DESC';

		$db->setQuery($query);
		$steps = $db->loadObjectList();

		return $steps;
	}

	/**
	 * Retrieves the average steps taken for the past number of days
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public static function getAverageSteps($days, SocialUser $user)
	{
		$db = ES::db();

		$query = array();
		$query[] = 'SELECT AVG(a.`value`) FROM `#__social_fitbit_data` AS a';
		$query[] = 'WHERE';
		$query[] = 'a.`user_id`=' . $db->Quote($user->id);
		$query[] = 'AND a.`date` >= DATE(NOW()) - INTERVAL ' . $days . ' DAY';

		$db->setQuery($query);
		$steps = (int) $db->loadResult();

		return $steps;
	}

	/**
	 * Retrieves the average steps taken for the past number of days
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public static function getHighestSteps($days, SocialUser $user)
	{
		$db = ES::db();

		$query = array();
		$query[] = 'SELECT * FROM `#__social_fitbit_data` AS a';
		$query[] = 'WHERE';
		$query[] = 'a.`user_id`=' . $db->Quote($user->id);
		$query[] = 'AND a.`date` >= DATE(NOW()) - INTERVAL ' . $days . ' DAY';
		$query[] = 'ORDER BY a.`value` DESC';
		$query[] = 'LIMIT 0,1';

		$db->setQuery($query);
		$day = $db->loadObject();

		return $day;
	}

	/**
	 * Retrieves total steps today
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public static function getTodaySteps(SocialUser $user)
	{
		$db = ES::db();

		$query = array();
		$query[] = 'SELECT `value` FROM `#__social_fitbit_data` AS a';
		$query[] = 'WHERE';
		$query[] = 'a.`user_id`=' . $db->Quote($user->id);
		$query[] = 'AND a.`date` = DATE(NOW())';

		$db->setQuery($query);
		$total = $db->loadResult();

		if (!$total) {
			return 0;
		}

		return $total;
	}

	/**
	 * Retrieves the average steps taken for the past number of days
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public static function getTotalSteps($days, SocialUser $user)
	{
		$db = ES::db();

		$query = array();
		$query[] = 'SELECT SUM(value) FROM `#__social_fitbit_data` AS a';
		$query[] = 'WHERE';
		$query[] = 'a.`user_id`=' . $db->Quote($user->id);
		$query[] = 'AND a.`date` >= DATE(NOW()) - INTERVAL ' . $days . ' DAY';

		$db->setQuery($query);
		$total = $db->loadResult();

		if (is_null($total)) {
			$total = 0;
		}

		return $total;
	}

	/**
	 * Release cron locks if all accounts are already accounted for
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public static function releaseCronLocks()
	{
		$db = ES::db();
		$query = array();
		$query[] = 'SELECT COUNT(1) FROM `#__social_fitbit` WHERE `cron`=0';
		$db->setQuery($query);

		$total = $db->loadResult();

		// When there are still items to process, skip this altogether
		if ($total > 0) {
			return;
		}

		$query = 'UPDATE `#__social_fitbit` SET `cron`=0';
		$db->setQuery($query);
		return $db->Query();
	}

	/**
	 * Purges all fitbit data for a user
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public static function purge($userId)
	{
		$db = ES::db();
		$userId = (int) $userId;

		$query = array();
		$query[] = 'DELETE FROM `#__social_fitbit_data` WHERE `user_id`=' . $db->Quote($userId);

		$db->setQuery($query);
		$db->Query();
	}

	/**
	 * Retrieves the table adapter for fitbit app
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public static function table($name)
	{
		$name = strtolower($name);

		require_once(dirname(__DIR__) . '/tables/' . $name . '.php');

		$table = JTable::getInstance($name, 'FitBitTable');

		return $table;
	}

	/**
	 * Unlinks a fitbit account
	 *
	 * @since	3.2.0
	 * @access	public
	 */
	public static function unlink($userId)
	{
		$db = ES::db();
		$userId = (int) $userId;

		$query = array();
		$query[] = 'DELETE FROM `#__social_fitbit` WHERE `user_id`=' . $db->Quote($userId);

		$db->setQuery($query);
		$db->Query();
	}
}
