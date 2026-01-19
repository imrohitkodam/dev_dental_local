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

class PPHelperEasysocialgroup extends PPHelperStandardApp
{

	/**
	* Determines if EasySocial is installed
	*
	* @since	4.0.0
	* @access	public
	*/
	public function exists()
	{
		static $exists = null;

		if (is_null($exists)) {
			$lib = PP::easysocial();
			$exists = false;

			if ($lib->exists()) {
				$exists = true;
			}
		}

		return $exists;
	}

	/**
	 * Retrieve Easysocial Groups
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getAllEasysocialGroups()
	{
		$lib = PP::easysocial();
		$groups = $lib->getGroups();

		return $groups;
	}


	/**
	 * Add user to the Easysocial Group
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function addUserToGroup($userId, $groupId)
	{
		$group = ES::group($groupId);
		return $group->createMember($userId, true);
	}

	/**
	 * Remove user from specific Easysocial group
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function removeUserFromGroup($userId, $groupId)
	{
		$group = ES::group($groupId);
		return $group->deleteMember($userId);
	}

	/**
	 * Method to remove selected Easysocial group from the user
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function removeForcefully($userId, $removalList, $subId, $groups)
	{
		$removeFromGroups = is_array($removalList) ? $removalList : array($removalList);

		foreach ($removeFromGroups as  $remGrpId) {
			$this->removeUserFromGroup($userId, $remGrpId);

			$user = PP::user($userId);

			$message = JText::_("COM_PAYPLANS_APP_EASYSOCIALGROUP_LOG_REMOVE_FROM_GROUP");
			$content = [
				'User Name' => $user->getName(), 
				'Usergroup' => $groups[$remGrpId]->title, 
				'Subscription Id' => $subId
			];

			PP::logger()->log(PPLogger::LEVEL_INFO, $message, $this->app->getId(), 'SYSTEM', $content, 'PayplansAppEasysocialgroupFormatter', md5(serialize($content)));
		}

		return true;
	}
}