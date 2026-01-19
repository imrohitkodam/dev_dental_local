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

class PPHelperJoomlaarticlerestriction extends PPHelperStandardApp
{
	const ALLOWED = 1;
	const BLOCKED = 0;

	/**
	 * Determine if this user is restricted to submit or not
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function restrictSubmission($categoryIds = [], $user = null, $sectionId = 0)
	{
		$allCategories = array();

		// Get all selected categories from the form
		foreach ($categoryIds as $categoryId) {
			$temp = $this->getAllParents($categoryId);
			$allCategories = array_merge($allCategories, $temp);
		}
		
		// We need treat section as category also
		$allCategories[] = 0;
		
		// Get restricted categories from existing apps
		$restrictedAppCategories = $this->getRestrictedAppCategories();

	
		// Filter out the categories that are not included in app config
		$count = 0;
		foreach ($allCategories as $categoryId) {

			// Unset those restricted categories id if not match with selected categories 
			if (!array_key_exists($categoryId, $restrictedAppCategories)) {
				unset($allCategories[$count]);
			}

			$count++;
		}

		// Skip this if there do not have return any categories 
		// which mean that there do not have match any restricted categories when user publish this new entry.
		if (empty($allCategories)) {
			return true;
		}

		// Ensure that all is unique category id
    	$allCategories = array_unique($allCategories);

    
		// Now check for individual category and store it's result in array
		$appResult = [];
		foreach ($allCategories as $categoryId) {
			$appResult[] = $this->isUserAllowed($user, $categoryId, $restrictedAppCategories);
		}

		// If appresult array contain BLOCKED then this user not allowed to post
		if (!in_array(self::ALLOWED, $appResult)) {
			return false;
		}

		return true;
	}

	/**
	 * Determine if user allowed or not
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function isUserAllowed($user, $categoryId, $restrictedAppCategories)
	{
		// Fetch all the user resource of that specific category
		$userResource = $this->getJoomlaAtricleResource($user->getId(), true, $categoryId);

		// App sobipro entry set to any category
		if ($categoryId == 0) {
			$userResource = $this->getJoomlaAtricleResource($user->getId(), false, $categoryId);
		}

		// If there do not have any resource from this current user
		// validate for the selected category id and restricted category id
		if (!$userResource) {

			if (array_key_exists($categoryId, $restrictedAppCategories)) {
				return self::BLOCKED;
			}

			return self::ALLOWED;
		}

		// determine for the current user whether exceeded that limit or not
		$isExceeded = $this->isExceeded($userResource, $categoryId, $user);

		return $isExceeded;
	}

	/**
	 * Get resource from the specific user
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function getJoomlaAtricleResource($userId, $specificCategoryOption = false, $categoryId = null)
	{
		// Currently App sobipro entry type has 3 options
		// - Any category 
		// - Specific category
		// - Section
		// check for the specific category type resources
		// at the same time this will be check also that section id as well since both type also using the same format e.g. com_content.entry+categoryId
		if ($specificCategoryOption) {
			$title = 'com_content.entry' . trim($categoryId);
			$specificCategoryResource = $this->getResource($userId, $categoryId, $title);

			if ($specificCategoryResource->resource_id) {
				return $specificCategoryResource;
			}
		}

		// This key for Any Category entry type
		$title = 'com_content.entry*';
		$resource = $this->getResource($userId, 0, $title);

		if (!$resource->resource_id) {
			return false;
		}

		return $resource;
	}

	/**
	 * Determine user entries exceeded resource entries
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function isExceeded($userResource, $categoryId, $user)
	{
		$userEntries = $this->getUserEntries($user->getId(), $categoryId);

		$entryId = $this->input->get('a_id', 0);

		// If user is editing his published entry,
		// we have to allow him
		if (isset($entryId) && key_exists($entryId, $userEntries)) {
			return self::ALLOWED;
		}

		// If user's entries is exceeded the allowed count, block
		if ($userEntries && $userResource->count && (count($userEntries) >= $userResource->count)) {
			return self::BLOCKED;
		}

		return self::ALLOWED;
	}

	/**
	 * Retrieve all user's entries
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function getUserEntries($userId, $categoryId = 0)
	{
		$db = PP::db();

		$query = [];
		$query[] = 'SELECT DISTINCT(`id`) FROM `#__content`';
		$query[] = 'WHERE ' . $db->qn('created_by') . ' =' . $db->Quote($userId);
		$query[] = ' AND ' . $db->qn('state') . ' = ' . $db->Quote(1);

		if ($categoryId) {
			$subCategories = $this->getAllSubCategories($categoryId);
			$subCategories[] = $categoryId;
			$query[] = 'AND '. $db->qn('catid') . ' IN ('.implode(',', $subCategories).')';
		}

		$query = implode(' ', $query);

		$db->setQuery($query);

		return $db->loadObjectList('id');
	}

	/**
	 * Retrieve all child categories
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function getAllSubCategories($catId, &$subCategories = [])
	{
		$db = PP::db();

		$query = [];
		$query[] = 'SELECT * FROM `#__categories`';
		$query[] = 'WHERE ' . $db->qn('extension') . '=' . $db->Quote('com_content');
		$query[] = 'AND ' . $db->qn('parent_id') . '=' . $db->Quote($catId);

		$query = implode(' ', $query);
		
		$db->setQuery($query);
		$results = $db->loadObjectList();
		
		if (!empty($results)) {
			foreach ($results as $res) {
				$subCategories[] = $res->id;
				$this->getAllSubCategories($res->id, $subCategories);
			}
		}

		return $subCategories;
	}

	/**
	 * Determine whether the site has setup any sobipro app
	 *
	 * @since   4.0.15
	 * @access  public
	 */
	public function hasJoomlaArticleApp()
	{
		$app = PPHelperApp::getAvailableApps('joomlaarticlerestriction');
		
		// Do not do anything if there do not have any subipro app
		if (!$app) {
			return false;
		}

		return $app;
	}

	/**
	 * Fetch all app restricted categories with their plans
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function getRestrictedAppCategories()
	{
		$data = [];

		$articleApps = PPHelperApp::getAvailableApps('joomlaarticlerestriction');

		foreach ($articleApps as $app) {

			$articleAppType = $app->getAppParam('addEntryIn', 'any_category');

			if ($articleAppType == 'any_category') {
				
				$appPlans = $app->getPlans();
				
				if (empty($appPlans)) {
					$appPlans = 0;
				}
				
				if (empty($data[0])) {
					$data[0] = $appPlans;
				} else {
					$data[0] = array_merge($data[0], $appPlans);
				}

				continue;
			}

			// Get the sobiPro category that been set in app
			$categories = $app->getAppParam('joomla_category');

			foreach ($categories as $category) {

				$appPlans = $app->getPlans();
				
				if (empty($appPlans)) {
					$appPlans = 0;
				}

				// $applyAll = $app->getParam('applyAll', 0);

				// if ($applyAll) {
				// 	$appPlans = PPHelperPlan::getPlans(array('published' => 1), false);
				// }

				if (empty($data[$category])) {
					$data[$category] = $appPlans;

				} else {
					$data[$category] = array_merge($data[$category], $appPlans);
				
				}
			}
		}

		return $data;
	}

	/**
	 * Get Entry category
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getEntryCategory($entryId = 0)
	{
		$db = PP::db();
		$query = [];

		$query[] = 'SELECT `id` FROM `#__content`';
		$query[] = 'WHERE ' . $db->qn('id') . '=' . $db->Quote($entryId);

		$query = implode(' ', $query);
		$db->setQuery($query);
		$results = $db->loadResult();

		return $results;
	}

	/**
	 * Fetch all the parent categories of specified category
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function getAllParents($categoryId, &$parentwithchild = [])
	{
		$parentwithchild[] = $categoryId;
		
		$db = PP::db();
		$query = [];

		$query[] = 'SELECT `parent_id` FROM `#__categories`';
		$query[] = 'WHERE ' . $db->qn('published') . '=' . $db->Quote(1);
		$query[] = 'AND ' . $db->qn('id') . '=' . $db->Quote($categoryId);

		$query = implode(' ', $query);
		
		$db->setQuery($query);
		$results = $db->loadObjectList();
		
		if (empty($results)) {
			return $parentwithchild;
		}
		
		$result = array_shift($results);
		
		if ($result->parent_id == 0) {
			return $parentwithchild;
		}

		$this->getAllParents($result->parent_id, $parentwithchild);
		
		return $parentwithchild;
	}

	/**
	 * Get Entry Resources
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function getEntryResource($userId)
	{
		$db = PP::db();
		$query = [];
		$query[] = 'SELECT * FROM `#__payplans_resource`';
		$query[] = 'WHERE `title` LIKE ' . $db->Quote('com_content.entry%');
		$query[] = 'AND `user_id` = ' . $db->Quote($userId);

		$query = implode(' ', $query);

		$db->setQuery($query);
		$records = $db->loadObjectList();

		foreach ($records as $record) {
			$record->title = str_replace('com_content.entry', '', $record->title);
			
			if ($record->title == '*') {
				$record->title = 0;
			}
			
			$entries = $this->getUserEntries($userId, $record->title);
			$record->consumed = count($entries);
			$record->title = $this->getCategoryTitle($record->title);
		}

		return $records;
	}

	/**
	 * Retrieve the category title from category id
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function getCategoryTitle($catId)
	{
		if ($catId == 0) {
			return JText::_("COM_PAYPLANS_APP_JOOMLA_ANY_CATEGORY");
		}

		$db = PP::db();
		$query = [];
		$query[] = 'SELECT title FROM `#__categories` as obj';
		$query[] = 'WHERE `id`=' . $db->Quote($catId);

		$query = implode(' ', $query);
		
		$db->setQuery($query);
		$results = $db->loadResult();
		
		return $results;
	}

	/**
	 * Toggle publish state
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function toggleCategoryEntry($userid, $publishCount, $action, $subscription, $categoryId = 0)
	{
		$complement = 1 - $action;
		$restricted_cat = array($categoryId);

		if ($categoryId != 0) {
			$restricted_cat = $this->getAllSubCategories($categoryId);
			$restricted_cat[] = $categoryId;
		}
		
		$db = PP::db();

		// Change state of the entry in object table
		$query = [];
		$query[] = 'UPDATE `#__content`';
		$query[] = 'SET `state`=' . $db->Quote($action) ;
		$query[] = 'WHERE `created_by`=' . $db->Quote($userid);
		$query[] = 'AND `state`=' . $db->Quote($complement);

		if ($categoryId != 0 && !empty($restricted_cat)) {
			$query[] = 'AND `catid` IN (' . implode(',',$restricted_cat) . ')';
		}

		$query = implode(' ', $query);

		$db->setQuery($query);
		$db->query();

	}
}