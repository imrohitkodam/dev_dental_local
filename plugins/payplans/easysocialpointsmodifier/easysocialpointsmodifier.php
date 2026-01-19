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

class plgPayplansEasySocialPointsModifier extends PPPlugins
{
	/**
	 * Determines which category to be shown to the author
	 *
	 * @since	4.2.4
	 * @access	public
	 */
	public function _onEasySocialBeforePointsAssignOld($command, $extension, $userId, &$totalPointsToGive)
	{
		$helper = $this->getAppHelper();
		$user = PP::user($userId);

		$modifier = $helper->hasPointsModifier($user);

		// dump('plgPayplansEasySocialPointsModifier::', $modifier);

		if ($modifier !== false) {
			$totalPointsToGive = $totalPointsToGive * $modifier;
		}
	}

	public function onEasySocialBeforePointsAssign($command, $extension, $userId, &$totalPointsToGive)
	{
		$user = PP::user($userId);

		$model = PP::model('Plan');
		$userPlans = $model->getUserPlans($user->getId());

		if (!$userPlans) {
			// if user has no plan subscribed, stop here
			return;
		}

		$userPlanIds = [];
		$helper = $this->getAppHelper();
		$apps = $this->getAvailableApps('easysocialpointsmodifier');


		// plan ids as flat array
		foreach ($userPlans as $plan) {
			$userPlanIds[] = $plan->id;
		}

		$stack = false;
		$modifiers = [];

		if ($apps) {
			foreach ($apps as $app) {

				$availablePlans = $app->getAppParam('modifier_mapping', array());
				$allowStack = $app->getAppParam('allow_stack', false);

				if ($availablePlans) {

					foreach ($availablePlans as $item) {
						$pid = $item[0];
						$limit = $item[1];

						if ($limit === 0 || $limit > 0) {
							if (in_array($pid, $userPlanIds)) {

								// now we need to check if this is valid data or not.
								$data = $helper->hasPointsModifier($user, $pid);
								if ($data !== false) {
									$modifiers[$pid] = $data;
								}

								if ($allowStack) {
									$stack = true;
								}
							}
						}
					}
				}
			}
		}

		$pointModifier = '';

		if ($modifiers) {
			foreach ($modifiers as $pid => $count) {
				if ($stack) {
					$pointModifier = ($pointModifier == '') ? $count : $pointModifier + $count;
				} else {
					$pointModifier = ($pointModifier == '' || $count == 0 || ($pointModifier && $count > $pointModifier)) ? $count : $pointModifier;
				}
			}
		}

		if ($pointModifier !== '') {
			$totalPointsToGive = $totalPointsToGive * $pointModifier;
		}
	}
}