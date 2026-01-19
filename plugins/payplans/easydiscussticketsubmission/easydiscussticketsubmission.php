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

class plgPayplansEasydiscussticketsubmission extends PPPlugins
{

	/**
	 * Triggered before saving a blog post
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onContentBeforeSave($context = 'post', $data = '', $isNew = null)
	{
		if ($context != 'com_easydiscuss.post') {
			return true;
		}

		$user = PP::user();

		// Do nothing when admin user post ticket
		if (PP::isFromAdmin() || $user->isAdmin()) {
			return true;
		}

		$categoryId = $this->input->get('category_id', '', 'default');

		$helper = $this->getAppHelper();
		$categoryAllowed = $helper->isCategoryAllowed($categoryId);

		// if category not restricted 
		if ($categoryAllowed) {
			return true;
		}

		// Ensure that the user is logged in
		PP::requireLogin();

		$userId = $user->getId();

		$allowed = $helper->isAllowed($userId, $categoryId);

		if (!$allowed) {
			$this->redirectDisallowed();
		}

		return true;
	}

	/**
	 * Standard redirection method
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function redirectDisallowed()
	{
		$helper = $this->getAppHelper();
		$redirect = $helper->getRedirectPlanLink();
		$message = JText::_('COM_PAYPLANS_APP_EASYDISCUSS_SUBMISSION_NOT_ALLOWED_MORE_POST');

		PP::info()->set($message, 'error');
		return PP::redirect($redirect);
	}
}
