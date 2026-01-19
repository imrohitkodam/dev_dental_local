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

jimport('joomla.filesystem.file');

$ppFile = JPATH_ADMINISTRATOR . '/components/com_payplans/includes/payplans.php';

if (!JFile::exists($ppFile)) {
	return;
}

require_once($ppFile);

class plgAuthenticationPayplans extends JPlugin
{
	/**
	 * Tests if EasySocial is installed
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function esExists()
	{
		static $exists = null;

		if (is_null($exists)) {
			jimport('joomla.filesystem.file');

			$file = JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/easysocial.php';
			$exists = JFile::exists($file);
			
			if (!$exists) {
				$exists = false;

				return $exists;
			}

			include_once($file);
			$exists = true;
		}

		return $exists;
	}

	/**
	 * This method use for set the user credentials on the session in order to login manually after done the payment.
	 *
	 * @since	4.1.2
	 * @access	public
	 */
	public function onUserAuthenticate(&$credentials, $options, &$response)
	{
		if (!$this->esExists()) {
			return;
		}

		$config = PP::config();
		$ppRegistrationType = $config->get('registrationType');

		if ($ppRegistrationType != 'easysocial') {
			return;
		}

		// Retrieve the new registration user id
		$session = PP::session();
		$newRegisterUserId = $session->get('REGISTRATION_USER_ID');

		// skip this if can't find that session key
		if (!$newRegisterUserId) {
			return;
		}

		// Need to check what is the current user register profile type
		$isRegistrationTypeAutoLogin = $this->isRegistrationTypeAutoLogin($newRegisterUserId);

		if (!$isRegistrationTypeAutoLogin) {
			return;
		}

		$userCredentials = array();

		$cUsername = isset($credentials['username']) ? $credentials['username'] : null;
		$cPassword = isset($credentials['password']) ? $credentials['password'] : null;

		if (is_null($cUsername) || is_null($cPassword)) {
			return;
		}

		$username = base64_encode($cUsername);
		$password = base64_encode($cPassword);

		// set this credentials session
		// need to use this session after done the payment
		$session->set('COM_PAYPLANS_AUTHENTICATION_USERNAME', $username);
		$session->set('COM_PAYPLANS_AUTHENTICATION_PASSWORD', $password);

		// this is to force the auto login to failed so that
		// user will not get login to the site before the payment success.
		$credentials['password'] = '';

		// If the current user register on auto login registration type
		// We need to disallow user to login first since he haven't done the payment
		$response->status = JAuthentication::STATUS_FAILURE;
		$response->error_message = JText::_('COM_PP_RESTRICTED_USER_AUTO_LOGIN');	
		$response->type = 'Payplans';

		return false;
	}

	/**
	 * This method use for set the user credentials on the session in order to login manually after done the payment.
	 *
	 * @since	4.1.2
	 * @access	public
	 */
	public function isRegistrationTypeAutoLogin($userId)
	{
		if (!$userId) {
			return false;
		}

		$user = ES::user($userId);
		$userProfileId = $user->getProfile()->id;

		$profile = ES::table('Profile');
		$profile->load($userProfileId);

		$registrationType = $profile->getRegistrationType();

		if ($registrationType != 'auto') {
			return false;
		}

		return true;
	}
}
