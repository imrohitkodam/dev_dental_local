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

class PPAppJoomlaarticlerestriction extends PPApp
{
	protected $_location = __FILE__;
	
	public function onPayplansSubscriptionAfterSave($prev, $new)
	{
		// No need to trigger if previous and current state is same
		if ($prev != null && $prev->getStatus() == $new->getStatus()) {
			return true;
		}

		$allowToSubmit = $this->getAppParam('addEntryIn', 'any_category');
		$restrictedcategories = $this->getAppParam('joomla_category', 0);
		$submissionLimit = $this->getAppParam('entriesToPublish', 0);

		$subscriptionId = $new->getId();
		$userId = $new->getBuyer()->getId();

		if ($new->isActive()) {
			$action = 1;

			if ($allowToSubmit == 'on_specific_category') {
				foreach ($restrictedcategories as $restrictedcategory) {
					$this->helper->addResource($subscriptionId, $userId, $restrictedcategory, 'com_content.entry' . $restrictedcategory, $submissionLimit);
					$this->helper->toggleCategoryEntry($userId, $submissionLimit, $action, $new, $restrictedcategory);
				}
			}
			
			if ($allowToSubmit == 'any_category') {
				$this->helper->addResource($subscriptionId, $userId, 0, 'com_content.entry*', $submissionLimit);
				$this->helper->toggleCategoryEntry($userId, $submissionLimit, $action, $new);
			}
		}
		
		if (($prev != null && $prev->isActive()) && ($new->isExpired() || $new->isOnHold())) {
			$action = 0;

			// Need to check Entries are allowed unpublish on expiration
			$unpublish = false;
			if ($this->getAppParam('unpublishEntries')) {
				$unpublish = true;
			}
			
			if ($allowToSubmit == 'on_specific_category') {
				foreach ($restrictedcategories as $restrictedcategory){
					if ($unpublish) {
						$this->helper->toggleCategoryEntry($userId, $submissionLimit, $action, $new,$restrictedcategory);
					}
					
					$this->helper->removeResource($subscriptionId, $userId,$restrictedcategory, 'com_content.entry' . $restrictedcategory, $submissionLimit);
				}
			}
			

			if ($allowToSubmit == 'any_category') {
				if ($unpublish) {
					$this->helper->toggleCategoryEntry($userId, $submissionLimit, $action, $new);	
				}
				
				$this->helper->removeResource($subscriptionId, $userId,0, 'com_content.entry*', $submissionLimit);
			}
		}

		return true;
	}
}