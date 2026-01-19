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

class PPAppStripeAlipay extends PPAppPayment
{
	/**
	 * Override parent's isApplicable method
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function isApplicable($refObject = null, $eventName='')
	{
		// return true for event onPayplansControllerCreation
		if ($eventName == 'onPayplansControllerCreation') {
			return true;
		}
		
		return parent::isApplicable($refObject, $eventName);
	}

	/**
	 * When controller called, we need to manipulate payment views
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansControllerCreation($view, $controller, $task, $format)
	{
		if ($view != 'payment' || $task != 'notify') {
			return true;
		}	


		$contents = file_get_contents('php://input');

		$headers = isset($_SERVER['HTTP_STRIPE_SIGNATURE']) ? $_SERVER['HTTP_STRIPE_SIGNATURE'] : '';

		// Verify IPN signature
		$verified = $this->helper->verifyWebhookSignature($contents, $headers);

		if (!$verified) {
			http_response_code(400);
			exit();
		}

		$obj = json_decode($contents);
		$metaData = $obj->data->object->metadata;

		$this->input->set('payment_key', $metaData->payment_key);
		$this->input->set('ipn_data', $contents);

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
		
		$helper = $this->getHelper();
		$invoice = $payment->getInvoice();
		$plan = $invoice->getPlan();
		$amount = $helper->getAmount($plan, $invoice->getTotal());
		$currency = $invoice->getCurrency('isocode');

		 //$returnUrl = rtrim(JURI::root(), '/') . '/index.php?option=com_payplans&view=payment&task=complete&';
		 $returnUrl = rtrim(JURI::root(), '/') . '/index.php?option=com_payplans&view=payment&task=complete&action=success&payment_key='.$payment->getKey().'&tmpl=component';

		
		$this->helper->loadLibrary();

		$intent = $this->helper->createPaymentIntent($amount, $currency, $payment, $userEmail);
		$this->set('paymentIntentSecret', $intent->client_secret);

		$this->set('payment', $payment);
		$this->set('publicKey', $publicKey);
		$this->set('returnUrl', $returnUrl);

		return $this->display('form');
	}

	/**
	 * Trigger after payment
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onPayplansPaymentAfter(PPPayment $payment, &$action, &$data, $controller)
	{
		if ($action == 'cancel') {
			return true;
		}
		
		return parent::onPayplansPaymentAfter($payment, $action, $data, $controller);
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
	 * Triggered during invoice.payment_succeeded on stripe when using subscriptions web hook
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function onPayplansPaymentNotify(PPPayment $payment, $data, $controller)
	{
		$helper = $this->getHelper();
		$invoice = $payment->getInvoice();
		$ipnData = json_decode($data['ipn_data']);
		$txnId = $ipnData->id;

		// Payment was successful for rebills
		if ($ipnData->type != 'payment_intent.succeeded' && $ipnData->type != 'payment_intent.payment_failed') {
			return true;
		}

		// Create a new transaction instance
		$transaction = PP::createTransaction($invoice, $payment, $txnId, 0, 0, $data);

		if ($ipnData->type == 'payment_intent.succeeded' && $ipnData->data->object->status == 'succeeded') {

			$transaction->amount = ($ipnData->data->object->amount / 100);	
			$transaction->message = 'COM_PP_STRIPEALIPAY_TRANSACTION_COMPLETED';

		} else {
			$transaction->message = 'COM_PP_STRIPEALIPAY_TRANSACTION_NOT_COMPLETED';
		}

		//store the response in the payment AND save the payment
		if (!$transaction->save()) {
			$message = JText::_('COM_PAYPLANS_LOGGER_ERROR_TRANSACTION_SAVE_FAILD');
			PP::logger()->log(PPLogger::LEVEL_ERROR, $message, $payment, $data, 'PayplansPaymentFormatter', '', true);
		}

		// Save the payment
		$payment->save();

		echo "DONE";
		exit;
	}
}
