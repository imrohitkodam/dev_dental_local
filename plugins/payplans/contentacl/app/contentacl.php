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

class PPAppContentacl extends PPApp
{
	public function isApplicable($refObject = null, $eventName = '')
	{
		$app = JFactory::getApplication();
		$option = $app->input->get('option', '', 'default');
		$view = $app->input->get('view', '', 'default');

		if ($eventName === 'onContentPrepare' && $option == 'com_content' && $view == 'article') {
			return true;
		}

		if ($eventName === 'onContentPrepare' && $option == 'com_easyblog' && $view == 'entry') {
			return true;
		}

		return false;
	}

	/**
	 * Joomla trigger when viewing an article
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		$contentType = $this->getAppParam('block_j17', 'none');
		$restrctionType = $this->getAppParam('restriction_layout', 'default');

		// validate for current page whether this is the page we looking for
		$isValidContentRestrict = $this->helper->validateContentPage($contentType, $context);
		
		if (!$isValidContentRestrict) {
			return true;
		}

		// IMPORTANT:
		// since this app will be trigger multiple times
		// we need a controller to controller how the 'non-applyAll' behaviour.
		// each block will responsible to only applyAll. only article block will check for both applyAll and non-applyAll.
		// thats mean to say, article is the controller.
		if ($contentType == 'joomla_category' || $contentType == 'easyblog_category') {
			$this->helper->processCategory($context, $article, $params, $page, $contentType, $restrctionType);
		}

		if ($contentType == 'joomla_article' || $contentType == 'easyblog_article') {
			$this->helper->processArticle($context, $article, $params, $page, $contentType, $restrctionType);
		}

		return true;
	}
}
