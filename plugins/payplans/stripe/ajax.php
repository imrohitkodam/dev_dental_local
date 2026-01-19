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

class plgPayplansStripeAjax extends PayPlans
{
	/**
	 * Renders the completion dialog
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function completed()
	{
		$theme = PP::themes();
		$contents = $theme->output('plugins:/payplans/stripe/dialogs/completed');

		$ajax = PP::ajax();
		return $ajax->resolve($contents);
	}

	/**
	 * Retrieve app details from query string
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getApp()
	{
		$appId = $this->input->get('appId', 0, 'int');

		if (!$appId) {
			throw new Exception('Not allowed');
		}

		$app = PP::app()->getAppInstance($appId);

		if ($app->type != 'stripe') {
			throw new Exception('Not allowed');
		}

		return $app;
	}

	/**
	 * Retrieve app details from query string
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getHelper($app)
	{
		require_once(__DIR__ . '/app/helper.php');

		$params = $app->getAppParams();
		$helper = new PPHelperStripe($params, $app);

		return $helper;
	}

	/**
	 * Updates customer billing details on Stripe
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function update()
	{
		$app = $this->getApp();
		$params = $app->getAppParams();

		$token = $this->input->get('token', '', 'default');

		if (!$token) {
			throw new Exception('Invalid token provided');
		}

		$subscriptionKey = $this->input->get('subscriptionKey', '', 'default');
		$subscriptionId = PP::getIdFromKey($subscriptionKey);

		if (!$subscriptionId) {
			throw new Exception('Invalid subscription key provided');
		}

		$helper = $this->getHelper($app);

		$subscription = PP::subscription($subscriptionId);

		// Get payment object
		$order = $subscription->getOrder();
		$invoice = $order->getInvoice();
		$payment = $invoice->getPayment();

		$gatewayParams = $payment->getGatewayParams();
		$customerId = $gatewayParams->get('stripe_customer');

		$state = $helper->updateCustomer($customerId, $token);

		$ajax = PP::ajax();
		
		if ($state && isset($state->default_source)) {

			//Update new payment method for existing payment intent
			if ($params->get('enable_sca')) {
				$gatewayParams->set('payment_method_id', $state->default_source);
				$payment->gateway_params = $gatewayParams->toString();
				$payment->save();
			}
			
			return $ajax->resolve();
		}

		return $ajax->reject($state);
	}

	/**
	 * Renders the dialog to update credit card details
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function updateForm()
	{
		$app = $this->getApp();
		
		$subscriptionKey = $this->input->get('subscriptionKey', '', 'default');
		$subscriptionId = PP::getIdFromKey($subscriptionKey);

		if (!$subscriptionId) {
			throw new Exception('Invalid subscription key provided');
		}

		$params = $app->getAppParams();
		$sandbox = $params->get('sandbox');
		$publicKey = $params->get('public_key', '');
		$dateFormat = $params->get('date_type', 'MM / YYYY');
		$billingDetails = $params->get('billing_details', false);

		$subscription = PP::subscription($subscriptionId);

		$theme = PP::themes();
		$theme->set('appId', $app->getId());
		$theme->set('publicKey', $publicKey);
		$theme->set('subscription', $subscription);
		$theme->set('sandbox', $sandbox);
		$theme->set('dateFormat', $dateFormat);
		$theme->set('billingDetails', $billingDetails);

		// Get the user business billing details
		if($billingDetails) {
			$user = $subscription->getBuyer();
			$billingData = $user->getBusinessData();

			$theme->set('billingData', $billingData);
		}

		$contents = $theme->output('plugins:/payplans/stripe/dialogs/update');

		$ajax = PP::ajax();
		return $ajax->resolve($contents);
	}
}
