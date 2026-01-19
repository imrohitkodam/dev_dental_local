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

class PPAppEasysocialgroupsubmission extends PPApp
{
	protected $_location = __FILE__;
	protected $_resource = 'com_easysocial.groupcategory.submission';

	/**
	 * Applicable only when EasySocial is installed
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

		// handling group pubish or unpublish
		$this->processGroups($prev, $new);

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


	/**
	 * Process user subscription
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function processGroups($prev, $new)
	{
		$subId =  $new->getId();

		$statusToPublish = $this->getAppParam('statusPublished', array());
		$statusToUnpublish = $this->getAppParam('statusUnpublished', array());
		
		$restrictType = $this->helper->getRestrictedType();

		if ($restrictType === false) {
			// look like this app is not being configured properly. abort here.
			return true;
		}

		$restrictCategories = $this->helper->getRestrictedCategories();
		$user =  $new->getBuyer();
		$userId = $user->getId();

		$subStatus = $new->getStatus();
		
		$groups = $this->helper->getUserEasysocialGroups($userId, $restrictType, $restrictCategories);

		// perform group unpublish.
		if ($groups && in_array($subStatus, $statusToUnpublish)) {

			$this->unpublishGroups($subId, $user, $groups);
		}

		// perform group unpublish.
		if ($groups && in_array($subStatus, $statusToPublish)) {
			
			$this->publishGroups($subId, $user, $groups);
		}

		return true;
	}

	/**
	 * Publish user's created easysocial groups.
	 *
	 * @since	4.2.0
	 * @access	private
	 */
	private function publishGroups($subId, $user, $groups = array())
	{
		if (!is_array($groups) || empty($groups)) {
			return true;
		}

		// lets filter unpublished groups.
		$data = array();

		foreach ($groups as $groupId => $group) {
			if ($group->state == SOCIAL_CLUSTER_UNPUBLISHED) {
				$data[$groupId] = $group;
			}
		}

		if ($data) {

			$groupIds = array_keys($data);

			// let batch update the groups.
			$state = $this->helper->toggleState(SOCIAL_CLUSTER_PUBLISHED, $groupIds);

			foreach ($data as $group) {

				$message = JText::_("COM_PP_APP_EASYSOCIALGROUPSUBMISSION_LOG_PUBLISH_GROUPS");
				$content = array('User Name' => $user->getName(), 'Easysocial Group' => $group->title,'Subscription Id' => $subId);

				PP::logger()->log(PPLogger::LEVEL_INFO, $message, $this->getId(), 'SYSTEM', $content, 'PayplansAppEasysocialgroupsubmissionFormatter', md5(serialize($content)));
			}
		}

		return true;
	}


	/**
	 * Unpublish user's created easysocial groups.
	 *
	 * @since	4.2.0
	 * @access	private
	 */
	private function unpublishGroups($subId, $user, $groups = array())
	{
		if (!is_array($groups) || empty($groups)) {
			return true;
		}

		// lets filter unpublished groups.
		$data = array();

		foreach ($groups as $groupId => $group) {
			if ($group->state == SOCIAL_CLUSTER_PUBLISHED) {
				$data[$groupId] = $group;
			}
		}

		if ($data) {

			$groupIds = array_keys($data);

			// let batch update the groups.
			$state = $this->helper->toggleState(SOCIAL_CLUSTER_UNPUBLISHED, $groupIds);

			foreach ($data as $group) {

				$message = JText::_("COM_PP_APP_EASYSOCIALGROUPSUBMISSION_LOG_UNPUBLISH_GROUPS");
				$content = array('User Name' => $user->getName(), 'Easysocial Group' => $group->title,'Subscription Id' => $subId);

				PP::logger()->log(PPLogger::LEVEL_INFO, $message, $this->getId(), 'SYSTEM', $content, 'PayplansAppEasysocialgroupsubmissionFormatter', md5(serialize($content)));
			}
		}

		return true;
	}
}
