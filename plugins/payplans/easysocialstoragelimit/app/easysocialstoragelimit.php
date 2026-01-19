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

class PPAppEasySocialStorageLimit extends PPApp
{
	private $_resource = 'com_easysocial.storagelimit';

	public function onPayplansSubscriptionAfterSave($prev, $new)
	{
		if ($prev == null || ($prev->getStatus() == $new->getStatus())) {
			return true;
		}

		$plan = $new->getPlan();
		$storageSize = $this->helper->isPlanApplicable($plan);

		if ($storageSize === false) {
			// do nothing;
			return true;
		}

		$subscriptionId = $new->getId();
		$user = $new->getBuyer();
		$userId = $user->id;
		$planId = $plan->getId();

		// Addition
		if ($new->isActive()) {
			$this->_addToResource($subscriptionId, $userId, $planId, $this->_resource, $storageSize);
			return true;
		}
		
		// Removal
		if ($prev->isActive() && !$new->isActive()) {
			$this->_removeFromResource($subscriptionId, $userId, $planId, $this->_resource, $storageSize);
			return true;
		}
		
		return true;
	}
}