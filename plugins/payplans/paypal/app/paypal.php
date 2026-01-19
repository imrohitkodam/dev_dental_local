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

class PPAppPaypal extends PPAppPayment
{
	public function isApplicable($refObject = null, $eventName = '')
	{
		// return true for event onPayplansControllerCreation
		if ($eventName == 'onPayplansControllerCreation') {
			return true;
		}
		
		return parent::isApplicable($refObject, $eventName);
	}

	/**
	 * Generates the payment form during checkout
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansPaymentForm(PPPayment $payment, $data = null)
	{
		// Check if this payment is already in process
		$paymentInprocess = $payment->getGatewayParams()->get('payment_in_process', 0);

		if ($paymentInprocess) {
			return $this->display('payment_inprocess');
		}


		$helper = $this->getHelper();
		$invoice = $payment->getInvoice();

		$merchantEmail = $helper->getMerchantEmail();

		$postUrl = $helper->getPaypalUrl();
		$callbacks = $helper->getCallbackUrls($payment);

		$languageEncoding = $helper->getLanguageEncoding();

		$this->set('merchant_email', $merchantEmail);
		$this->set('payment', $payment);
		$this->set('invoice', $invoice);
		$this->set('post_url', $postUrl);
		$this->set('return_url', $callbacks['return']);
		$this->set('cancel_url', $callbacks['cancel']);
		$this->set('notify_url', $callbacks['notify']);
		$this->set('cmd', '_xclick');
		$this->set('language_encoding', $languageEncoding);

		if (!$invoice->isRecurring()) {
			return $this->display('form');
		}

		$counter = $invoice->getCounter();
		
		// Regular expiration parameters
		$regularExpTime = $invoice->getExpiration(PP_RECURRING);
		$regularExpTime = $helper->getRecurrenceTime($regularExpTime);
		
		$this->set('p3', $regularExpTime['period']);
		$this->set('t3', $regularExpTime['unit']);
		
		// Regular recurring parameters
		if ($invoice->getRecurringType() == PP_PRICE_RECURRING) {
			$this->set('a3', $invoice->getTotal());
		}
		
		// First trial subscription parameters
		if ($invoice->hasRecurringWithTrials()) {

			$expTime = $invoice->getExpiration(PP_PRICE_RECURRING_TRIAL_1);
			$expTime = $helper->getRecurrenceTime($expTime);

			$this->set('a1', $invoice->getTotal());
			$this->set('p1', $expTime['period']);
			$this->set('t1', $expTime['unit']);
			$this->set('a3', $invoice->getTotal($counter + 1));
		}
		
		// Second trial subscription parameters
		if ($invoice->getRecurringType() == PP_PRICE_RECURRING_TRIAL_2) {
			$expTime = $invoice->getExpiration(PP_RECURRING_TRIAL_2);
			$expTime = $helper->getRecurrenceTime($expTime);

			$this->set('a2', $invoice->getTotal($counter + 1));
			$this->set('p2', $expTime['period']);
			$this->set('t2', $expTime['unit']);
			$this->set('a3', $invoice->getTotal($counter + 2));
		}

		// Determine whether failed recurring payment should re-attempt or not
		$reAttempt = $helper->isReattemptFailedRecurring();
		$sra = 0;
		if ($reAttempt) {
			$sra = 1;
		}

		$recurrenceCount = $invoice->getRecurrenceCount();
		$this->set('srt', $recurrenceCount);
		$this->set('sra', $sra);
		$this->set('cmd', '_xclick-subscriptions');
			
		return $this->display('form');
	}

	/**
	 * This method is triggered when paypal notifies us
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansControllerCreation($view, $controller, $task, $format)
	{
		if ($view != 'payment' || $task != 'notify') {
			return true;
		}
		
		$paymentKey = $this->input->get('invoice', null);

		if (!empty($paymentKey)) {
			$this->input->set('payment_key', $paymentKey, 'POST');
		}
		
		return true;
	}

	/**
	 * Retrieves the verification library
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getVerificationLibrary()
	{
		$params = $this->getAppParams();

		$lib = new PPValidationPaypal($params);

		return $lib;
	}

	/**
	 * Log after a payment is received
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansPaymentAfter(PPPayment $payment, &$action, &$data, $controller)
	{			
		if ($action == 'error') {
			return true;
		}

		// To prevent multiple payment for same invoice
		if ($action == 'success') {

			$gatewayParams = $payment->getGatewayParams();
			$gatewayParams->set('payment_in_process', 1);
			$payment->gateway_params = $gatewayParams->toString();
			$payment->save();
		}
		
		return parent::onPayplansPaymentAfter($payment, $action, $data, $controller);
	}

	/**
	 * This method is triggered when PayPal connects to our payment notification page
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansPaymentNotify(PPPayment $payment, $data, $controller)
	{
		$invoice = $payment->getInvoice();
		$errors = array();	

		$helper = $this->getHelper();	

		// Check with PayPal to see if the IPN is valid
		$valid = $helper->validateIPN($data, $payment, $invoice, $this->getAppParam('sandbox', false));

		if (!$valid) {
			$transaction = PP::transaction();
			$transaction->user_id = $payment->getBuyer();
			$transaction->invoice_id = $payment->getId();
			$transaction->gateway_txn_id = 0;
			$transaction->gateway_subscr_id = 0;
			$transaction->gateway_parent_txn = 0;
			$transaction->params = json_encode($data);
			$transaction->amount = 0;
			$transaction->message = 'COM_PAYPLANS_APP_PAYPAL_INVALID_IPN';
			$transaction->save();
		
			$errors[] = JText::_('COM_PAYPLANS_INVALID_IPN');
			$message = JText::_('COM_PAYPLANS_LOGGER_PAYMENT_INVALID_IPN');

			PPLog::log(PPLogger::LEVEL_ERROR, $message, $payment, $data, 'PayplansPaymentFormatter', '', true);
			
			return false;
		}

		// Update payment in-process status
		$gatewayParams = $payment->getGatewayParams();
		$gatewayParams->set('payment_in_process', 0);
		$payment->gateway_params = $gatewayParams->toString();
		$payment->save();
		
		// If same notification came more than once, check if transaction already exists
		// if yes then do nothing and return
		$txnId = isset($data['txn_id']) ? $data['txn_id'] : 0;
		$subscrId = isset($data['subscr_id']) ? $data['subscr_id'] : 0;
		$parentTxn = 0;

		$transactions = $this->getExistingTransaction($invoice->getId(), $txnId, $subscrId, $parentTxn);

		if ($transactions) {			
			foreach ($transactions as $transaction) {
				
				$transaction = PP::transaction($transaction->transaction_id, null, $transaction);
				if ($transaction->getParam('payment_status','') == $data['payment_status']) {
					return true;
				} else {

					if ($data['payment_status'] == 'Completed'){

						//check for Fixed payments only
						if (!$invoice->isRecurring() && $invoice->isPaid()) {
								return true;
						}
					}
				}
			}
		}

		// If notification data contain special charcter then encode that data
		$response = array();

		foreach ($data as $key => $value) {
			$response[$key] = urlencode(stripslashes($value));
		}
		
		// Get the transaction instace of lib
		$transaction = PP::createTransaction($invoice, $payment, $txnId, $subscrId, 0, $response);

		$recurringCallback = isset($data['txn_type']) ? 'onProcess' . strtolower($data['txn_type']) : false;
		$standardCallback = isset($data['payment_status']) ? 'onPayment' . strtolower($data['payment_status']) : false;


		$errors = '';

		$lib = $this->getVerificationLibrary();

		// Recurring subscriptions
		if ($recurringCallback && method_exists($lib, $recurringCallback)) {
			$errors = $lib->$recurringCallback($payment, $data, $transaction);			
		}

		// Non recurring subscriptions
		if ($standardCallback && method_exists($lib, $standardCallback)) {
			$errors = $lib->$standardCallback($payment, $data, $transaction);
		}

		//if error present in the transaction then redirect to error page
		if ($errors) {
			$message = JText::_('COM_PAYPLANS_LOGGER_ERROR_IN_PAYPAL_PAYMENT_PROCESS');
			$response = array();
			$response['error_message'] = array_pop($errors);
			$response['response_data'] = $data; 
			
			PPLog::log(PPLogger::LEVEL_ERROR, $message, $payment, $response, 'PayplansPaymentFormatter', '', true);
		}
	
		//store the response in the payment AND save the payment
		if (!$transaction->save()) {
			$message = JText::_('COM_PAYPLANS_LOGGER_ERROR_TRANSACTION_SAVE_FAILD');
			PPLog::log(PPLogger::LEVEL_ERROR, $message, $payment, $data, 'PayplansPaymentFormatter', '', true);
		}
		
		// Save the payment
		$payment->save();

		return true;
	}
}