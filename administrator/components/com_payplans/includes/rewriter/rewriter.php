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

class PPRewriter extends PayPlans
{
	public $mapping = [];

	public function __construct()
	{
		parent::__construct();
	}

	public function rewriterMapping()
	{
		$this->mapConfig();
		$this->setMapping(PP::plan(), false);
		$this->setMapping(PP::subscription(), false);
		$this->setMapping(PP::invoice(), false);
		$this->setMapping(PP::transaction(), false);
		$this->setMapping(PP::user(), false);
		$this->setMapping(PP::subscription(), false);

		// Formatting items
		$items = [
			'CONFIG' => [],
			'PLAN' => [],
			'SUBSCRIPTION' => [],
			'INVOICE' => [],
			'TRANSACTION' => [],
			'USER' => [],
			'SUBSCRIPTION' => []
		];

		$keys = array_keys($items);

		foreach ($this->mapping as $key => $value) {
			$parts = explode('_', $key);
			$index = $parts[0];

			$items[$index][] = $key;
		}

		return $items;
	}

	/**
	 * Sets configuration mapping
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function mapConfig()
	{
		static $mapping = false;

		if ($mapping) {
			return true;
		}

		// initialize it with some hard coded tokens
		$jconfig = PP::jconfig();
		
		$config = new stdClass();
		$config->site_name = rtrim($jconfig->get('sitename'), '/');
		$config->company_name = $this->config->get('companyName');
		$config->company_address = nl2br($this->config->get('companyAddress'));
		$config->company_city_country = $this->config->get('companyCityCountry');
		$config->company_phone = $this->config->get('companyPhone');
		$config->company_post_code = $this->config->get('companyPostCode');
		$config->company_tax_id = $this->config->get('companyTaxId');
		$config->site_url = rtrim(JURI::root(), '/');
		$config->name = 'config';
		$config->plan_renew_url = '';
		$config->dashboard_url = '';

		$this->setMapping($config, false);
		$mapping = true;

		return true;
	}

	/**
	 * Replaces tokens with proper values
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function rewrite($string, $object, $retrieveRelatedObjects = true)
	{
		// Initialize the configuration mapping
		$this->mapConfig();

		// Initialize object's mapping
		$this->setMapping($object, $retrieveRelatedObjects);

		//trigger apps for mapping rewriter tokens
		$args = [&$object, $this];
		PP::event()->trigger('onPayplansRewriterReplaceTokens', $args);

		$showBlankToken = $this->config->get('show_blank_token', false);

		foreach ($this->mapping as $key => $value) {
			
			if (!$showBlankToken && !is_array($value)) {
				$string = preg_replace('/\[\['.$key.'\]\]/', $value, $string);
				continue;
			}

			if ($showBlankToken && isset($value) && ($value != null || $value != '')) {
				$string = preg_replace('/\[\['.$key.'\]\]/', $value, $string);
				continue;
			}
		}

		return $string;
	}

	/**
	 * Initializes mapping for the object
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function setMapping($object, $retrieveRelatedObjects = true)
	{
		$objects = array($object);

		if ($retrieveRelatedObjects) {
			$objects = $this->getRelatedObjects($object);
		}

		if (!$objects) {
			return $this;
		}

		foreach ($objects as $object) {
			
			if (!$object) {
				continue;
			}

			$name = $object->name;

			if (method_exists($object, 'getName')) {
				$name = $object->getName();
			}

			// For objects that has a custom key to be used
			if (method_exists($object, 'getRewriterKey')) {
				$name = $object->getRewriterKey();
			}

			$properties = [];

			// New implementation in 4.0 to get tokens
			if (method_exists($object, 'getRewriterTokens')) {
				$properties = $object->getRewriterTokens();

				if ($properties === false) {
					continue;
				}
			}

			if (!$properties) {
				$properties = (method_exists($object, 'toArray')) ? $object->toArray(true, true) : (array) $object;

				if (isset($object->_blacklist_tokens)) {
					foreach ($object->_blacklist_tokens as $token) {
						unset($properties[$token]);
					}
				}
			}

			$map = [];
			foreach ($properties as $key => $value) {
				
				// if key name starts with _ then continue
				$key = PPJString::trim($key);

				if (PPJString::substr($key, 0, 1) == '_') {
					continue;
				}

				// JParameter will be an array, so handle it
				if (is_array($value)) {
					
					foreach ($value as $childKey => $childValue) {
						$index = PPJString::strtoupper($name . '_' . $key . '_' . $childKey);
						$map[$index] = $childValue;
					}

					continue;
				}

				if (strtolower($key) == 'status') {
					$value = JText::_('COM_PAYPLANS_STATUS_' . PP::string()->getStatusName($value));
				}

				if (($object instanceOf PPMaskableInterface) && strtolower($key)== 'currency' && method_exists($object, 'getCurrency')) {
					$value = $object->getCurrency();
				}

				if (stristr($key, 'date')) {
					
					if (($value == null || $value == '0000-00-00 00:00:00')) {
						$value = JText::_('COM_PAYPLANS_NEVER');
					} else {
						// Show date as per format set in setting
						$value = PP::date($value, true)->toDisplay(PP::getDateFormat());
					} 
				} 

				if (in_array($key, ['subtotal','total','amount'])) {
					$value = PPFormats::price($value);
				}

				$index = PPJString::strtoupper($name . '_' . $key);
				$map[$index] = $value;
			}

			// XITODO : clean this code, move the below code from forloop
			$this->mapping = array_merge($this->mapping, $map);

			// add key of PPMaskableInterface object
			if ($object instanceof PPMaskableInterface) {
				$index = PPJString::strtoupper($object->getName()) . '_KEY';
				$this->mapping[$index] = $object->getKey();
			}

			if ($name == 'invoice') {
				$this->mapping['INVOICE_INVOICE_SCREEN_LINK'] = PPR::external("index.php?option=com_payplans&view=checkout&tmpl=component&invoice_key=" . PP::getKeyFromId($this->mapping['INVOICE_INVOICE_ID']));
			}

			//Assign subscription Renew Link.
			if (isset($this->mapping['SUBSCRIPTION_SUBSCRIPTION_ID'])) {
				$this->mapping['CONFIG_PLAN_RENEW_URL'] = PPR::external("index.php?option=com_payplans&view=order&layout=processRenew&subscription_key=" . PP::getKeyFromId($this->mapping['SUBSCRIPTION_SUBSCRIPTION_ID']) . "&tmpl=component");
			}

			//token rewriter for dashboard and order details page
			if ($name == 'config') {
				$this->mapping['CONFIG_DASHBOARD_URL'] = PPR::external("index.php?option=com_payplans&view=dashboard");
			}

			// token rewrite for plan
			if (isset($this->mapping['PLAN_TITLE'])) {
				$this->mapping['PLAN_TITLE'] = JText::_($this->mapping['PLAN_TITLE']);
			}

			if (isset($this->mapping['PLAN_DESCRIPTION'])) {
				$this->mapping['PLAN_DESCRIPTION'] = JText::_($this->mapping['PLAN_DESCRIPTION']);
			}

			// token rewite for plan modifier 
			if ($object instanceof PPSubscription) {
				$subscription = PP::subscription($this->mapping['SUBSCRIPTION_SUBSCRIPTION_ID']);

				if ($subscription) {
					$this->mapping['PLAN_TITLE'] = $subscription->getTitle();
				}
			}

			// token rewrite for invoice tax
			if ($object instanceof PPInvoice) {

				$invoice = PP::invoice($this->mapping['INVOICE_INVOICE_ID']);

				$this->mapping['INVOICE_TAX_AMOUNT'] = $invoice->getTaxAmount();
				$this->mapping['INVOICE_DISCOUNT_AMOUNT'] = $invoice->getDiscount();

				// Plan title from subscription param to fix dynamic modifier plan title 
				$subscription = $invoice->getSubscription();
				if ($subscription) {
					$this->mapping['PLAN_TITLE'] = $subscription->getTitle();
				}

				$modifiers = $invoice->getModifiers(['type' => 'plan_addons']);
                if ($modifiers) {
                	foreach ($modifiers as $modifier) {
                		$addonToken = PPJString::strtoupper('INVOICE_'.str_replace(' ', '_', $modifier->getMessage()));
                		$this->mapping[$addonToken.'_TITLE'] = JText::_($modifier->getMessage());
                		$this->mapping[$addonToken.'_VALUE'] = PPFormats::price($modifier->getAmount());
                	}
                }
			}

			//token rewriter for users wallet balance
			if ($object instanceof PPUser) {
				$user = PP::user($this->mapping['USER_USER_ID']);

				// Defaulttoken for User Prefrences app
				$preferences = $user->getPreferences();
				$preferences = $preferences->toArray();

				if (empty($preferences)) {
					$this->mapping['USER_PREFERENCE_BUSINESS_NAME'] = '';
					$this->mapping['USER_PREFERENCE_TIN'] = '';
					$this->mapping['USER_PREFERENCE_SHIPPING_ADDRESS'] = '';
					$this->mapping['USER_PREFERENCE_BUSINESS_ADDRESS'] = '';
					$this->mapping['USER_PREFERENCE_BUSINESS_CITY'] = '';
					$this->mapping['USER_PREFERENCE_BUSINESS_STATE'] = '';
					$this->mapping['USER_PREFERENCE_BUSINESS_ZIP'] = '';
				}

				$countryCode  = $user->getCountry();
				$items = PP::model('country')->loadRecords([
					'id' => $countryCode
				]);

				$this->mapping['USER_COUNTRY'] = '';
				
				if (!empty($items)) {
					$this->mapping['USER_COUNTRY'] = PPFormats::country(array_shift($items));
				}
			}
		}
		
		return $this;
	}

	/**
	 * Given an object, try to figure out the order item
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getOrder($object)
	{
		$order = false;

		// Added compitibility for pp 3.x version, need to remove later
		if ($object instanceof PPOrder || $object instanceof PayplansOrder) {
			$order = $object;
		}

		if ($object instanceof PPPayment) {
			$invoice = $object->getInvoice();

			if (!$invoice) {
				return [];
			}

			$order = $invoice->getReferenceObject();
		}

		if ($object instanceof PPInvoice) {
			$order = $object->getReferenceObject();
			$latestInvoice = $object;
		}

		// If all else fail, try to get the order
		if (!$order) {
			$order = $object->getOrder();
		}

		return $order;
	}

	/**
	 * Given an object, try to figure out the order item
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getLatestInvoice($object)
	{
		if (!$object instanceof PPInvoice) {
				return false;
		}

		return $object;
	}

	/**
	 * Retrieve related objects given the provided object
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getRelatedObjects($object)
	{
		$obj = [];
		$latestInvoice = '';

		if (!$object || !isset($object)) {
			return [];
		}

		$order = $this->getOrder($object);
		$latestInvoice = $this->getLatestInvoice($object);

		$obj[] = $order->getSubscription(true);
		$obj[] = $order->getPlan();
		$obj[] = $order->getBuyer();

		if (!$latestInvoice) {
			$invoices = $order->getInvoices();

			if (!$invoices) {
				return $obj;
			}

			$latestInvoice = array_pop($invoices);
		}

		$obj[] = $latestInvoice;

		if ($latestInvoice->isRecurring()) {

			$firstInvoice = $order->getFirstInvoice();
			if ($firstInvoice) {
				$payment = $firstInvoice->getPayment();

			
				// Get the transaction as per invoice counter as transaction is attached to master invoice
				if ($object instanceof PPInvoice) {
					$counter = $object->getCounter();
					$transactionData = $firstInvoice->getTransactions();

					$transactions = [];

					if ($transactionData) {
						$transactionData = array_reverse($transactionData);

						$transactionCounter = (int)$counter - 1;
						$transactions[] = isset($transactionData[$transactionCounter]) ? $transactionData[$transactionCounter] : array_pop($transactionData);
					}
				} else {
					$transactions = $firstInvoice->getTransactions();
				}
			}

		} else {
			$payment = $latestInvoice->getPayment();
			$transactions = $latestInvoice->getTransactions();
		}
		
		if ($payment instanceof PPPayment) {
			$obj[] = $payment;
		} else {
			$obj[] = PP::payment();
		}

		if (!empty($transactions)) {
			$transaction = array_pop($transactions);

			if ($transaction instanceof PPTransaction) {
				$obj[] = $transaction;
			} else {
				$obj[] = PP::transaction();
			}
			
		} else {
			$obj[] = PP::transaction();
		}
		 
		$obj[] = $order;

		return $obj;
	}
}