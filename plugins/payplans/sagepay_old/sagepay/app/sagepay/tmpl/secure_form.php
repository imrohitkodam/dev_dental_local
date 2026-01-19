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

 <script type="text/javascript">document.form.submit();</script>

<form action="<?php echo $acsUrl; ?>" method="post" >

	<input type="hidden" name="PaReq" 			value="<?php echo $PaReq;?>">
	<input type="hidden" name="TermUrl" 			value="<?php echo $TermUrl;?>">
	<input type="hidden" name="MD" 				value="<?php echo $MD;?>">

    <div id="payment-sagepay" class="pp-payment-pay-process">		
		<div id="payment-redirection">
			<h4>
				<?php echo XiText::_('Redirecting you for 3d Secure authentication'); ?>
			</h4>
			<div class="loading"></div>
		</div>
		<div id="payment-submit">
			<button type="submit" class="btn btn-primary btn-large"
					name="payplans_payment_btn"><?php echo XiText::_('Proceed to 3D secure authentication')?></button>
		</div>
	</div>

</form>

