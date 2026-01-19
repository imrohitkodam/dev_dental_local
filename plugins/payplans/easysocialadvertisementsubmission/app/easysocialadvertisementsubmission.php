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

require_once(__DIR__ . '/formatter.php');

class PPAppEasysocialadvertisementsubmission extends PPApp
{
	protected $_location = __FILE__;
	protected $_resource = 'com_easysocial.ads.submission';

	/**
	 * Applicable only when EasySocial is installed
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function _isApplicable(PPAppTriggerableInterface $refObject, $eventName = '')
	{
		return $this->helper->exists();
	}

	/**
	 * Trigger event after user subscription is saved
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function onPayplansSubscriptionAfterSave($prev, $new)
	{
		// no need to trigger if previous and current state is same
		if ($prev == null || ($prev->getStatus() == $new->getStatus())) {
			return true;
		}

		// handling user's resources.
		$this->processResource($prev, $new);

		// handling ads pubish or unpublish
		$this->processAds($prev, $new);

		return true;
	}


	/**
	 * Process user resource
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function processResource($prev, $new)
	{
		$subscriptionId = $new->getId();
		$user = $new->getBuyer();
		$userId = $user->id;
		$totalAds = $this->helper->getTotalAdsSubmission();

		// Addition
		if ($new->isActive()) {
			$this->_addToResource($subscriptionId, $userId, 0, $this->_resource, $totalAds);
			return true;
		}
		
		// Removal
		if ($prev->isActive() && !$new->isActive()) {
			$this->_removeFromResource($subscriptionId, $userId, 0, $this->_resource, $totalAds);
		}

		return true;
	}


	/**
	 * Process user subscription
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function processAds($prev, $new)
	{
		$subId =  $new->getId();

		$user =  $new->getBuyer();
		$userId = $user->getId();

		$subStatus = $new->getStatus();
		
		$ads = $this->helper->getUserEasysocialAds($userId);
		// perform Ads unpublish.
		if ($new->isExpired() || $new->isOnHold()) {

			if ($this->helper->getUnpublishAds()) {
				$this->unpublishAds($subId, $user, $ads);
			}
		}

		return true;
	}


	/**
	 * Unpublish user's created easysocial ads.
	 *
	 * @since	4.2.0
	 * @access	private
	 */
	private function unpublishAds($subId, $user, $ads = array())
	{
		if (!is_array($ads) || empty($ads)) {
			return true;
		}

		// lets filter unpublished pages.
		$data = array();

		foreach ($ads as $adId => $ad) {
			if ($ad->state == SOCIAL_CLUSTER_PUBLISHED) {
				$data[$adId] = $ad;
			}
		}

		if ($data) {
			$adIds = array_keys($data);

			// let batch update the pages.
			$state = $this->helper->toggleState(SOCIAL_CLUSTER_UNPUBLISHED, $adIds);

			foreach ($data as $ad) {

				$message = JText::_("COM_PP_APP_EASYSOCIALADSSUBMISSION_NOT_ALLOWED_MORE_ADS");
				$content = array('User Name' => $user->getName(), 'Easysocial Ad' => $ad->title,'Subscription Id' => $subId);

				PP::logger()->log(PPLogger::LEVEL_INFO, $message, $this->getId(), 'SYSTEM', $content, 'PayplansAppEasysocialadvertisementsubmissionFormatter', md5(serialize($content)));
			}
		}

		return true;
	}
}
