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

class PPAppBitpay extends PPAppPayment
{
	/**
	 * Override parent's isApplicable method
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansPaymentForm(PPPayment $payment, $data = null)
	{
		$helper = $this->getHelper();
		$invoice = $payment->getInvoice();
		$recurring = $invoice->isRecurring();

		if ($recurring) {
			PP::info()->set('COM_PP_SELECTED_PAYMENT_METHOD_DOES_NOT_SUPPORT_RECURRING', 'error');
		}

		$user = $invoice->getBuyer();
		$token = $helper->getToken();

		$postData = [
			'invoice_key' => $invoice->getKey(), 
			'hash' => crypt($invoice->getKey(), $token)
		];

		$rootUrl = $helper->getNotifyUrl();

		$options = [
			'price' => $invoice->getTotal(),
			'currency' => $invoice->getCurrency('isocode'),
			'notificationURL' => $rootUrl . 'index.php?option=com_payplans&view=payment&task=notify&gateway=bitpay&payment_key=' . $payment->getKey(),
			'redirectURL' => $rootUrl . 'index.php?option=com_payplans&gateway=bitpay&view=payment&task=complete&action=success&payment_key=' . $payment->getKey(),
			'transactionSpeed' => 'high',
			'fullNotifications' => true,
			'notificationEmail' => $user->getEmail(),
			'orderID' => $payment->getKey(),
			'posData' => json_encode($postData),
			'token' => $token,
		];

		// $post = json_encode($options);
		$post = PP::makeObject($options);

		$redirectUrl = $helper->createInvoice($post, $payment, $invoice);

		if (!$redirectUrl) {
			PP::info()->set('COM_PP_BITPAY_INVALID_RESPONSE', 'error');

			return false;
		}

		$transaction = PP::transaction();
		$transaction->user_id = $payment->getBuyer();
		$transaction->invoice_id = $invoice->getId();
		$transaction->payment_id = $payment->getId();

		$transactionParams = new JRegistry($response);
		$transaction->params = $transactionParams->toString();
		$transaction->amount = 0;
		$transaction->message = 'COM_PAYPLANS_APP_BITPAY_REDIRECTED_TO_BITPAY_SUCCESSFULLY';
		$transaction->save();

		// Redirect to bitpay's url
		PP::redirect($redirectUrl);
	}
	
	/**
	 * Triggered when notification came from bitpay
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansPaymentNotify(PPPayment $payment, $data, $controller)
	{
		$helper = $this->getHelper();
		$invoice = $payment->getInvoice();
		
		// Get the transaction instace of lib
		$dataInput = file_get_contents("php://input");
		$result = $helper->validate($dataInput);
		
		if (!$result) {
			$errors = array(JText::_('COM_PP_BITPAY_INVALID_DATA'));
			return $errors;
		}
		
		// Get invoice confirmation response
		$response = $helper->confirm($result);

		// Ensure that there aren't any duplicates
		$transactionId = $response->getId();
		$transactions = $this->getExistingTransaction($invoice->getId(), $transactionId, 0, 0);

		if (!empty($transactions)) {
			foreach ($transactions as $transaction) {
				$transaction = PP::transaction($transaction->transaction_id, null, $transaction);

				if (strtolower($transaction->getParam('status','')) == strtolower($response->getStatus())) {
						return true;
				}
			}
		}

		$transaction = PP::transaction();
		$transaction->user_id = $payment->getBuyer();
		$transaction->invoice_id = $invoice->getId();
		$transaction->payment_id = $payment->getId();
		$transaction->gateway_txn_id = $response->getId();
		$transaction->gateway_parent_txn = 0;

		$transactionParams = new JRegistry($response);
		$transaction->params = $transactionParams->toString();
 
		$method = '';
		$message = '';

		if ($response->getStatus()) {
			$method = 'process' . ucfirst($response->getStatus());
		}

		$exists = method_exists($helper, $method);

		if (!$method || !$exists) {
			$message = 'COM_PAYPLANS_APP_BITPAY_INVALID_MESSAGE_TYPE';
		}

		if ($exists) {
			$helper->$method($payment, $response, $transaction);
		}

		$transaction->save();

		if ($message) {
			return $message;
		}

		return true;
	}
}
