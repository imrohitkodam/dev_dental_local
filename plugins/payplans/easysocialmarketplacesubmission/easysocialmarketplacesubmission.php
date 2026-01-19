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

class plgPayplansEasysocialmarketplacesubmission extends PPPlugins
{
	/**
	 * Triggered by Joomla system events
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onAfterRoute()
	{
		$controller = $this->input->get('controller', '', 'default');
		$task = $this->input->get('task', '', 'cmd');

		if ($controller !== 'marketplaces' || $task !== 'selectCategory') {
			return true;
		}

		$user = PP::user();

		// Do nothing when logged in user is admin
		if ($user->isAdmin()) {
			return true;
		}
		
		$categoryId = $this->input->get('category_id');
		$userId = $user->id;

		$helper = $this->getAppHelper();

		// check if category applicable or not on this app.
		if (!$helper->isCategoryApplicable($categoryId)) {
			return true;
		}

		if (!$helper->isAllowed($categoryId, $userId)) {
			return $this->redirectDisallowed();
		}

		return;
	}

	/**
	 * Triggered when category selected on marketplace story form.
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function onEasySocialSelectMarketplaceCategoryUser(&$user, &$category, &$canCreateInCategory)
	{
		// Do nothing when logged in user is admin
		if ($user->isSiteAdmin()) {
			return;
		}

		$helper = $this->getAppHelper();
		$categoryId = $category->id;
		$userId = $user->id;

		// check if category applicable or not on this app.
		if (!$helper->isCategoryApplicable($categoryId)) {
			return;
		}

		if (!$helper->isAllowed($categoryId, $userId)) {
			return $this->redirectDisallowed();
		}

		return;
	}

	/**
	 * Triggered when new page after save.
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function onMarketplaceBeforeSaveUser(&$marketplace, &$user, $isNew)
	{
		// Do nothing when logged in user is admin
		if ($user->isSiteAdmin()) {
			return true;
		}
		
		// do not process further.
		if (!$isNew) {
			return;
		}

		$helper = $this->getAppHelper();
		$categoryId = $marketplace->category_id;
		$userId = $user->id;

		// check if category applicable or not on this app.
		if (!$helper->isCategoryApplicable($categoryId)) {
			return;
		}

		if (!$helper->isAllowed($categoryId, $userId)) {
			return $this->redirectDisallowed();
		}

		return true;
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
		$message = JText::_('COM_PP_APP_EASYSOCIALMARKETPLACESUBMISSION_NOT_ALLOWED_MORE_MARKETPLACE');

		$doc = JFactory::getDocument();

		if ($doc->getType() != 'html') {
			return ES::ajax()->reject(ES::exception($message))->send();
		}

		PP::info()->set($message, 'error');
		return PP::redirect($redirect);
	}
}
