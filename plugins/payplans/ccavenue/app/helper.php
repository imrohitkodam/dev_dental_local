<?php
/**
* @package		PayPlans
* @copyright	Copyright Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class PPHelperCcavenue extends PPHelperPayment
{

	/**
	 * Loads the crypto library
	 *
	 * @since	4.0.0
	 * @access	private
	 */
	private function loadLibrary()
	{
		require_once(__DIR__ . '/Crypto.php');
	}

	/**
	 * Retrieves the merchant id
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getMerchantId()
	{
		static $id = null;

		if (is_null($id)) {
			$id = $this->params->get('merchant_id', '');
		}
		return $id;
	}

	/**
	 * Retrieves the access code
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getAccessCode()
	{
		static $code = null;

		if (is_null($code)) {
			$code = $this->params->get('access_code', '');
		}
		return $code;
	}

	/**
	 * Retrieves the encryption key
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getEncryptionKey()
	{
		static $key = null;

		if (is_null($key)) {
			$key = $this->params->get('encyption_key', '');
		}
		return $key;
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
	 * Retrieves the redirection url
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getRedirectUrl($paymentKey)
	{
		static $url = null;

		if (is_null($url)) {
			$url = rtrim(JURI::root(), '/') . '/index.php?option=com_payplans&gateway=ccavenue&view=payment&task=complete&action=success&payment_key=' . $paymentKey;
		}

		return $url;
	}

	/**
	 * Retrieves the cancellation url
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getCancelUrl($paymentKey)
	{
		static $url = null;

		if (is_null($url)) {
			$url = rtrim(JURI::root(), '/') . '/index.php?option=com_payplans&gateway=ccavenue&view=payment&task=complete&action=cancel&payment_key=' . $paymentKey;
		}

		return $url;
	}

	/**
	 * Retrieves the post url
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getPostUrl($paymentKey)
	{
		static $url = null;

		if (is_null($url)) {
			$url = rtrim(JURI::root(), '/') . '/index.php?option=com_payplans&view=payment&task=complete&payment_key=' . $paymentKey;
		}

		return $url;
	}

	/**
	 * Retrieves the form url
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getFormUrl()
	{
		static $url = null;

		if (is_null($url)) {
			$url = 'https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction';

			if ($this->isSandbox()) {
				$url = 'https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction';
			}
		}

		return $url;
	}

	/**
	 * Initiate Payment Request
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function createPaymentRequest(PPPayment $payment, PPInvoice $invoice)
	{	
		$merchantId = $this->getMerchantId();
		$paymentKey = $payment->getKey();
		$buyer = $invoice->getBuyer();
		
		$redirectUrl = $this->getRedirectUrl($paymentKey);
		$cancelUrl = $this->getCancelUrl($paymentKey);
		$postUrl = $this->getPostUrl($paymentKey);
		
		$payload = array(
			'merchant_id' => $merchantId,
			'order_id' => $paymentKey,
			'amount' => $invoice->getTotal(),
			'currency' => $invoice->getCurrency('isocode'),
			'redirect_url' => $redirectUrl,
			'cancel_url' => $cancelUrl,
			'language' => 'EN',
			'merchant_param1' => $paymentKey
		);

		return $payload;
	}

	/**
	 * Given a set of data, get encrypted data
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getEncryptedData($data)
	{
		$this->loadLibrary();

		$encryptionKey = $this->getEncryptionKey();
		$gatewayData =  array();

		$gatewayData['merchant_id'] = $data['merchant_id'];
		$gatewayData['amount'] = $data['amount'];
		$gatewayData['order_id'] = $data['order_id'];
		$gatewayData['redirect_url'] = $data['redirect_url'];
		$gatewayData['cancel_url'] = $data['cancel_url'];
		$gatewayData['billing_name'] = $data['billing_name'];
		$gatewayData['billing_address'] = $data['billing_address'];
		$gatewayData['billing_city'] = $data['billing_city'];
		$gatewayData['billing_state'] = $data['billing_state'];
		$gatewayData['billing_zip'] = $data['billing_zip'];
		$gatewayData['billing_country'] = $data['billing_country'];
		$gatewayData['billing_tel'] = $data['billing_tel'];
		$gatewayData['billing_email'] = $data['billing_email'];
		$gatewayData['language'] = $data['language'];
		$gatewayData['currency'] = $data['currency'];
		$gatewayData['merchant_param1']	= $data['merchant_param1'];
			
		$merchantData = '';	
		foreach ($gatewayData as $key => $value) {
			$merchantData .= $key.'='.$value.'&';
		}

		$encryptedData	= encrypt($merchantData, $encryptionKey);

		return $encryptedData;
	}

	/**
	 * Validate data came from payment gateway
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function validateData($data)
	{
		$this->loadLibrary();
		
		$encryptionKey	= $this->getEncryptionKey();		
		$encResponse = $data["encResp"];	
		
		//This is the response sent by the CCAvenue Server
		//Crypto Decryption used as per the specified working key.
		$rcvdString	= decrypt($encResponse,$encryptionKey);				
		$orderStatus = "";
		$decryptValues = explode('&', $rcvdString);
		$dataSize = sizeof($decryptValues);
	
		for ($i = 0; $i < $dataSize; $i++) {
			$information = explode('=',$decryptValues[$i]);
			
			if ($i == 3) {
				$orderStatus = $information[1];
			}
		}
		
		for ($i = 0; $i < $dataSize; $i++) {
			$information = explode('=',$decryptValues[$i]);
			$response[$information[0]] = urldecode($information[1]);
		}

		return array($orderStatus, $response);
	}

}