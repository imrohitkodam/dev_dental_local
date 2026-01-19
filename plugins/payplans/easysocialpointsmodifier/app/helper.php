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

class PPHelperEasySocialPointsModifier extends PPHelperStandardApp
{
	private $resource = 'com_easysocial.pointsmodifier';

	/**
	 * Check to see if the plan applicatable or not.
	 *
	 * @since	4.2.4
	 * @access	public
	 */
	public function isPlanApplicable(PPPlan $plan)
	{
		$planId = $plan->getId();
		$availablePlans = $this->params->get('modifier_mapping', array());

		if ($availablePlans) {

			$modifier = '';

			foreach ($availablePlans as $item) {
				$pid = $item[0];
				$limit = $item[1];

				if ($pid == $planId) {
					$modifier = ($limit == 0 || $limit > 0) ? $limit : '';
					break;
				}
			}

			if ($modifier != '') {
				return $modifier;
			}
		}

		return false;
	}

	/**
	 * Check to see if users has any points modifier
	 *
	 * @since	4.2.4
	 * @access	public
	 */
	public function hasPointsModifier(PPUser $user, $planId)
	{
		$model = PP::model('Resource');
		$items = $model->loadRecords(array(
					'user_id' => $user->id,
					'title' => $this->resource,
					'value' => $planId
				));

		if ($items) {
			$modifier = '';
			foreach ($items as $item) {
				$modifier = ($modifier == '' || $item->count == 0 || ($modifier && $item->count > $modifier)) ? $item->count : $modifier;
			}

			if ($modifier != '') {
				return $modifier;
			}
		}

		return false;
	}
}