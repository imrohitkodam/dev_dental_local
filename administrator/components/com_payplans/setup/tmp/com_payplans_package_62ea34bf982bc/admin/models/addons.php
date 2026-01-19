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

class PayplansModelAddons extends PayPlansModel
{
	public $filterMatchOpeartor = [
				'title' 	=> array('LIKE'),
				'published' => array('='),
				'visible' 	=> array('=')
			];

	public function __construct()
	{
		parent::__construct('addons');
	}

	/**
	 * Initialize default states used by default
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function initStates()
	{
		parent::initStates();

		$ordering = $this->getUserStateFromRequest('ordering', 'planaddons_id');

		$this->setState('ordering', $ordering);
	}

	/**
	 * Get list of addons created in payplans
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getItems()
	{
		$search = $this->getState('search');
		$state = $this->getState('published');
		$ordering = $this->getState('ordering');
		$direction	= $this->getState('direction');

		$db = $this->db;

		$query = [];

		$query[] = "select a.*, count(b.planaddons_id) as `usage`";
		$query[] = " from `#__payplans_planaddons` as a";
		$query[] = " left join `#__payplans_planaddons_stats` as b on a.planaddons_id = b.planaddons_id";


		$wheres = [];

		if ($search) {
			$wheres[] = $db->nameQuote('a.title') . " like " . $db->Quote('%' . $search . '%');
		}

		if ($state != 'all' && $state != '') {
			$wheres[] = $db->nameQuote('a.published') . " = " . $db->Quote((int) $state);
		}

		$where = '';
		if (count($wheres) > 0) {
			$where = ' where ';
			$where .= (count($wheres) == 1) ? $wheres[0] : implode(' and ', $wheres);
		}

		$query = implode(' ', $query);
		$query .= $where;

		$query .= " group by a.`planaddons_id`";


		if ($ordering) {
			$query .= " ORDER BY " . $ordering . " " . $direction;
		}

		$this->setTotal($query, true);
		$results = $this->getData($query);

		$addons = [];

		if ($results) {
			foreach ($results as $row) {
				$addon = PP::addon($row);
				$addon->usage = $row->usage;
				$addons[] = $addon;
			}
		}

		return $addons;

	}


	public function getStats($addonId, $options = [])
	{
		$ordering = $this->getUserStateFromRequest('ordering', 'planaddons_stats_id');
		$search = $this->getState('search');
		$state = $this->getState('published');
		$direction	= $this->getState('direction');

		$db = $this->db;

		$query = [];

		$query[] = "select a.* ";
		$query[] = " from `#__payplans_planaddons_stats` as a";

		$wheres = [];

		$wheres[] = $db->nameQuote('a.planaddons_id') . " = " . $db->Quote($addonId);

		if ($search) {
			$wheres[] = $db->nameQuote('a.title') . " like " . $db->Quote('%' . $search . '%');
		}

		if ($state != 'all' && $state != '') {
			$wheres[] = $db->nameQuote('a.published') . " = " . $db->Quote((int) $state);
		}

		$where = '';
		if (count($wheres) > 0) {
			$where = ' where ';
			$where .= (count($wheres) == 1) ? $wheres[0] : implode(' and ', $wheres);
		}

		$query = implode(' ', $query);
		$query .= $where;

		// dump($ordering);

		if ($ordering) {
			$query .= " ORDER BY " . $ordering . " " . $direction;
		}

		$this->setTotal($query, true);
		$results = $this->getData($query);

		$stats = [];

		if ($results) {
			foreach ($results as $result) {
				$stat = PP::table('AddonStat');
				$stat->bind($result);

				$stats[] = $stat;
			}
		}

		return $stats;
	}

	/**
	 * Retrieve the plan addon id
	 *
	 * @since	4.2
	 * @access	public
	 */
	public function getAddOnId($addOnStatsId)
	{
		$db = $this->db;
		$query = [];

		$query[] = 'SELECT ' . $db->nameQuote('planaddons_id') . ' FROM ' . $db->nameQuote('#__payplans_planaddons_stats'); 
		$query[] = 'WHERE ' . $db->nameQuote('planaddons_stats_id') . ' = ' . $db->quote($addOnStatsId);

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadColumn();

		return (int) $result[0];
	}

	/**
	 * Retrieve the total addon stats
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function getTotalStats($addonId, $options = [])
	{
		$db = $this->db;
		$userId = isset($options['userId']) ? $options['userId'] : false;
		$consumed = isset($options['consumed']) ? $options['consumed'] : false;

		$query = [];

		$query[] = "select count(*) ";
		$query[] = " from `#__payplans_planaddons_stats`";

		$wheres = [];

		$wheres[] = $db->nameQuote('planaddons_id') . ' = ' . $db->Quote($addonId);

		if ($userId) {
			$wheres[] = $db->nameQuote('user_id') . ' = ' . $db->Quote($userId);
		}

		if ($consumed) {
			$wheres[] = $db->nameQuote('consumed') . ' = ' . $db->Quote($consumed);
		}

		$where = '';
		if (count($wheres) > 0) {
			$where = ' where ';
			$where .= (count($wheres) == 1) ? $wheres[0] : implode(' and ', $wheres);
		}

		$query = implode(' ', $query);
		$query .= $where;


		$total = $this->db->setQuery($query)->loadResult();

		return $total;
	}

	/**
	 * Get list of available plans for addons
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function loadAvailableServices(Array $plans = [], $options = [])
	{
		static $results = [];

		$idx = serialize($options);

		if (isset($results[$idx])) {
			return $results[$idx];
		}

		$results[$idx] = [];

		$db = $this->db;
		$now = JFactory::getDate();
		$nowStr = $now->toSql();
		$emptyDateStr = '0000-00-00 00:00:00';

		// $allServices = $this->loadRecords(array('published' => 1 , 'start_date' => array(array("<=", $now->toSql())) , 'end_date' => array(array(">=", $now->toSql()))));

		$defaultOnly = PP::normalize($options, 'default', 0);

		$query = "SELECT a.* FROM `#__payplans_planaddons` AS a";
		$query .= " WHERE a.`published` = 1";
		$query .= " AND (";
		$query .= " (a.start_date <= " . $db->Quote($nowStr) . ' AND a.end_date > ' . $db->Quote($nowStr) . ")";
		$query .= " OR (a.start_date <= " . $db->Quote($nowStr) . ' AND a.end_date = ' . $db->Quote($emptyDateStr) . ")";
		$query .= " OR (a.start_date = " . $db->Quote($emptyDateStr) . ' AND a.end_date > ' . $db->Quote($nowStr) . ")";
		$query .= ' OR (a.start_date IS NULL AND a.end_date > ' . $db->Quote($nowStr) . ')';
		$query .= " OR (a.start_date = " . $db->Quote($emptyDateStr) . ' AND a.end_date = ' . $db->Quote($emptyDateStr) . ")";
		$query .= ' OR (a.start_date IS NULL AND a.end_date IS NULL)';
		$query .= ")";
		$query .= ' ORDER BY `ordering` ASC';

		$db->setQuery($query);
		$allServices = $db->loadObjectList();

		if (!$allServices) {
			return $results[$idx];
		}
		
		foreach ($allServices as $service) {
			$addonLib = PP::addon($service);

			if (!$addonLib->isAvailable($defaultOnly)) {
				continue;
			}

			if ($service->apply_on == 1) {
				$results[$idx][] = $service;
				continue;
			}

			if ($plans) {
				$servicePlans = json_decode($service->plans);

				if (array_intersect($plans, $servicePlans)) {
					$results[$idx][] = $service;
				}
			}
		}

		return $results[$idx];
	}


	/**
	 * Get list of available plans for addons and return as PPAddon
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getAvailableServices(Array $plans = [])
	{
		$data = $this->loadAvailableServices($plans);

		$addons = [];

		if ($data) {
			foreach ($data as $row) {
				$addon = PP::addon($row);
				$addons[$row->planaddons_id] = $addon;
			}
		}

		return $addons;
	}


	/**
	 * Get list of default addons
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getDefaultServices(Array $plans = [], $includePurchased = false)
	{
		$option = ['default' => true];

		if ($includePurchased) {
			$option['includePurchased'] = true;
		}

		$data = $this->loadAvailableServices($plans, $option);

		$addons = [];

		if ($data) {
			foreach ($data as $row) {
				$addon = PP::addon($row);
				$addons[$row->planaddons_id] = $addon;
			}
		}

		return $addons;
	}

	/**
	 * First make entries in planaddons stats then apply then on invoices
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function calculateCharges(PPInvoice $invoice, $addOnServices = [])
	{
		// add into planaddons_stats
		$this->addServicesStats($invoice, $addOnServices);

		//calculate all modifiers
		$this->addInvoiceModifiers($invoice);

		return true;
	}

	/**
	 * Remove single entry in service stats.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function removeService(PPInvoice $invoice, PPAddon $addon)
	{
		$db = $this->db;

		$invoiceId = $invoice->getId();
		$userId = $invoice->getBuyer()->getId();

		// make sure this addon is not already added previously.
		$query = "select * from `#__payplans_planaddons_stats`";
		$query .= " where `reference` = " . $db->Quote($invoiceId);
		$query .= " and `planaddons_id` = " . $db->Quote($addon->getId());
		$db->setQuery($query);
		$stat = $db->loadObject();

		if (! $stat->planaddons_stats_id) {
			return false;
		}

		// TODO: remove from planaddons_stats;
		$query = "delete from `#__payplans_planaddons_stats`";
		$query .= " where `planaddons_stats_id` = " . $db->Quote($stat->planaddons_stats_id);
		$db->setQuery($query);
		$state = $db->query();

		if ($state) {
			// now lets remove from modifier
			$this->removeModifier($invoice, $stat);
		}

		return $state;
	}



	/**
	 * Add single entry in service stats according to purchased service.
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function addService(PPInvoice $invoice, PPAddon $addon)
	{
		$db = $this->db;

		$invoiceId = $invoice->getId();
		$userId = $invoice->getBuyer()->getId();

		// make sure this addon is not already added previously.
		$query = "select count(1) from `#__payplans_planaddons_stats`";
		$query .= " where `reference` = " . $db->Quote($invoiceId);
		$query .= " and `planaddons_id` = " . $db->Quote($addon->getId());
		$db->setQuery($query);
		$exists = $db->loadResult();

		$config = PP::config();

		if (!$config->get('addons_select_multiple', 1)) {
			// Remove other addon if multiple addon not allowed
			$query1 = "select planaddons_id from `#__payplans_planaddons_stats`";
			$query1 .= " where `reference` = " . $db->Quote($invoiceId);
			$query1 .= " and `planaddons_id` != " . $db->Quote($addon->getId());
			$db->setQuery($query1);

			$existingAddons = $db->loadObjectList();

			if ($existingAddons) {
				foreach ($existingAddons as $addonId) {
					$existAddon = PP::addon($addonId);
					$this->removeService($invoice, $existAddon);
				}
			}
		}

		if ($exists) {
			return true;
		}

		$now = JFactory::getDate();

		//create new entry according to new details
		$stat = PP::table('AddonStat');

		$stat->user_id = $userId;
		$stat->planaddons_id = $addon->getId();
		$stat->title = $addon->title;
		$stat->price = $addon->price;
		$stat->price_type = $addon->price_type;
		$stat->addons_condition = ($addon->addons_condition) ? $addon->addons_condition : PP_PLANADDONS_ONETIME;
		$stat->reference = $invoiceId;
		$stat->status = 0;
		$stat->purchase_date = $now->toSql();
		$stat->params = $addon->params;
		$state = $stat->store();

		if ($state) {
			// now add into modifier
			$this->addModifier($invoice, $stat);
		}

		return $state;
	}


	/**
	 * Add entry in service stats according to purchases services.
	 * Firstly delete all entries related to this particular invoice then create new entries, it solves the modification in add services.
	 * in this stats we replicate the service data casue admin may change it later on after puchase, so current stats may also get change,
	 * so to avoide this thing we just replicate this data.
	 *
	 * @since	4.0
	 * @access	public
	 */
	protected function addServicesStats(PPInvoice $invoice, $addOnServices = [])
	{
		$db = $this->db;

		$invoiceId = $invoice->getId();
		$userId = $invoice->getBuyer()->getId();
		$isRecurring = $invoice->isRecurring();

		//delete all entries that are related to this invoice
		$query = 'delete from `#__payplans_planaddons_stats` where `reference` = ' . $db->Quote($invoiceId);
		$db->setQuery($query);
		$db->query();

		if ($addOnServices) {

			//create new entries for each purchased
			foreach ($addOnServices as $service) {

				//create new entry according to new details
				$stat = PP::table('AddonStat');

				$now = JFactory::getDate();

				$stat->user_id = $userId;
				$stat->planaddons_id = $service->getId();
				$stat->title = $service->title;
				$stat->price = $service->price;
				$stat->price_type = $service->price_type;
				$stat->addons_condition = ($service->addons_condition) ? $service->addons_condition : PP_PLANADDONS_ONETIME;
				$stat->reference = $invoiceId;
				$stat->status = 0;
				$stat->purchase_date = $now->toSql();
				$stat->params = $service->params;
				$stat->store();
			}

		}

		return true;
	}

	/**
	 * Get a list of services purchased in an invoice
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getPurchasedServices($invoiceId, $idOnly = false, $consumed = 0)
	{
		$db = $this->db;

		$keyColumn = 'planaddons_id';

		$query = "select * from `#__payplans_planaddons_stats`";
		$query .= " where `reference` = " . $db->Quote($invoiceId);
		$query .= " AND `consumed` = " . $db->Quote($consumed);

		$db->setQuery($query);
		$results = $db->loadObjectList($keyColumn);

		return $results;
	}


	/**
	 * This method collect all planaddons an apply them on the invoice.
	 * Apply them individual not collectly becasue when some of service may be in percentage or some may be fixed
	 * or some service may be recurring or some may not.
	 *
	 * @since	4.0
	 * @access	public
	 */
	protected function addInvoiceModifiers(PPInvoice $invoice)
	{
		$db = $this->db;

		$invoiceId = $invoice->getId();
		$userId = $invoice->getBuyer()->getId();
		$orderId = $invoice->getObjectId();

		$type 	= PP_PLANADDONS_MODIFIER;

		//delete all previously attached addon modifiers
		$query = "delete from `#__payplans_modifier`";
		$query .= " where `invoice_id` = " . $db->Quote($invoiceId);
		$query .= " and `type` = " . $db->Quote($type);
		$db->setQuery($query);
		$db->query();


		//get all purhased services
		$purchasedServices = $this->getPurchasedServices($invoiceId);

		if ($purchasedServices) {

			//add each service as modifier on this invoice
			foreach ($purchasedServices as $service) {
				$this->addModifier($invoice, $service);
			}

			$invoice->refresh();
			$invoice->save();
		}

		return true;
	}

	/**
	 * Method to remove addon modifier
	 * @since	4.0
	 * @access	private
	 */
	private function removeModifier(PPInvoice $invoice, $service)
	{
		$db = $this->db;
		$invoiceId = $invoice->getId();
		$userId = $invoice->getBuyer()->getId();

		$query = "delete from `#__payplans_modifier`";
		$query .= " where `invoice_id` = " . $db->Quote($invoiceId);
		$query .= " and `reference` = " . $db->Quote($service->planaddons_stats_id);
		$query .= " and `type` = " . $db->Quote(PP_PLANADDONS_MODIFIER);

		$db->setQuery($query);
		$state = $db->query();

		return $state;
	}


	/**
	 * Method to insert addon modifier
	 * @since	4.0
	 * @access	private
	 */
	private function addModifier(PPInvoice $invoice, $service)
	{
		$invoiceId = $invoice->getId();
		$userId = $invoice->getBuyer()->getId();
		$orderId = $invoice->getObjectId();

		$statsParams = PP::registry($service->params);

		//if nothing is available then neither discountale nor taxable
		$applicability = $statsParams->get('applicability','PERCENT_NON_TAXABLE');
		$applicability = constant('PP_MODIFIER_' . $applicability);


		//Note : if we do check service condition in doEntryServiceStats instead of createInvoiceModifiers,
		//we can skip to create servicestats for which services is not applicable, if we can skip to apply.
		//think of these conditions after adding any new service condition
		$condition = ($service->addons_condition == PP_PLANADDONS_ONETIME)
								? PP_MODIFIER_FREQUENCY_ONE_TIME
								: PP_MODIFIER_FREQUENCY_EACH_TIME;

		$data = [
			'user_id' => $userId,
			'order_id' => $orderId,
			'invoice_id' => $invoiceId,
			'amount' => $service->price,
			'type' => PP_PLANADDONS_MODIFIER,
			'reference' => $service->planaddons_stats_id,
			'message' => $service->title,
			'percentage' => $service->price_type,
			'frequency' => $condition,
			'serial' => $applicability,
		];

		$modifier = PPHelperModifier::create($data);

		// If addon is one time applicable , then recurring plan convert to recurring + 1 trial
		if ($service->addons_condition == PP_PLANADDONS_ONETIME && $invoice->getParam('expirationtype') == PP_RECURRING) {

			$invParams = $invoice->getParams();

			$recurrenceCount = (int) $invParams->get('recurrence_count');

			$invParams->set('expirationtype', 'recurring_trial_1');
			$invParams->set('recurrence_count', $recurrenceCount > 0 ? $recurrenceCount - 1 : 0);
			$invParams->set('trial_price_1', $invParams->get('price'));
			$invParams->set('trial_time_1', $invParams->get('expiration'));

			$invoice->params = $invParams;
		}

		$invoice->refresh();
		$invoice->save();

		return $modifier;
	}

	/**
	 * Saves the ordering of payment method
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function updateOrdering($id, $order)
	{
		$db = PP::db();

		$query = "update `#__payplans_planaddons` set ordering = " . $db->Quote($order);
		$query .= " where planaddons_id = " . $db->Quote($id);

		$db->setQuery($query);
		$state = $db->query();

		return $state;
	}

	/**
	 * Rebuild ordering for plan addon
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function rebuildOrdering()
	{
		$db = PP::db();

		$querySet1 = "SET @ordering_interval = 1";
		$querySet2 = "SET @new_ordering = 0";

		$query = "UPDATE `#__payplans_planaddons` SET `ordering` = (@new_ordering := @new_ordering + @ordering_interval)";
		$query .= " ORDER BY `ordering` ASC";

		// execute ordering_interval variable initiation.
		$db->setQuery($querySet1);
		$db->query();

		// execute new_ordering variable initiation.
		$db->setQuery($querySet2);
		$db->query();

		// now perform the update
		$db->setQuery($query);
		$db->query();

		return true;
	}

}
