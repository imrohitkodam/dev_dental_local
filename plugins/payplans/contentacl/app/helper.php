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

class PPHelperContentacl extends PPHelperStandardApp
{
	/**
	 * Determine for which content ACL types for the setting
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function normalizeExtensionContentType($type)
	{
		$type = explode('_', $type);
		$type = $type[0];

		return $type;
	}

	/**
	 * Determine for which content ACL types for the setting
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function normalizeContent($article, $type)
	{
		$app = JFactory::getApplication();
		$option = $app->input->get('option', '', 'default');
		$view = $app->input->get('view', '', 'default');
		
		$extensionName = $this->normalizeExtensionContentType($type);

		if ($option == 'com_easyblog' && $view == 'entry' && $extensionName == 'easyblog') {
			return $article->intro;
		}

		return $article->introtext;
	}

	/**
	 * Normalise the article category attribute name
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function normalizeCategory($article, $type)
	{
		$extensionName = $this->normalizeExtensionContentType($type);

		// For Easyblog extension
		if ($extensionName == 'easyblog') {

			$categories = array();
			$blogCategories = $this->getBlogCategories($article->id);

			foreach ($blogCategories as $cat) {
				$categories[] = $cat->id;
			}

			return $categories;
		}

		// This only for joomla article
		return $article->catid;
	}

	/**
	 * Determine for whether this is the correct article content we should restrict it.
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function validateContentPage($contentType, $context)
	{
		$app = JFactory::getApplication();
		$option = $app->input->get('option', '', 'default');
		$view = $app->input->get('view', '', 'default');

		$allowContextType = array('com_content.article', 'easyblog.blog');

		// Skip all this if that is not Easyblog and Joomla article content
		if (!in_array($context, $allowContextType)) {
			return false;
		}

		if ($this->isUserAllowed()) {
			return false;
		}

		if ($contentType == 'none' || !$contentType) {
			return false;
		}

		$joomlaContent = array('joomla_article', 'joomla_category');
		$easyblogContent = array('easyblog_article', 'easyblog_category');

		if ($option == 'com_content' && $view == 'article' && (in_array($contentType, $joomlaContent))) {
			return true;
		}
		
		if ($option == 'com_easyblog' && $view == 'entry' && (in_array($contentType, $easyblogContent))) {
			return true;
		}

		return false;
	}

	/**
	 * Construct plan url
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getPlanUrl()
	{
		return PPR::_('index.php?option=com_payplans&view=plan');
	}

	/**
	 * Process category ACL
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function processCategory($context, &$article, &$params, $page = 0, $contentType = '', $restrictionType = 'default')
	{
		// Determine which article content type e.g. Easyblog or Joomla article
		$extensionName = $this->normalizeExtensionContentType($contentType);

		$restrictedCatIds = $this->params->get($extensionName . '_category', 0);
		$restrictedCatIds = is_array($restrictedCatIds) ? $restrictedCatIds : array($restrictedCatIds);

		// Retrieve the article post category ids
		$catIds = $this->normalizeCategory($article, $extensionName);

		// Get user properties.
		$this->user = PP::user();

		// Admin should always able to read the article
		if ($this->user->isAdmin()) {
			return true;
		}

		// get all parent category of current category
		// For Easyblog retrieve a list of categories under this blog		
		$allCats = $this->getParentCategories($catIds, $extensionName);

		// compares the values of two arrays and returns the matches
		$tempArray = array_intersect($allCats, $restrictedCatIds);

		if (empty($tempArray)) {
			return true;
		}

		$allPlanData = array();

		if (!$this->app->getParam('applyAll', false)) {

			// we need to check if there is any other content app that is responsible for article since the article is the controller.
			$hasArticleApps = false;
			$contentApps = PPHelperApp::getAvailableApps('contentacl');

			foreach ($contentApps as $app) {
				
				$type = $app->getAppParam('block_j17', 'none');

				if ($type == $extensionName . '_article') {

					$allowedArticleIds = $app->getAppParam($extensionName . '_article', 0);

					$allowedArticleIds = is_array($allowedArticleIds) ? $allowedArticleIds : array($allowedArticleIds);

					$articleId 	= (isset($article->id)) ? $article->id : null;

					if (in_array($articleId, $allowedArticleIds)) {
						$hasArticleApps = true;
					}
				}
			}

			if (!$hasArticleApps) {

				if ($restrictionType == 'default') {

					$plans = $this->getPlan();

					if ($plans) {
						foreach ($plans as $plan) {
							$allPlanData[] = $plan;
						}
					}

				} else {
					$links = $this->getPlanlinks(true);

					$planLinks = '';
					$tmp="";
					if ($links) {
						foreach ($links as $link) {
							$tmp = $link ;
							$planLinks .= ($planLinks) ? ', ' . $tmp : $tmp;
						}
					}

					$plansLink = "<strong>". JText::_('COM_PAYPLANS_CONTENTACL_SUBSCRIBE_PLAN') ."</strong> (". $planLinks. ")";
				}
				
			}
		}

		if ($restrictionType == 'default') {

			$output = $this->generateTemplateContents($allPlanData);
			$content = $this->normalizeContent($article, $contentType);

			$article->text  = $content . $output;

			return;
		}

		$content = $this->normalizeContent($article, $contentType);

		if ($this->app->getParam('applyAll', false)) {
			$article->text = $content . '<a id="pp_contentacl_joomla_category" href="' . $this->getPlanUrl() . '">' . JText::_('COM_PAYPLANS_CONTENTACL_SUBSCRIBE_PLAN') . '</a>';
		} else {
			$article->text  = $content . $plansLink;
		}		
	}

	/**
	 * Process Article ACL
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function processArticle($context, &$article, &$params, $page = 0, $contentType = '', $restrictionType = 'default')
	{

		// Determine which article content type e.g. Easyblog or Joomla article
		$extensionName = $this->normalizeExtensionContentType($contentType);

		$restrictedArticleIds = $this->params->get($extensionName . '_article', 0);

		$restrictedArticleIds = is_array($restrictedArticleIds) ? $restrictedArticleIds : array($restrictedArticleIds);
		$articleId = (isset($article->id)) ? $article->id : null;

		// Get user properties.
		$this->user = PP::user();

		// Admin should always able to read the article
		if ($this->user->isAdmin()) {
			return true;
		}

		// Skip if the article is not restricted in any app
		if (!in_array($articleId, $restrictedArticleIds)) {
			return true;
		}

		$planData = array();
		$plans = array();
		$contentApps = PPHelperApp::getAvailableApps('contentacl');

		// Retrieve the article post category ids
		$categoryIds = $this->normalizeCategory($article, $extensionName);
		$planLinks = [];

		foreach ($contentApps as $app) {

			$type = $app->getAppParam('block_j17', 'none');

			if ($type == $extensionName . '_category') {

				// Check restricted categories first
				$resctrictedCatIds = $app->getAppParam($extensionName . '_category', 0);

				if ($resctrictedCatIds) {

					$resctrictedCatIds = is_array($resctrictedCatIds) ? $resctrictedCatIds : array($resctrictedCatIds);

					// get all parent category of current category
					// For Easyblog retrieve a list of categories under this blog
					$allCats = $this->getParentCategories($categoryIds, $extensionName);

					// compares the values of two arrays and returns the matches
					$tempArray = array_intersect($allCats, $resctrictedCatIds);

					if (!empty($tempArray)) {

						if (!$app->getParam('applyAll', false)) {

							$plans = $app->getPlans();
							$plans = $this->isValidPlan($plans);

							if (empty($plans)) {
								continue;
							}

							if ($restrictionType == 'default') {

								foreach ($plans as $plan) {
									$planData[] = PP::plan($plan);
								}
							} else {
								foreach ($plans1 as $plan) {
									$planName = $plan->title;
									$planLinks[] = '<a href="' . PPR::_('index.php?option=com_payplans&task=plan.subscribe&plan_id=' . $plan->plan_id) . '&tmpl=component">' . $planName . '</a>';
								}
							}
						}
					}
				}
			}
		}

		// Default Template
		if ($restrictionType == 'default') {
			$theme = PP::themes();

			$allPlanData = array();

			if (!$this->app->getParam('applyAll', false)) {

				// retrieve article's plan data
				$articlePlanData = $this->getPlan();
				$allPlan = array_merge($articlePlanData, $planData);

				$existingPlan = array();

				// make sure there is no duplicate plan
				foreach ($allPlan as $plan) {

					if (in_array($plan->getId(), $existingPlan)) {
						continue;
					}

					$allPlanData[] = $plan;
					$existingPlan[] = $plan->getId();
				}
			}

			$output = $this->generateTemplateContents($allPlanData);
			$content = $this->normalizeContent($article, $contentType);

			$article->text  = $content . $output;

			return;
		}

		$content = $this->normalizeContent($article, $contentType);

		if ($this->app->getParam('applyAll', false)) {
			$article->text = $content . '<a id="pp_contentacl_joomla_category" href="' . $this->getPlanUrl() . '">' . JText::_('COM_PAYPLANS_CONTENTACL_SUBSCRIBE_PLAN') . '</a>';

			return;
		}

		$artLinks  = $this->getPlanlinks(true);
		$allPlanLinks = array_merge($artLinks, $planLinks);
		$allPlanLinks = array_unique($allPlanLinks);

		$links = '';
		$tmp='';
		if ($allPlanLinks) {
			foreach ($allPlanLinks as $link) {
				$tmp = $link ;
				$links .= ($links) ? ', ' . $tmp : $tmp;
			}
		}

		$links = "<strong>". JText::_('COM_PAYPLANS_CONTENTACL_SUBSCRIBE_PLAN') ."</strong> (". $links. ")";

		$article->text  = $content . $links;
	}

	/**
	 * Retrieve plans data that need to be append to the content
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function getPlan()
	{
		$plans = $this->app->getPlans();

		$plans = $this->isValidPlan($plans);

		// Don't proceed this if don't have valid plans.
		if (!$plans) {
			return [];
		}

		$planData = array();

		foreach ($plans as $plan) {
			$planData[] = PP::plan($plan);
		}

		return $planData;
	}

	/**
	 * Determine if user is allowed here
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function isUserAllowed()
	{
		$user = PP::user();

		if (!$user->user_id) {
			return false;
		}

		$userSubs = $user->getPlans();

		// return false when user is non-subscriber
		if (empty($userSubs)) {
			return false;
		}

		// return true when app is core app,
		// no need to check whether plan is attached with this app or not
		if ($this->app->getParam('applyAll', false) != false) {
			return true;
		}

		$plans = $this->app->getPlans();
		
		// if user have an active subscription of the plan attached with the app then return true
		foreach ($userSubs as $sub) {
			$planId = $sub->getId();

			if (in_array($planId, $plans)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Retrieve all of the parent categories
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getParentCategories($catId, $extensionName)
	{
		if ($extensionName == 'easyblog') {

			$categories = $catId;

			return $categories;
		}

		$allCat = array();

		while ($catId) {
			$allCat[] = $catId;

			$db = PP::db();
			$query = 'SELECT `parent_id` FROM `#__categories`';
			$query .= ' WHERE `published` = 1 AND `id` = ' . $db->Quote($catId);

			$db->setQuery($query);
			$catId = $db->loadResult();
		}

		return $allCat;
	}

	/**
	 * Retrieve all of the categories for entry post
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getBlogCategories($postId)
	{
		$model = EB::model('Categories');
		$categories = $model->getBlogCategories($postId);

		return $categories;
	}

	/**
	 * Retrieve plans links that need to be append to the content
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getPlanlinks($returnAsArray = false)
	{
		$plans = $this->app->getPlans();

		$links = '<strong>' . JText::_('COM_PAYPLANS_CONTENTACL_SUBSCRIBE_PLAN') . '</strong> ( ';
		$plans = $this->isValidPlan($plans);

		// If plans are not vaild then show subscribe page link
		if ($plans == false) {
			$planLinks = '<a href="' . $this->getPlanUrl() . '">' . JText::_('COM_PAYPLANS_CONTENTACL_SUBSCRIBE_PLAN') . '</a>';

			if ($returnAsArray) {
				$planLinks = array($planLinks);
			}

			return $planLinks;
		}

		$planLinks = array();

		foreach ($plans as $plan) {
			$planName = $plan->title;
			$planLinks[] = '<a href="' . PPR::_('index.php?option=com_payplans&task=plan.subscribe&plan_id=' . $plan->plan_id) . '&tmpl=component">' . $planName . '</a>';
		}

		if ($returnAsArray) {
			return $planLinks;
		}

		$planLinks = implode(" , ", $planLinks);
		$links .= $planLinks;
		$links .= ' )';

		return $links;
	}

	/**
	 * Get login url
	 *
	 * @since   4.1.0
	 * @access  public
	 */
	public function getLoginUrl()
	{
		return JRoute::_('index.php?option=com_users&view=login', false);
	}

	/**
	 * Generates the default template for the protected area
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function generateTemplateContents($allPlanData = array())
	{
		// If we need to render the overlay, we need to load the site's stylesheet for payplans
		PP::initialize();

		$overlayRgb = PP::string()->hexToRGB($this->params->get('overlay_color', 'FFFFFF'));
		
		$theme = PP::themes();

		$theme->set('overlayRgb', $overlayRgb);
		$theme->set('allPlanData', $allPlanData);
		$theme->set('params', $this->params);
		$theme->set('user', $this->user);
		$theme->set('viewPlans', $this->getPlanUrl());
		$theme->set('loginUrl', $this->getLoginUrl());

		$output = $theme->output('apps:/contentacl/restricted');

		return $output;
	}

	/**
	 * Determine if the given plan ids is a valid plan
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function isValidPlan($plans)
	{
		$model = PP::model('Plan');
		$records = $model->getPlans($plans);

		if (!empty($records)) {
			return $records;
		}

		return false;
	}
}