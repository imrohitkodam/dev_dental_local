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

require_once(__DIR__ . '/formatter.php');

class PPAppAcymailing extends PPApp
{
	/**
	 * Applicable only when Acymailing is installed
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function _isApplicable(PPAppTriggerableInterface $refObject, $eventname = '')
	{
		return $this->helper->exists();
	}

	/**
	 * Triggered after subscription is saved
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansSubscriptionAfterSave($prev, $new)
	{
		// Do not need to trigger if previous and current state is same
		if ($prev != null && $prev->getStatus() == $new->getStatus()) {
			return true;
		}

		// Retrieve the buyer user id
		$user = $new->getBuyer();
		$userId = $user->getId();

		if (!$this->helper->exists()) {
			$message = JText::_('COM_PAYPLANS_LOGGER_ACYMAILING_LOG_FILE_NOT_FOUND');

			$previous = [
				'added_user' => $userId, 
				'message' => $message
			];

			$content = [
				'previous' => $previous
			];

			PP::logger()->log(PPLogger::LEVEL_INFO, $message, $this->getId(), $content, 'PayplansAppAcymailingFormatter');
			return false;
		}

		$subscriptionId = $new->getId();
		$remove = [];

		// when subscription status is active
		if ($new->isActive()) {

			$addToListActive = $this->getAppParam('addToListonActive');

			if (is_null($addToListActive) || !$addToListActive) {
				$addToListActive = [];	
			}

			$addToListHold = $this->getAppParam('addToListonHold');

			if (is_null($addToListHold) || !$addToListHold) {
				$addToListHold = [];	
			}

			$addToListExpire = $this->getAppParam('addToListonExpire');

			if (is_null($addToListExpire) || !$addToListExpire) {
				$addToListExpire = [];	
			}

			$remove = array_merge($addToListHold, $addToListExpire);

			$this->helper->addOrRemoveFromAcymailingList($userId, $addToListActive, $remove, $subscriptionId);
			
			// forcefully remove the user from provided mailing list, irrespective of plan subscription.
			$removeFromListActive = $this->getAppParam('removeFromDefault');

			if (!empty($removeFromListActive)) {
				$this->helper->removeForcefully($userId, $removeFromListActive);
			}

			return true;
		}

		// when subscription status is hold
		if ($new->isOnHold()) {

			$addToListHold = $this->getAppParam('addToListonHold');

			if (is_null($addToListHold) || !$addToListHold) {
				$addToListHold = [];
			}

			$addToListActive = $this->getAppParam('addToListonActive');

			if (is_null($addToListActive) || !$addToListActive) {
				$addToListActive = [];	
			}

			$addToListExpire = $this->getAppParam('addToListonExpire');

			if (is_null($addToListExpire) || !$addToListExpire) {
				$addToListExpire = [];	
			}

			$remove = array_merge($addToListActive, $addToListExpire);

			return $this->helper->addOrRemoveFromAcymailingList($userId, $addToListHold, $remove, $subscriptionId);
		}
		
		// when subscription status is expire
		if ($new->isExpired()) {

			$addToListExpire = $this->getAppParam('addToListonExpire');

			if (is_null($addToListExpire) || !$addToListExpire) {
				$addToListExpire = [];
			}

			$addToListActive = $this->getAppParam('addToListonActive');

			if (is_null($addToListActive) || !$addToListActive) {
				$addToListActive = [];	
			}

			$addToListHold = $this->getAppParam('addToListonHold');

			if (is_null($addToListHold) || !$addToListHold) {
				$addToListHold = [];	
			}

			// Check if other app is applicable for same active acymailing list
			$state = $this->helper->checkUserSubscription($user, $addToListActive);

			if ($state) {
				return true;
			}

			$remove = array_merge($addToListActive, $addToListHold);

			return $this->helper->addOrRemoveFromAcymailingList($userId, $addToListExpire, $remove, $subscriptionId);
		}

	}

	/**
	 * Retrieve the name from the resource
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function getNameFromResourceValue($resource, $value)
	{
		// if its a different resource
		if ($resource != $this->_resource) {
			return false;
		}
		
		// Retrieve a list of list name from Acymailing
		$result = $this->helper->listsName($value);
		
		return $result;
	}
}
