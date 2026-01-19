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

class PPHelperEasydiscussticketsubmission extends PPHelperStandardApp
{
	protected $_resource = 'com_discuss.category.submission';

	/**
	* Determines if EasySocial is installed
	*
	* @since	4.2.0
	* @access	public
	*/
	public function exists()
	{
		static $exists = null;

		if (is_null($exists)) {
			$lib = PP::easydiscuss();
			$exists = false;

			if ($lib->exists()) {
				$exists = true;
			}
		}

		return $exists;
	}

	/**
	 * Retrieve the restricted categories from app param if any.
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getRestrictedCategories()
	{
		$restrictCategories = $this->params->get('restrict_in_category', array());

		if ($restrictCategories) {
			if (!is_array($restrictCategories)) {
				$restrictCategories = PP::makeArray($restrictCategories);
			}
			return $restrictCategories;
		}

		return array();
	}

	/**
	 * Retrieve the restricted type from app param.
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getRestrictedType()
	{
		$restrictType = $this->params->get('restrict_type', '');

		if (!$restrictType) {
			return false;
		}

		return $restrictType;
	}

	/**
	 * Retrieve the restricted type from app param.
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getTotalSubmission()
	{
		$total = (int) $this->params->get('total_submisssion', 0);
		return $total;
	}


	/**
	 * check if user allowed to create event or not.
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function isAllowed($userId, $categoryId)
	{
		$apps = $this->getAvailableApps('easydiscussticketsubmission');

		$isAllowed = true;

		$user = PP::user($userId);
		$userPlans = $user->getPlans(PP_SUBSCRIPTION_ACTIVE);
		$plans = PP::getIds($userPlans);

		foreach ($apps as $app) {

			$totalSubmission = (int)$app->getAppParam('total_submisssion');
			$restrictType = $app->getAppParam('restrict_type');

			if ($restrictType == 'any_category') {
				// /get total ticket by user
				$totalPost = $this->getDailyTicketByUser($userId);

			} else {
				$appCategories = $app->getAppParam('restrict_in_category');

				if (!in_array($categoryId, $appCategories)) {
					continue;
				}
				
				// get total ticket by category
				$totalPost = $this->getDailyTicketByUser($userId, $categoryId);
			}

			// check for user plan 
			$validSubscription = false;
			$applyAll = $app->getParam('applyAll', 0);
		
			if ($applyAll) {
				$validSubscription = !empty($plans) ? true : false;
			} else {
				$appPlans = $app->getPlans();
				if (array_intersect($plans, $appPlans)) {
					$validSubscription = true;
				}
			}

			if (!$validSubscription) {
				$redirect = $this->getRedirectPlanLink();
				$message = JText::_('COM_PAYPLANS_APP_EASYDISCUSS_SUBMISSION_NOT_ALLOWED');

				PP::info()->set($message, 'error');
				return PP::redirect($redirect);
			}

			if ($totalPost >= $totalSubmission) {
				$isAllowed = false;
			}
		}

		return $isAllowed;
	}


	/**
	 * check that user is allowed to post ticket in cetagory
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function isCategoryAllowed($categoryId)
	{
		$apps = $this->getAvailableApps('easydiscussticketsubmission');

		$isAllowed = true;

		foreach ($apps as $app) {
			if ($app->getAppParam('restrict_type') == 'any_category') {
				$isAllowed = false;
			} else {
				$appCategories = $app->getAppParam('restrict_in_category');
				if (in_array($categoryId, $appCategories)) {
					$isAllowed = false;
				}
			}
		}

		return $isAllowed;
	}

	/**
	 * check the cluster category applicable to any restriction or not.
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function isCategoryApplicable($clusterCategoryId)
	{
		$restrictType = $this->getRestrictedType();

		// something wrong with the app configuration.
		// just return true.
		if (!$restrictType) {
			return false;
		}

		if ($restrictType != 'restrict_specific') {
			return true;
		}

		$categories = $this->getRestrictedCategories();
		if (in_array($clusterCategoryId, $categories)) {
			return true;
		}

		return false;
	}

	public function getDailyTicketByUser($userId, $categoryId = '')
	{
		$db = PP::db();

		$startDate = PP::date()->modify('-1 day')->format('Y-m-d');
		$endDate = PP::date()->modify('+1 day')->format('Y-m-d');

		$startDate = PP::date($startDate);
		$endDate = PP::date($endDate);

		$query = array();
		$query[] = 'SELECT COUNT(*) FROM ' . $db->qn('#__discuss_posts');
		$query[] = 'WHERE ' . $db->qn('created') . '>=' . $db->Quote($startDate->toMySQL());
		$query[] = 'AND ' . $db->qn('created') . '<=' . $db->Quote($endDate->toMySQL());
		$query[] = 'AND ' . $db->qn('user_id') .'=' . $db->Quote($userId);
		$query[] = 'AND' . $db->qn('published') .'=' . $db->Quote(1);

		if ($categoryId) {
			$query[] = 'AND ' . $db->qn('category_id') . '=' . $db->Quote($categoryId);
		}

		$query = implode(' ', $query);
		$db->setQuery($query);
		
		$count = (int) $db->loadResult();

		return $count;

	}

	/**
	 * Get available resource count.
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getCurrentAvailableUsage($clusterCategoryId, $userId)
	{
		$restrictType = $this->getRestrictedType();
		$catId = $clusterCategoryId;

		if ($restrictType != 'restrict_specific') {
			$catId = 0;
		}

		$model = PP::model('Resource');
		$total = $model->loadRecords(array(
					'user_id' => $userId,
					'title' => $this->_resource,
					'value' => $catId
				));

		if (!$total) {
			// if no result found, mean user do not have any resource belong to this app.
			return 0;
		}

		$totalAllowed = array_shift($total);
		
		//if not allowed then it's resource counts are 0.
		if ($totalAllowed->count <= 0) {
			return 0;
		}

		return $totalAllowed->count;
	}

}