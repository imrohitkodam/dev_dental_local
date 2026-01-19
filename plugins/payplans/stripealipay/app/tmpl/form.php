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
<script src="https://js.stripe.com/v3/"></script>

<?php 
$animated = PP::isAnimatedIconsEnabled();
?>

<?php if ($animated) { ?>
<style type="text/css">
div#pp-payment-redirect {width: 280px; height: auto; margin: 0 auto;}
</style>
<?php } ?>

<form method="post" autocomplete="off" action="<?php echo JRoute::_('index.php?option=com_payplans&view=payment&task=complete&action=process&payment_key=' . $payment->getKey());?>" data-pp-stripe-alipay-form>
	<div class="o-card" style="width: 700px;">
		<div class="o-card__body">
			<div class="pp-result">

				<div class="pp-result__icons" id="pp-payment-redirect">
					<?php echo PP::getAnimatedImageHtml('redirection', '<i class="fas fa-spinner fa-pulse"></i>'); ?>
				</div>

				<div class="pp-result__desc">
					<strong><?php echo JText::_('COM_PP_STRIPE_ALIPAY_REDIRECT_TO_MERCHANT'); ?></strong>
				</div>

				<div class="pp-result__action text-left">
					<div class="flex items-center">
						<?php echo $this->output('site/payment/default/cancel', ['payment' => $payment]); ?>

						<div class="flex-shrink-0">
							<?php echo $this->fd->html('button.standard', 'COM_PP_COMPLETE_PAYMENT_BUTTON', 'primary', 'default', ['attributes' => 'data-pp-stripe-alipay-submit data-key="' . $publicKey . '"']); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>