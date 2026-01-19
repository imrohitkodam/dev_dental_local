<?php
/**
* @package      PayPlans
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Payplans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

PP::import('admin:/includes/statistics/adapters/statistics');

class PayplansStatisticsPlan extends PayplansStatistics
{
	public $_statistics_type = 'plan';

	/**
	 * Retrieve plan's subscription count and its status
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getSubscriptionStats()
	{
		// return all plan's subscriptions and it status
		$model = PP::model('plan');
		$results = $model->getAllSubscriptionStats();

		$stats = [];

		if ($results) {
			foreach ($results as $stat) {
				$stats[$stat->plan_id][$stat->status] = isset($stat->count) ? $stat->count : 0;
			}
		}

		return $stats;
	}

	/**
	 * Calculate statistics details for plan
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function setDetails($data = [], $datesToProcess = [])
	{
		list($first, $last) = $this->getFirstAndEndDates($datesToProcess);
		
		$planModel = PP::model('Plan');
		$subscriptionModel = PP::model('Subscription');
		$transactionModel = PP::model('Transaction');

		$plans = $planModel->loadRecords([]);
		$salesOfPlans = $subscriptionModel->getSalesOfPlans($first, $last); // sales excluding recurring as recurring one we fetch seperatly 
		$upgradesOfPlans = $subscriptionModel->getUpgradesOfPlans($first, $last);
		$revenueOfPlans = $transactionModel->getRevenuesOfPlans($first, $last); // revenue excluding recurring as recurring one we fetch seperatly 

		foreach ($datesToProcess as $processDate) {
			list($firstDate, $lastDate) = $this->getFirstAndLastDates($processDate);
			$key = $processDate->toUnix();
			$processDateUnformat = $processDate->toMySQL(false, "%Y-%m-%d");

			// //Renewal per plan
			$renewSubscriptions = $planModel->getTotalRenewalPerPlan($firstDate, $lastDate); 

			// Recurring Sales as per plan
			$recurringSubscriptions = $planModel->getTotalRecurringPerPlan($firstDate, $lastDate); 
			
			foreach ($plans as $pid => $plan) {
				$key .= $pid;
				$data[$key]['purpose_id_1'] = $pid;
				$data[$key]['statistics_type'] = $this->_statistics_type;
			
				$count1 = 0 ;
				if (isset($salesOfPlans[$pid][$processDateUnformat])) {
					$count1 = $salesOfPlans[$pid][$processDateUnformat]; // sales of plan (fixed and forever plans)
				} 

				// Recurring payment unit
				if (isset($recurringSubscriptions[$pid])) {
					$count1 = $count1 + $recurringSubscriptions[$pid];
				}

				// Renewal plan unit
				// don't add renew subscription count for recurring subscription  if recurring subscription count exist for plan id
				// As in recurring subscription it aso include the renewal invoice of recurring payments and there is no way to exclude them
				if (!isset($recurringSubscriptions[$pid]) && isset($renewSubscriptions[$pid])) {
					$count1 = $count1 + $renewSubscriptions[$pid];  // Recurring payments sales unit
				}

				// Totel unit of sales of plan
				$data[$key]['count_1'] = $count1;

				$revenue = 0;
				if (isset($revenueOfPlans[$pid][$processDateUnformat])) {
					$revenue = $revenueOfPlans[$pid][$processDateUnformat]; // revenue of plan
				}

				// Add recurring payment revenue
				if (isset($recurringSubscriptions[$pid.'_amount'])) {
					$revenue = $recurringSubscriptions[$pid.'_amount'] + $revenue;
				}

				$data[$key]['count_2'] = $revenue; // Revenue Per Plan

				//added this code to save the renewal per plan
				$data[$key]['count_3'] = isset($renewSubscriptions[$pid]) ? $renewSubscriptions[$pid]:0;
				$data[$key]['count_4'] = isset($upgradesOfPlans[$pid][$processDateUnformat]) ? $upgradesOfPlans[$pid][$processDateUnformat] : 0; // Upgrades Per Plan
				$data[$key]['count_5'] = isset($renewSubscriptions[$pid.'_amount']) ? $renewSubscriptions[$pid.'_amount'] : 0; // Renewal Revenue
				$data[$key]['count_6'] = isset($upgradesOfPlans[$pid]['amount']) ? $upgradesOfPlans[$pid]['amount'] : 0;   // Upgrade Revenew

				$data[$key]['details_1'] = $plan->title;
				$data[$key]['statistics_date'] = $processDate;
			}
		}
		
		return parent::setDetails($data);
	}
}
