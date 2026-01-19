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

class PPHelperStripeAlipay extends PPHelperPayment
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

			$lib = __DIR__ . '/scaLib/Stripe.php';

			include_once($lib);

			\Stripe\Stripe::setApiKey($this->params->get('secret_key'));	

			$curl = new \Stripe\HttpClient\CurlClient(array(CURLOPT_SSLVERSION => CURL_SSLVERSION_TLSv1_2, CURLOPT_CAINFO => PP_CACERT));
			\Stripe\ApiRequestor::setHttpClient($curl);

			$loaded = true;
		}

		return $loaded;
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
						'payment_method_types' => ["alipay"],
						'amount' => $amount,
						'currency' => $currency,
						'description' => $invoice->getKey(),
						'metadata' => [
									'payment_key' => $payment->getKey()
								]
				]);

		$gatewayParams = $payment->getGatewayParams();
		$gatewayParams->set('payment_intent_id', $intent->id);
		$gatewayParams->set('payment_intent', $intent->client_secret);
		$payment->gateway_params = $gatewayParams->toString();
		$payment->save();

		return $intent;
	}

	/**
	 * Verifies the webhook signature
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function verifyWebhookSignature($payload, $headers)
	{
		$this->loadLibrary();

		$secret = $this->params->get('webhook_secret', '');

		try {
			$event = \Stripe\Webhook::constructEvent($payload, $headers, $secret);
		} catch(\UnexpectedValueException $e) {
			return false;

		} catch(\Stripe\Error\SignatureVerificationException $e) {
			return false;
		}

		return true;
	}
}