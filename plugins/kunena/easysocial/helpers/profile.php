<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

use Joomla\CMS\Factory;

class KunenaProfileEasySocial extends KunenaProfile
{
	protected $params = null;

	public function __construct($params)
	{
		$this->params = $params;
	}

	/**
	 * Retrieves the user listings url
	 *
	 * @since	2.1.11
	 * @access	public
	 */
	public function getUserListURL($action = '', $xhtml = true)
	{
		$config = KunenaFactory::getConfig();
		$my = Factory::getUser();

		if ($config->userlist_allowed == 0 && $my->guest) {
			return false;
		}

		return ESR::users([], $xhtml);
	}

	/**
	 * Generates the permalink to a user's profile
	 *
	 * @since	2.1.11
	 * @access	public
	 */
	public function getProfileURL($userid, $task = '', $xhtml = true)
	{
		if (!$userid) {
			$alias = $userid;
		}

		if ($userid) {
			$user = ES::user($userid);

			$config  = ES::config();
			$jConfig = ES::jConfig();
			$esURLPluginEnabled = JPluginHelper::isEnabled('system', 'easysocialurl');

			if (!ES::isSh404Installed() && $esURLPluginEnabled && $jConfig->getValue('sef')) {

				$rootUri = rtrim(JURI::root(), '/');

				$alias = $user->getAlias(false);

				$url = $rootUri . '/' . $alias;

				// Retrieve current site language code
				$langCode = ES::getCurrentLanguageCode();

				// Append language code from the simple url
				if (!empty($langCode)) {
					$url = $rootUri . '/' . $langCode . '/' . $alias;
				}

				if ($jConfig->getValue('sef_suffix') && !(substr($url, -9) == 'index.php' || substr($url, -1) == '/')) {

					$format = 'html';
					$url .= '.' . $format;

				}

				return $url;
			}

			// If it's not enable shortener URL plugin, just set the alias
			$alias = $user->getAlias();
		}

		$options = ['id' => $alias];

		if ($task) {
			$options['layout'] = $task;
		}

		$url = ESR::profile($options, $xhtml);

		return $url;
	}

	/**
	 * Renders the profile information
	 *
	 * @since	2.1.11
	 * @access	public
	 */
	public function showProfile($view, &$params)
	{
		$userid = $view->profile->userid;
		$user = ES::user($userid);

		$gender = $user->getFieldData('GENDER');

		if (!empty($gender)) {
			$view->profile->gender = $gender;
		}

		$data = $user->getFieldData('BIRTHDAY');
		$json = ES::json();
		$birthday = null;

		// Legacy
		if (isset($data['date']) && $json->isJsonString($data['date']) && !$birthday) {
			$birthday = $this->getLegacyDate($data['date']);
		}

		// Legacy
		if ($json->isJsonString($data) && !$birthday) {
			$birthday = $this->getLegacyDate($data);
		}

		// New format
		if (isset($data['date']) && !$birthday) {
			$birthday = ES::date($data['date']);
		}

		if ($birthday !== null) {
			$view->profile->birthdate = $birthday->format('Y-m-d');
		}
	}

	/**
	 * Generates the birthday based on legacy data
	 *
	 * @since	2.1.11
	 * @access	public
	 */
	public function getLegacyDate($birthday)
	{
		$birthday = json_decode($birthday);
		$birthday = ES::date($birthday->day . '-' . $birthday->month . '-' . $birthday->year);

		return $birthday;
	}

	/**
	 * Generates the link for editing profile
	 *
	 * @since	2.1.11
	 * @access	public
	 */
	public function getEditProfileURL($userid, $xhtml = true)
	{
		$options = ['layout' => 'edit'];
		$url = ESR::profile($options, $xhtml);

		return $url;
	}
}
