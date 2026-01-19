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

class PPHelperStripe extends PPHelperPayment
{
	/**
	 * Load stripe's library
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function loadLibrary()
	{
		static $loaded = null;

		if (is_null($loaded)) {

			$lib = __DIR__ . '/lib/Stripe.php';

			if ($this->params->get('enable_sca')) {
				$lib = __DIR__ . '/scaLib/Stripe.php';
			}

			include_once($lib);

			\Stripe\Stripe::setApiKey($this->params->get('secret_key'));	

			$curl = new \Stripe\HttpClient\CurlClient(array(CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2, CURLOPT_CAINFO => PP_CACERT));
			\Stripe\ApiRequestor::setHttpClient($curl);

			$loaded = true;
		}

		return $loaded;
	}

	/**
	 * Stripe does not supports free trial directly so additional handling introduced to support free trials 
	 * in this case getExistingTransaction is not checked intensionally since stripe does not create any 
	 * transaction at their end so we do not have any reference to check with 
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function addFreeTrialSupport(PPPayment $payment, $customer)
	{
		$gatewayParams = $payment->getGatewayParams();
		$recurrenceCount = $gatewayParams->get('pending_recur_count');

		if ($recurrenceCount != 0) {

			$invoice = $payment->getInvoice();

			if (!$invoice->isRecurringOnetimeDiscountApplied()) {
				$pendingRecurrenceCount = $recurrenceCount - 1;
				$gatewayParams->set('pending_recur_count', $pendingRecurrenceCount);
			}

			$payment->gateway_params = $gatewayParams->toString();
			$payment->save();
		}

		$invoice = $payment->getInvoice();
		$invoiceId = $invoice->getId();
		$userId = $payment->getBuyer();

		$transaction = PP::transaction();
		$transaction->user_id = $userId;
		$transaction->invoice_id = $invoiceId;
		$transaction->payment_id = $payment->getId();
		$transaction->gateway_txn_id = 0;
		$transaction->gateway_subscr_id = $customer->id;
		$transaction->gateway_parent_txn = 0;
		$transaction->amount = 0;
		$transaction->message = 'COM_PAYPLANS_APP_STRIPE_TRANSACTION_COMPLETED';
		$transaction->save();

	   return $transaction; 
	}

	/**
	 * Updates customer object on Stripe
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function updateCustomer($customerId, $token)
	{
		$this->loadLibrary();

		try {
			$customer = \Stripe\Customer::retrieve($customerId);

			$customer->source = $token;
			$state = $customer->save();

			return $state;
		} catch(Exception $e) {
			$error = array_shift($e->jsonBody);
			return $error['message'];
		}
	}

	/**
	 * Retrieves the user country iscode
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getCountry($countryId) 
	{
		$db = PP::db();

		// Update the current country
		$query = array(
			'SELECT * FROM `#__payplans_country`',
			'WHERE `country_id`=' . $db->Quote($countryId)
		);

		$db->setQuery($query);
		$object = $db->loadObject();

		$countryCode = $object->isocode2;

		return $countryCode;
	}

	/**
	 * Creates a new customer object in Stripe
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function createCustomer(PPUser $user, $card, &$payment, $data)
	{
		$invoice = $payment->getInvoice();
		$plan = $invoice->getPlan();

		$options = [
			'name' => $user->getUsername(),
			'description' => $plan->getTitle(),
			'email' => $user->getEmail(),
			'card' => $card,
		];

		if ($this->params->get('billing_details')) {
			$options['address'] = [
				'city' => $data['city'],
				'country' => $this->getCountry($data['country']),
				'line1' => $data['address'],
				'line2' => '',
				'postal_code' => $data['zip'],
			    'state' => $data['state']	
			];

		}

		$this->loadLibrary();

		try {
			$response = \Stripe\Customer::create($options);
			$recurrenceCount = 0;

			if ($invoice->isRecurring()) {
				$recurrenceCount = $invoice->getRecurrenceCount();

				if ($recurrenceCount == PP_RECURRING_TRIAL_1) {
					$recurrenceCount++;
				}

				if ($recurrenceCount == PP_RECURRING_TRIAL_2) {
					$recurrenceCount + 2;
				}
			}

			$gatewayParams = $payment->getGatewayParams();

			$gatewayParams->set('pending_recur_count', $recurrenceCount);
			$gatewayParams->set('stripe_customer', $response->id);

			$payment->gateway_params = $gatewayParams->toString();
			$payment->save();
			
			return $response;

		} catch (Exception $e) {
			$error = array_shift($e->jsonBody);
			throw new Exception($error['message']);
		}
	}

	/**
	 * Creates a new customer object in Stripe
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function createSCACustomer(PPUser $user, $intent, &$payment, $data)
	{ 
		$this->loadLibrary();

		$invoice = $payment->getInvoice();
		$plan = $invoice->getPlan();

		$options = [
			'name' => $user->getUsername(),
			'description' => $plan->getTitle(),
			'email' => $user->getEmail(),
			'payment_method' => $intent->payment_method
		];

		if ($this->params->get('billing_details')) {
			$options['address'] = [
				'city' => $data['city'],
				'country' => $data['country'],
				'line1' => $data['address'],
				'line2' => '',
				'postal_code' => $data['zip'],
				'state' => $data['state']
			];
		}

		try {
			$response = \Stripe\Customer::create($options);
			$recurrenceCount = 0;

			if ($invoice->isRecurring()) {
				$recurrenceCount = $invoice->getRecurrenceCount();

				if ($recurrenceCount == PP_RECURRING_TRIAL_1) {
					$recurrenceCount++;
				}

				if ($recurrenceCount == PP_RECURRING_TRIAL_2) {
					$recurrenceCount + 2;
				}
			}

			$gatewayParams = $payment->getGatewayParams();

			$gatewayParams->set('pending_recur_count', $recurrenceCount);
			$gatewayParams->set('stripe_customer', $response->id);
			$gatewayParams->set('payment_method_id', $intent->payment_method);

			$payment->gateway_params = $gatewayParams->toString();
			$payment->save();
			
			return $response;

		} catch (Exception $e) {
			$error = array_shift($e->jsonBody);
			throw new Exception($error['message']);
		}
	}

	/**
	 * Since stripe bills customer in cents, this method is used to convert the amount to it's appropriate value
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getAmount($plan, $amount, $reverse = false)
	{
		$planCurrency = $plan->getCurrency('isocode');
		$japaneseCurrency = 'JPY';

		if ($reverse) {
			// If currecy is Japanese Yen then no need to divide amount by 100
			if ($planCurrency != $japaneseCurrency) {
				$amount = ($amount / 100);	
			}
			
			return $amount;
		} 

		// If currency is Japanees then no need to multiply amount by 100
		// In zero-decimal currencies, need to sent the amount as it is 
		if ($planCurrency == $japaneseCurrency) {
			$amount = str_replace(',', '', $amount);
			$amount = (int) $amount;
			$amount = (float) $amount;
			
			return $amount;
		} 
		
		$amount = ($amount * 100);

		return $amount;
	}

	/**
	 * Process payment on stripe
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function processPayment(PPPayment $payment, $data, $customer, $invoiceCount = 0)
	{
		$invoice = $payment->getInvoice();
		$plan = $invoice->getPlan();
		$gatewayTransactionId = PP::normalize($data, 'id', 0);

		foreach ($data as $key => $value) {
			if ($value instanceof Stripe_Object) {
				$value = $value->__toArray();
			}

			if (is_array($value)) {
				unset($data[$key]);
				$data = array_merge($data, $value);
			}
		}

		$user = PP::user($payment->getBuyer());
		$userId = $user->getId();
		$invoiceId = $invoice->getId();
		$customer = (is_object($customer)) ? $customer->__toArray() : $customer;

		$transaction = PP::createTransaction($invoice, $payment, $gatewayTransactionId, $customer['id'], 0, $data);

		// if response code is 0 then transaction is successful
		//$amount = $this->getAmount($plan, $data['amount'], true);
		
		$transaction->amount = $invoice->getTotal($invoiceCount);
		$transaction->message = 'COM_PAYPLANS_APP_STRIPE_TRANSACTION_COMPLETED';

		if (!isset($data['paid']) || !$data['paid']) {
			$transaction->amount = 0;
			$transaction->message = 'COM_PAYPLANS_APP_STRIPE_TRANSACTION_NOT_COMPLETED';
		}

		$transaction->save();

		if (!isset($data['paid']) || !$data['paid']) {
			$transactionKey = PayplansHelperUtils::getKeyFromId($transaction->getId());
			$invoiceKey = PayplansHelperUtils::getKeyFromId($invoice->getId());

			PPLog::log(PPLogger::LEVEL_ERROR, JText::_('COM_PAYPLANS_APP_STRIPE_LOGGER_ERROR_IN_STRIPE_PAYMENT_PROCESS'), $payment, $data, 'PayplansPaymentFormatter', '', true);
			
			return false;
		}

		return true;
	}

		/**
	 * Process payment on stripe
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function processSCAPayment(PPPayment $payment, $data, $customerId, $failureCount = 0, $invoiceCount = 0)
	{
		$invoice = $payment->getInvoice();
		$plan = $invoice->getPlan();
		$gatewayTransactionId = PP::normalize($data, 'id', 0);

		foreach ($data as $key => $value) {
			if ($value instanceof Stripe_Object) {
				$value = $value->__toArray();
			}

			if (is_array($value)) {
				unset($data[$key]);
				$data = array_merge($data, $value);
			}
		}

		$user = PP::user($payment->getBuyer());
		$userId = $user->getId();
		$invoiceId = $invoice->getId();

		$transaction = PP::createTransaction($invoice, $payment, $gatewayTransactionId, $customerId, 0, $data);

		// if response code is 0 then transaction is successful
		$amount = $this->getAmount($plan, $data['amount'], true);

		$transaction->amount = $invoice->getTotal($invoiceCount);
		$transaction->message = 'COM_PAYPLANS_APP_STRIPE_TRANSACTION_COMPLETED';

		if ($data['status'] != 'succeeded') {
			$transaction->amount = 0;
			$transaction->message = 'COM_PAYPLANS_APP_STRIPE_TRANSACTION_NOT_COMPLETED';

			// Update failure attempt limit
			if ($failureCount != 0) {
				$failureCount++;
			} else {
				$failureCount = 1;
			}

			$params = new JRegistry();
			$params->set('failure_attempt', $failureCount);

			$payment->params = $params->toString();
			$payment->save();
		}

		if ($data['status'] == 'succeeded') {
			$gatewayParams = $payment->getGatewayParams();

			// Decrease recurrence count when it is marked as paid
			$recurrenceCount = $gatewayParams->get('pending_recur_count');

			if ($recurrenceCount != 0) {
				$pendingRecurrenceCount = $recurrenceCount - 1;
				$gatewayParams->set('pending_recur_count', $pendingRecurrenceCount);
				$payment->table->gateway_params = $gatewayParams->toString();
				$payment->table->store();
			}
		}
		
		$transaction->save();

		return true;
	}

	/**
	 * Refunds a transaction
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function refund(PPTransaction $transaction, $amount)
	{
		try{

			if ($this->params->get('enable_sca')) {
				$payment = $transaction->getPayment();
				$paymentIntentId = $payment->getGatewayParams()->get('payment_intent_id', '');

				$intent = \Stripe\PaymentIntent::retrieve($paymentIntentId);
				$response = $intent->charges->data[0]->refund(['amount' => $amount]);
				
			} else {
				
				$options = ['amount' => $amount];

				$chargeId = $transaction->getGatewayTxnId();
				$charge = \Stripe\Charge::retrieve($chargeId);			
				$response = $charge->refund($options);	
			}	
			

			if ($response->paid && $response->refunded) {
				$data = $response->__tostring();
				$data = json_decode($data, true);
				
				foreach ($data as $key => $value) {		
					if (is_array($value)) {
						unset($data[$key]);
						$data = array_merge($data,$value);
					}
				}

				$refundTransaction = PP::transaction();
				$refundTransaction->user_id = $transaction->getBuyer()->getId();
				$refundTransaction->invoice_id = $transaction->getInvoice()->getId();

				$paymentId = PP::getIdFromKey($response->description);
				$refundTransaction->payment_id = $paymentId;

				$refundTransaction->gateway_txn_id = $response->id;
				$refundTransaction->gateway_subscr_id = 0;
				$refundTransaction->gateway_parent_txn = 0;

				$transactionParams = new JRegistry($data);
				$refundTransaction->params = $transactionParams->toString();

				$plan = $transaction->getInvoice()->getPlan();

				$negativeAmount = -(self::getAmount($plan, $amount, true));
				$refundTransaction->amount = $negativeAmount;

				$refundTransaction->message = 'COM_PAYPLANS_APP_STRIPE_TRANSACTION_REFUNDED';
				$refundTransaction->save();

				return true;
			}
		} catch (Exception $e) {
			$user = $transaction->getBuyer();
			$username = $user->getUsername();
			$userId = $user->getId();
			$invoice = $transaction->getInvoice();
			
			//It is needed to create a log for wrong response
			$errors = [
				'error_code' => $e->getCode(),
				'error_message' => JText::sprintf('COM_PAYPLANS_APP_STRIPE_LOGGER_ERROR_IN_STRIPE_PAYMENT_PROCESS_DETAILS', $e->getMessage(), $username, $userId, $invoice->getKey())
			];

			PPLog::log(PPLogger::LEVEL_ERROR, JText::_('COM_PAYPLANS_APP_STRIPE_LOGGER_ERROR_IN_STRIPE_RESPONSE_INVALID'), $transaction, $errors, 'PayplansPaymentFormatter', '', true);
			
			return false;
		}
		
		return false;
	}

	/**
	 * Create Payment Intent
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function createPaymentIntent($amount, $currency, $payment) 
	{
		$invoice = $payment->getInvoice();

		// Create payment intent
		$intent = \Stripe\PaymentIntent::create([
			'amount' => $amount,
			'currency' => $currency,
			'description' => $invoice->getKey(),
			'payment_method_types' => ["card"],
			'setup_future_usage' => 'off_session'
		]);

		$gatewayParams = $payment->getGatewayParams();
		$gatewayParams->set('payment_intent_id', $intent->id);
		$gatewayParams->set('payment_intent', $intent->client_secret);
		$payment->gateway_params = $gatewayParams->toString();
		$payment->save();

		return $intent;
	}
}