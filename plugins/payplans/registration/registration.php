<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');

$file = JPATH_ADMINISTRATOR . '/components/com_payplans/includes/payplans.php';
$exists = JFile::exists($file);

if (!$exists) {
	return;
}

require_once($file);

class plgPayplansRegistration extends PPPlugins
{
	/**
	 * Triggered during Joomla's onAfterRoute trigger
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onAfterRoute()
	{
		if (PP::isFromAdmin() || $this->my->id) {
			return;
		}

		$registration = PP::registration();
		$registration->onAfterRoute();
	}

	/**
	 * Joomla 1.6 compatibility
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onUserAfterSave($user, $isnew, $success, $msg)
	{
		if (PP::isFromAdmin()) {
			return;
		}

		return $this->onAfterStoreUser($user, $isnew, $success, $msg);
	}

	/**
	 * Joomla 1.6 compatibility
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onUserBeforeSave($user, $isnew)
	{
		if (PP::isFromAdmin()) {
			return;
		}

		return $this->onBeforeStoreUser($user, $isnew);
	}

	/**
	 * Trigger registration library
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onBeforeStoreUser($user, $isnew)
	{
		if (PP::isFromAdmin()) {
			return;
		}

		$registration = PP::registration();
		$registration->onBeforeStoreUser($user, $isnew);

		return true;
	}
	
	/**
	 * Some registrations requires onAfterDispatch
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onAfterDispatch()
	{
		$registration = PP::registration();
		$registration->onAfterDispatch();
	}

	/**
	 * Triggered when a new user is created. This is to allow us to facilitate user registrations
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onAfterStoreUser($user, $isnew, $success, $msg)
	{
		if (PP::isFromAdmin()) {
			return;
		}

		// Process registration systems
		$registration = PP::registration();
		$registration->onAfterStoreUser($user, $isnew, $success, $msg);
	}

	/**
	 * Performs access check
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansAccessCheck()
	{
		if (PP::isFromAdmin()) {
			return;
		}

		$registration = PP::registration();
		$registration->onPayplansAccessCheck();
	}

	/**
	 * Perform account activation for users make a successful purchase after register (built-in)
	 * 
	 * @since	4.1.0
	 * @access	public
	 */
	public function onPayplansSubscriptionAfterSave($prev, $new)
	{
		$config = PP::config();
		$registrationType = $config->get('registrationType');
		$accountVerification = $config->get('account_verification');

		// Check if Inbuilt registration set and account verification on Subscription Activation 
		if ($registrationType == 'auto' && $accountVerification == 'active_subscription') {

			// no need to trigger if previous and current state is same
			if (($new->getStatus() == PP_NONE) || ($prev != null && $prev->getStatus() == $new->getStatus())) {
				return true;
			}

			if ($new->isActive()) {

				$userId = $new->getBuyer()->getId();
				$user = JFactory::getUser((Int) $userId);
		
				if ($user->get('block') == 0) {
					return true;
				}

				$user->set('block', '0');
				$user->set('activation', '');

				$state = $user->save();

				if (!$state) {
					
					$this->setError($user->getError());
					return false;
				}

				// Auto login after payment completion
				// Regarding this session retrieve it from the authentication plugin during registration
				$session = PP::session();
				$username = $session->get('COM_PAYPLANS_AUTHENTICATION_USERNAME');
				$password = $session->get('COM_PAYPLANS_AUTHENTICATION_PASSWORD');

				if (!$username && !$password) {
					return;
				}

				$username = base64_decode($username);
				$password = base64_decode($password);

				$user = PP::user();
				$state = $user->login($username, $password);

				$session->clear('COM_PAYPLANS_AUTHENTICATION_USERNAME');
				$session->clear('COM_PAYPLANS_AUTHENTICATION_PASSWORD');

				return $state;
			}
		}
		
		return true;
	}
}
