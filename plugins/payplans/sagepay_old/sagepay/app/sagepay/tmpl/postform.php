<?php
/**
* @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package		PayPlans
* @subpackage	Ccavenue
* @contact 		support+payplans@readybytes.in
*/
if(defined('_JEXEC')===false) die();?>

<script type="text/javascript">
	payplans.jQuery(document).ready(function(){
		if(payplans.jQuery("form[name='site_app_sagepay_form'] input[name='<?php echo JSession::getFormToken();?>']")){
		payplans.jQuery("form[name='site_app_sagepay_form'] input[name='<?php echo JSession::getFormToken();?>']").remove();
		}

	   setTimeout(function sagepaySubmit(){
	       document.forms["site_app_sagepay_form"].submit();
	    }, 1000);
	});
</script>


<div class="row-fluid">
	<form method="post" name="site_app_sagepay_form" action="<?php echo $url; ?>">
		<input type="hidden" name="VPSProtocol" value= "3.00">
    	<input type="hidden" name="TxType" 		value= "<?php echo $gatewaydata['TxType']?>">
    	<input type="hidden" name="Vendor" 		value= "<?php echo $gatewaydata['Vendor']?>">
    	<input type="hidden" name="Crypt" 		value= "<?php echo $gatewaydata['Crypt']; ?>">
			

	      	<fieldset class="form-horizontal row-fluid">
		     <div id="payment-paypal" class="pp-payment-pay-process">		
				<div id="payment-redirection">
					<h4>
						<?php echo XiText::_('Redirecting you to Sagepay for further Payment process.'); ?>
					</h4>
					<div class="loading"></div>
				</div>
				<div id="payment-submit">
					<button type="submit" class="btn btn-primary btn-large"
							name="payplans_payment_btn"><?php echo XiText::_('COM_PAYPLANS_PAYPAL_PAYMENT')?></button>
				</div>
	</div>
			</fieldset>  
		</form>
</div>
