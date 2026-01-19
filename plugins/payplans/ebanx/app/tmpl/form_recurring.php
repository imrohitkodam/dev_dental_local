<?php
/**
* @package      PayPlans
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form method="post" autocomplete="off" action="<?php echo JRoute::_('index.php?option=com_payplans&view=payment&task=complete&action=process&payment_key=' . $payment->getKey());?>" data-pp-ebanx-form>

	<div class="o-card o-card--borderless t-lg-mb--lg">
		<div class="o-card__header o-card__header--nobg t-lg-pl--no"><?php echo JText::_('COM_PP_CARD_DETAILS');?></div>

		<div class="o-card__body space-y-sm">
			<div data-pp-ebanx-result>
			</div>

			<div class="o-form-group">
				<select name="payment_type_code" class="pp-autocomplete o-form-control">
					<option value="creditcard"><?php echo JText::_('COM_PP_CREDIT_CARD');?></option>
					<option value="debitcard"><?php echo JText::_('COM_PP_DEBIT_CARD');?></option>
				</select>
			</div>

			<?php echo $this->html('floatlabel.text', 'COM_PP_NAME_ON_CARD', 'card_name', '', 'card_name'); ?>

			<?php echo $this->html('form.card', ['card' => 'card_number', 'expire_month_year' => '' , 'expire_month' => 'ebanx_exp_month', 'expire_year' => 'ebanx_exp_year', 'code' => 'card_cvv'], 
				['card_number' => '', 'exp_month_year' => 'MM / YYYY', 'exp_month' => '', 'exp_year' => '', 'card_cvv' => '']
			); ?>
		</div>
	</div>

	<div class="flex items-center">
		<?php echo $this->output('site/payment/default/cancel', ['payment' => $payment]); ?>

		<div class="flex-shrink-0">
			<?php echo $this->fd->html('button.standard', 'COM_PP_COMPLETE_PAYMENT_BUTTON', 'primary', 'default', ['attributes' => 'data-pp-ebanx-submit']); ?>
		</div>
	</div>

	<?php echo $this->html('form.hidden', 'initiateRecurring', true); ?>
</form>