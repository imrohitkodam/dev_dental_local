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

class plgPayplansJoomlaarticlerestriction extends PPPlugins
{
	public function __construct(&$subject, $config = array())
	{
		parent::__construct($subject, $config);

		$this->helper = $this->getAppHelper();

		return true;
	}


	public function onPayplansAccessCheck(PPUser $user)
	{
		// Skip this check if the user is admin
		if ($user->isAdmin()) {
			return false;
		}

		$option = $this->input->get('option');
		$task = $this->input->get('task');
		
		$message = JText::_('COM_PP_APP_JOOMLA_ARTICLE_RESTRICTION_YOU_ARE_NOT_ALLOWED_TO_ADD_ENTRY_IN_SELECTED_CATEGORY');

		// Retrieve current user subscribed plans
		$userPlans = $user->getPlans();

		// Only run this if this is submitting entry in sobipro
		if ($option != 'com_content' ||  $task != 'article.save') {
			return false;
		}

		$appExist = $this->helper->hasJoomlaArticleApp();

		$url = PPR::_('index.php?option=com_payplans&view=plan&task=subscribe');

		if ($task == 'article.save') {


			// Do not do anything if there do not have any subipro app
			if (!$appExist) {
				return false;
			}

			// Do not do anything if current user do not have any subscribed plan
			if ($appExist && !$userPlans) {
				PP::info()->set($message, 'info');
				return PP::redirect($url);
			}

			// Get the category id of that entry
			$params = $this->input->get('jform', array(), 'array');

			$categoryId = $params['catid'];
			$categoryIds[] = $categoryId;

			// Check for submission restriction
			$isRestricted =	$this->helper->restrictSubmission($categoryIds, $user, $categoryId);
			if ($isRestricted) {
				return true;
			}

			PP::info()->set($message, 'info');
			return PP::redirect($url);			
		}

		return true;
	}

}
