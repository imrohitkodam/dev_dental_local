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

class PPAppEasysocialmarketplacesubmission extends PPApp
{
	protected $_location = __FILE__;
	protected $_resource = 'com_easysocial.marketplacecategory.submission';

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

		// handling marketplace pubish or unpublish
		$this->processMarketplaces($prev, $new);

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
		$restrictType = $this->helper->getRestrictedType();
		if ($restrictType === false) {
			// look like this app is not being configured properly. abort here.
			return true;
		}

		$subscriptionId = $new->getId();
		$user = $new->getBuyer();
		$userId = $user->id;
		$total = $this->helper->getTotalSubmission();

		$restrictCategories = $this->helper->getRestrictedCategories();

		// Addition
		if ($new->isActive()) {

			if ($restrictType !== 'restrict_specific') {
				$this->_addToResource($subscriptionId, $userId, 0, $this->_resource, $total);
				return true;
			}

			if ($restrictCategories) {
				foreach ($restrictCategories as $category) {
					$this->_addToResource($subscriptionId, $userId, $category, $this->_resource, $total);
				}
			}

			return true;
		}
		
		// Removal
		if ($prev->isActive() && !$new->isActive()) {

			if ($restrictType !== 'restrict_specific') {
				$this->_removeFromResource($subscriptionId, $userId, 0, $this->_resource, $total);
				return true;
			}

			if ($restrictCategories) {
				foreach ($restrictCategories as $category) {
					$this->_removeFromResource($subscriptionId, $userId, $category, $this->_resource, $total);
				}
			}
		}

	}


	/**
	 * Process user subscription
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function processMarketplaces($prev, $new)
	{
		$subId =  $new->getId();

		$statusToPublish = $this->getAppParam('statusPublished', []);
		$statusToUnpublish = $this->getAppParam('statusUnpublished', []);

		$statusToFeatured = $this->getAppParam('statusFeatured', []);
		$statusToUnfeatured = $this->getAppParam('statusUnfeatured', []);

		$totalLimitItemRestriction = $this->helper->getTotalSubmission();
		$restrictType = $this->helper->getRestrictedType();

		if ($restrictType === false) {
			// look like this app is not being configured properly. abort here.
			return true;
		}

		$restrictCategories = $this->helper->getRestrictedCategories();
		$user = $new->getBuyer();
		$userId = $user->getId();

		$subStatus = $new->getStatus();
		
		$marketplaces = $this->helper->getUserEasysocialMarketplaces($userId, $restrictType, $restrictCategories);

		if ($marketplaces) {

			// perform marketplace unpublish.
			if (in_array($subStatus, $statusToUnpublish)) {
				$this->unpublishMarketplaces($subId, $user, $marketplaces);
			}

			// perform marketplace unpublish.
			if (in_array($subStatus, $statusToPublish)) {
				$this->publishMarketplaces($subId, $user, $marketplaces, $totalLimitItemRestriction);
			}

			// perform marketplace featured
			if (in_array($subStatus, $statusToFeatured)) {
				$this->featuredMarketplaces($subId, $user, $marketplaces);
			}

			// perform marketplace to be unfeatured
			if (in_array($subStatus, $statusToUnfeatured)) {
				$this->unfeaturedMarketplaces($subId, $user, $marketplaces);
			}
		}

		return true;
	}

	/**
	 * Publish user's created easysocial marketplaces.
	 *
	 * @since	4.2.0
	 * @access	private
	 */
	private function publishMarketplaces($subId, $user, $marketplaces = [], $totalLimitItemRestriction = '')
	{
		if (!is_array($marketplaces) || empty($marketplaces)) {
			return true;
		}

		// lets filter unpublished pages.
		$data = [];

		foreach ($marketplaces as $marketplaceId => $marketplace) {
			if ($marketplace->state == SOCIAL_CLUSTER_UNPUBLISHED) {
				$data[$marketplaceId] = $marketplace;
			}
		}

		if ($data) {

			// count total of Marketplace item ids
			$totalMarketplaceIds = count($data);
			$totalLimitItemRestriction = (int) $totalLimitItemRestriction;

			// if the restriction item limit is less than the user marketplace item then only re-publish max limit base on the ascending order
			if ($totalLimitItemRestriction != 0 && ($totalLimitItemRestriction < $totalMarketplaceIds)) {
				$data = array_slice($data, 0, $totalLimitItemRestriction, true);
			}

			$marketplaceIds = array_keys($data);

			// let batch update the pages.
			$state = $this->helper->toggleState(SOCIAL_CLUSTER_PUBLISHED, $marketplaceIds);

			foreach ($data as $marketplace) {

				$message = JText::_("COM_PP_APP_EASYSOCIALMARKETPLACESUBMISSION_LOG_PUBLISH_MARKETPLACES");
				$content = [
					'User Name' => $user->getName(), 
					'Easysocial MArketPlace' => $marketplace->title,
					'Subscription Id' => $subId
				];

				PP::logger()->log(PPLogger::LEVEL_INFO, $message, $this->getId(), 'SYSTEM', $content, 'PayplansAppEasysocialmarketplacesubmissionFormatter', md5(serialize($content)));
			}
		}

		return true;
	}


	/**
	 * Unpublish user's created easysocial marketplaces.
	 *
	 * @since	4.2.0
	 * @access	private
	 */
	private function unpublishMarketplaces($subId, $user, $marketplaces = [])
	{
		if (!is_array($marketplaces) || empty($marketplaces)) {
			return true;
		}

		// lets filter unpublished pages.
		$data = [];

		foreach ($marketplaces as $marketplaceId => $marketplace) {
			if ($marketplace->state == SOCIAL_CLUSTER_PUBLISHED) {
				$data[$marketplaceId] = $marketplace;
			}
		}

		if ($data) {
			$marketplaceIds = array_keys($data);

			// let batch update the pages.
			$state = $this->helper->toggleState(SOCIAL_CLUSTER_UNPUBLISHED, $marketplaceIds);

			foreach ($data as $marketplace) {

				$message = JText::_("COM_PP_APP_EASYSOCIALMARKETPLACESUBMISSION_LOG_UNPUBLISH_MARKETPLACES");
				$content = [
					'User Name' => $user->getName(), 
					'Easysocial Marketplace' => $marketplace->title,
					'Subscription Id' => $subId
				];

				PP::logger()->log(PPLogger::LEVEL_INFO, $message, $this->getId(), 'SYSTEM', $content, 'PayplansAppEasysocialmarketplacesubmissionFormatter', md5(serialize($content)));
			}
		}

		return true;
	}

	/**
	 * Featured user's created easysocial marketplaces.
	 *
	 * @since	5.0.0
	 * @access	private
	 */
	private function featuredMarketplaces($subId, $user, $marketplaces = array())
	{
		if (!is_array($marketplaces) || empty($marketplaces)) {
			return true;
		}

		// lets filter unpublished marketplaces.
		$data = array();

		foreach ($marketplaces as $marketplaceId => $marketplace) {
			if ($marketplace->featured == 0) {
				$data[$marketplaceId] = $marketplace;
			}
		}

		if ($data) {

			$marketplaceIds = array_keys($data);

			// let batch update the marketplaces.
			$state = $this->helper->toggleFeatured(1, $marketplaceIds);

			foreach ($data as $marketplace) {

				$message = JText::_("COM_PP_APP_EASYSOCIALMARKETPLACESUBMISSION_LOG_FEATURED_MARKETPLACES");
				$content = [
					'User Name' => $user->getName(), 
					'Easysocial Marketplace' => $marketplace->title,
					'Subscription Id' => $subId
				];

				PP::logger()->log(PPLogger::LEVEL_INFO, $message, $this->getId(), 'SYSTEM', $content, 'PayplansAppEasysocialmarketplacesubmissionFormatter', md5(serialize($content)));
			}
		}

		return true;
	}

	/**
	 * Featured user's created easysocial marketplaces.
	 *
	 * @since	5.0.0
	 * @access	private
	 */
	private function unfeaturedMarketplaces($subId, $user, $marketplaces = array())
	{
		if (!is_array($marketplaces) || empty($marketplaces)) {
			return true;
		}

		// lets filter unpublished marketplaces.
		$data = array();

		foreach ($marketplaces as $marketplaceId => $marketplace) {
			if ($marketplace->featured == 1) {
				$data[$marketplaceId] = $marketplace;
			}
		}

		if ($data) {

			$marketplaceIds = array_keys($data);

			// let batch update the marketplaces.
			$state = $this->helper->toggleFeatured(0, $marketplaceIds);

			foreach ($data as $marketplace) {

				$message = JText::_("COM_PP_APP_EASYSOCIALMARKETPLACESUBMISSION_LOG_UNFEATURED_MARKETPLACES");
				$content = [
					'User Name' => $user->getName(), 
					'Easysocial Marketplace' => $marketplace->title,
					'Subscription Id' => $subId
				];

				PP::logger()->log(PPLogger::LEVEL_INFO, $message, $this->getId(), 'SYSTEM', $content, 'PayplansAppEasysocialmarketplacesubmissionFormatter', md5(serialize($content)));
			}
		}

		return true;
	}
}
