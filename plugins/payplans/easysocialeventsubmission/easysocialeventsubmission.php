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

$file = JPATH_ADMINISTRATOR . '/components/com_payplans/includes/payplans.php';

if (!JFile::exists($file)) {
	return;
}

require_once($file);

class plgPayplansEasysocialeventsubmission extends PPPlugins
{

	/**
	 * Triggered on selecting easysocial's event category
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function onEasySocialSelectCategoryEvent(SocialUser &$user, &$clusterCategory, &$canCreateInCategory)
	{
		$helper = $this->getAppHelper();
		$categoryId = $clusterCategory->id;
		$userId = $user->id;

		// check if category applicable or not on this app.
		if (!$helper->isCategoryApplicable($categoryId)) {
			return;
		}

		if (!$helper->isAllowed($categoryId, $userId)) {
			//$canCreateInCategory = false;
			return $this->redirectDisallowed();
		}

		return;
	}

	/**
	 * Triggered when new event after save.
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function onEventBeforeSaveUser(&$event, &$user, $isNew)
	{
		// do not process further.
		if (!$isNew) {
			return;
		}

		$helper = $this->getAppHelper();
		$categoryId = $event->getCategory()->id;
		$userId = $user->id;

		// check if category applicable or not on this app.
		if (!$helper->isCategoryApplicable($categoryId)) {
			return;
		}

		if (!$helper->isAllowed($categoryId, $userId)) {
			return $this->redirectDisallowed();
		}

		$userId = $user->id;
		$currentAvailableCount = $helper->getCurrentAvailableUsage($categoryId, $userId);

		if (($currentAvailableCount - 1) >= 0) {
			$helper->updateResource('decrease', $categoryId, $userId);
		}
	}


	/**
	 * Triggered when easysocial event after deleted
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function onEventAfterDeleteUser($event, $state)
	{
		$helper = $this->getAppHelper();
		$categoryId = $event->getCategory()->id;

		$user = $event->getCreator();
		$userId = $user->id;

		// check if category applicable or not on this app.
		if (!$helper->isCategoryApplicable($categoryId)) {
			return;
		}

		$userId = $user->id;
		$currentAvailableCount = $helper->getCurrentAvailableUsage($categoryId, $userId);

		$totalSubmission = $helper->getTotalSubmission();

		if (($currentAvailableCount + 1) <= $totalSubmission) {
			$helper->updateResource('increase', $categoryId, $userId);
		}
	}

	/**
	 * Standard redirection method
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function redirectDisallowed()
	{
		$helper = $this->getAppHelper();
		$redirect = $helper->getRedirectPlanLink();
		$message = JText::_('COM_PP_APP_EASYSOCIALEVENTSUBMISSION_NOT_ALLOWED_MORE_EVENT');

		$doc = JFactory::getDocument();
		if ($doc->getType() != 'html') {
			return ES::ajax()->reject(ES::exception($message))->send();
		}

		PP::info()->set($message, 'error');
		return PP::redirect($redirect);
	}

}
