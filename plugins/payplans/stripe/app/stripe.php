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

require_once(__DIR__ . '/helper.php');

class PPAppStripe extends PPAppPayment
{
	/**
	 * This option determines if the app supports refund requests
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function supportForRefund()
	{
		return true;
	}

	/**
	 * Recurring cancellation supported
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function isSupportPaymentCancellation($invoice)
	{
		if ($invoice->isRecurring()) {
			return true;
		}

		return false;
	}

	/**
	 * Renders the payment form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansPaymentForm(PPPayment $payment, $data = null)
	{
		//if some error occured when click on buy but response is not successful then show error msg
		$error_code = $this->input->get('error_code');
		$error_msg = $this->input->get('error_msg', '', 'string');
		$error_html = '';

		if (isset($error_code) && isset($error_msg)) {
			$invoice = $payment->getInvoice();

			$theme = PP::themes();
			$theme->set('error_code', $error_code);
			$theme->set('error_msg', $error_msg);
			$theme->set('invoice', $invoice);
			return $theme->output('apps:/stripe/buying_error');
		}

		$user = $payment->getBuyer(true);
		$userEmail = $user->getEmail();

		$publicKey = $this->getAppParam('public_key', '');
		$sandbox = $this->getAppParam('sandbox', false);
		$storeName = $this->getAppParam('popup_store_name', '');
		$populateEmail = $this->getAppParam('auto_fill_email', false);
		$billingDetails = $this->getAppParam('billing_details', false);
		$dateFormat = $this->getAppParam('date_type', 'MM / YYYY');
		$helper = $this->getHelper();
		$invoice = $payment->getInvoice();
		$plan = $invoice->getPlan();
		$amount = $helper->getAmount($plan, $invoice->getTotal());
		$currency = $invoice->getCurrency('isocode');
		$cancelLink = PPR::_("index.php?option=com_payplans&view=payment&task=complete&action=cancel&payment_key=" . $payment->getKey() . '&tmpl=component');

		$this->set('amount', $amount);
		$this->set('populateEmail', $populateEmail);
		$this->set('cancelLink', $cancelLink);
		$this->set('storeName', $storeName);
		$this->set('sandbox', $sandbox);
		$this->set('payment', $payment);
		$this->set('publicKey', $publicKey);
		$this->set('currency', $currency);
		$this->set('dateFormat', $dateFormat);
		$this->set('email', $userEmail);
		$this->set('billingDetails', $billingDetails);

		// Get the user business billing details
		if($billingDetails) {
			$billingData = $user->getBusinessData();

			// Get country codes
			$model = PP::model('Country');
			$countries = $model->loadRecords(array('published' => 1));

			$this->set('billingData', $billingData);
			$this->set('countries', $countries);
		}

		$type = $this->getAppParam('form_type', 'form');

		if ($this->getAppParam('enable_sca')) {

			// set form type
			$type = 'form_sca';

			$this->helper->loadLibrary();

			$intent = $this->helper->createPaymentIntent($amount, $currency, $payment, $userEmail);
			$this->set('paymentIntentSecret', $intent->client_secret);
		}

		return $this->display($type);
	}

	/**
	 * Trigger after payment COM_PAYPLANS_APP_STRIPE_LOGGER_ERROR_IN_STRIPE_PAYMENT_PROCESS_DETAILS
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansPaymentAfter(PPPayment $payment, &$action, &$data, $controller)
	{
		if ($action == 'cancel') {
			return true;
		}

		if ($action == 'process') {

			if ($this->getAppParam('enable_sca')) {
				$state = $this->stripeScaProcessPayment($payment, $data);
			} else {
				$state = $this->stripeProcessPayment($payment, $data);
			}

			if ($state) {
				$redirect = PPR::_('index.php?option=com_payplans&view=payment&layout=complete&payment_key='.$payment->getKey() . '&action=success', false);

				return PP::redirect($redirect);
			}
		}
		
		return parent::onPayplansPaymentAfter($payment, $action, $data, $controller);
	}

	/**
	 * Process payments with Stripe
	 *
	 * @since	4.0.0
	 * @access	private
	 */
	private function stripeProcessPayment(&$payment, $data, $invoiceCount = 0, $failureCount = 0)
	{
		$this->helper->loadLibrary();

		$invoice = $payment->getInvoice();
		$plan = $invoice->getPlan();
		
		// This need to be done because stripe accept payment only in cents
		$amount = $this->helper->getAmount($plan, $invoice->getTotal($invoiceCount));

		if (!isset($data['stripeToken']) && !isset($data['process_payment'])) {
			$redirect = PPR::_('index.php?option=com_payplans&view=payment&task=pay&error_msg=Invalid_Token&payment_key=' . $payment->getKey(), false);

			return PP::redirect($redirect);
		}

		$user = PP::user((int) $payment->getBuyer());

		// Check if there is a customer id
		$customerId = $payment->getGatewayParams()->get('stripe_customer', '');

		// Retrieve the customer information
		if ($customerId) {
			$customer = \Stripe\Customer::retrieve($customerId);
		}

		// If the account doesn't exist, create a new account
		if (!$customerId) {

			try {
				$customer = $this->helper->createCustomer($user, $data['stripeToken'], $payment, $data);
			} catch(Exception $e) {

				if ($invoiceCount) {
					$errors['error_code'] = $e->getCode();
					$errors['error_message'] = $e->getMessage();
					$errors['invoice_key'] = $invoice->getKey();

					PPLog::log(PPLogger::LEVEL_ERROR, JText::_('COM_PAYPLANS_APP_STRIPE_LOGGER_ERROR_IN_STRIPE_RESPONSE_INVALID'), $payment, $errors, 'PayplansPaymentFormatter', '', true);

					return false;

				}

				PP::info()->set($e->getMessage(), 'error');

				$redirect = PPR::_('index.php?option=com_payplans&view=payment&task=pay&payment_key=' . $payment->getKey() . '&tmpl=component', false);

				return PP::redirect($redirect);
			}
		}

		// IMPORTANT
		// Stripe does not support free trials directly but provides a work around for trials.
		// Additional transaction can be created, we need to check whether the plan has (1 free trial + recurring or 2 free trial + recurring)
		
		// If the amount is 0 and the invoice does not have free trials, something is not right
		if ($amount == 0 && !$invoice->hasRecurringWithTrials()) {
			die('Invalid amount');
		}

		// Probably this is free trials
		if ($amount == 0 && $invoice->hasRecurringWithTrials()) {
			$transaction = $this->helper->addFreeTrialSupport($payment, $customer);

			return true;
		}

		$errors = array(
			// Standard bills
			'error_code' => '',
			'error_message' => ''
		);

		try {
			$options = array(
				'amount' => $amount,
				'currency' => $invoice->getCurrency('isocode'),
				'customer' => $customer,
				'description' => $invoice->getKey()
			);

			$response = \Stripe\Charge::create($options);


			if ($response->paid) {
				$gatewayParams = $payment->getGatewayParams();

				// Decrease recurrence count when it is marked as paid
				$recurrenceCount = $gatewayParams->get('pending_recur_count');
				if ($recurrenceCount && (!$invoiceCount && !$invoice->isRecurringOnetimeDiscountApplied())) {
					$pendingRecurrenceCount = $recurrenceCount - 1;
					$gatewayParams->set('pending_recur_count', $pendingRecurrenceCount);
					$payment->table->gateway_params = $gatewayParams->toString();
					$payment->table->store();
				}

				$response = (is_object($response)) ? $response->__toArray() : $response;

				$gatewayTransactionId = PP::normalize($response, 'id', 0);

				// Check if previous transactions already exists
				$transactions = $this->getExistingTransaction($invoice->getId(), $gatewayTransactionId, 0, 0);

				if ($transactions) {
					return true;
				}

				$result = $this->helper->processPayment($payment, $response, $customer, $invoiceCount);

				return $result;
			}

			if ($invoiceCount) {
				// Update Failure attempt limit
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

			$response = (is_object($response)) ? $response->__toArray() : $response;
			$errors['error_code'] = $response['failure_code'];
			$errors['error_message']  = $response['failure_message'];

			return $errors;

		} catch(Exception $e) {

			// If some exception is occured then create an log and handle it 
			$username = $user->getUsername();
			$userId	= $user->getId();

			if ($invoiceCount) {
				// Update Failure attempt limit
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

			//It is needed to create a log for wrong response
			$errors['error_code'] = $e->getCode();
			$errors['error_message'] = sprintf(JText::_('COM_PAYPLANS_APP_STRIPE_LOGGER_ERROR_IN_STRIPE_PAYMENT_PROCESS_DETAILS'), $e->getMessage(), $username, $userId, $invoice->getKey());
			$errors['invoice_key'] = $invoice->getKey();
			
			PPLog::log(PPLogger::LEVEL_ERROR, JText::_('COM_PAYPLANS_APP_STRIPE_LOGGER_ERROR_IN_STRIPE_RESPONSE_INVALID'), $payment, $errors, 'PayplansPaymentFormatter', '', true);

			if ($invoiceCount) {
				return false;
			}

			$errors['error_message'] = $e->getMessage();

			$redirect = PPR::_('index.php?option=com_payplans&view=payment&task=pay&payment_key=' . $payment->getKey() . '&error_code=' . $errors['error_code'] . '&error_msg=' . urlencode($errors['error_message']), false);

			return PP::redirect($redirect);
		}
	}

	/**
	 * Process payments with Stripe SCA
	 *
	 * @since	4.0.0
	 * @access	private
	 */
	private function stripeScaProcessPayment(&$payment, $data, $invoiceCount = 0)
	{
		$this->helper->loadLibrary();

		$invoice = $payment->getInvoice();

		if (!isset($data['dataSecret']) && !isset($data['process_payment'])) {
			$redirect = PPR::_('index.php?option=com_payplans&view=payment&task=pay&error_msg=Invalid_Token&payment_key=' . $payment->getKey(), false);

			return PP::redirect($redirect);
		}

		$user = $invoice->getBuyer();
		
		// Retrieve Payment Intent
		$paymentIntentId = $payment->getGatewayParams()->get('payment_intent_id', '');
		$customerId = $payment->getGatewayParams()->get('stripe_customer', '');

		$intent = \Stripe\PaymentIntent::retrieve($paymentIntentId);

		if (!$customerId) {
			try {

			$customer = $this->helper->createSCACustomer($user, $intent, $payment, $data);
			$customerId = $customer->id;

			} catch (Exception $e) {

				if ($invoiceCount) {
					$errors['error_code'] = $e->getCode();
					$errors['error_message'] = $e->getMessage();
					$errors['invoice_key'] = $invoice->getKey();

					PPLog::log(PPLogger::LEVEL_ERROR, JText::_('COM_PAYPLANS_APP_STRIPE_LOGGER_ERROR_IN_STRIPE_RESPONSE_INVALID'), $payment, $errors, 'PayplansPaymentFormatter', '', true);

					return false;

				}

				PP::info()->set($e->getMessage(), 'error');

				$redirect = PPR::_('index.php?option=com_payplans&view=payment&task=pay&payment_key=' . $payment->getKey() . '&tmpl=component', false);

				return PP::redirect($redirect);
			}
		}

		if ($invoice->isRecurring) {
			$paymentMethod = \Stripe\PaymentMethod::retrieve($intent->payment_method);
			$paymentMethod->attach(['customer' => $customerId]);
		}

		$paymentIntent = (is_object($intent)) ? $intent->__toArray() : $intent;
		return $this->helper->processSCAPayment($payment, $paymentIntent,  $customerId);
		
	}

	/**
	 * Initiated during cron to process recurring payments
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function processPayment(PPPayment $payment, $invoiceCount)
	{
		// Check for failure attempt limit
		$failureCount = $payment->getParams()->get('failure_attempt');
		$failureLimit = $this->config->get('recurring_failure_limit');

		if($failureCount && $failureCount >= $failureLimit){
			return false;   // do nothing if faulire attempt limit exhausted 
		}

		$invoice = $payment->getInvoice();

		// If it isn't recurring, ignore this altogether
		if (!$invoice->isRecurring()) {
			return;
		}

		$lifetime = false;
		$lifetime = ($invoice->getRecurrenceCount() == 0)? true : false;
		
		$counter = $invoiceCount +1;

		$gatewayParams = $payment->getGatewayParams();
		$recurrenceCount = $gatewayParams->get('pending_recur_count');

		if ($recurrenceCount > 0 || $lifetime) {

			// Checking for payment intent for older subscription before sca enable
			$paymentIntentId = $payment->getGatewayParams()->get('payment_intent_id', '');
			if ($this->getAppParam('enable_sca') && $paymentIntentId) {
				$this->stripeProcessRecurringPayment($payment, array('process_payment' => true), $counter, $failureCount);
			} else {
				$this->stripeProcessPayment($payment, array('process_payment' => true), $counter, $failureCount);
			}
		}
	}

	/**
	 * Triggered when refunding a transaction
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function refundRequest(PPTransaction $transaction, $amount)
	{
		$this->helper->loadLibrary();
		
		$plan = $transaction->getInvoice()->getPlan();

		$gatewayTransactionId = $transaction->getGatewayTxnId();
		$amount = $this->helper->getAmount($plan, $amount);

		return $this->helper->refund($transaction, $amount);
	}
	
	/**
	 * Triggered to terminate a payment
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansPaymentTerminate(PPPayment $payment, $invoiceController) 
	{
		parent::onPayplansPaymentTerminate($payment, $invoiceController);

		return true;
	}

	/**
	 * Render actions for a subscription
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getSubscriptionActions($subscription)
	{
		if (!$subscription->isRecurring()) {
			return;
		}

		// To prevent multiple click events
		$uid = uniqid();

		$this->set('uid', $uid);
		$this->set('subscription', $subscription);
		$this->set('appId', $this->getId());

		$output = $this->display('button');

		return $output;
	}

	/**
	* Process Recurring Payments
	*
	* @since 4.0.0
	* @access public
	*/
	public function stripeProcessRecurringPayment(&$payment, $data, $invoiceCount = 0, $failureCount = 0)
	{
		$this->helper->loadLibrary();

		$invoice = $payment->getInvoice();
		$plan = $invoice->getPlan();

		// This need to be done because stripe accept payment only in cents
		$amount = $this->helper->getAmount($plan, $invoice->getTotal($invoiceCount));


		if (!isset($data['dataSecret']) && !isset($data['process_payment'])) {
			$redirect = PPR::_('index.php?option=com_payplans&view=payment&task=pay&error_msg=Invalid_Token&payment_key=' . $payment->getKey(), false);

			return PP::redirect($redirect);
		}

		$user = $invoice->getBuyer();
		
		// Retrieve Payment Intent
		$paymentIntentId = $payment->getGatewayParams()->get('payment_intent_id', '');

		$intent = \Stripe\PaymentIntent::retrieve($paymentIntentId);

		$customerId = $payment->getGatewayParams()->get('stripe_customer', '');
		$paymentMethodId = $payment->getGatewayParams()->get('payment_method_id', '');

		$this->helper->loadLibrary();

		try {

			$paymentIntent = \Stripe\PaymentIntent::create([
						    'amount' => $amount,
						    'currency' => $invoice->getCurrency('isocode'),
						    'payment_method_types' => ['card'],
						    'customer' => $customerId,
						    'payment_method' => $paymentMethodId,
						    'off_session' => true,
						    'confirm' => true,
						]);

			$paymentIntent = (is_object($intent)) ? $intent->__toArray() : $intent;
			return $this->helper->processSCAPayment($payment, $paymentIntent,  $customerId, $failureCount, $invoiceCount);
		}
		catch(Exception $e) {

			// Update Failure attempt limit
			if ($failureCount != 0) {
				$failureCount++;
			} else {
				$failureCount = 1;
			}

			$params = new JRegistry();
			$params->set('failure_attempt', $failureCount);

			$payment->params = $params->toString();
			$payment->save();

			$errors['error_code'] = $e->getCode();
			$errors['error_message'] = $e->getMessage();
			$errors['invoice_key'] = $invoice->getKey();

			PPLog::log(PPLogger::LEVEL_ERROR, JText::_('COM_PAYPLANS_APP_STRIPE_LOGGER_ERROR_IN_STRIPE_RESPONSE_INVALID'), $payment, $errors, 'PayplansPaymentFormatter', '', true);

			return false;
		}
	}
}
