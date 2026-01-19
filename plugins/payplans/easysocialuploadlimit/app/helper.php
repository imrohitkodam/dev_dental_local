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

class PPHelperEasySocialUploadLimit extends PPHelperStandardApp
{
	private $resource = 'com_easysocial.uploadlimit';

	/**
	 * Check to see if the plan applicatable or not.
	 *
	 * @since	4.2.4
	 * @access	public
	 */
	public function isPlanApplicable(PPPlan $plan)
	{
		$planId = $plan->getId();
		$availablePlans = $this->params->get('upload_mapping', array());

		if ($availablePlans) {

			$size = '';

			foreach ($availablePlans as $item) {
				$pid = $item[0];
				$limit = $item[1];

				if ($pid == $planId) {
					$size = ($limit == 0 || $limit > 0) ? $limit : '';
					break;
				}
			}

			if ($size != '') {
				return $size;
			}
		}

		return false;
	}

	/**
	 * Check to see if users has any file upload size overrides.
	 *
	 * @since	4.2.4
	 * @access	public
	 */
	public function hasUploadSizeOverride(PPUser $user)
	{
		$model = PP::model('Resource');
		$items = $model->loadRecords(array(
					'user_id' => $user->id,
					'title' => $this->resource
				));

		if ($items) {
			$limit = '';
			foreach ($items as $item) {
				// when count == 0 (zero), mean its unlimited storage.
				$limit = ($limit == '' || $item->count == 0 || ($limit && $item->count > $limit)) ? $item->count : $limit;
			}

			if ($limit != '') {
				return $limit;
			}
		}

		return false;
	}
}