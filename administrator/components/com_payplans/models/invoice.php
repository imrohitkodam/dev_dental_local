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

PP::import('admin:/includes/model');

class PayplansModelInvoice extends PayPlansModel
{
	public $filterMatchOpeartor = [
		'status' => ['='],
		'total' => ['>=', '<='],
		'cross_users_username' => ['LIKE'],
		'cross_subscription_plan_id' => ['='],
		'paid_date' => ['>=', '<=']
	];

	public $crossTableNetwork = [
		"users" => ['users'],
		"subscription" => ['subscription'],
		"usergroups" => ['user_usergroup_map','usergroups']
	];

	public $innerJoinCondition = [
		"tbl-subscription" => " `#__payplans_subscription` as cross_subscription on cross_subscription.order_id = tbl.object_id ",
		"tbl-users" => " `#__users` as cross_users on tbl.user_id = cross_users.id "
	];

	public function __construct()
	{
		parent::__construct('invoice');
	}

	/**
	 * Initialize default states
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function initStates()
	{
		parent::initStates();

		$ordering = $this->getUserStateFromRequest('ordering', 'invoice_id', 'string');
		$planId = $this->getUserStateFromRequest('plan_id', -1, 'int');
		$status = $this->getUserStateFromRequest('status', -1, 'int');

		$this->setState('plan_id', $planId);
		$this->setState('ordering', $ordering);
		$this->setState('status', $status);
	}

	/**
	 * Get the list of Items
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getItems()
	{
		$paidDate = $this->getState('paid_date');
		$total = $this->getState('total');
		$username = $this->getState('username');
		$planId = $this->getState('plan_id');
		$status = $this->getState('status');
		$search = $this->getState('search');
		$limit = $this->getState('limit');

		$db = $this->db;

		$query = [];

		$query[] = "select invoice.*, subscription.`plan_id`, user.`username`";
		$query[] = "from `#__payplans_invoice` as `invoice`";
		$query[] = "left join `#__payplans_order` as `orders` on invoice.`object_id` = orders.`order_id`";
		$query[] = "left join `#__payplans_subscription` as `subscription` on subscription.`order_id` = orders.`order_id`";
		$query[] = "left join `#__users` as `user` on user.`id` = invoice.`user_id`";

		$wheres = [];

		if ($planId && $planId != -1) {
			$wheres[]  = $db->nameQuote('subscription.plan_id') . " = " . $db->Quote((int) $planId );
		}

		if ($status !== -1 && $status !== '') {
			$wheres[] = $db->nameQuote('invoice.status') . " = " . $db->Quote((int) $status);
		} else {
			$wheres[] = $db->nameQuote('invoice.status') . " != " . $db->Quote('0');
		}

		// Date range filter
		$dateRange = $this->getState('dateRange');

		if ($dateRange) {
			$startDate = PP::normalize($dateRange, 'start', '');
			$endDate = PP::normalize($dateRange, 'end', '');

			// If the start and end date is the same, we need to add 1 day to the end
			$end = $this->getEndingDateRange($startDate, $endDate);

			$wheres[] = $db->qn('paid_date') . '>' . $db->Quote($startDate);
			$wheres[] = $db->qn('paid_date') . '<' . $db->Quote($end);
		}

		if ($search !== '') {
			$search = PPJString::trim($search);

			$searchQuery = [];

			// Search by username, email or name
			$searchQuery[] = 'LOWER(user.' . $db->qn('name') . ') LIKE ' . $db->Quote('%' . PPJString::strtolower($search) . '%');
			$searchQuery[] = 'LOWER(user.' . $db->qn('username') . ') LIKE ' . $db->Quote('%' . PPJString::strtolower($search) . '%');
			$searchQuery[] = 'LOWER(user.' . $db->qn('email') . ') LIKE ' . $db->Quote('%' . PPJString::strtolower($search) . '%');

			// Search text could be invoice key
			$searchQuery[] = 'LOWER(invoice.' . $db->qn('invoice_id') . '=' . $db->Quote(PP::getIdFromKey($search, true)) . ')';
			$searchQuery[] = 'LOWER(invoice.' . $db->qn('invoice_id') . '=' . $db->Quote($search) . ')';

			// Search text could be invoice serial
			$searchQuery[] = 'LOWER(invoice.' . $db->qn('serial') . '=' . $db->Quote($search) . ')';

			$wheres[] = '(' . implode(' OR ', $searchQuery) . ')';
		}

		$where = '';

		if (count($wheres) > 0) {
			$where = ' where ';
			$where .= (count($wheres) == 1) ? $wheres[0] : implode(' and ', $wheres);
		}

		$query = implode(' ', $query);
		$query .= $where;

		$ordering = $this->getState('ordering');
		$direction = $this->getState('direction');
		
		if ($ordering) {
			$query .= " ORDER BY " . $ordering . " " . $direction;
		}

		$this->setTotal($query, true);
		$result	= $this->getData($query);

		return $result;
	}

	/**
	 * Retrieves a list of transactions given the list of invoices
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getTransactions($invoices = [])
	{
		if (!$invoices) {
			return [];
		}

		$model = PP::model('Transaction');
		$invoiceIds = [];

		foreach ($invoices as $invoice) {
			$invoiceIds[] = $invoice->getId();
		}

		// $options = array('invoice_id' => $invoiceIds)
		$rows = $model->loadRecords([
			'invoice_id'=> [['IN', "(".implode(",", $invoiceIds).")"]]
		]);
		$transactions = [];

		if ($rows) {
			foreach ($rows as $row) {
				$transaction = PP::transaction($row);

				$transactions[] = $transaction;
			}
		}
		return $transactions;
	}

	/**
	 * Retrieve record to export to CSV
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function getDataToExport($options = [])
	{
		$db = PP::db();

		$query = [];
		$query[] = "SELECT " . $db->nameQuote('a.invoice_id') . ", " . $db->nameQuote('d.username') . ", " . $db->nameQuote('a.user_id') . ", " . $db->nameQuote('a.subtotal') . ", " . $db->nameQuote('a.total') . ", " . $db->nameQuote('a.currency') . ", " . $db->nameQuote('a.status') . ", " . $db->nameQuote('a.paid_date');

		// to export free invoices , if gateway not selcted then no need to get app_id
		if (isset($options['gateway']) && $options['gateway']) {
			$query[] = ", " . $db->nameQuote('p.app_id') . ' as gateway';
		}

		$query[] = "from `#__payplans_invoice` as a";
		$query[] = "inner join `#__payplans_subscription` as s on s.order_id = a.object_id";

		if (isset($options['plans']) && $options['plans']) {
			$query[] = "AND s.plan_id in (".implode(',', $options['plans']).")";
		}

		if (isset($options['status']) && $options['status']) {
			$query[] = "AND a.status in (".implode(',',$options['status']).")";
		}

		if (isset($options['dateFrom']) && $options['dateFrom']) {
			$query[] = "AND a.paid_date >= " . $db->Quote($options['dateFrom']);
		}

		if (isset($options['dateTo']) && $options['dateTo']) {
			$query[] = "AND a.paid_date <= " . $db->Quote($options['dateTo']);
		}

		if (isset($options['gateway']) && $options['gateway']) {
			$query[] = "inner join `#__payplans_payment` as p on p.invoice_id = a.invoice_id";
		}
			
		$query[] = "inner join `#__users` as d on d.id = a.user_id";

		if (isset($options['gateway']) && $options['gateway']) {
			$query[] = "AND p.app_id in (".implode(',', $options['gateway']).")";
		}

		$query[] = "group by a.invoice_id LIMIT " . $options['limit'];

		$query = implode(' ', $query);

		$db->setQuery($query);
		$results = $db->loadObjectList();

		return $results;
	}

	/**
	 * Retrieve invoices between given dates
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function getInvoiceWithinDates($options = [])
	{
		$db = PP::db();

		$query = [];
		$query[] = "SELECT " . $db->nameQuote('invoice_id') . " FROM `#__payplans_invoice` as a";
		$query[] = "inner join `#__payplans_subscription` as s on s.order_id = a.object_id";
		$query[] = "WHERE " . $db->nameQuote('a.paid_date') . " >= " . $db->Quote($options['from']);
		$query[] = "AND " . $db->nameQuote('a.paid_date') . " <= " . $db->Quote($options['to']);
		
		// exclude plans from report
		if (isset($options['plans']) && $options['plans']) {
			$query[] = "AND  ".$db->nameQuote('s.plan_id') . "not in (".implode(',', $options['plans']).")";
		}

		$query[] = "LIMIT " . $options['limit'];

		$query = implode(' ', $query);
		
		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	public function getStatusString($statusCode)
	{
		$text = 'COM_PP_NA';

		if ($statusCode == 401) {
			$text = 'COM_PP_CONFIRMED';
		}

		if ($statusCode == 402) {
			$text = 'COM_PP_PAID';
		}

		if ($statusCode == 403) {
			$text = 'COM_PP_REFUNDED';
		}

		return JText::_($text);
	}

	public function getLastYearLatestInvoice ($date) 
	{
		$db = PP::db();

		$query = [];
		$query[] = "SELECT *  FROM `#__payplans_invoice`";
		$query[] = "WHERE " . $db->nameQuote('status') . " = " . $db->Quote(PP_INVOICE_PAID);
		$query[] = "AND " . $db->nameQuote('paid_date') . " < " . $db->Quote($date);
		$query[] = "ORDER BY `paid_date` DESC LIMIT 1";

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	public function getCurrentYearFirstInvoice($date, $limit = 0)
	{
		$db = PP::db();

		$query = [];
		$query[] = "SELECT * FROM `#__payplans_invoice`";
		$query[] = "WHERE " . $db->nameQuote('status') . " = " . $db->Quote(PP_INVOICE_PAID);
		$query[] = "AND " . $db->nameQuote('paid_date') . " >= " . $db->Quote($date);
		$query[] = "ORDER BY `paid_date` ASC";

		if ($limit) {
			$query[] = "LIMIT ".$limit;
		}

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

}

class PayplansModelformInvoice extends PayPlansModelform {}

