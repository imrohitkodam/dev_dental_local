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

class PPHelperBraintree extends PPHelperPayment
{
	/**
	 * Retrieves the Merchant Id
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
	 * Retrieves the Secondary Merchant Id
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getSecondaryMerchantId()
	{
		static $id = null;

		if (is_null($id)) {
			$id = $this->params->get('secondary_merchant_id', '');
		}

		return $id;
	}


	/**
	 * Retrieves the Public Key
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getPublicKey()
	{
		static $code = null;

		if (is_null($code)) {
			$code = $this->params->get('public_key', '');
		}

		return $code;
	}

	/**
	 * Retrieves the Private Key
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getPrivateKey()
	{
		static $code = null;

		if (is_null($code)) {
			$code = $this->params->get('private_key', '');
		}

		return $code;
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
	 * Determines if it is SCA mode 
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function isSCA()
	{
		static $sca = null;

		if (is_null($sca)) {
			$sca = $this->params->get('sca', '');
		}

		return $sca;
	}

	/**
	 * Retrieve success redirection url
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getCancelUrl($paymentKey)
	{
		static $url = null;

		if (is_null($url)) {
			$url = rtrim(JURI::root(), '/') . '/index.php?option=com_payplans&payment_key='.$paymentKey.'&gateway=braintree&view=payment&task=complete&action=cancel';
		}

		return $url;
	}


	/**
	 * Retrieve the payload xml contents
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function loadConfig()
	{
		static $loaded = null;

		if (is_null($loaded)) {
			require_once(__DIR__ . '/lib/Braintree.php');
			
			$environment = 'production';

			if ($this->isSandbox()) {
				$environment = 'sandbox';
			} 

			Braintree_Configuration::environment($environment);
			Braintree_Configuration::merchantId($this->getMerchantId());
			Braintree_Configuration::publicKey($this->getPublicKey());
			Braintree_Configuration::privateKey($this->getPrivateKey());

			$gateway = new Braintree_Gateway([
			    'environment' => $environment,
			    'merchantId' => $this->getMerchantId(),
			    'publicKey' => $this->getPublicKey(),
			    'privateKey' => $this->getPrivateKey()
			]);

			$loaded = $gateway;
		}

		return $loaded;
	}


	/**
	 * Method to create customer profile at Braintree
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function createCustomer(PPInvoice $invoice, PPPayment $payment, $data)
	{ 
		$user = $invoice->getBuyer();

		$briantreeGateway = $this->loadConfig();

		//create customer at braintree
		$result = $briantreeGateway->customer()->create([
				'firstName' => $user->getName(),
				'email' => $user->getEmail(),
				'paymentMethodNonce' => $data['payment_method_nonce']
		]);

		if ($result->success) {

			$customerId = $result->customer->id;
			$paymentToken = $result->customer->paymentMethods[0]->token;

			// get the transaction instace of lib
			$transaction = PP::transaction();
			$transaction->user_id = $payment->getBuyer();
			$transaction->invoice_id = $invoice->getId();
			$transaction->payment_id = $payment->getId();
			$transaction->gateway_subscr_id = $result->customer->id;
			$transaction->message = 'COM_PAYPLANS_LOGGER_BRAINTREE_CUSTOMER_CREATED';

			$transactionParams = new JRegistry($result);
			$transaction->params = $transactionParams->toString();
			$transaction->save();	

			$gatewayParams = $payment->getGatewayParams();

			$gatewayParams->set('customer_id', $customerId);
			$gatewayParams->set('payment_token', $paymentToken);

			$payment->gateway_params = $gatewayParams->toString();
			$payment->save();			
		} 		

		return $result;
	}

	/**
	 * Method to create subscription at briantree
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function createSubscription(PPInvoice $invoice, PPPayment $payment, $data)
	{
		$briantreeGateway = $this->loadConfig();

		$result = $briantreeGateway->subscription()->create($data);

		if ($result instanceof Braintree_Result_Error) {
			 return $result;
		}
		else {
			// get the transaction instace of lib
			$transaction = PP::transaction();
			$transaction->user_id = $invoice->getBuyer()->id;
			$transaction->invoice_id = $invoice->getId();
			$transaction->payment_id = $payment->getId();
			$transaction->gateway_subscr_id = $result->subscription->id;
			$transaction->message = 'COM_PAYPLANS_LOGGER_BRAINTREE_SUBSCRIPTION_SIGN_UP';
			
			$transactionParams = new JRegistry($result);
			$transaction->params = $transactionParams->toString();
			$transaction->save();	

			$gatewayParams = $payment->getGatewayParams();
			$gatewayParams->set('subscription_id', $result->subscription->id);

			$payment->table->gateway_params = $gatewayParams->toString();
			$payment->save();

			// Case of Free Trial 
			// Do not create Transaction Profile of user
			// (Just create transaction  and mark invoice as paid)
			if ($invoice->getTotal() == 0 && $invoice->isRecurring()) {
				$transaction = PP::transaction();
				$transaction->user_id = $payment->getBuyer();
				$transaction->invoice_id = $invoice->getId();
				$transaction->payment_id = $payment->getId();
				$transaction->amount = 0;
				$transaction->gateway_txn_id = 0;
				$transaction->gateway_subscr_id = $result->subscription->id;
				$transaction->gateway_parent_txn = 0;
				$transaction->message = 'COM_PAYPLANS_PAYMENT_APP_BRAINTREE_PAYMENT_COMPLETED_SUCCESSFULLY';
				
				$transactionParams = new JRegistry($result);
				$transaction->params = $transactionParams->toString();
				$transaction->save();
			}	
		}
		
		return $result;
	}

	/**
	 * Given the response, try to get the transaction params
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getTransactionParams($response)
	{
		$items = $response->results;

		$params = new JRegistry();

		foreach ($items as $key => $value) {
			$params->set('param' . $key, $value);
		}

		return $params;
	}

	/**
	 * Get Recurrence Time
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getRecurrenceTime($expTime)
	{
		$expTime['year'] = isset($expTime['year']) ? intval($expTime['year']) : 0;
		$expTime['month'] = isset($expTime['month']) ? intval($expTime['month']) : 0;
		$expTime['day'] = isset($expTime['day']) ? intval($expTime['day']) : 0;;
		
		// years
		if(!empty($expTime['year'])){
			$months =  $expTime['year'] * 12;
			if(isset($expTime['month']) && $expTime['month']){
				$months	 += $expTime['month'];
			}	

			return [
				'period' => $months, 
				'unit' => 'month', 
				'frequency' => JText::_('COM_PAYPLANS_RECURRENCE_FREQUENCY_GREATER_THAN_ONE'),
				'message' => JText::_('COM_PAYPLANS_PAYMENT_APP_BRAINTREE_RECURRING_MESSAGE')
			];
		}
		
		// if months are set
		if(!empty($expTime['month'])){
			// if days are empty
			if(empty($expTime['day'])){
				return [
					'period' => $expTime['month'], 
					'unit' => 'month', 
					'frequency' => JText::_('COM_PAYPLANS_RECURRENCE_FREQUENCY_GREATER_THAN_ONE'),
					'message' => JText::_('COM_PAYPLANS_PAYMENT_APP_BRAINTREE_RECURRING_MESSAGE')
				];
			}
			
			// if total days are less or equlas to 90, then return days
			//  IMP : ASSUMPTION : 1 month = 30 days
			$days = $expTime['month'] * 30;
			if(($days + $expTime['day']) <= 999){
				return [
					'period' => $days + $expTime['day'], 
					'unit' => 'day', 
					'frequency' => JText::_('COM_PAYPLANS_RECURRENCE_FREQUENCY_GREATER_THAN_ONE'),
					'message' => JText::_('COM_PAYPLANS_PAYMENT_APP_BRAINTREE_RECURRING_MESSAGE')
				];
			}
			
			return [
				'period' => $expTime['month'], 
				'unit' => 'month', 
				'frequency' => JText::_('COM_PAYPLANS_RECURRENCE_FREQUENCY_GREATER_THAN_ONE'),
				'message' => JText::_('COM_PAYPLANS_PAYMENT_APP_BRAINTREE_RECURRING_MESSAGE')
			];
		}
		
		// if only days are set then return days as it is
		if(!empty($expTime['day'])){
			return [
				'period' => intval($expTime['day'], 10), 
				'unit' => 'day', 
				'frequency' => JText::_('COM_PAYPLANS_RECURRENCE_FREQUENCY_GREATER_THAN_ONE'),
				'message' => JText::_('COM_PAYPLANS_PAYMENT_APP_BRAINTREE_RECURRING_MESSAGE')
			];
		}
		
		return false;
	}

}

class PPValidationBraintree
{
	public function __construct($params)
	{
		$this->params = $params;
	}

	/**
	 * Payment Cancellation
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onProcessSubscription_canceled($payment, $data, $transaction)
	{
		$transaction->message = 'COM_PAYPLANS_APP_BRAINTREE_SUBSCR_CANCEL';

		//terminate the order
		$invoice = $payment->getInvoice();
		$invoice->terminate();

		return [];
	}

	/**
	 * Payment charged successfully
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onProcessSubscription_charged_successfully($payment, $amount, $transaction)
	{
		$transaction->message = 'COM_PAYPLANS_APP_BRAINTREE_SUBSCRIPTION_CHARGED_SUCCESSFULLY';
		$transaction->amount = $amount;

		return [];
	}

	/**
	 * Payment charged unsuccessfully
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onProcessSubscription_charged_unsuccessfully($payment, $data, $transaction)
	{
		$transaction->message = 'COM_PAYPLANS_APP_BRAINTREE_SUBSCRIPTION_CHARGED_UNSUCCESSFULLY';

		return [];
	}

	/**
	 * Subscription Expired
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onProcessSubscription_expired($payment, $data, $transaction)
	{
		$transaction->message = 'COM_PAYPLANS_APP_BRAINTREE_SUBSCR_EXPIRED';

		return [];
	}

	/**
	 * Subscription Trial End
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onProcessSubscription_trial_ended($payment, $data, $transaction)
	{
		$transaction->message = 'COM_PAYPLANS_APP_BRAINTREE_SUBSCR_TRIAL_ENDED';

		return [];
	}

	/**
	 * Subscription Activated
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onProcessSubscription_went_active($payment, $amount, $transaction)
	{
		$transaction->message = 'COM_PAYPLANS_APP_BRAINTREE_SUBSCR_WENT_ACTIVE';
		$transaction->amount = $amount;

		return [];
	}

	/**
	 * Subscription Due
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onProcessSubscription_went_past_due($payment, $data, $transaction)
	{
		$transaction->message = 'COM_PAYPLANS_APP_BRAINTREE_SUBSCR_WENT_PAST_DUE';

		return [];
	}

	/**
	 * Subscription Account Approved
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onProcessSub_merchant_account_approved($payment, $data, $transaction)
	{
		$transaction->message = 'COM_PAYPLANS_APP_BRAINTREE_SUBSCR_MERCHANT_ACCOUNT_APPROVED';

		return [];
	}

	/**
	 * Subscription Merchant Account Declined
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onProcessSub_merchant_account_declined($payment, $data, $transaction)
	{
		$transaction->message = 'COM_PAYPLANS_APP_BRAINTREE_SUBSCR_MERCHANT_ACCOUNT_DECLINED';

		return [];
	}

	/**
	 * Transaction Disbursed
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onProcessTtransaction_disbursed($payment, $data, $transaction)
	{
		$transaction->message = 'COM_PAYPLANS_APP_BRAINTREE_TRANSACTION_DISBURSED';

		return [];
	}

	/**
	 * ransaction Disbursed Exception
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onProcessDisbursement_exception($payment, $data, $transaction)
	{
		$transaction->message = 'COM_PAYPLANS_APP_BRAINTREE_DISBURSEMENT_EXCEPTION';

		return [];
	}

	/**
	 * Subscription Disbursement
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onProcessDisbursement($payment, $data, $transaction)
	{
		$transaction->message = 'COM_PAYPLANS_APP_BRAINTREE_DISBURSEMENT';

		return [];
	}


	/**
	 * Subscription Despute Open
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onProcessDispute_opened($payment, $data, $transaction)
	{
		$transaction->message = 'COM_PAYPLANS_APP_BRAINTREE_DISPUTE_OPENED';

		return [];
	}

	/**
	 * Subscription Despute Lost
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onProcessDispute_lost($payment, $data, $transaction)
	{
		$transaction->message = 'COM_PAYPLANS_APP_BRAINTREE_DISPUTE_LOST';

		return [];
	}


	/**
	 * Subscription Despute won
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onProcessDispute_won($payment, $data, $transaction)
	{
		$transaction->message = 'COM_PAYPLANS_APP_BRAINTREE_DISPUTE_WON';

		return [];
	}

	/**
	 * Merchant Partner Connected
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onProcessPartner_merchant_connected($payment, $data, $transaction)
	{
		$transaction->message = 'COM_PAYPLANS_APP_BRAINTREE_PARTNER_MERCHANT_CONNECTED';

		return [];
	}

	/**
	 * Merchant Partner Disconnected
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onProcessPartner_merchant_disconnected($payment, $data, $transaction)
	{
		$transaction->message = 'COM_PAYPLANS_APP_BRAINTREE_PARTNER_MERCHANT_DISCONNECTED';

		return [];
	}

	/**
	 * Merchant Partner Declined
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onProcessPartner_merchant_declined($payment, $data, $transaction)
	{
		$transaction->message = 'COM_PAYPLANS_APP_BRAINTREE_PARTNER_MERCHANT_DECLINED';

		return [];
	}

}
