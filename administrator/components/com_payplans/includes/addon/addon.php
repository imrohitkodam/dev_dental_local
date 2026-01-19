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

require_once(PP_LIB . '/abstract.php');

class PPAddon extends PPAbstract
{
	public static function factory($id)
	{
		return new self($id);
	}

	/**
	 * not for table fields
	 * @since	4.0
	 * @access	public
	 */
	public function reset($config = [])
	{
		$this->table->planaddons_id = 0;
		$this->table->title = '';
		$this->table->description = '';
		$this->table->price = 0.0000;
		$this->table->consumed = 0;
		$this->table->addons_condition = null;
		$this->table->price_type = 0;
		$this->table->apply_on = 1;
		$this->table->plans = '';
		$this->table->start_date = '0000-00-00 00:00:00';
		$this->table->end_date = '0000-00-00 00:00:00';
		$this->table->published = 1;
		$this->table->params = PP::Registry();

		return $this;
	}

	public function getConditionRules()
	{
		$conditions = [
			PP_PLANADDONS_ONETIME => JText::_('COM_PP_ADDONS_CONDITION_ONETIME'), 
			PP_PLANADDONS_EACHRECURRING => JText::_('COM_PP_ADDONS_CONDITION_EACHRECURRING')
		];

		return $conditions;
	}

	/**
	* Override parent's bind behavior
	*
	* @since   5.0.3
	* @access  public
	*/
	public function bind($data = [], $ignore = [])
	{
		if (is_object($data)) {
		  $data = (array) ($data);
		}

		parent::bind($data, $ignore);

		// Bind details for atart and end dates
		if ($data['start_date'] === '') {
		  $this->start_date = "0000-00-00 00:00:00";
		}

		if ($data['end_date'] === '') {
		  $this->end_date = "0000-00-00 00:00:00";
		}

		return $this;
	}

	/**
	 * Retrieve the amount of the addon item
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getTitle($includePrice = false, $invoice = null)
	{
		$title = JText::_($this->table->title);

		if ($includePrice) {
			$amount = $this->getAmount($includePrice, $invoice);

			$title = JText::sprintf('COM_PP_PLAN_ADDON_TITLE_WITH_PRICE', $title, $amount);
		}

		return $title;
	}

	/**
	 * Retrieve the amount of the addon item
	 *
	 * @since	4.2
	 * @access	public
	 */
	public function getAmount($includePrice = false, $invoice = null)
	{
		$price = $this->table->price;
		$amount = $price;

		if ($includePrice) {
			$priceType = $this->table->price_type;

			if ($priceType) {
				$planPrice = $invoice->getSubtotal();

				if ($price) {
					$price = ($planPrice * $price ) / 100;
				}
			}

			$currency = PP::config()->get('currency');
			$amount = PP::themes()->html('html.amount', $price, $currency);

			if ($invoice) {
				$amount = PP::themes()->html('html.amount', $price, $invoice->getCurrency(), true);
			}

		}

		return $amount;
	}

	/**
	 * @since	4.0
	 * @access	public
	 */
	public function getDescriptions()
	{
		return $this->table->description;
	}

	/**
	 * @since	4.0
	 * @access	public
	 */
	public function getId()
	{
		return (int) $this->table->planaddons_id;
	}

	/**
	 * @since	4.0
	 * @access	public
	 */
	public function getPlans()
	{
		$plans = [];

		if ($this->table->plans) {
			$plans = json_decode($this->table->plans);
		}

		return $plans;
	}

	/**
	 * @since	4.0
	 * @access	public
	 */
	public function getApplyOn()
	{
		return $this->table->apply_on;
	}

	/**
	 * @since	4.0
	 * @access	public
	 */
	public function getParam($key, $default = null)
	{
		return $this->table->params->get($key, $default);
	}

	/**
	 * @since	4.0
	 * @access	public
	 */
	public function getConsumed()
	{
		return $this->table->consumed;
	}

	/**
	 * @since	4.0
	 * @access	public
	 */
	public function getPrice($raw = false)
	{
		if ($raw) {
			return $this->table->price;
		}

		return PPFormats::displayAmount($this->table->price);
	}

	/**
	 * @since	4.0
	 * @access	public
	 */
	public function getPriceType()
	{
		return $this->table->price_type;
	}

	/**
	 * @since	4.0
	 * @access	public
	 */
	public function getStartDate($nonAsEmpty = true)
	{
		if ($nonAsEmpty && stristr($this->table->start_date, '0000-00-00') !== false) {
			return '';
		}

		return $this->table->start_date;
	}

	/**
	 * @since	4.0
	 * @access	public
	 */
	public function getEndDate($nonAsEmpty = true)
	{
		if ($nonAsEmpty && stristr($this->table->end_date, '0000-00-00') !== false) {
			return '';
		}

		return $this->table->end_date;
	}

	/**
	 * @since	4.0
	 * @access	public
	 */
	public function getCondition($displayAsText = false)
	{
		if ($displayAsText) {
			$conditions = $this->getConditionRules();
			return isset($conditions[$this->table->addons_condition]) ? $conditions[$this->table->addons_condition] : '';
		}

		return $this->table->addons_condition;
	}

	/**
	 * @since	4.0
	 * @access	public
	 */
	public function getAvailability($displayAsText = false)
	{
		$params = $this->getParams();
		$availability = $params->get('availability', 0);

		if ($displayAsText) {
			if ($availability) {
				$stock = $params->get('stock');

				return $stock;
			}

			return JText::_('COM_PP_ADDONS_AVAILABILITY_UNLIMITED');
		}

		return $availability;
	}

	/**
	 * @since	4.0
	 * @access	public
	 */
	public function getStock()
	{
		$params = $this->getParams();
		$stock = $params->get('stock', 0);

		return $stock;
	}

	/**
	 * Get usage type of this addon
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function getUsageType()
	{
		$params = $this->getParams();
		$usageType = $params->get('usage_type', 0);

		return $usageType;
	}

	/**
	 * Get usage type of this addon in form of text
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function getUsageTypeText()
	{
		$usageType = $this->getUsageType();

		if ($usageType) {
			return $this->getUsageLimit();
		}

		return JText::_('COM_PP_ADDONS_AVAILABILITY_UNLIMITED');
	}

	/**
	 * Get Maximum usage of this addon
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function getUsageLimit()
	{
		$maxUsage = $this->getParams()->get('usage_limit', 0);
		return $maxUsage;
	}

	/**
	 * Get the total usage of this add on by user
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function getTotalUsageByUser($userId = null)
	{
		$user = PP::user($userId);

		$model = PP::model('addons');
		$totalUsage = $model->getTotalStats($this->getId(), [
			'userId' => $user->id, 
			'consumed' => 1
		]);

		return $totalUsage;
	}

	/**
	 * Determine if this add on is available to use
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function isAvailable($defaultOnly = false)
	{
		$params = $this->getParams();

		$isDefault = $params->get('default', 0);

		// we only want default addons?
		if ($defaultOnly && !$isDefault) {
			return false;
		}

		$isLimited = $this->getAvailability();
		if ($isLimited) {
			$stock = $this->getStock();
			$remaining = $stock - $this->getConsumed();

			//calulate remaing stock
			if ($remaining <= 0) {
				//do not count-in  if out of stock
				return false;
			}
		}

		$limitedUsage = $this->getUsageType();

		if ($limitedUsage) {
			$usageLimit = $this->getUsageLimit();
			$totalUsage = $this->getTotalUsageByUser();

			if ($totalUsage >= $usageLimit) {
				return false;
			}
		}

		return true;
	}
}
