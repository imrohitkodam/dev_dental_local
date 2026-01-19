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
?>
<script src="https://js.braintreegateway.com/v2/braintree.js"></script>
<form action="<?php echo $post_url;?>" method="post" autocomplete="off" data-braintree-form >
	<div class="o-card o-card--borderless">
		<div class="o-card__body space-y-sm">
			<div id="dropin-container"></div>
		</div>
	</div>

	<div class="flex items-center">
		<div class="flex-grow">
			<a href="<?php echo $cancel_url; ?>" class="no-underline"><?php echo JText::_('COM_PAYPLANS_PAYMENT_APP_BRAINTREE_CANCEL')?></a>
		</div>

		<div class="flex-shrink-0">
			<?php echo $this->fd->html('button.submit', 'COM_PAYPLANS_PAYMENT_APP_BRAINTREE_BUY', 'primary', 'default'); ?>
		</div>
	</div>
</form>