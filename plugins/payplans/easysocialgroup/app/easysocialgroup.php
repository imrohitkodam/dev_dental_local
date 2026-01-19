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

class PPAppEasysocialgroup extends PPApp
{
	protected $_location = __FILE__;
	protected $_resource = 'com_easysocial.group';

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
	 * @since   4.0.0
	 * @access  public
	 */
	public function onPayplansSubscriptionAfterSave($prevSubscription, $newSubscription)
	{
		return $this->processSubscription($prevSubscription, $newSubscription);
	}

	/**
	 * Process user subscription
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function processSubscription($prevSubscription, $newSubscription)
	{
		// no need to trigger if previous and current state is same
		if ($prevSubscription != null && $prevSubscription->getStatus() == $newSubscription->getStatus()) {
			return true;
		}

		$subId =  $newSubscription->getId();

		$active = $this->getAppParam('esgroupOnActive', array());
		$hold = $this->getAppParam('esgroupOnHold', array());
		$expire = $this->getAppParam('esgroupOnExpire', array());

		$user =  $newSubscription->getBuyer();
		$userId = $user->getId();

		$active = (is_array($active)) ? $active : array($active);
		$hold = (is_array($hold)) ? $hold : array($hold);
		$expire = (is_array($expire)) ? $expire : array($expire);
		
		$groups = $this->helper->getAllEasysocialGroups();

		// Process active subscription
		if ($newSubscription->isActive()) {

			$holdActiveDiff = array_diff($hold, $active);
			$expireActiveDiff = array_diff($expire, $active);

			$result = $this->setGroup($userId, $active, $subId, $groups);
			
			$this->unsetGroup($userId, $holdActiveDiff, $subId, $groups);
			$this->unsetGroup($userId, $expireActiveDiff, $subId, $groups);

			//forcefully removes the user from provided user group, irrespective of plan subscription.
			$removeFromListActive = $this->getAppParam('removeFromGroup');

			if (!empty($removeFromListActive)) {
				return $this->helper->removeForcefully($userId, $removeFromListActive, $subId, $groups);
			}

			return $result;
		}

		// Process on hold subscription
		if ($newSubscription->isOnHold()) {

			$activeHoldDiff = array_diff($active, $hold);
			$expireHoldDiff = array_diff($expire, $hold);

			$result = $this->setGroup($userId, $hold, $subId, $groups);

			$this->unsetGroup($userId, $activeHoldDiff, $subId, $groups);
			$this->unsetGroup($userId, $expireHoldDiff, $subId, $groups);

			return $result;
		}

		// Process expired subscription
		if ($newSubscription->isExpired()) {

			$activeExpireDiff = array_diff($active, $expire);
			$holdExpireDiff = array_diff($hold, $expire);

			$result = $this->setGroup($userId, $expire, $subId, $groups);

			$this->unsetGroup($userId, $activeExpireDiff, $subId, $groups);
			$this->unsetGroup($userId, $holdExpireDiff, $subId, $groups);

			return $result;
		}

		return true;
	}

	/**
	 * Assign joomla user type to the user
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function setGroup($userId, $group, $subId, $groups)
	{
		if (!is_array($group) || empty($group)) {
			return true;
		}

		foreach ($group as $groupId) {

			$this->helper->addUserToGroup($userId, $groupId);
			PP::resource()->add($subId, $userId, $groupId, $this->_resource);

			$user = PP::user($userId);

			$message = JText::_("COM_PAYPLANS_APP_EASYSOCIALGROUP_LOG_ADD_INTO_GROUP");
			$content = [
				'User Name' => $user->getName(), 
				'Easysocial Group' => $groups[$groupId]->title,
				'Subscription Id' => $subId
			];

			PP::logger()->log(PPLogger::LEVEL_INFO, $message, $this->getId(), 'SYSTEM', $content, 'PayplansAppEasysocialgroupFormatter', md5(serialize($content)));
		}

		return true;
	}

	/**
	 * Unset joomla user type from the user
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function unsetGroup($userId, $group, $subId, $groups)
	{
		if (!is_array($group) || empty($group)) {
			return true;
		}

		foreach ($group as $groupId) {

			if (PP::resource()->remove($subId, $userId, $groupId, $this->_resource)) {
				$this->helper->removeUserFromGroup($userId, $groupId);

				$user = PP::user($userId);

				$message = JText::_("COM_PAYPLANS_APP_EASYSOCIALGROUP_LOG_REMOVE_FROM_GROUP");
				$content = [
					'User Name'=> $user->getName(), 
					'Easysocial Group' => $groups[$groupId]->title, 
					'Subscription Id'=> $subId
				];

				PP::logger()->log(PPLogger::LEVEL_INFO, $message, $this->getId(), 'SYSTEM', $content, 'PayplansAppEasysocialgroupFormatter', md5(serialize($content)));
			}
		}

		return true;
	}

	/**
	 * Retrieve the name from the resource
	 *
	 * @since   1.0
	 * @access  public
	 */
	public function getNameFromResourceValue($resource, $value)
	{
		// if its a different resource
		if ($resource != $this->_resource) {
			return false;
		}

		$groups = $this->helper->getAllEasysocialGroups();
		return $groups[$value]->title;
	}

	/**
	 * Trigger during cleaning the resources such as deleting subscriptions order
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansSubscriptionCleanResource($sub)
	{
		$userId = $sub->getBuyer();
		$subId = $sub->getId();
		$groups = $this->helper->getAllUserGroups();

		$options = [];
		$options['subscription_ids'] = $subId;
		$options['title'] = $this->_resource;

		$resourcesModel = PP::model('resource');
		$resources = $resourcesModel->getRecords($options);
		
		//Imp : before unseting usergroup ensure that user must have any other usergroup attached
		//if no other usergroup is attached apart from the one which is going to be unset then add user to default usergroup
		foreach ($resources as $res) {
			$subscriptionIds = explode(',', PPJString::trim($res->subscription_ids, ','));
			$subscriptionIds = array_unique($subscriptionIds);

			foreach ($subscriptionIds as $key => $value) {
				PP::resource()->remove($subId, $userId, $res->value, $this->_resource);
			}
		}
		 return true;
	}
}
