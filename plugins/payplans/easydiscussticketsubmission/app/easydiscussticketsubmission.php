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

class PPAppEasydiscussticketsubmission extends PPApp
{
	protected $_location = __FILE__;
	protected $_resource = 'com_discuss.category.submission';

	/**
	 * Applicable only when EasyDiscuss is installed
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function _isApplicable(PPAppTriggerableInterface $refObject, $eventName = '')
	{
		return $this->helper->exists();
	}

	/**
	 * Trigger event after user subscription is saved
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function onPayplansSubscriptionAfterSave($prev, $new)
	{
		// no need to trigger if previous and current state is same
		if ($prev == null || ($prev->getStatus() == $new->getStatus())) {
			return true;
		}

		// handling user's resources.
		$this->processResource($prev, $new);

		return true;
	}


	/**
	 * Process user resource
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function processResource($prev, $new)
	{
		$restrictType = $this->helper->getRestrictedType();
		if ($restrictType === false) {
			// look like this app is not being configured properly. abort here.
			return true;
		}

		$subscriptionId = $new->getId();
		$user = $new->getBuyer();
		$userId = $user->id;
		$total = $this->helper->getTotalSubmission();

		$restrictCategories = $this->helper->getRestrictedCategories();

		// Addition
		if ($new->isActive()) {

			if ($restrictType != 'restrict_specific') {
				$this->_addToResource($subscriptionId, $userId, 0, $this->_resource, $total);
				return true;
			}

			if ($restrictCategories) {
				foreach ($restrictCategories as $category) {
					$this->_addToResource($subscriptionId, $userId, $category, $this->_resource, $total);
				}
			}

			return true;
		}
		
		// Removal
		if ($prev->isActive() && !$new->isActive()) {

			if ($restrictType != 'restrict_specific') {
				$this->_removeFromResource($subscriptionId, $userId, 0, $this->_resource, $total);
				return true;
			}

			if ($restrictCategories) {
				foreach ($restrictCategories as $category) {
					$this->_removeFromResource($subscriptionId, $userId, $category, $this->_resource, $total);
				}
			}
		}

	}

}
