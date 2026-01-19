<?php
/**
* @copyright	Copyright (C) 2009 - 2009 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package		PayPlans
* @subpackage	Frontend
* @contact 		shyam@readybytes.in
*/
if(defined('_JEXEC')===false) die();?>
<div id="sagepay" class="authorize span12">
	<form method="post" action="<?php echo $post_url;?>" id="checkout_form">

      <fieldset class="pp-secondary pp-color pp-border pp-background">
       <div class="form-horizontal">
      		<div  class="text-info">    
        		 <?php  $Currency = $invoice->getCurrency();
         				echo $this->_render('partial_amount', compact('currency', 'amount'), 'default')." " .XiText::_('COM_PAYPLANS_PAYMENT_APP_SAGEPAY_PAYMENT_AMOUNT');
         			?>
        	</div>
         	<div>&nbsp;</div>
 
      </fieldset>

	<fieldset class="pp-secondary pp-color pp-border pp-background">
      		<legend><?php echo XiText::_('COM_PAYPLANS_PAYMENT_APP_SAGEPAY_PERSONAL_DETAILS'); ?></legend>
	      	<div class="form-horizontal">
	     	 	<div class="control-group">
				<div class="control-label"><?php echo XiText::_('COM_PAYPLANS_PAYMENT_APP_SAGEPAY_FIRST_NAME')?></div>
		  		<div class="controls"><input type="text" class="required" name="BillingFirstnames" value=""></input></div>
				</div>
			
				<div class="control-group">
					<div class="control-label"><?php echo XiText::_('COM_PAYPLANS_PAYMENT_APP_SAGEPAY_LAST_NAME')?></div>
			  		<div class="controls"><input type="text" class="required" name="BillingSurname" value=""></input></div>
				</div>

				<div class="control-group">
					<div class="control-label"><?php echo XiText::_('Email')?></div>
			  		<div class="controls"><input type="text" class="required" name="customerEMail" value=""></input></div>
				</div>
			
				<div class="control-group">
					<div class="control-label"><?php echo XiText::_('COM_PAYPLANS_PAYMENT_APP_SAGEPAY_BILLING_ADDRESS1')?></div>
			  		<div class="controls"><input type="text" class="required" name="BillingAddress1" value=""></input></div>
				</div>
			
				<!-- <div class="control-group">
					<div class="control-label"><?php echo XiText::_('COM_PAYPLANS_PAYMENT_APP_SAGEPAY_BILLING_ADDRESS2')?></div>
			  		<div class="controls"><input type="text" class="required" name="BillingAddress2" value=""></input></div>
				</div> -->
			
				<div class="control-group">
					<div class="control-label"><?php echo XiText::_('COM_PAYPLANS_PAYMENT_APP_SAGEPAY_CITY')?></div>
			  		<div class="controls"><input type="text" class="required" name="BillingCity" value=""></input></div>
				</div>
			
				<div class="control-group">
					<div class="control-label"><?php echo XiText::_('COM_PAYPLANS_PAYMENT_APP_SAGEPAY_COUNTRY')?></div>
			  		<div class="controls"><?php echo PayplansHtml::_('country.edit', 'BillingCountry','GB', array('option_none'=>false, 'style'=>'required="required"'), 'isocode2');?></div>	
				</div>
				
				<div class="control-group">
					<div class="control-label"><?php echo XiText::_('COM_PAYPLANS_PAYMENT_APP_SAGEPAY_POSTCODE')?></div>
			  		<div class="controls"><input type="text" class="required" name="BillingPostCode" value=""></input></div>
				</div>
			</div>
     </fieldset>
      
      <fieldset class="form-horizontal row-fluid">
	  	<div class="offset4 span8">
			<button id="pp-payment-app-buy" type="submit" name="buy" class="btn btn-primary btn-large"><?php echo XiText::_('COM_PAYPLANS_PAYMENT_APP_SAGEPAY_BUY')?></button>
        		<div class="btn btn-large">
        			<a id="sagepay-pay-cancel" href="<?php echo XiRoute::_("index.php?option=com_payplans&view=payment&task=complete&action=cancel&payment_key=".$payment_key); ?>">
        				<?php echo XiText::_('COM_PAYPLANS_PAYMENT_APP_SAGEPAY_CANCEL')?>
        			</a>
        		</div>        	
        </div>
      </fieldset>
      
      <input type="hidden" name="Amount" 			value="<?php echo $amount;?>" />
      <input type="hidden" name="Currency" 			value="<?php echo $currency;?>" />
      <input type="hidden" name="invoice_key" 		value="<?php echo $invoice_key;?>" />
      <input type="hidden" name="Vendor" 			value="<?php echo $vendor;?>" />
      <input type="hidden" name="VendorTxCode" 		value="<?php echo 'prefix_' . time() . rand(0, 9999);?>" />
      <input type="hidden" name="Description"  		value="<?php echo $invoice->getTitle();?>" />
      <input type="hidden" name="BillingAgreement" 	value="<?php echo $billingAgreement?>" />
      <input type="hidden" name="TxType" 			value="PAYMENT" />
	  <input type="hidden" name="initiate"  		value="1">
      
	</form>
</div>
<?php 
