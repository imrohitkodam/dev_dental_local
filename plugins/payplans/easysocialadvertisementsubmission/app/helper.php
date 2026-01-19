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

class PPHelperEasysocialadvertisementsubmission extends PPHelperStandardApp
{
	protected $_resource = 'com_easysocial.ads.submission';

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

				$version = ES::getLocalVersion();
				if (version_compare($version, '4.0.0', '>=')) {
					$exists = true;
				}
			}
		}

		return $exists;
	}


	/**
	* Retrieve ES Ads created by user.
	*
	* @since	4.2.0
	* @access	public
	*/
	public function getUserEasysocialAds($userId)
	{
		$esLib = PP::easysocial();

		$results = $esLib->getAppUserEasysocialAds($userId);
		return $results;
	}

	/**
	* Retrieve ES Ads created by user.
	*
	* @since	4.2.0
	* @access	public
	*/
	public function getUserEasysocialAdsData($userId)
	{
		$esLib = PP::easysocial();

		$results = $esLib->getAppUserEasysocialAdsData($userId);
		return $results;
	}


	/**
	* Update page's state
	*
	* @since	4.2.0
	* @access	public
	*/
	public function toggleState($state, $adsIds)
	{
		$esLib = PP::easysocial();

		$result = $esLib->appToggleAdsState($state, $adsIds);
		return $result;
	}



	/**
	 * Retrieve the restricted type from app param.
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getTotalAdsSubmission()
	{
		$total = (int) $this->params->get('total_ads_submisssion', 0);
		return $total;
	}

	/**
	 * Retrieve the Unpublish ads state
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getUnpublishAds()
	{
		$state = $this->params->get('statusUnpublished', 0);
		return $state;
	}


	/**
	 * check if user allowed to create page or not.
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function isAllowed($userId)
	{
		$apps = $this->getAvailableApps('easysocialadvertisementsubmission');

		$user = PP::user($userId);
		$userPlans = $user->getPlans(PP_SUBSCRIPTION_ACTIVE);
		$plans = PP::getIds($userPlans);

		foreach ($apps as $app) {

			$isAllowed = true;
			$totalSubmission = (int)$app->getAppParam('total_ads_submisssion');
	
			// get total ads for user
			$totalAds = $this->getUserEasysocialAds($userId);

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
				$isAllowed = false;
			}

			if ($totalAds >= $totalSubmission) {
				$isAllowed = false;
			}
		} 

		return $isAllowed;
	}

	/**
	 * get current available resource count.
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function getCurrentAvailableUsage($userId)
	{
		$model = PP::model('Resource');
		$total = $model->loadRecords(array(
					'user_id' => $userId,
					'title' => $this->_resource,
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
	public function updateResource($operation, $userId)
	{
		$totalSubmission = $this->getTotalSubmission();

		$esLib = PP::easysocial();

		if ($operation == 'decrease') {
			$esLib->decreaseAppResource($this->_resource, 0, $userId);
		}

		if ($operation == 'increase') {
			$esLib->increaseAppResource($this->_resource, $totalSubmission, 0, $userId);
		}

		return true;
	}
}