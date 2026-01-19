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

class PayPlansViewAnalytics extends PayPlansAdminView
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('statistics');
		$this->allowed = [
			PP_STATISTICS_TYPE_ALL,
			PP_STATISTICS_TYPE_SALES,
			PP_STATISTICS_TYPE_REVENUE,
			PP_STATISTICS_TYPE_RENEWALS,
			PP_STATISTICS_TYPE_UPGRADES,
			PP_STATISTICS_TYPE_GROWTH,
			PP_STATISTICS_TYPE_MEMBERS
		];
	}

	public function display($tpl = null)
	{
		$layout = $this->input->get('layout', 'sales', 'default');

		// Exploit checking. Default to sales statistic for unsupported layout.
		if (!in_array($layout, $this->allowed)) {
			$layout = PP_STATISTICS_TYPE_SALES;
			$this->input->set('layout', PP_STATISTICS_TYPE_SALES);
		}

		$this->heading('analytics_' . $layout);

		JToolbarHelper::custom('updateStat', '', '', 'COM_PAYPLANS_REFRESH_TOOLBAR', false);
		JToolbarHelper::custom('rebuildStat', '', '', 'COM_PAYPLANS_PPRECREATE_TOOLBAR', false);

		$duration = $this->input->get('duration', PP_STATS_DURATION_LAST_30_DAYS, 'default');
		$customStartDate = $this->input->get('customStartDate', '', 'default');
		$customEndDate = $this->input->get('customEndDate', '', 'default');
		$dateRange = $this->input->get('daterange', [], 'array');
		$dummyData = $this->input->get('dummyData', 0, 'int');

		$start = PP::normalize($dateRange, 'start', '');
		$end = PP::normalize($dateRange, 'end', '');

		if (!$start && !$end) {
			$dateRange = PP::statistics()->getFirstAndLastDate($duration, [$customStartDate, $customEndDate]);

			$start = PP::normalize($dateRange, 'start', '');
			$end = PP::normalize($dateRange, 'end', '');
		}

		if ($start && $end) {
			$dateRange = [];
			$customStartDate = PP::date($start)->toSql();
			$customEndDate = PP::date($end)->toSql();

			$dateRange['start'] = $customStartDate;
			$dateRange['end'] = $customEndDate;

			// Tell the library to use custom to support various date range
			$duration = PP_STATS_DURATION_CUSTOM;
		}

		$type = $layout == 'sales' ? 'all' : $layout;

		$renderPlan = true;

		if ($type === PP_STATISTICS_TYPE_GROWTH || $type === PP_STATISTICS_TYPE_MEMBERS) {
			$renderPlan = false;
		}

		$this->set('type', $type);
		$this->set('duration', $duration);
		$this->set('customStartDate', $customStartDate);
		$this->set('customEndDate', $customEndDate);
		$this->set('dateRange', $dateRange);
		$this->set('dummyData', $dummyData);
		$this->set('renderPlan', $renderPlan);

		parent::display('analytics/default');
	}
}
