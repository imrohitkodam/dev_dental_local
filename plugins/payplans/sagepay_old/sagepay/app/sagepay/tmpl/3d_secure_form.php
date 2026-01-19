<?php
/**
* @copyright	Copyright (C) 2009 - 2016 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package		PayPlans
* @subpackage		Frontend
* @contact 		support+payplans@readybytes.in
*/
if(defined('_JEXEC')===false) die();
?>

<script type="text/javascript">
payplans.jQuery(document).ready(function()
{
         setTimeout(function paypalSubmit(){
              	document.forms["site_app_<?php echo $this->getName(); ?>_form"].submit();
           }, 1000);
});
</script>

<form action="<?php echo $post_url ?>"
	  method="post" name="site_app_<?php echo $this->getName(); ?>_form" >

	<!--ORDER INFO-->
	<input TYPE="hidden" name="charset"     value="utf-8">
	<input type="hidden" name="order_id" 	value="<?php echo $order_id;?>" />
	<input type='hidden' name='invoice' 	value='<?php echo $invoice; ?>'>
	<input type='hidden' name='item_name' 	value='<?php echo $item_name;?>'>
	<input type='hidden' name='item_number' value='<?php echo $item_number; ?>'>
	<input type='hidden' name='amount' 		value='<?php echo $amount; ?>'>


	<input type='hidden' name='cmd' 			value='<?php echo $cmd;?>'>
	<input type="hidden" name="business" 		value="<?php echo $merchant_email; ?>" />
	<input type='hidden' name='return' 			value='<?php echo $return_url; ?>'>
	<input type='hidden' name='cancel_return' 	value='<?php echo $cancel_url; ?>'>
	<input type="hidden" name="notify_url" 		value="<?php echo $notify_url; ?>" />
	<input type='hidden' name='currency_code' 	value='<?php echo $currency; ?>'>
	<input type='hidden' name='no_note' 		value='1'>
	<input type="hidden" name="bn" 			value="ReadyBytes_SP" >

    <div id="payment-paypal" class="pp-payment-pay-process">		
		<div id="payment-redirection">
			<h4>
				<?php echo XiText::_('COM_PAYPLANS_APP_PAYPAL_PAYMENT_REDIRECTION'); ?>
			</h4>
			<div class="loading"></div>
		</div>
		<div id="payment-submit">
			<button type="submit" class="btn btn-primary btn-large"
					name="payplans_payment_btn"><?php echo XiText::_('COM_PAYPLANS_PAYPAL_PAYMENT')?></button>
		</div>
	</div>

</form>
