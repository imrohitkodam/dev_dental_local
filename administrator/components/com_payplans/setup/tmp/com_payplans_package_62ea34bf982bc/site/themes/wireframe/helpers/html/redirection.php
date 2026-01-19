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

$animated = PP::isAnimatedIconsEnabled();
?>
<?php if ($animated) { ?>
<style type="text/css">
div#pp-payment-redirect {width: 280px; height: auto; margin: 0 auto;}
</style>
<?php } ?>
<div class="o-card" style="width: 700px;">
	<div class="o-card__body">
		<div class="pp-result">

			<div class="pp-result__icons" id="pp-payment-redirect">
				<?php echo PP::getAnimatedImageHtml('redirection', '<i class="fdi fas fa-spinner fa-pulse"></i>'); ?>
			</div>

			<div class="pp-result__title">
				<?php echo JText::_('COM_PP_REDIRECT_TO_MERCHANT_HEADING'); ?>
			</div>

			<div class="pp-result__desc">
				<?php echo JText::_('COM_PP_REDIRECT_TO_MERCHANT'); ?>
			</div>

			<div class="pp-result__action">
				<?php echo $this->fd->html('button.submit', 'COM_PP_PROCEED_TO_PAYMENT_BUTTON', 'primary'); ?>
			</div>
		</div>
	</div>
</div>