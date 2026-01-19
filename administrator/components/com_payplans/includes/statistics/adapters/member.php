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

PP::import('admin:/includes/statistics/adapters/statistics');

class PayplansStatisticsMember extends PayplansStatistics
{
	protected $_purpose_id = '4001'; // Unique id for query optimization
	public $_statistics_type = 'member';
	
	/**
	 * Store subscriptions details
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function setDetails($data = [], $dates_to_process = [])
	{
		$model = PP::model('User');

		list($firstDate, $lastDate) = $this->getFirstAndEndDates($dates_to_process);

		$registration = $model->getRegistrationStat($firstDate, $lastDate);

		// set cart statistics details
		foreach ($dates_to_process as $id => $date) {
			$key = $date->toUnix();

			$process_date = $date->toMySQL(false, "%Y-%m-%d");

			$data[$key]['statistics_type'] = $this->_statistics_type;
			$data[$key]['purpose_id_1'] = $this->_purpose_id;
			$data[$key]['count_1'] = isset($registration[$process_date]->count) ? $registration[$process_date]->count : 0;
		
			$data[$key]['statistics_date'] = $date;
		}

		return parent::setDetails($data);
	}
}
