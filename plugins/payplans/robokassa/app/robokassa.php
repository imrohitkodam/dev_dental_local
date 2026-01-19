<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) 2010 - 2020 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class PPAppRobokassa extends PPAppPayment
{
	/**
	 * Override parent's isApplicable method
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function isApplicable($refObject = null, $eventName='')
	{
		if ($eventName == 'onPayplansControllerCreation') {
			return true;
		}

		return parent::isApplicable($refObject, $eventName);
	}

	/**
	 * Triggered during controller creation to determine if it should execute any tasks
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansControllerCreation($view, $controller, $task, $format)
	{
		if ($view != 'payment' || ($task != 'notify' && $task != 'complete')) {
			return;
		}

		$paymentKey = $this->input->get('Shp_paymentKey', null);
		if (!empty($paymentKey)) {
			$this->input->set('payment_key', $paymentKey, 'POST');
		}

		return true;
	}

	/**
	 * Renders the payment form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansPaymentForm(PPPayment $payment, $data = null)
	{
		$invoice = $payment->getInvoice();
		$plan = $invoice->getPlan();
		$amount = $invoice->getTotal();
		$paymentKey = $payment->getKey();

		$helper = $this->getHelper();
		$formUrl = $helper->getFormUrl();

		$merchantLogin = $helper->getMerchantLogin();
		$merchantPassword = $helper->getMerchantPass1();

		$invId = $helper->getInvId();
		$outSum = $amount;

		$signature = $helper->getSignature($outSum, $invId, $paymentKey);
		$buyer = $invoice->getBuyer(true);

		$this->set('formUrl', $formUrl);
		$this->set('merchantLogin', $merchantLogin);
		$this->set('invId', $invId);
		$this->set('desc', $plan->getTitle());
		$this->set('signature', $signature);
		$this->set('outSum', $outSum);
		$this->set('currencyLabel', $invoice->getCurrency('isocode'));
		$this->set('Shp_paymentKey', $paymentKey);
		$this->set('sandbox', $helper->isSandbox());
		$this->set('email', $buyer->getEmail());

		if ($helper->isEuroCurrencySupported()) {
			$this->set('OutSumCurrency', $invoice->getCurrency('isocode'));
		}

		return $this->display('form');
	}

	/**
	 * IPN from Robokassa
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansPaymentNotify(PPPayment $payment, $data, $controller)
	{
		$invoice = $payment->getInvoice();
		$helper = $this->getHelper();

		$state = $helper->validate($data);
		if (!$state) {
			$message = JText::_('COM_PAYPLANS_APP_ROBOKASSA_INVALID_CRC');
			PPLog::log(PPLogger::LEVEL_ERROR, $message, $payment, $data, 'PayplansPaymentFormatter');

			return false;
		}

		$transactionId = PP::normalize($data, 'InvId', 0);
		$transaction = PP::createTransaction($invoice, $payment, $transactionId, 0, 0, $data);
		$transaction->message = 'COM_PAYPLANS_APP_ROBOKASSA_TRANSACTION_INITIATED';
		$transaction->save();

		if (isset($data['IsTest']) && $data['IsTest']) {
			$transactionId = PP::normalize($data, 'InvId', 0);
			$transaction = PP::createTransaction($invoice, $payment, $transactionId, 0, 0, $data);
			$transaction->amount = $invoice->getTotal();
			$transaction->message = 'COM_PAYPLANS_APP_ROBOKASSA_TRANSACTION_COMPLETED';

			$transaction->save();
			return 'OK'.$transactionId;
		}

		// if same notification came more than one time
		// Check if transaction already exists,if yes then do nothing and return

		//Special Case :- Since robokassa does not allow multiple notification for single transaction(invId) hence we are not handling
		//multiple transaction check here.

		// Check the status of payment notification
		$response = $helper->getPaymentStatus($transactionId);

		// Check for duplicate notifications
		$transactions = $this->getExistingTransaction($invoice->getId(), $transactionId, 0, 0);
		if ($transactions) {
			foreach ($transactions as $transaction) {
				$transaction = PP::transaction($transaction);

				if ($transaction->getParam('State_Code','') == $response['State_Code']) {
					return true;
				}
			}
		}

		$transaction = PP::createTransaction($invoice, $payment, $transactionId, $subscriptionId, 0, $response);
		if ($response['State_Code'] == 100) {

			$transaction->amount = $invoice->getTotal();
			$transaction->message = 'COM_PAYPLANS_APP_ROBOKASSA_TRANSACTION_COMPLETED';
		} else {
			$transaction->message = 'COM_PAYPLANS_APP_ROBOKASSA_TRANSACTION_IN_PROCESS';
		}

		$transaction->save();

		// Special case as in robokassa we have to return the 'OKInvId' from notify script. Where InvId is the unique invoice id. 		
		return 'OK' . $transactionId;
	}

	/**
	 * Triggered after being redirected to payment complete page
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansPaymentAfter(PPPayment $payment, &$action, &$data, $controller)
	{
		if ($action == 'error') {

			// Assuming that it comes in here, there are errors
			$invoice = $payment->getInvoice();
			$invoiceId = $data['InvId'];

			$helper = $this->getHelper();
			$response = $helper->getPaymentStatus($invoiceId);

			$transactionId = PP::normalize($data, 'InvId', 0);
			$subscriptionId = 0;
			$parentTransaction = 0;

			// Check for duplicate transactions
			$transactions = $this->getExistingTransaction($invoice->getId(), $transactionId, $subscriptionId, $parentTransaction);

			if ($transactions) {
				foreach ($transactions as $transaction) {
					$transaction = PP::transaction($transaction->transaction_id);
					if ($transaction->getParam('State_Code','') == $response['State_Code']) {
						return true;
					}
				}
			}

			$transaction = PP::createTransaction($invoice, $payment, $transactionId, $subscriptionId, $parentTransaction, $data);

			if ($response['State_Code'] == 100) {
				$transaction->amount = $invoice->getTotal();
				$transaction->message =	'COM_PAYPLANS_APP_ROBOKASSA_TRANSACTION_COMPLETED';
			} else {
				$transaction->message = 'COM_PAYPLANS_APP_ROBOKASSA_TRANSACTION_NOT_COMPLETED';
			}

			$transaction->save();
		}

		return parent::onPayplansPaymentAfter($payment, $action, $data, $controller);

	}

}