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

class PPStatistics extends PayPlans
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Retrieve adapter
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getAdapter($type)
	{
		static $adapter = null;

		if (!isset($adapter[$type])) {
			$fileName = strtolower($type);

			$helperFile	= dirname(__FILE__) . '/adapters/' . $fileName . '.php';
			require_once($helperFile);

			$className = 'PayPlansStatistics' . ucfirst($type);

			$adapter[$type] = new $className();
		}

		return $adapter[$type];
	}

	/**
	 * Retrieve statistic that are optimized for dashboard page
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getStatistics($duration = PP_STATS_DURATION_LIFETIME)
	{
		// Update any new stats
		$this->calculateStatistics();

		$stats = new stdClass();
		$stats->totalSales = 0;
		$stats->totalRevenue = 0;
		$stats->totalRenewals = 0;
		$stats->totalUpgrades = 0;
		$stats->currentActiveSubscription = 0;
		$stats->currentExpiredSubscription = 0;

		// Get graph statistics for current month
		$dateRange = $this->getFirstAndLastDate($duration);
		$startDate = $dateRange->start;
		$endDate = $dateRange->end;
		$allActiveExpiredSubscription = $this->getAllActiveExpiredSubscription($startDate, $endDate);

		// Fetch this using simple query instead.
		$results = $this->getPlanDataWithinDates($startDate, $endDate);
		$data = [];

		if ($results) {
			foreach ($results as $record) {
				$stats->totalSales += intval($record->sales);
				$stats->totalRevenue += floatval($record->revenue);
				$stats->totalRenewals += intval($record->renewals);
				$stats->totalUpgrades += intval($record->upgrades);
			}
		}

		if (isset($allActiveExpiredSubscription[PP_SUBSCRIPTION_ACTIVE])) {
			$stats->currentActiveSubscription = $allActiveExpiredSubscription[PP_SUBSCRIPTION_ACTIVE]->count;
		}

		if (isset($allActiveExpiredSubscription[PP_SUBSCRIPTION_EXPIRED])) {
			$stats->currentExpiredSubscription = $allActiveExpiredSubscription[PP_SUBSCRIPTION_EXPIRED]->count;
		}

		return $stats;
	}

	/**
	 * Retrieve data for statistics graph
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getStatisticsGraph($duration = PP_STATS_DURATION_MONTHLY, $dateRange = [], $type = PP_STATISTICS_TYPE_ALL, $dummyData = null)
	{
		// debug
		if ($dummyData) {
			return $this->generateDummyData($duration, $dateRange);
		}

		// Update any new stats
		$this->calculateStatistics();

		$duration = (int) $duration;

		// Get graph statistics for current month
		$dateRange = $this->getFirstAndLastDate($duration, $dateRange);

		$startDate = $dateRange->start;
		$endDate = $dateRange->end;

		if ($type === PP_STATISTICS_TYPE_GROWTH) {
			$results = $this->getSubscriptionDataWithinDates($startDate, $endDate);
		} else if ($type === PP_STATISTICS_TYPE_MEMBERS) {
			$results = $this->getTotalMembersWithinDates($startDate, $endDate);
		} else {
			$results = $this->getPlanDataWithinDates($startDate, $endDate, $type);
		}

		// Format the graph
		$stats = $this->formatStatisticsGraph($results, $startDate, $endDate, $duration, $type, $dummyData);

		return $stats;
	}

	/**
	 * Format the data for statistics graphs
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function formatStatisticsGraph($results, $startDate, $endDate, $duration, $type = PP_STATISTICS_TYPE_ALL)
	{
		// Specifically used for sales and dashboard chart
		if ($type === PP_STATISTICS_TYPE_ALL) {
			return $this->formatStatisticsGraphAll($results, $startDate, $endDate, $duration);
		}

		if ($type === PP_STATISTICS_TYPE_GROWTH) {
			return $this->formatStatisticsGraphGrowth($results, $startDate, $endDate, $duration);
		}

		if ($type === PP_STATISTICS_TYPE_MEMBERS) {
			return $this->formatStatisticsGraphMember($results, $startDate, $endDate, $duration);
		}

		// Generic graph format for other chart type
		return $this->formatStatisticsGraphGeneric($results, $startDate, $endDate, $duration, $type);
	}

	/**
	 * Format the statistics graph for All type
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function formatStatisticsGraphAll($results, $startDate, $endDate, $duration)
	{
		$currency = PP::getCurrency($this->config->get('currency'))->symbol;

		$salesFigure = [];
		$plansFigure = [];
		$planColor = [];
		$total = 0;
		$totalRevenue = 0;
		$totalUnits = 0;

		if ($results) {
			foreach ($results as $record) {

				$date = PP::date($record->statistics_date);
				$formattedDate = $date->format('d M');

				// Format sales figures
				$cumulativeSalesRevenue = 0;
				$cumulativeSalesUnits = 0;

				if (isset($salesFigure[$formattedDate])) {
					$cumulativeData = $salesFigure[$formattedDate];
					$cumulativeSalesRevenue = $cumulativeData['total_1'];
					$cumulativeSalesUnits = $cumulativeData['total_2'];
				}

				// Display the data if there is at least one figure to show
				$totalRevenue += $record->revenue;
				$totalUnits += $record->sales;

				// Display the data if there is at least one figure to show
				$revenue = $record->revenue + $cumulativeSalesRevenue;
				$units = $record->sales + $cumulativeSalesUnits;

				$formattedRevenue = PPFormats::amount($revenue, $currency);

				$salesData = [
					'date' => $date,
					'tooltip_title' => $date->format('l, F d, Y'),
					'tooltip_text' => JText::sprintf('COM_PP_SALES_GRAPH_TOOLTIP', $formattedRevenue, $units),
					'total_1' => $revenue,
					'total_2' => $units
				];

				$salesFigure[$formattedDate] = $salesData;

				if (!$record->sales) {
					continue;
				}

				// Format plans figures
				$cumulativePlansRevenue = 0;
				$cumulativePlansUnits = 0;

				if (isset($plansFigure[$record->plan_id])) {
					$cumulativeData = $plansFigure[$record->plan_id];
					$cumulativePlansRevenue = $cumulativeData['total_1'];
					$cumulativePlansUnits = $cumulativeData['total_2'];
				} else {
					$planColor[$record->plan_id] = $total;
					$total++;
				}

				$revenue = $record->revenue + $cumulativePlansRevenue;
				$units = $record->sales + $cumulativePlansUnits;

				$formattedRevenue = PPFormats::amount($revenue, $currency);

				$originalTitle = JText::_($record->title);
				$shortTitle = html_entity_decode($originalTitle);
				
				if ((PPJString::strlen(preg_replace('/<.*?>/', '', $shortTitle)) >= 15)) {
					$shortTitle = PPJString::substr($shortTitle, 0, 15) . JText::_('COM_PP_ELLIPSES');
				}

				$plansData = [
					'title' => $originalTitle,
					'shortTitle' => $shortTitle,
					'tooltip_text' => JText::sprintf('COM_PP_SALES_GRAPH_TOOLTIP', $formattedRevenue, $units),
					'total_1' => $revenue,
					'total_2' => $units,
					'background_color' => $this->getChartLabelColor($planColor[$record->plan_id])
				];

				$plansFigure[$record->plan_id] = $plansData;
			}
		}

		$startDate = PP::date($startDate);
		$endDate = PP::date($endDate);
		$startDateFormat = $startDate->format('F d, Y');
		$endDateFormat = $endDate->format('F d, Y');

		// For weekly we need to display a minimum of 7 days
		if ($duration == PP_STATS_DURATION_WEEKLY) {
			$newSalesFigure = [];

			for ($i = 0; $i < 7; $i++) { 
				$date = PP::date(strtotime('+' . $i . ' days', $startDate->toUnix()));
				$formattedDate = $date->format('d M');

				if (isset($salesFigure[$formattedDate])) {
					$newSalesFigure[$formattedDate] = $salesFigure[$formattedDate];
					continue;
				}

				$formattedRevenue = PPFormats::amount(0, $currency);

				$salesData = [
					'date' => $date,
					'tooltip_title' => $date->format('l, F d, Y'),
					'tooltip_text' => JText::sprintf('COM_PP_SALES_GRAPH_TOOLTIP', $formattedRevenue, 0),
					'total_1' => 0,
					'total_2' => 0,
					'currency' => $currency
				];

				$newSalesFigure[$formattedDate] = $salesData;
			}

			$salesFigure = $newSalesFigure;
		}

		$stats = new stdClass();
		$stats->chartTitle = JText::sprintf('COM_PP_CHART_SALES_TITLE', $startDateFormat, $endDateFormat);
		$stats->chartFigure = $totalRevenue && !empty($salesFigure) ? $salesFigure : false;
		$stats->plansFigure = $totalRevenue && !empty($plansFigure) ? $plansFigure : false;

		$chartFigureLabel = new stdClass();
		$chartFigureLabel->title = JText::_('COM_PP_CHART_COLUMN_TOTAL_REVENUE');
		$chartFigureLabel->value = PP::themes()->html('html.amount', $totalRevenue, PPFormats::currency(PP::getCurrency()));
		$chartFigureLabel->icon = 'fa-money-bill-alt';
		$chartFigureLabel->iconKey = '2';

		$planFigureLabel = new stdClass();
		$planFigureLabel->title = JText::_('COM_PP_CHART_COLUMN_TOTAL_UNITS');
		$planFigureLabel->value = $totalUnits;
		$planFigureLabel->icon = 'fa-cart-arrow-down';
		$planFigureLabel->iconKey = '1';

		$chartLabelData = [];
		$chartLabelData[] = $chartFigureLabel;
		$chartLabelData[] = $planFigureLabel;

		// Render label for chart
		$theme = PP::themes();
		$theme->set('chartLabelData', $chartLabelData);
		$theme->set('customClass', ' w-full items-center');
		$chartFigureLabel = $theme->output('admin/analytics/charts/labels/generic');

		$stats->chartFigureLabel = $chartFigureLabel;

		// Render the listing.
		$theme = PP::themes();
		$theme->set('results', array_reverse($salesFigure));

		$stats->listings = $theme->output('admin/analytics/charts/listings/sales');

		return $stats;
	}

	/**
	 * Format the statistics graph for upgrades
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function formatStatisticsGraphGeneric($results, $startDate, $endDate, $duration, $type)
	{
		$currency = PP::getCurrency($this->config->get('currency'))->symbol;

		$chartFigure = [];
		$plansFigure = [];
		$planColor = [];
		$total = 0;
		$totalItems = 0;
		$totalRevenue = 0;

		if ($results) {
			foreach ($results as $record) {

				$date = PP::date($record->statistics_date);
				$formattedDate = $date->format('d M');

				// Format sales figures
				$cumulativeItems = 0;
				$cumulativeRevenue = 0;

				if (isset($chartFigure[$formattedDate])) {
					$cumulativeData = $chartFigure[$formattedDate];
					$cumulativeItems = $cumulativeData['total_1'];
					$cumulativeRevenue = $cumulativeData['total_2'];
				}

				$totalItems += $record->$type;
				$items = $record->$type + $cumulativeItems;

				$valueType = $type."_revenue";
				$totalRevenue += $record->$valueType;
				$revenue = $record->$valueType + $cumulativeRevenue;

				$formattedRevenue = PPFormats::amount($revenue, $currency);

				$itemsDateData = [
					'date' => $date,
					'tooltip_title' => $date->format('l, F d, Y'),
					'tooltip_text' => JText::sprintf('COM_PP_' . strtoupper($type) . '_GRAPH_TOOLTIP', $formattedRevenue, $items),
					'total_1' => $items,
					'total_2' => $revenue
				];

				$chartFigure[$formattedDate] = $itemsDateData;

				if (!$record->$type) {
					continue;
				}

				// Format plans figures
				$cumulativePlansItems = 0;
				$cumulativePlansRevenue = 0;

				if (isset($plansFigure[$record->plan_id])) {
					$cumulativeData = $plansFigure[$record->plan_id];
					$comulativePlansRevenue = $comulativeData['total_1'];
					$cumulativePlansItems = $cumulativeData['total_2'];
				} else {
					$planColor[$record->plan_id] = $total;
					$total++;
				}

				$plansItems = $record->$type + $cumulativePlansItems;

				$planRevenue = $record->$valueType + $cumulativePlansRevenue;

				$formattedRevenue = PPFormats::amount($planRevenue, $currency);

				$originalTitle = JText::_($record->title);
				$shortTitle = $originalTitle;
				
				if ((PPJString::strlen(preg_replace('/<.*?>/', '', $shortTitle)) >= 15)) {
					$shortTitle = PPJString::substr($shortTitle, 0, 15) . JText::_('COM_PP_ELLIPSES');
				}

				$plansData = [
					'title' => $originalTitle,
					'shortTitle' => $shortTitle,
					'tooltip_text' => JText::sprintf('COM_PP_' . strtoupper($type) . '_GRAPH_TOOLTIP', $formattedRevenue, $plansItems),
					'total_1' => $planRevenue,
					'total_2' => $plansItems,
					'background_color' => $this->getChartLabelColor($planColor[$record->plan_id])
				];

				$plansFigure[$record->plan_id] = $plansData;
			}
		}

		$startDate = PP::date($startDate);
		$endDate = PP::date($endDate);
		$startDateFormat = $startDate->format('F d, Y');
		$endDateFormat = $endDate->format('F d, Y');

		$stats = new stdClass();
		$stats->chartTitle = JText::sprintf('COM_PP_CHART_' . strtoupper($type) . '_DATE_TITLE', $startDateFormat, $endDateFormat);
		$stats->chartFigure = $totalItems && !empty($chartFigure) ? $chartFigure : false;
		$stats->plansFigure = $totalItems && !empty($plansFigure) ? $plansFigure : false;

		// Get icons and icon key
		$icons = $this->getLabelIcons($type);

		$chartFigureLabel = new stdClass();
		$chartFigureLabel->title = JText::_('COM_PP_CHART_COLUMN_TOTAL_' . strtoupper($type));
		$chartFigureLabel->value = $totalItems;
		$chartFigureLabel->icon = $icons['icon'];
		$chartFigureLabel->iconKey = $icons['key'];

		$chartLabelData = [];
		$chartLabelData[] = $chartFigureLabel;

		// Render label for chart
		$theme = PP::themes();
		$theme->set('chartLabelData', $chartLabelData);
		$chartFigureLabel = $theme->output('admin/analytics/charts/labels/generic');

		$stats->chartFigureLabel = $chartFigureLabel;

		// Render the listing.
		$theme = PP::themes();
		$theme->set('results', array_reverse($chartFigure));
		$theme->set('type', $type);

		$stats->listings = $theme->output('admin/analytics/charts/listings/generic');

		return $stats;
	}

	/**
	 * Format the statistics chart for subscription growth
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function formatStatisticsGraphGrowth($results, $startDate, $endDate, $duration)
	{
		$chartFigure = [];
		$totalActive = 0;
		$totalExpire = 0;

		if ($results) {
			foreach ($results as $record) {
				$date = PP::date($record->statistics_date);
				$formattedDate = $date->format('d M');

				$active = (int) $record->active;
				$expire = (int) $record->expire;

				$totalActive += $active;
				$totalExpire += $expire;

				$chartData = [
					'date' => $date,
					'tooltip_title' => $date->format('l, F d, Y'),
					'tooltip_text' => JText::sprintf('COM_PP_GROWTH_GRAPH_TOOLTIP', $active, $expire),
					'total_1' => $active,
					'total_2' => $expire
				];

				$chartFigure[$formattedDate] = $chartData;
			}
		}

		$startDate = PP::date($startDate);
		$endDate = PP::date($endDate);
		$startDateFormat = $startDate->format('F d, Y');
		$endDateFormat = $endDate->format('F d, Y');

		$stats = new stdClass();
		$stats->chartTitle = JText::sprintf('COM_PP_CHART_GROWTH_DATE_TITLE', $startDateFormat, $endDateFormat);
		$stats->chartFigure = $chartFigure;

		$activeFigure = new stdClass();
		$activeFigure->title = JText::_('DASHBOARD_STATISTICS_ACTIVE_SUBSCRIPTIONS');
		$activeFigure->value = $totalActive;
		$activeFigure->icon = '';
		$activeFigure->iconKey = '';

		$expireFigure = new stdClass();
		$expireFigure->title = JText::_('DASHBOARD_STATISTICS_EXPIRE_SUBSCRIPTIONS');
		$expireFigure->value = $totalExpire;
		$expireFigure->icon = '';
		$expireFigure->iconKey = '';

		$chartLabelData = [];
		$chartLabelData[] = $activeFigure;
		$chartLabelData[] = $expireFigure;

		// Render label for chart
		$theme = PP::themes();
		$theme->set('chartLabelData', $chartLabelData);
		$theme->set('customClass', ' w-full items-center');
		$chartFigureLabel = $theme->output('admin/analytics/charts/labels/generic');

		$stats->chartFigureLabel = $chartFigureLabel;


		// Render the listing.
		$theme = PP::themes();
		$theme->set('results', array_reverse($chartFigure));

		$stats->listings = $theme->output('admin/analytics/charts/listings/growth');

		return $stats;
	}

	/**
	 * Format the statistics chart for subscription growth
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function formatStatisticsGraphMember($results, $startDate, $endDate, $duration)
	{
		$chartFigure = [];
		$totalRegistration = 0;

		if ($results) {
			foreach ($results as $record) {
				$date = PP::date($record->statistics_date);
				$formattedDate = $date->format('d M');

				$register = (int) $record->total_registration;

				$totalRegistration += $register;

				$chartData = [
					'date' => $date,
					'tooltip_title' => $date->format('l, F d, Y'),
					'tooltip_text' => JText::sprintf('COM_PP_MEMBERS_GRAPH_TOOLTIP', $register),
					'total_1' => $register
				];

				$chartFigure[$formattedDate] = $chartData;
			}
		}

		$startDate = PP::date($startDate);
		$endDate = PP::date($endDate);
		$startDateFormat = $startDate->format('F d, Y');
		$endDateFormat = $endDate->format('F d, Y');

		$icons = $this->getLabelIcons(PP_STATISTICS_TYPE_MEMBERS);

		$stats = new stdClass();
		$stats->chartTitle = JText::sprintf('COM_PP_CHART_MEMBERS_DATE_TITLE', $startDateFormat, $endDateFormat);
		$stats->chartFigure = $chartFigure;

		$chartFigureLabel = new stdClass();
		$chartFigureLabel->title = JText::_('COM_PP_CHART_COLUMN_TOTAL_MEMBERS');
		$chartFigureLabel->value = $totalRegistration;
		$chartFigureLabel->icon = $icons['icon'];
		$chartFigureLabel->iconKey = $icons['key'];

		$chartLabelData = [];
		$chartLabelData[] = $chartFigureLabel;

		// Render label for chart
		$theme = PP::themes();
		$theme->set('chartLabelData', $chartLabelData);
		$chartFigureLabel = $theme->output('admin/analytics/charts/labels/generic');

		$stats->chartFigureLabel = $chartFigureLabel;

		// Render the listing.
		$theme = PP::themes();
		$theme->set('results', array_reverse($chartFigure));

		$stats->listings = $theme->output('admin/analytics/charts/listings/member');


		return $stats;
	}

	/**
	 * Use to generate dummy data for debugging purpose
	 *
	 * @since	4.0.0
	 * @access	private
	 */
	private function generateDummyData($duration = PP_STATS_DURATION_MONTHLY, $dateRange = [])
	{
		$currency = PP::getCurrency($this->config->get('currency'))->symbol;

		$startDate = PP::date('21-03-2022');
		$endDate = PP::date('1-04-2022');
		$dateRangeDays = 7;

		if ($dateRange) {
			$startDate = PP::date($dateRange[0]);
			$endDate = PP::date($dateRange[1]);

			// $startDate = new DateTime($dateRange[0]);
			// $endDate = new DateTime($dateRange[1]);
			$interval = $endDate->diff($startDate);
			$dateRangeDays = (int) $interval->format('%a');
		}

		$startDateFormat = $startDate->format('F d, Y');
		$endDateFormat = $endDate->format('F d, Y');

		$salesFigure = [];
		$plansFigure = [];

		$totalRevenue = 0;
		$totalUnits = 0;

		for ($day = 0; $day < $dateRangeDays; $day++) {
			$date = PP::date(strtotime('+' . $day . ' days', $startDate->toUnix()));
			$formattedDate = $date->format('d M');

			$revenue = rand(0,100);
			$units = rand(0,15);

			$formattedRevenue = PPFormats::amount($revenue, $currency);

			$salesData = [
				'date' => $date,
				'tooltip_title' => $date->format('l, F d, Y'),
				'tooltip_text' => JText::sprintf('COM_PP_SALES_GRAPH_TOOLTIP', $formattedRevenue, $units),
				'total_1' => $revenue,
				'total_2' => $units
			];

			$plansData = [
				'title' => 'Plan ' . $day,
				'shortTitle' => 'Plan ' . $day,
				'tooltip_text' => JText::sprintf('COM_PP_SALES_GRAPH_TOOLTIP', $formattedRevenue, $units),
				'total_1' => $revenue,
				'total_2' => $units,
				'background_color' => $this->getChartLabelColor($day)
			];

			$salesFigure[$formattedDate] = $salesData;
			$plansFigure[$formattedDate] = $plansData;

			$totalRevenue += $revenue;
			$totalUnits += $units;
		}

		$stats = new stdClass();
		$stats->chartTitle = JText::sprintf('COM_PP_CHART_SALES_TITLE', $startDateFormat, $endDateFormat);
		$stats->chartFigure = $salesFigure;
		$stats->plansFigure = $plansFigure;

		$chartFigureLabel = new stdClass();
		$chartFigureLabel->title = JText::_('COM_PP_CHART_COLUMN_TOTAL_REVENUE');
		$chartFigureLabel->value = PP::themes()->html('html.amount', $totalRevenue, PPFormats::currency(PP::getCurrency()));
		$chartFigureLabel->icon = 'fa-money-bill-alt';
		$chartFigureLabel->iconKey = '2';

		$planFigureLabel = new stdClass();
		$planFigureLabel->title = JText::_('COM_PP_CHART_COLUMN_TOTAL_UNITS');
		$planFigureLabel->value = $totalUnits;
		$planFigureLabel->icon = 'fa-cart-arrow-down';
		$planFigureLabel->iconKey = '1';

		$chartLabelData = [];
		$chartLabelData[] = $chartFigureLabel;
		$chartLabelData[] = $planFigureLabel;

		// Render label for chart
		$theme = PP::themes();
		$theme->set('chartLabelData', $chartLabelData);
		$theme->set('customClass', ' w-full items-center');
		$chartFigureLabel = $theme->output('admin/analytics/charts/labels/generic');

		$stats->chartFigureLabel = $chartFigureLabel;

		// Render the listing.
		$theme = PP::themes();
		$theme->set('results', array_reverse($salesFigure));

		$stats->listings = $theme->output('admin/analytics/charts/listings/sales');

		return $stats;
	}

	/**
	 * Get the label icons for specific chart type
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getLabelIcons($type)
	{
		$icons = [
			PP_STATISTICS_TYPE_RENEWALS => [
				'icon' => 'fa-redo', 
				'key' => '1'
			],
			PP_STATISTICS_TYPE_UPGRADES => [
				'icon' => 'fa-arrow-circle-up', 
				'key' => '1'
			],
			PP_STATISTICS_TYPE_MEMBERS => [
				'icon' => 'fa-user',
				'key' => '1'
			]
		];

		return $icons[$type];
	}

	/**
	 * Retrieve first and last date
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getFirstAndLastDate($duration = PP_STATS_DURATION_WEEKLY, $customDates = [], $string = false)
	{
		$year = date("Y");
		$month = date("m");
		$day = date("d");
		$date = mktime(0, 0, 0, $month, $day, $year);
		$current = PP::date();

		$dateRange = new stdClass();
		$dateRange->start = '0000-00-00 00:00:00';
		$dateRange->end = $current->toSql();

		// Current date will be the end date
		if ($duration === PP_STATS_DURATION_LIFETIME) {
			return $dateRange;
		}

		if ($duration === PP_STATS_DURATION_WEEKLY) {
			$dateRange->start = $this->getStartWeekDates($current);
			$dateRange->end = $current;
		}

		if ($duration === PP_STATS_DURATION_DAILY) {
			$dateRange->start = PP::date(mktime(0, 0, 0, $month, $day, $year));
			$dateRange->end = PP::date(mktime(23, 59, 59, $month, $day, $year));
		}

		if ($duration === PP_STATS_DURATION_MONTHLY) {
			$dateRange->start = $this->getStartMonthDates($month, $year);
			$dateRange->end = $this->getEndMonthDates($month, $year);
		}

		if ($duration === PP_STATS_DURATION_YEARLY) {
			$dateRange->start = $this->getStartYearDates($year);
			$dateRange->end = $this->getEndYearDates($year);
		}

		if ($duration === PP_STATS_DURATION_LAST_30_DAYS) {
			$dateRange->start = $this->getLast30Days($current);
			$dateRange->end = $current;
		}

		if ($duration === PP_STATS_DURATION_CUSTOM) {

			list($startDate, $endDate) = $customDates;
			$dateRange->start = PP::date($startDate);
			$dateRange->end = PP::date($endDate);
		}

		if ($dateRange->start instanceof PPDate) {
			$dateRange->start = $dateRange->start->toSql();
		}

		if ($dateRange->end instanceof PPDate) {
			$dateRange->end = $dateRange->end->toSql();
		}

		// Return the range in form of string
		if ($string) {
			$startDate = PP::date($dateRange->start);
			$startString = $startDate->format("Y-m-d");

			$endDate = PP::date($dateRange->end);
			$endString = $endDate->format("Y-m-d");

			return $startString . ' - ' . $endString;
		}

		return $dateRange;
	}

	/**
	 * Get start week dates
	 *
	 * @since	4.0
	 * @access	private
	 */
	private function getStartWeekDates($current)
	{
		// Only minus 6 days from current date as we need to include current date as well
		$startDate = PP::date(strtotime("-6 days", $current->toUnix()));
		return $startDate;
	}

	/**
	 * Get the last 30 days from current
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getLast30Days($current)
	{
		$date = PP::date(strtotime("-29 days", $current->toUnix()));
		return $date;
	}

	/**
	 * Get start month dates
	 *
	 * @since	4.0
	 * @access	private
	 */
	private function getStartMonthDates($month, $year)
	{
		$startDate = PP::date(mktime(0,0,0,$month, 1, $year));
		return $startDate;
	}

	/**
	 * Get start year dates
	 *
	 * @since	4.0
	 * @access	private
	 */
	private function getStartYearDates($year)
	{
		$startDate = PP::date(mktime(0, 0, 0, 1, 1, $year));
		return $startDate;
	}

	/**
	 * Get previous month dates
	 *
	 * @since	4.0
	 * @access	private
	 */
	private function getEndMonthDates($month, $year)
	{
		$endDate = PP::date(mktime(23, 59, 59, $month + 1, 0, $year));
		return $endDate;
	}

	/**
	 * Get previous year dates
	 *
	 * @since	4.0
	 * @access	private
	 */
	private function getEndYearDates($year)
	{
		$endDate = PP::date(mktime(23, 59, 59, 1, 0, $year));
		return $endDate;
	}

	/**
	 * Retrieve data for ActiveExpired Subscriptions
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getAllActiveExpiredSubscription($firstDate, $lastDate)
	{
		$model = PP::model('Statistics');
		$results = $model->getAllActiveExpiredSubscription($firstDate, $lastDate);

		return $results;
	}

	/**
	 * Get sum of sales and revenue in between two dates
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getPlanDataWithinDates($firstDate, $lastDate, $type = PP_STATISTICS_TYPE_ALL)
	{
		$model = PP::model('Statistics');
		$results = $model->getPlanDataWithinDates($firstDate, $lastDate, $type);

		return $results;
	}

	/**
	 * Retrieve data for active expired subscription
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getSubscriptionDataWithinDates($firstDate, $endDate)
	{
		$model = PP::model('Statistics');
		$results = $model->getSubscriptionDataWithinDates($firstDate, $endDate);

		return $results;
	}

	public function getTotalMembersWithinDates($firstDate, $endDate)
	{
		$model = PP::model('Statistics');
		$results = $model->getTotalMembersWithinDates($firstDate, $endDate);

		return $results;
	}

	/**
	 * Get statistics data per plans
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getDataPerPlan($duration, $currentFirstDate, $currentLastDate)
	{
		$model = PP::model('Statistics');
		$results = $model->getSumOfRecords();

		// Format the data
		$data['plans'] = [];
		$totalSales = 0; // count_1
		$totalRevenue = 0; // count_2
		$totalRenewals = 0; // count_3
		$totalUpgrades = 0; // count_4
		$totalRenewalRevenue = 0; // count_5
		$totalUpgradeRevenue = 0; // count_6

		if ($results) {
			foreach ($results as $record) {
				$date = strtotime($record->statistics_date);
				$planId = $record->plan_id;

				$data['plans'][$planId] = $record->title;
				$data['upgrade'][$planId] = intval($record->count_4);

				// Plan Specific
				$data[$date]['sales'][$planId] = intval($record->count_1);
				$data[$date]['revenue'][$planId] = floatval($record->count_2);
				$data[$date]['renewals'][$planId] = intval($record->count_3);

				$data[$date]['renewals_revenue'][$planId] = floatval($record->count_5);
				$data[$date]['upgrades_revenue'][$planId] = floatval($record->count_6);

				if (isset($data[$date]['sales_day'])) {
					$data[$date]['sales_day'] += intval($record->count_1);
				} else {
					$data[$date]['sales_day'] = intval($record->count_1);
				}

				$totalSales += intval($record->count_1);
				$totalRevenue += floatval($record->count_2);
				$totalRenewals += intval($record->count_3);
				$totalUpgrades += intval($record->count_4);
				$totalRenewalRevenue += intval($record->count_5);
				$totalUpgradeRevenue += intval($record->count_6);
			}
		}

		$data['sales_all'] = $totalSales;
		$data['revenue_all'] = $totalRevenue;
		$data['renewal_all'] = $totalRenewals;
		$data['upgrades_all'] = $totalUpgrades;
		$data['revenue_renewal'] = $totalRenewalRevenue;
		
		ksort($data);
		return json_encode($data);
	}

	/**
	 * Method to recalculate statistics
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function calculateStatistics()
	{
		$adapters = ['plan', 'subscription', 'member'];

		foreach ($adapters as $adapter) {
			$adapterLib = $this->getAdapter($adapter);
			$datesToProcess = $adapterLib->getDates($adapter);

			if (!empty($datesToProcess)) {
				$adapterLib->setDetails([], $datesToProcess);
			}
		}

		return true;
	}

	/**
	 * Determine the limit for the rebuild process
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getRebuildLimit()
	{
		// @TODO: add settings for this
		// $limit = JRequest::getVar('limit', 10);
		$limit = 10;
		return $limit;
	}

	/**
	 * Return the total number of days to process in the statistics
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getDaysToProcess()
	{
		$subscriptionLib = $this->getAdapter('subscription');
		$first_date = PP::date($subscriptionLib->getOldestDate());
		$today_date = PP::date('now');

		//Calculation for number of days
		$days = abs((($today_date->toUnix()) - ($first_date->toUnix())) / 86400); // 86400 seconds in one day
		return intval($days);
	}

	/**
	 * Method to truncate all the statistics data
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function truncateStatistics()
	{
		$model = PP::model('Statistics');
		$state = $model->truncateStatistics();

		return $state;
	}

	/**
	 * Retrieve a set of colors for chart label
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getChartLabelColor($key = 0)
	{
		$color = [
			'rgb(78, 114, 226)',
			'rgb(0, 165, 118)',
			'rgb(211, 220, 248)',
			'rgb(188, 110, 45)',
			'rgb(220, 227, 234)',
			'rgb(252, 179, 119)',
			'rgb(52, 211, 153)',
			'rgb(102, 129, 152)',
			'rgb(253, 164, 175)',
			'rgb(198, 210, 221)',
			'rgb(4, 120, 87)',
			'rgb(47, 68, 136)',
			'rgb(254, 205, 211)',
			'rgb(110, 231, 183)',
			'rgb(6, 78, 59)',
			'rgb(131, 156, 235)',
			'rgb(68, 86, 101)',
			'rgb(226, 131, 54)',
			'rgb(136, 19, 55)',
			'rgb(151, 88, 36)',
			'rgb(254, 228, 206)',
			'rgb(55, 70, 83)',
			'rgb(59, 86, 170)',
			'rgb(6, 95, 70)',
			'rgb(190, 18, 60)',
			'rgb(159, 18, 57)',
			'rgb(251, 113, 133)',
			'rgb(251, 146, 60)',
			'rgb(244, 63, 94)',
			'rgb(184, 199, 243)',
			'rgb(113, 143, 169)',
			'rgb(38, 56, 111)',
			'rgb(225, 29, 72)',
			'rgb(85, 107, 127)',
			'rgb(70, 103, 203)',
			'rgb(156, 177, 195)',
			'rgb(4, 142, 99)',
			'rgb(167, 243, 208)',
			'rgb(253, 211, 177)',
			'rgb(123, 72, 29)'
		];

		return $color[$key];
	}
}
