<?php
/**
* @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package		PayPlans
* @subpackage	SagePay
* @contact 		support+payplans@readybytes.in
*/
if(defined('_JEXEC')===false) die();

class PayplansAppSagepay extends PayplansAppPayment
{	
	protected $_location	= __FILE__;

	// App support payment cancel
	public function isSupportPaymentCancellation($invoice)
	{
		if($invoice->isRecurring()){
			return true;
		}
		return false;
	}
	
	public function onPayplansPaymentForm(PayplansPayment $payment, $data = null)
	{
		if(is_object($data)){
			$data = (array)$data;
		}

		$paymentKey		= $payment->getKey();

		if(isset($data["initiate"])){
			return $this->initiatePaymentRequest($data, $paymentKey);
		}
		
		$invoice 		=	$payment->getInvoice(PAYPLANS_INSTANCE_REQUIRE);
		$amount 		=   $invoice->getTotal() ;
		$billingAgreement = 0;
		if($invoice->isRecurring()){
			$billingAgreement = 1;
		}
		
		$this->assign('post_url',         	XiRoute::_("index.php?option=com_payplans&view=payment&task=pay&payment_key=".$paymentKey));     	
		$this->assign('invoice',   	 		$invoice);
    	$this->assign('payment',   	 		$payment);
		$this->assign('vendor',    			$this->getAppParam('vendor', ''));
		$this->assign('invoice_key', 		$invoice->getKey());
		$this->assign('payment_key', 		$payment->getKey());
		$this->assign('amount', 			$amount);
		$this->assign('currency', 			$invoice->getCurrency('isocode'));
		$this->assign('billingAgreement', 	$billingAgreement);
		
        return $this->_render('form');
	}

	public function initiatePaymentRequest($data, $paymentKey)
	{		
		require_once __DIR__.'/Sagepay.php';

		$root = JURI::root();
		if(XiFactory::getConfig()->https == true){
		    $root         = JString::str_ireplace("http:", "https:", $root);
		}
		
		$redirectURL     = $root.'index.php?option=com_payplans&gateway=sagepay&view=payment&task=complete&action=success&payment_key='.$paymentKey;
		$cancelURL       = $root.'index.php?option=com_payplans&gateway=sagepay&view=payment&task=complete&action=cancel&payment_key='.$paymentKey;
		$notifyURL       = $root.'index.php?option=com_payplans&gateway=sagepay&view=payment&task=notify';

		if($this->getAppParam('sandbox', 0)){
				$env  = SAGEPAY_ENV_TEST;
		}else {
				$env  = SAGEPAY_ENV_LIVE;
		}

		/*$configData  							= array();
		$configData['VendorName'] 				= $this->getAppParam('vendor', '');
		$configData['Env']						= $env;
		$configData['VendorEmail']				= $this->getAppParam('vendorEmail', '');
		$configData['Currency']					= $data['Currency'];
		$configData['TxType']					= $data['Vendor'];
		$configData['FormPassword']				= $this->getAppParam('encryptionKey', '');
		$configData['FormSuccessUrl']			= $redirectURL;
		$configData['FormFailureUrl']			= $cancelURL;
		$configData['serverNotificationUrl']	= $notifyURL;
		$configData['BillingAgreement']			= $data['BillingAgreement'];*/

		$config   = SagepaySettings::getInstance();
		$config->setEnv($env);
		$config->setVendorName($this->getAppParam('vendor', ''));
		$config->setServerProfile(SAGEPAY_SERVER_PROFILE_NORMAL);
		$config->setserverNotificationUrl($notifyURL);
		$config->setBillingAgreement(1);
		$config->setVendorEmail($this->getAppParam('vendorEmail', ''));

		$formData      					= array();
		$formData['VPSProtocol']		= '3.00';
    	$formData['TxType']				= $data['TxType'];
    	$formData['Vendor']				= $data['Vendor'];
    	$formData['Amount']				= $data['Amount'];
    	$formData['Currency']			= $data['Currency'];
    	$formData['Description']		= $data['Description'];
    	$formData['NotificationURL']	= $notifyURL;
    	$formData['BillingSurname']		= $data['BillingSurname'];
    	$formData['BillingFirstnames']	= $data['BillingFirstnames'];
    	$formData['BillingAddress1']	= $data['BillingAddress1'];
    	$formData['BillingCity']		= $data['BillingCity'];
    	$formData['BillingPostCode']	= $data['BillingPostCode'];
    	$formData['BillingCountry']		= $data['BillingCountry'];
    	$formData['DeliverySurname']	= $data['BillingSurname'];
    	$formData['DeliveryFirstnames']	= $data['BillingFirstnames'];
    	$formData['DeliveryAddress1']	= $data['BillingAddress1'];
    	$formData['DeliveryCity']		= $data['BillingCity'];
    	$formData['DeliveryPostCode']	= $data['BillingPostCode'];
    	$formData['DeliveryCountry']	= $data['BillingCountry'];
    	$formData['StoreToken']			= 1;

    	SagepayServerApi::setData($formData);

    	$api = SagepayApiFactory::create('SERVER', $config);
    	$a = $api::createRequest();
    	var_dump($a);die;
    	
		/*$sagePay = new SagePay();
		$sagePay->setCurrency($data['Currency']);
		$sagePay->setAmount($data['Amount']);
		$sagePay->setDescription($data['Description']);
		$sagePay->setVendorEMail($this->getAppParam('vendorEmail', ''));
		$sagePay->setCustomerEMail($data['customerEMail']);
		$sagePay->setBillingSurname($data['BillingSurname']);
		$sagePay->setBillingFirstnames($data['BillingFirstnames']);
		$sagePay->setBillingCity($data['BillingCity']);
		$sagePay->setBillingPostCode($data['BillingPostCode']);
		$sagePay->setBillingAddress1($data['BillingAddress1']);
		$sagePay->setBillingCountry($data['BillingCountry']);
		$sagePay->setDeliverySameAsBilling();
		$sagePay->setSuccessURL($redirectURL);
		$sagePay->setFailureURL($cancelURL);
		$sagePay->setBillingAgreement($data['BillingAgreement']);
		$sagePay->setencryptPassword($this->getAppParam('encryptionKey', ''));

		$gatewaydata						= array();
		$gatewaydata['Currency'] 			= $data['Currency'];
		$gatewaydata['Amount'] 				= $data['Amount'];
		$gatewaydata['Description'] 		= $data['Description'];
		$gatewaydata['BillingSurname'] 		= $data['BillingSurname'];
		$gatewaydata['BillingFirstnames'] 	= $data['BillingFirstnames'];
		$gatewaydata['BillingCity'] 		= $data['BillingCity'];
		$gatewaydata['BillingPostCode'] 	= $data['BillingPostCode'];
		$gatewaydata['BillingAddress1'] 	= $data['BillingAddress1'];
		$gatewaydata['redirectUrl'] 		= $redirectURL;
		$gatewaydata['cancelUrl'] 			= $cancelURL;
		$gatewaydata['Crypt']				= $sagePay->getCrypt();
		$gatewaydata['TxType']				= $data['TxType'];
		$gatewaydata['Vendor']				= $data['Vendor'];


		$post_url = 'https://live.sagepay.com/gateway/service/vspform-register.vsp';
		if($this->getAppParam('sandbox', 0)){
			$post_url  				= 'https://test.sagepay.com/gateway/service/vspform-register.vsp';
		}

		$this->assign('gatewaydata', 		$gatewaydata);
    	$this->assign('url',         		$post_url);
	    	
		return $this->_render('postform'); */
	}



	public function onPayplansPaymentAfter(PayplansPayment $payment, &$action, &$data, $controller)
	{
		if($action == 'cancel'){
			return true;
		}

		require_once __DIR__.'/lib/SagePay.php';

		$sagePay 	= new SagePay();
		$response 	= $sagePay->decode($data['crypt']);
		
		$invoice 		= $payment->getInvoice(PAYPLANS_INSTANCE_REQUIRE);
		// If same notification came more than one time
    	// Check if transaction already exist then do nothing and return
		$txn_id			= isset($response['VPSTxId']) 	? $response['VPSTxId'] 	: 0;
		$subscr_id		= isset($response['TxAuthNo']) 	? $response['TxAuthNo'] : 0;
		$transactions = $this->_getExistingTransaction($invoice->getId(), $txn_id, $subscr_id, 0);
    	if($transactions !== false){
    		foreach($transactions as $transaction){
    			$transaction = PayplansTransaction::getInstance($transaction->transaction_id, null, $transaction);
    			if($transaction->getParam('Status','') == $response['Status']){
    				return true;
    			}
    		}
    	}
		
		if($response['Status'] == 'OK'){
			$VendorTxCode	= $response['VendorTxCode'];
			$recurring 		= $invoice->isRecurring();
			if($recurring)
			{
				$recurrence_count 	= $this->_getRecurrenceCount($invoice, $recurring);
				$this->__processRecurringPayment($payment, $invoice, $recurrence_count, $response, $VendorTxCode);
			}else {
				$this->__processNonRecurringPayment($payment, $invoice, $response, $VendorTxCode);
			}
		}
		else {
			$message	= XiText::_('COM_PAYPLANS_APP_SAGEPAY_LOGGER_ERROR_IN_PAYMENT');
			PayplansHelperLogger::log(XiLogger::LEVEL_ERROR, $message, $payment, $response, 'PayplansPaymentFormatter', '', true);
			$error		= $response['StatusDetail'];
			$getApp 	= XiFactory::getApplication();
			$getApp->enqueueMessage(XiText::_($error),'error');
	  		$getApp->redirect(XiRoute::_('index.php?option=com_payplans&view=payment&task=pay&payment_key='.$payment->getKey()));

		}
		
		$payment->save();
		return parent::onPayplansPaymentAfter($payment, $action, $data, $controller);	
	}
	
	public function formatData($data)
	{
		$params  = array();
		
		$params['VPSProtocol']			= '3.00';
		$params['TxType']			= $data['TxType'];
		$params['Vendor']			= $data['Vendor'];
	  	$params['VendorTxCode'] 		= $data['VendorTxCode'];   
	  	$params['Amount'] 			= $data['Amount'];
	  	$params['Currency'] 			= $data['Currency']; 
	  	$params['Description']      		= $data['Description'];   
	  	$params['CardHolder'] 			= $data['CardHolder'];
		$params['CardNumber'] 			= $data['CardNumber'];
		$params['StartDate'] 			= $data['StartDateMonth'].$data['StartDateYear'];
		$params['ExpiryDate'] 			= $data['ExpiryDateMonth'].$data['ExpiryDateYear'];
		$params['CV2']				= $data['CV2'];
		$params['CardType'] 			= $data['CardType'];
		$params['BillingAgreement']		= $data['BillingAgreement'];
		$params['BillingFirstnames'] 		= $data['BillingFirstnames'];
		$params['BillingSurname'] 		= $data['BillingSurname'];
		$params['BillingAddress1'] 		= $data['BillingAddress1'];
		//$params['BillingAddress2'] 		= $data['BillingAddress2'];
		$params['BillingCity'] 			= $data['BillingCity'];
		$params['BillingCountry'] 		= $data['BillingCountry'];
		$params['BillingPostCode'] 		= $data['BillingPostCode'];
        
        $string		= array();
       	foreach ($params as $key => $value) {
        	$string[] = $key . '='. urlencode($value);
        }

        // Implode the array using & as the glue and store the data
       	$string = implode('&', $string);			
       	return $string;
	}
	
	
	public function __processNonRecurringPayment(PayplansPayment $payment, PayplansInvoice $invoice, $data, $VendorTxCode)
	{
		$invoice 		= $payment->getInvoice(PAYPLANS_INSTANCE_REQUIRE);
		$transaction 	= PayplansTransaction::getInstance();
		$transaction->set('user_id', 			$invoice->getBuyer())
					->set('invoice_id', 		$invoice->getId())
					->set('payment_id', 		$payment->getId())
					->set('amount',				$invoice->getTotal())
					->set('gateway_txn_id', 	isset($data['VPSTxId']) 	? $data['VPSTxId'] 	: 0)
					->set('gateway_subscr_id', 	isset($data['TxAuthNo']) 	? $data['TxAuthNo'] : 0)
					->set('gateway_parent_txn', 0)
					->set('message',  			XiText::_('COM_PAYPLANS_APP_SAGEPAY_TRANSACTION_COMPLETED'))
					->set('params', 			PayplansHelperParam::arrayToIni($data))
					->save();	
					
		$payment->getGatewayParams()->set('VendorTxCode',$VendorTxCode);
		$payment->save();
	}
	
	public function __processRecurringPayment(PayplansPayment $payment, PayplansInvoice $invoice, $recurrence_count, $data, $VendorTxCode)
	{
		 $recurrence_count 	    = $recurrence_count - 1;
         $amount				= number_format($invoice->getTotal(), 2);
         $transaction           = PayplansTransaction::getInstance();

         $transaction->set('user_id', 			$invoice->getBuyer())
		             ->set('invoice_id', 		$invoice->getId())
		             ->set('payment_id', 		$payment->getId())
		             ->set('amount',			$invoice->getTotal())
					 ->set('gateway_txn_id', 	isset($data['VPSTxId']) 	? $data['VPSTxId'] 	: 0)
					 ->set('gateway_subscr_id', isset($data['TxAuthNo']) 	? $data['TxAuthNo'] : 0)
		             ->set('message', 			'COM_PAYPLANS_PAYMENT_SAGEPAY_RECURRING_PAYMENT_COMPLETED_SUCCESSFULLY')
		             ->set('params', 			PayplansHelperParam::arrayToIni($data))
		             ->save();

        $params = array('VPSTxId' 				=> $data['VPSTxId'],
        				'VendorTxCode' 			=> $VendorTxCode,
        				'SecurityKey'  			=> $data['SecurityKey'],
        				'TxAuthNo'     			=> $data['TxAuthNo'],
            			'pending_recur_count' 	=> $recurrence_count);

        $payment->set('gateway_params', PayplansHelperParam::arrayToIni($params))
            	->save();
	}
	
	
	// Triggered when a subscription is about to expire.
    public function processPayment(PayplansPayment $payment, $invoiceCount)
    {
        //this is the previous payment object?
        $invoice 		= $payment->getInvoice(PAYPLANS_INSTANCE_REQUIRE);
        $invoice_count 	= $invoiceCount + 1;
            		
        if($invoice->isRecurring())
        {
            $recurrence_count 	= $payment->getGatewayParam('pending_recur_count');

            if($recurrence_count > 0)
            {
                $data							= array();
                $data['VPSProtocol']			= '3.00';
                $data['TxType']					= 'REPEAT';
                $data['Vendor']					= $this->getAppParam('vendor', '');
                $data['VendorTxCode']			= 'prefix_' . time() . rand(0, 9999);
                $data['Amount']					= number_format($invoice->getTotal($invoice_count), 2);
                $data['Currency']				= $invoice->getCurrency('isocode');
                $data['Description']			= $invoice->getTitle();
                $data['RelatedVPSTxId']			= $payment->getGatewayParam('VPSTxId');
                $data['RelatedVendorTxCode']	= $payment->getGatewayParam('VendorTxCode');
                $data['RelatedSecurityKey']		= $payment->getGatewayParam('SecurityKey');
                $data['RelatedTxAuthNo']		= $payment->getGatewayParam('TxAuthNo');
                
                $string		= array();
       			foreach ($data as $key => $value) {
        			$string[] = $key . '='. urlencode($value);
        		}

        		// Implode the array using & as the glue and store the data
       			$data1 = implode('&', $string);	
                
       			$postUrl	= 'https://live.sagepay.com/gateway/service/repeat.vsp';
       			if($this->getAppParam('sandbox', 0)){
					$postUrl	= 'https://test.sagepay.com/gateway/service/repeat.vsp';
       			}
				
				$response						= $this->execute($postUrl, $data1);  

                if($response['Status'] != 'OK')
                {                	
                	$message	= XiText::_('COM_PAYPLANS_APP_SAGEPAY_LOGGER_ERROR_IN_PAYMENT');
					$error		= $response['Status'].'('.$response['StatusDetail'].')';
                	PayplansHelperLogger::log(XiLogger::LEVEL_ERROR, $message, $payment, $response, 'PayplansPaymentFormatter', '', true);

                }else {
                    $transaction           = PayplansTransaction::getInstance();
         			$transaction->set('user_id', 			$invoice->getBuyer())
			             		->set('invoice_id', 		$invoice->getId())
					            ->set('payment_id', 		$payment->getId())
					            ->set('amount',				$invoice->getTotal())
								->set('gateway_txn_id', 	isset($data['VPSTxId']) 	? $data['VPSTxId'] 	: 0)
								->set('gateway_subscr_id', 	isset($data['TxAuthNo']) 	? $data['TxAuthNo'] : 0)
					            ->set('message', 			'COM_PAYPLANS_PAYMENT_SAGEPAY_RECURRING_PAYMENT_COMPLETED_SUCCESSFULLY')
					            ->set('params', 			PayplansHelperParam::arrayToIni($response))
					            ->save();
				             
		          $payment->getGatewayParams()->set('pending_recur_count',$recurrence_count-1);
		          $payment->save();
                }
			}
        }
    }
    
    
	public function onPayplansPaymentTerminate(PayplansPayment $payment, $controller)
	{
		$transactions    		= $payment->getTransactions();
	    $transaction     		= array_pop($transactions);
	    $subscriptionId  		= $transaction->get('gateway_subscr_id', 0);
		
		$data					= array();
        $data['VPSProtocol']	= '3.00';
        $data['TxType']			= 'CANCEL';
        $data['Vendor']			= $this->getAppParam('vendor', '');
        $data['VendorTxCode']	= 'prefix_' . time() . rand(0, 9999);
        $data['VPSTxId']		= $payment->getGatewayParam('VPSTxId');
        $data['SecurityKey']	= $payment->getGatewayParam('SecurityKey');
         
        $string		= array();
       	foreach ($data as $key => $value) {
        	$string[] = $key . '='. urlencode($value);
        }
     	$data 		=	implode('&', $string);	
     	
     	$url        = 'https://live.sagepay.com/gateway/service/cancel.vsp';
     	if($this->getAppParam('sandbox', 0)){
     		$url = 'https://test.sagepay.com/gateway/service/cancel.vsp';
     	}
     	
     	$response 	= $this->execute($url, $data);
     	if($response['Status'] == 'OK')
     	{
     		$invoice = $payment->getInvoice(PAYPLANS_INSTANCE_REQUIRE);
     		
			$transaction = PayplansTransaction::getInstance();
			$transaction->set('user_id', 			$payment->getBuyer())
						->set('invoice_id', 		$invoice->getId())
						->set('payment_id', 		$payment->getId())
						->set('gateway_txn_id', 	0)
						->set('gateway_subscr_id', 	$subscriptionId)
						->set('gateway_parent_txn', 0)
						->set('message', 			'COM_PAYPLANS_PAYMENT_APP_SAGEPAY_TRANSACTION_FOR_CANCEL_ORDER')
						->save();
     	}else{
     		$message = XiText::_('COM_PAYPLANS_PAYMENT_APP_SAGEPAY_LOGGER_ERROR_IN_CANCEL_RECURRING_PAYMENT');
			PayplansHelperLogger::log(XiLogger::LEVEL_ERROR, $message, $payment, $response, 'PayplansPaymentFormatter', '', true);
			$this->assign('errors', $response['StatusDetail']);
			return $this->_render('cancel_error');
     	}
                
		parent::onPayplansPaymentTerminate($payment, $controller);
		return $this->_render('cancel_success');
	}
	
  	
		
 	public function execute($url, $data)
    {
    	// Max exec time of 1 minute.
    	set_time_limit(60);
                
        $curlSession = curl_init();
        curl_setopt ($curlSession, CURLOPT_URL, $url);
        curl_setopt ($curlSession, CURLOPT_HEADER, 0);
        curl_setopt ($curlSession, CURLOPT_POST, 1);
        curl_setopt ($curlSession, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curlSession, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlSession, CURLOPT_TIMEOUT,30);
        curl_setopt($curlSession, CURLOPT_SSL_VERIFYPEER, FALSE);
                
       	$response = preg_split('/$\R?^/m',curl_exec($curlSession));
        curl_close ($curlSession);
                
        // Turn the response into an associative array
        for ($i=0; $i < count($response); $i++)
        {
	         $splitAt = strpos($response[$i], "=");
	         $response[trim(substr($response[$i], 0, $splitAt))] = trim(substr($response[$i], ($splitAt+1)));
        }
        
        return $response;
	}
	
 	// Get Recurrence Count
	private function _getRecurrenceCount(PayplansInvoice $invoice, $recurring)
    {
        $count = $invoice->getRecurrenceCount();
        if(intval($count) === 0){
            return 9999;
        }
        
        // Recurrence Count For Regular Recurring Plan
        if($recurring == PAYPLANS_RECURRING){
        	$recurrence_count = $invoice->getRecurrenceCount();
        }
            
        // Recurrence Count For Recurring + Trial 1 Plan
        if($recurring == PAYPLANS_RECURRING_TRIAL_1){
            $recurrence_count = $invoice->getRecurrenceCount() + 1;
        }
        
        // Recurrence Count For Recurring + Trial 2 Plan
        if($recurring == PAYPLANS_RECURRING_TRIAL_2){
             $recurrence_count = $invoice->getRecurrenceCount() + 2;
        }
        
        return $recurrence_count;
    }

}
