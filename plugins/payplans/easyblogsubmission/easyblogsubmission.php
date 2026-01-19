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

class plgPayplansEasyblogSubmission extends PPPlugins
{
	/**
	 * Determines which category to be shown to the author
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onEasyBlogPrepareComposerCategories(&$categories, $selectedCategories)
	{
		$user = PP::user();

		if (PP::isFromAdmin() || $user->isAdmin()) {
			return true;
		}

		if (!$categories) {
			return;
		}

		$selectedIds = array();

		// If there are selected categories, it means user is trying to edit the post.
		if ($selectedCategories) {
			foreach ($selectedCategories as $selectedCategory) {
				$selectedIds[$selectedCategory->id] = $selectedCategory->id;
			}
		}

		$uid = $this->input->get('uid', '', 'string');
		$post = EB::Post($uid);

		if (!$post->isNew() && $selectedIds) {
			foreach ($categories as $index => $category) {

				if (!in_array($category->id, $selectedIds)) {
					unset($categories[$index]);
				}
			}

			// so that it reset the array index to start from 0
			$categories = array_values($categories);
			return;
		}

		$helper = $this->getAppHelper();
		$user = PP::user();

		foreach ($categories as $index => $category) {

			// Remove the selected category from the list since they have no access to post into it
			if (!$helper->isAllowedInCategory($category->id, $user->id)) {

				// Determine if there pre-selected categories in the post
				if (!$selectedIds || ($selectedIds && !isset($selectedIds[$category->id]))) {
					unset($categories[$index]);
				}
			}
		}

		$categories = array_values($categories);

		//If catgeories blank then redirect to plan page
		if (!$categories) {
			return $this->redirectDisallowed();
		}
	}

	/**
	 * Triggered after a blog post is being deleted
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onAfterEasyBlogDelete(EasyBlogPost $post, $isNew = false)
	{
		$option = $this->input->get('option', '', 'default');

		if ($option != 'com_easyblog') {
			return;
		}

		// Ensure that the user is logged in
		PP::requireLogin();

		$user = PP::user($post->getAuthor()->id);
		if ($user->isAdmin()) {
			return true;
		}

		// no need to verify if user is saving this as a draft.
		if ($post->isBlank() || $post->isDraft()) {
			return true;
		}



		$categories = $post->getCategories();
		if (!$categories) {
			return true;
		}

		$cats = array();
		foreach ($categories as $cat) {
			$cats[] = $cat->id;
		}

		$helper = $this->getAppHelper();
		
		$apps = $this->getAvailableApps('easyblogsubmission');

		$isApplicable = false;
		$hasAny = false;
		$hasSingleCat = false;
		$anyCatUsages = 0;
		$catUsages = array();

		if ($apps) {
			foreach ($apps as $app) {
				$addentryin = $app->getAppParam('add_entry_in');

				if ($addentryin == 'any_category') {
					$anyCatUsages = $app->getAppParam('no_of_submisssion', 0);
					$isApplicable = true;
					$hasAny = true;
				}

				if ($addentryin != 'any_category') {
					$appCategories = $app->getAppParam('add_entry_in_category');

					foreach ($appCategories as $ac) {
						$catUsages[$ac] = $app->getAppParam('no_of_submisssion', 0);
					}

					$usedCats = array_intersect($cats, $appCategories);
					if ($usedCats) {
						$isApplicable = true;
						$hasSingleCat = true;
					}
				}
			}
		}

		if ($isApplicable && $hasSingleCat) {
			// now we need to give user back his resource.
			foreach ($cats as $cat) {
				$currentUsage = $helper->getCategoryUsage($cat, $user);

				$count = $currentUsage + 1;
				if ($count > $catUsages[$cat]) {
					$count = $catUsages[$cat];
				}

				// okay its safe to relocate the resource back to user.
				$helper->increase($cat, $user, $count);
			}
		}

		if ($isApplicable && $hasAny) {
			$currentUsage = $helper->getCategoryUsage('0', $user);

			$count = $currentUsage + count($cats);
			// make sure the limit do exceed its available value.
			if ($count > $anyCatUsages) {
				$count = $anyCatUsages;
			}

			$helper->increase('0', $user, $count);
		}

		return true;
	}

	/**
	 * Triggered before saving a blog post
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onBeforeEasyBlogSave(EasyBlogPost $post, $isNew = false)
	{
		$option = $this->input->get('option', '', 'default');
		$categoryId = $this->input->get('category_id', '', 'default');
		$categories = $this->input->get('categories', '', 'default');

		// if single category posted the create array of categories
		if ($categories == null) {
			$categories = array($categoryId);
		}

		if ($option != 'com_easyblog' || !$categoryId) {
			return;
		}

		// Ensure that the user is logged in
		PP::requireLogin();

		$user = PP::user();

		if ($user->isAdmin()) {
			return true;
		}

		// no need to verify if user is saving this as a draft.
		if ($post->isBeingCreated() || $post->isBeingDrafted() || $post->isBlank() || $post->isDraft()) {
			return true;
		}

		// dump($categoryId, $categories);
		if (!$post->isNew()) {

			$oriCategories = $post->getCategories();

			$oriCatIds = array();

			foreach ($oriCategories as $cat) {
				$oriCatIds[] = $cat->id;
			}

			$diff = array_diff($categories, $oriCatIds);
			if (!$diff) {
				// if no diference, we just let it pass.
				return true;
			}
		}

		$helper = $this->getAppHelper();
		$allowed = $helper->isAllowed($user);

		if (!$allowed) {
			// save post as draft	
			$post->published = EASYBLOG_POST_DRAFT;
			$post->save(array('validateData' => false));	
			
			return $this->redirectDisallowed();
		}

		// check if any app applicable for category
		$userId = $user->getId();

		foreach ($categories as $categoryId) {
			$allowedCat = $helper->isAllowedInCategory($categoryId, $userId);

			if (!$allowedCat) {
				// save post as draft	
				$post->published = EASYBLOG_POST_DRAFT;
				$post->save(array('validateData' => false));	

				return $this->redirectDisallowed();
			}
				
			// The user is allowed, check their limits
			$exceededLimit = $helper->exceededLimit($categoryId, $user);

			// Decrease their limits
			if (!$exceededLimit) {
				$helper->decreaseAll(0, $user);
				$helper->decreaseAll($categoryId, $user);
			}
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
		$message = JText::_('COM_PAYPLANS_APP_EASYBLOG_SUBMISSION_NOT_ALLOWED_MORE_POST');

		$doc = JFactory::getDocument();

		if ($doc->getType() != 'html') {
			return EB::ajax()->reject(EB::exception($message))->send();
		}

		PP::info()->set($message, 'error');
		return PP::redirect($redirect);
	}

}