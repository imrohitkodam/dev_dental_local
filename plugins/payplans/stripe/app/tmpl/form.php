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
<script src="https://js.stripe.com/v2/"></script>

<form method="post" autocomplete="off" action="<?php echo JRoute::_('index.php?option=com_payplans&view=payment&task=complete&action=process&payment_key=' . $payment->getKey());?>" data-pp-stripe-form>

	<div class="o-card o-card--borderless t-lg-mb--lg">
		<div class="o-card__header o-card__header--nobg t-lg-pl--no"><?php echo JText::_('COM_PP_CARD_DETAILS');?></div>

		<div class="o-card__body">
			<div data-pp-stripe-result>
			</div>

			<?php echo $this->html('form.card', ['card' => 'stripe_card_num', 'expire_month_year' => $dateFormat, 'expire_month' => 'stripe_exp_month', 'expire_year' => 'stripe_exp_year', 'code' => 'stripe_card_code'], 
				['stripe_card_num' => $sandbox ? '4012888888881881' : '', 'exp_month_year' => $dateFormat ? $dateFormat : 'MM / YYYY', 'exp_month' => $sandbox ? '12' : '', 'exp_year' => $sandbox ? '2024' : '', 'stripe_card_code' => $sandbox ? '123' : '']
			); ?>
		</div>

		<?php if ($billingDetails) { ?>
		<div class="o-card__body space-y-sm">
			<?php echo $this->fd->html('form.floatingLabel', 'COM_PP_CHECKOUT_ADDRESS', 'address', 'text', $billingData->address, false, [
			'fieldAttributes' => 'data-pp-address',
			'attributes' => 'data-pp-form-group'
			]); ?>

			<div class="grid md:grid-cols-3 gap-sm">
				<div>
					<?php echo $this->fd->html('form.floatingLabel', 'COM_PP_CHECKOUT_CITY', 'city', 'text', $billingData->city, false, [
					'fieldAttributes' => 'data-pp-city',
					'attributes' => 'data-pp-form-group'
				]); ?>
				</div>

				<div>
					<?php echo $this->fd->html('form.floatingLabel', 'COM_PP_CHECKOUT_STATE', 'state', 'text', $billingData->state, false, [
					'fieldAttributes' => 'data-pp-state',
					'attributes' => 'data-pp-form-group'
				]); ?>
				</div>

				<div>
					<?php echo $this->fd->html('form.floatingLabel', 'COM_PP_CHECKOUT_ZIP', 'zip', 'text', $billingData->zip, false, [
					'fieldAttributes' => 'data-pp-zip',
					'attributes' => 'data-pp-form-group'
				]); ?>
				</div>
			</div>

			<?php echo $this->html('floatlabel.country',  'COM_PP_CHECKOUT_COUNTRY', 'country', $billingData->country, '', ['data-pp-country' => '']); ?>
		</div>
		<?php } ?>
	</div>

	<div class="flex items-center">
		<?php echo $this->output('site/payment/default/cancel', ['payment' => $payment]); ?>

		<div class="flex-shrink-0">
			<?php echo $this->fd->html('button.standard', 'COM_PP_COMPLETE_PAYMENT_BUTTON', 'primary', 'default', ['attributes' => 'data-pp-stripe-submit data-key="' . $publicKey . '"']); ?>
		</div>
	</div>

	<?php echo $this->html('form.hidden', 'stripeToken', '', 'data-stripe-token=""'); ?>
</form> 