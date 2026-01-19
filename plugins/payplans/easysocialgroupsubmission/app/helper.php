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

class PPHelperEasysocialgroupsubmission extends PPHelperStandardApp
{
	protected $_resource = 'com_easysocial.groupcategory.submission';

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
			$lib = PP::easysocial();
			$exists = false;

			if ($lib->exists()) {
				$exists = true;
			}
		}

		return $exists;
	}


	/**
	* Retrive ES groups created by user.
	*
	* @since	4.2.0
	* @access	public
	*/
	public function getUserEasysocialGroups($userId, $restrictType, $restrictCategories)
	{
		$esLib = PP::easysocial();

		$results = $esLib->getAppUserEasysocialCluster(SOCIAL_TYPE_GROUP, $userId, $restrictType, $restrictCategories);
		return $results;
	}


	/**
	* Update group's state
	*
	* @since	4.2.0
	* @access	public
	*/
	public function toggleState($state, $groupIds)
	{
		$esLib = PP::easysocial();

		$result = $esLib->appToggleClusterState($state, $groupIds);
		return $result;
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
	 * check if user allowed to create group or not.
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function isAllowed($clusterCategoryId, $userId)
	{
		$restrictType = $this->getRestrictedType();

		// something wrong with the app configuration.
		// just return true.
		if (!$restrictType) {
			return true;
		}

		if ($restrictType != 'restrict_specific') {
			$allow = $this->isCategoryAllowed('0', $userId);
			return $allow;
		}

		// is this cluster category id applicable or not.
		$categories = $this->getRestrictedCategories();
		if (in_array($clusterCategoryId, $categories)) {
			$allow = $this->isCategoryAllowed($clusterCategoryId, $userId);
			return $allow;
		}

		return true;
	}


	/**
	 * check the resouce count if user will have any avaiable slot or not.
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function isCategoryAllowed($clusterCategoryId, $userId)
	{
		$model = PP::model('Resource');
		$total = $model->loadRecords(array(
					'user_id' => $userId,
					'title' => $this->_resource,
					'value' => $clusterCategoryId
				));

		if (!$total) {
			// if no result found, mean user do not have any resource belong to this app.
			return false;
		}

		$totalAllowed = array_shift($total);
		
		//if not allowed then it's resource counts are 0.
		if ($totalAllowed->count == 0) {
			return false;
		}

		return true;
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

	/**
	 * get current available resource count.
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

	/**
	 * update app resource counter
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function updateResource($operation, $categoryId, $userId)
	{
		$restrictType = $this->getRestrictedType();

		if ($restrictType != 'restrict_specific') {
			$categoryId = 0;
		}

		$totalSubmission = $this->getTotalSubmission();

		$esLib = PP::easysocial();

		if ($operation == 'decrease') {
			$esLib->decreaseAppResource($this->_resource, $categoryId, $userId);
		}

		if ($operation == 'increase') {
			$esLib->increaseAppResource($this->_resource, $totalSubmission, $categoryId, $userId);
		}

		return true;
	}
}