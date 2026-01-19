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

class PPAppCcavenue extends PPAppPayment
{
	/**
	 * Override parent's isApplicable method
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function isApplicable($refObject = null, $eventName = '')
	{
		if ($eventName == 'onPayplansControllerCreation') {
			return true;
		}
		
		return parent::isApplicable($refObject, $eventName);
	}


	/**
	 * When controller called
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansControllerCreation($view, $controller, $task, $format)
	{ 
		$orderId = $this->input->get('orderNo', null);

		if ($orderId) {

			$view = 'payment';
			$task = 'complete';

			$this->input->set('payment_key', $orderId, 'POST');

			$this->input->set('view', 'payment', 'POST');
			$this->input->set('task', 'complete', 'POST');

		} 

		return true;
	}

	/**
	 * Renders the payment form
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function onPayplansPaymentForm(PPPayment $payment, $data = null)
	{
		if (is_object($data)) {
			$data = (array)$data;
		}

		$invoice = $payment->getInvoice();
		
		$helper = $this->getHelper();
		$data = $helper->createPaymentRequest($payment, $invoice);
		$postUrl = $helper->getPostUrl($payment->getKey());

		$this->set('data', $data);
		$this->set('payment', $payment);
		$this->set('postUrl', $postUrl);
		
		return $this->display('form');
	}

	/**
	 * Triggered after payment process
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function onPayplansPaymentAfter(PPPayment $payment, &$action, &$data, $controller)
	{
		if ($action == 'cancel') {
				return true;
		}
		
		if (!isset($data["encResp"])) {
			return $this->initiatePaymentRequest($data);
		}

		$invoice = $payment->getInvoice();
		$helper = $this->getHelper();

		// Decrypt response sent by CcAvenue serv
		list($orderStatus, $response) = $helper->validateData($data);
		
		$this->_processPayment($invoice, $payment, $orderStatus, $response);

		return parent::onPayplansPaymentAfter($payment, $action, $data, $controller);
	}
	
	/**
	 * Initiate Payment Request
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function initiatePaymentRequest($data)
	{
		$helper = $this->getHelper();

		$encryptedData = $helper->getEncryptedData($data);
		$accessCode = $helper->getAccessCode();	
		$merchantId = $helper->getMerchantId();		
		$formUrl = $helper->getFormUrl();
		
		// get redirection url for further payment process
		$redirectUrl = $formUrl.'&encRequest='.$encryptedData.'&access_code='.$accessCode;			
		
		return PP::redirect($redirectUrl);
	}
	
	/**
	 * Process for payment
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function _processPayment($invoice, $payment, $orderStatus, $data)
	{
		$transactionId = PP::normalize($data, 'tracking_id', 0);
		$subscriptionId = PP::normalize($data, 'Customer_identifier', 0);

		// Check for duplicate transactions
		$transactions = $this->getExistingTransaction($invoice->getId(), $transactionId, 0, 0);
		if ($transactions) {
			foreach ($transactions as $transaction) {
				$transaction = PP::transaction($transaction->transaction_id);
				$params = $transaction->getParams();

				if ($params->get('order_status', '') === $orderStatus) {
					return true;
				}
			}
		 }

		// create the transaction
		$transaction = PP::createTransaction($invoice, $payment, $transactionId, $subscriptionId, 0, $data);

		$error = '';
		if ($orderStatus === "Success") {
			$message = JText::_('COM_PAYPLANS_PAYMENT_APP_CCAVENUE_TRANSACTION_COMPLETED');
			$transaction->amount =  $data['amount'];
			$transaction->message =  $message;

		} else if ($orderStatus === "Aborted") {
			$error = JText::_('COM_PAYPLANS_PAYMENT_APP_CCAVENUE_TRANSACTION_ABORTED');
			$transaction->message = $error;
		}
		else if ($orderStatus === "Failure") {
			$error = JText::_('COM_PAYPLANS_PAYMENT_APP_CCAVENUE_TRANSACTION_FAILED');
			$transaction->message =  $error;
		} else {
			$error = JText::_('COM_PAYPLANS_PAYMENT_APP_CCAVENUE_TRANSACTION_ILLEGAL');
			$transaction->message = $error;
		}
		
		if ($error) {
			$message = JText::_('COM_PAYPLANS_LOGGER_ERROR_IN_CCAVENUE_PAYMENT_PROCESS');
			PPLog::log(PPLogger::LEVEL_ERROR, $message, $payment, $data, 'PayplansPaymentFormatter', '', true);
		}
		
		// save transaction
		$state = $transaction->save();
		if (!$state) {
			$message = JText::_('COM_PAYPLANS_LOGGER_ERROR_TRANSACTION_SAVE_FAILD');
			PPLog::log(PPLogger::LEVEL_ERROR, $message, $payment, $data, 'PayplansPaymentFormatter', '', true);
		}
		
		$payment->save();
		return true;
	}
	
}
