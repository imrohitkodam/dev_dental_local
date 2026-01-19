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

class PPHelperAcymailing6 extends PPHelperStandardApp
{
	protected $_location = __FILE__;
	protected $_resource = 'com_acym.list';

	/**
	 * Determines if Acymailing exists on the site
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function exists()
	{
		static $exists = null;

		if (is_null($exists)) {
			$enabled = JComponentHelper::isEnabled('com_acym');
			$file = JPATH_ROOT . '/administrator/components/com_acym/helpers/helper.php';

			$fileExists = JFile::exists($file);
			$exists = false;

			if ($enabled && $fileExists) {
				$exists = true;
				require_once($file);
			}
		}

		return $exists;
	}

	/**
	 * Retrieve a list of list name from Acymailing
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function listsName($value)
	{
		$db = PP::db();
		$query = 'SELECT `name` FROM `#__acym_list` WHERE `id` = "' . $value . '"';
		$db->setQuery($query);
		$result = $db->loadResult();
		
		return $result;
	}

	/**
	 * Retrieve a list of listid from Acymailing
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function getAcymailingList()
	{
		$db = PP::db();

		$query = 'SELECT `id` as list_id, `name` FROM `#__acym_list`';
		$db->setQuery($query);
		$result = $db->loadObjectList('list_id');

		return $result;
	}

	/**
	 * Retrieve a list of listid from Acymailing
	 *
	 * @since	4.2.11
	 * @access	public
	 */
	public function getAcymailingListIds()
	{
		$db = PP::db();

		$query = 'SELECT `id` FROM `#__acym_list` WHERE `active` = 1';
		
		$db->setQuery($query);
		$result = $db->loadObjectList();

		if ($result) {

			$listIds = [];

			foreach ($result as $list) {
				$listIds[] = $list->id;
			}
			
			return $listIds;
		}

		return [];
	}

	/**
	 * Forcefully remove the user from provided mailing list, irrespective of plan subscription.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function removeForcefully($userId, $removalList) 
	{
		$removeFromListActive = is_array($removalList) ? $removalList : array($removalList);

		$lib = acym_get('class.user');
		$unsubscribedList = [];
		$result = false;

		// Retrieve a list of list ids
		$currentListIds = $this->getAcymailingListIds();

		foreach ($removeFromListActive as $remListId) {

			// skip the unavailable id if the list id no longer exist on the site
			if (!in_array($remListId, $currentListIds)) {
				continue;
			}

			$unsubscribedList[] = $remListId;
		}

		if ($unsubscribedList) {

			// Or you can get the AcyMailing user from his site user ID
			$user = $lib->getOneByCMSId($userId);
			$result = $lib->unsubscribe($user->id, $unsubscribedList);
		}

		return $result;
	}

	/**
	 * Proceed add or remove subscription from Acymailing list
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function addOrRemoveFromAcymailingList($userid, $addToList, $removeFromList, $subscriptionId)
	{
		$lib = acym_get('class.user');
		$newSubscription = [];

		$user = PP::user($userid);

		$userExist = $lib->getOneByEmail($user->email);
		$subid = $userExist->id;

		if (!$subid) {
			$myUser = new stdClass();
			$myUser->email = $user->email;
			$myUser->name = $user->name;
		
			$subid = $lib->save($myUser);
		}

		// Retrieve a list of list ids
		$currentListIds = $this->getAcymailingListIds();

		// user subscribe to this list id
		$subscribe = is_array($addToList) ? $addToList : array($addToList); 

		// user who want to remove this list id
		$removeListIds = is_array($removeFromList) ? $removeFromList : array($removeFromList);

		if (!empty($removeListIds)) {

			foreach ($removeListIds as $listId) {
				
				if (is_array($listId)) {
					$this->addOrRemoveFromAcymailingList($userid, array(), $listId, $subscriptionId);

				} else {

					$status = $this->removeResource($subscriptionId, $userid, $listId, $this->_resource);

					// we didn't find the user in the AcyMailing tables
					if (empty($subid)) {
						return false;
					}

					// unset the array value if the list id no longer exist on the site
					if (!in_array($listId, $currentListIds)) {
						continue;
					}

					$result = $lib->unsubscribe($subid, $listId);
				}
			}
		}

		if (!empty($subscribe)) {

			foreach ($subscribe as $key => $listId) {

				// unset the array value if the list id no longer exist on the site
				if (!in_array($listId, $currentListIds)) {
					unset($subscribe[$key]);
					continue;
				}

				$newList = null;
				$newList['status'] = 1;
				$newSubscription[$listId] = $newList;

				$this->addResource($subscriptionId, $userid, $listId, $this->_resource);
			}
		}

		if (empty($newSubscription)) {
			return;
		}

		// we didn't find the user in the AcyMailing tables
		if (empty($subid)) {
			return false;
		}

		$result = $lib->subscribe($subid, $subscribe);

		return $result;
	}	
	
	/**
	 * Check ative subscription of user
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function checkUserSubscription($user, $acymailingListIds)
	{
		$userPlans = $user->getPlans();

		$planIds = [];
		foreach ($userPlans as $plan) {
			$planIds[] = $plan->getId();
		}

		$acymailingApp = PPHelperApp::getAvailableApps('acymailing6');
		foreach ($acymailingApp as $app) {
			
			// Get app plans
			$appPlans = $app->getPlans();

			
			if (array_intersect($planIds, $appPlans)) {

				// Do nothing id Profile not matched in app instance
				$activeListIds = $app->getAppParam('addToListonActive', 0);

				if (array_intersect($acymailingListIds, $activeListIds)) {
					return true;
				}
			}
		} 

		return false;
	}
}