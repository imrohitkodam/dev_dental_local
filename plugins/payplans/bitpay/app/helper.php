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

class PPHelperBitpay extends PPHelperPayment
{
	/**
	 * Load bitpay's library
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function loadLibrary()
	{
		static $loaded = null;

		if (is_null($loaded)) {

			$lib = __DIR__ . '/BitPayLib/BitPay.php';

			include_once($lib);

			$token = $this->getToken();
			$environment = BitPaySDKLight\Env::Test;

			if (!$this->isSandbox()) {
				$environment = BitPaySDKLight\Env::Prod;
			}

			$bitpay = new BitPaySDKLight\Client($token, $environment);

			$loaded = true;
		}

		return $loaded;
	}
	/**
	 * Create invoice for bitpay payment
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function createInvoice($post, $payment, $invoice)
	{
		$this->loadLibrary();

		$token = $this->getToken();
		$environment = BitPaySDKLight\Env::Test;

		if (!$this->isSandbox()) {
			$environment = BitPaySDKLight\Env::Prod;
		}

		$bitpay = new BitPaySDKLight\Client($token, $environment);
		// $invoice = $bitpay->createInvoice(new BitPaySDKLight\Model\Invoice\Invoice($post->price, $post->currency));

		$invTmpl = new BitPaySDKLight\Model\Invoice\Invoice($post->price, $post->currency);

		// setup the require data
		$invTmpl->setNotificationURL($post->notificationURL);
		$invTmpl->setRedirectURL($post->redirectURL);
		$invTmpl->setTransactionSpeed($post->transactionSpeed);
		$invTmpl->setFullNotifications($post->fullNotifications);
		$invTmpl->setNotificationEmail($post->notificationEmail);
		$invTmpl->setOrderId($post->orderID);
		$invTmpl->setPosData($post->posData);
		$invTmpl->setToken($post->token);

		$invoice = $bitpay->createInvoice($invTmpl);

		$url = $invoice->getURL();

		return $url;
	}

	/**
	 * Confirms the payment
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function confirm($data)
	{
		$this->loadLibrary();

		$token = $this->getToken();
		$environment = BitPaySDKLight\Env::Test;

		if (!$this->isSandbox()) {
			$environment = BitPaySDKLight\Env::Prod;
		}

		$bitpay = new BitPaySDKLight\Client($token, $environment);

		$invoice = $bitpay->getInvoice($data->id);
		return $invoice;
	}

	/**
	 * Retrieves the notification url
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getNotifyUrl()
	{
		$url = JURI::root();
		return $url;
	}

	/**
	 * Retrieve the merchant id
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getToken()
	{
		static $token = null;

		if (is_null($token)) {
			$token = trim($this->params->get('api_token', ''));
		}

		return $token;
	}

	/**
	 * Determines if it is sandbox mode
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function isSandbox()
	{
		static $sandbox = null;

		if (is_null($sandbox)) {
			$sandbox = $this->params->get('sandbox', '');
		}

		return $sandbox;
	}

	/**
	 * Logic to handle new subscription
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function processNew($payment, $data, $transaction)
	{
		$transaction->message = 'COM_PAYPLANS_APP_BITPAY_NEW';
	}

	/**
	 * Logic to handle paid notifications
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function processPaid($payment, $data, $transaction)
	{
		$transaction->message = 'COM_PAYPLANS_APP_BITPAY_PAID';
		$transaction->amount = $data->getPrice();
	}

	/**
	 * Logic to handle confirmed notifications
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function processConfirmed($payment, $data, $transaction)
	{
		$transaction->message = 'COM_PAYPLANS_APP_BITPAY_CONFIRMED';
		$transaction->amount = $data->getPrice();
	}

	/**
	 * Logic to handle completed payments
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function processComplete($payment, $data, $transaction)
	{
		$transaction->message = 'COM_PAYPLANS_APP_BITPAY_COMPLETE';
	}

	/**
	 * Logic to handle expired payments
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function processExpired($payment, $data, $transaction)
	{
		$transaction->message = 'COM_PAYPLANS_APP_BITPAY_EXPIRED';
	}

	/**
	 * Logic to handle incomplete payments
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function processIncomplete($payment, $data, $transaction)
	{
		$transaction->message = 'COM_PAYPLANS_APP_BITPAY_INCOMPLETE';
	}

	/**
	 * Validates a notification data
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function validate($data)
	{
		$merchantToken = $this->getToken();
		$result = json_decode($data);
		
		if (is_string($result) || (isset($result->error))) {
			return false;
		}

		// Ensure that the hash matches
		$posData = json_decode($result->posData);

		if ($posData->hash != crypt($posData->invoice_key, $merchantToken)) {
			return false;
		}

		$result->posData = $posData;

		return $result;
	}

}
