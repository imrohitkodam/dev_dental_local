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

class PPAppOfflinepay extends PPAppPayment
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
	 * Renders the payment form during checkout
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansPaymentForm(PPPayment $payment , $data = null)
	{
		$invoice = $payment->getInvoice();
		$amount = $invoice->getTotal();
		$currency = $invoice->getCurrency('symbol','isocode');
		$params = $this->getAppParams();
		$email = $params->get('email', '');

		$this->set('email', $email);
		$this->set('params', $params);
		$this->set('invoice', $invoice);
		$this->set('payment', $payment);
		$this->set('amount', $amount);
		$this->set('currency', $currency);

		return $this->display('form');
	}

	/**
	 * Renders after the payment
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansPaymentAfter(PPPayment $payment, &$action, &$data, $controller)
	{
		$gatewayParams = PP::normalize($data, 'gateway_params', '');

		// Ensure that the data is passed correctly
		if (!$gatewayParams || !isset($gatewayParams['amount'])) {
			return false;
		}

		if ($action != 'success') {
			return;
		}

		$params = $this->getAppParams();
		$gatewayParams = new JRegistry(array_merge($params->toArray(), $gatewayParams));
		$payment->gateway_params = $gatewayParams->toString();

		$payment->save();

		$invoice = $payment->getInvoice();

		// Once a payment is saved, create a transaction
		$transaction = PP::createTransaction($invoice, $payment, $gatewayParams->get('id'), 0, 0);
		$transaction->params = $gatewayParams->toString();
		$transaction->amount = 0;
		$transaction->message = JText::_('COM_PAYPLANS_APP_OFFLINE_TRANSACTION_CREATED_FOR_INVOICE');
		
		$transaction->save();

		return true;
	}

	/**
	 * Record a transaction
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansTransactionRecord(PPTransaction $transaction = null)
	{
		$payment = $transaction->getPayment();
		
		// If gateway parameter exists then display in the transaction record
		if ($payment->getGatewayParams()) {
			$this->set('transaction_html', $payment->getGatewayParams()->toArray());
			
			return $this->display('transaction');
		}	
	}

	/**
	 * Occurs before a transaction is stored
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansTransactionBeforeSave($previous, PPTransaction $new)
	{
		// This should only get executed when this is a new transaction
		if ($previous != null) {
			return true;
		}

		$params = new JRegistry($new->getParams());

		//if gateway transaction id is not mentioned then 
		//fetch the txn id from payment params and set as gateway txn id
		$gatewayTxnId = $new->gateway_txn_id;

		if (!$gatewayTxnId && $params->get('id')) {
			$new->gateway_txn_id = $params->get('id');
		}

		$message = $new->message;

		if (!$message) {
			$new->message = 'COM_PAYPLANS_APP_OFFLINE_TRANSACTION_CREATED';
		}
		
		return true;
	}

	/**
	 * Triggered when a payment is terminated
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansPaymentTerminate(PPPayment $payment, $controller)
	{
		parent::onPayplansPaymentTerminate($payment, $controller);
		return true;
	}

	/**
	 * Notify when transaction is saved
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansTransactionAfterSave($previousTransaction, $newTransaction)
	{
		if (PP::isFromAdmin() || !$this->getAppParam('notify_users')) {
			return true;
		}
		
		$payment = $newTransaction->getPayment();
		$params = $newTransaction->getParams();
		$buyer = $newTransaction->getBuyer();
		$invoice = $payment->getInvoice();

		$bankName = $this->getAppParam('bankname');
		$bankAccount = $this->getAppParam('account_number');
		$accountName = $this->getAppParam('account_name');

		$data = array(
			'buyer' => $buyer,
			'params' => $params,
			'name' => $buyer->getDisplayName(),
			'email' => $buyer->getEmail(),
			'username' => $buyer->getUsername(),
			'bankName' => $bankName,
			'bankAccount' => $bankAccount,
			'accountName' => $accountName,
			'invoice' => $invoice
		);

		$subject = JText::_('COM_PAYPLANS_APP_OFFLINE_ADMIN_EMAIL_SUBJECT');
		$namespace = 'emails/offlinepay/admin.cheque';

		if ($params->get('from') == 'Cash') {
			$namespace = 'emails/offlinepay/admin.cash';
		}

		// Send out the e-mails to the admins now
		$mailer = PP::mailer();
		$emails = $mailer->getAdminEmails();

		if ($emails) {
			foreach ($emails as $email) {
				$mailer->send($email, $subject, $namespace, $data);
			}
		}

		// Now, we'll need to notify the user
		$subject = JText::_('COM_PAYPLANS_APP_OFFLINE_USER_EMAIL_SUBJECT');
		$namespace = 'emails/offlinepay/user.completed';

		$attachment = array();

		if ($this->config->get('enable_pdf_invoice')) {

			$pdf = PP::pdf($invoice);
			$pdf->generateFile();
			$pdfFile = $pdf->getFilePath();

			// If the filepath is exist, include it in attachment
			if ($pdfFile) {
				$attachment[] = $pdfFile;
			}
		}

		$state = $mailer->send($buyer->getEmail(), $subject, $namespace, $data, array($attachment));

		if ($attachment) {
			$pdf->delete();
		}

		return true;
	}

	/**
	 * Refunds a transaction
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function refundRequest(PPTransaction $transaction, $refund_amount)
	{
		// Get Invoice 
		$invoice = $transaction->getInvoice();
		$payment    = $transaction->getPayment();
			
		if ($payment) {

			$transactionId = $transaction->getGatewayTxnId();
			$newtransaction = PP::createTransaction($invoice, $payment, $transactionId, 0, 0, $response);

			$newtransaction->amount = - $refund_amount;
			$newtransaction->message = 'COM_PAYPLANS_TRANSACTION_TRANSACTION_MADE_FOR_REFUND';
			$newtransaction->save();

			return true;
		}

		return false;

	}
}