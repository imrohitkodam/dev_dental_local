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
<div class="grid grid-cols-1 md:grid-cols-12 gap-md">
	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_INVOICE_EDIT_PARAMETERS'); ?>

			<div class="panel-body">
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_EDIT_PLAN_TITLE', 'params[title]'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.text', 'params[title]', $params->get('title'), 'params[title]'); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_TIME_EXPIRATION_TYPE', 'params[expirationtype]'); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.expiration', 'params[expirationtype]', $params->get('expirationtype'), 'params[expirationtype]', array('data-form-select-dropdown' => '')); ?>
					</div>
				</div>

				<!-- appear on recurring_trial_1 and recurring_trial_2-->
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md t-hidden expirationtype trial_price_1">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_TIME_TRIAL_PRICE_1', 'params[trial_price_1]'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.text', 'params[trial_price_1]', $params->get('trial_price_1'), 'params[trial_price_1]'); ?>
					</div>
				</div>

				<!-- appear on recurring_trial_1 and recurring_trial_2-->
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md t-hidden expirationtype trial_price_1">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_TIME_TRIAL_TIME_1', 'params[trial_time_1]'); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.timer', 'params[trial_time_1]', $params->get('trial_time_1'), 'params[trial_time_1]'); ?>
					</div>
				</div>

				<!-- appear on recurring_trial_2-->
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md t-hidden expirationtype trial_price_2">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_TIME_TRIAL_PRICE_2', 'params[trial_price_2]'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.text', 'params[trial_price_2]', $params->get('trial_price_2'), 'params[trial_price_2]'); ?>
					</div>
				</div>

				<!-- appear on recurring_trial_2-->
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md t-hidden expirationtype trial_price_2">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_TIME_TRIAL_TIME_2', 'params[trial_time_2]'); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.timer', 'params[trial_time_2]', $params->get('trial_time_2'), 'params[trial_time_2]'); ?>
					</div>
				</div>

				<!-- appear on all-->
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md t-hidden expirationtype price">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_PAYMENT_PRICE', 'params[price]'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.text', 'params[price]', $params->get('price'), 'params[price]'); ?>
					</div>
				</div>

				<!-- appear on all-->
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md t-hidden expirationtype expiration">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_TIME_EXPIRATION_TIME', 'params[expiration]'); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.timer', 'params[expiration]', $params->get('expiration'), 'params[expiration]'); ?>
					</div>
				</div>

				<!-- appear on recurring, recurring_trial_1 and recurring_trial_2-->
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md t-hidden expirationtype recurrence_count">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_TIME_RECURRENCE_COUNT', 'params[recurrence_count]'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.text', 'params[recurrence_count]', $params->get('recurrence_count'), 'params[recurrence_count]'); ?>
					</div>
				</div>
			</div>
		</div>

		<?php echo $this->renderPlugins($pluginResult, 'pp-invoice-details'); ?>

		<?php if ($this->config->get('enableDiscount') && !$invoice->isPaid() && !$invoice->isRefunded()) { ?>
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_DISCOUNTS'); ?>

			<div class="panel-body">
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_DISCOUNT_CODE_AMOUNT', 'app_discount_code'); ?>

					<div class="flex-grow">
						<div class="o-input-group">
							<?php echo $this->fd->html('form.text', 'app_discount_code', '', 'app_discount_code_id', array('placeholder' => JText::_('COM_PAYPLANS_PRODISCOUNT_ENTER_DISCOUNT_CODE_OR_AMOUNT'))); ?>

							<?php echo $this->fd->html('button.standard', 'COM_PAYPLANS_PRODISCOUNT_APPLY', 'default', 'default', [
								'attributes' => 'data-pp-discount-apply data-pp-invoice-id="' .$invoice->getId() . '"',
								'outline' => true
							]); ?>
						</div>
					</div>
				</div>

				<div class="text-danger" data-pp-discount-message></div>
			</div>
		</div>
		<?php } ?>

	</div>
	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_INVOICE_EDIT_DETAILS'); ?>

			<div class="panel-body">
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_ID', 'id'); ?>

					<div class="flex-grow col-md-7">
						<?php echo $invoice->getId(); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_OBJECT', 'object'); ?>

					<div class="flex-grow col-md-7">
						<?php $refObject = $invoice->getReferenceObject(); ?>
						<?php $subscription = $refObject->getSubscription(); ?>

						<?php if ($subscription) { ?>
							<a href="index.php?option=com_payplans&view=subscription&layout=form&id=<?php echo $subscription->getId();?>"><?php echo $subscription->getId()." (".$subscription->getKey().")"; ?></a>
						<?php } else { ?>
							(<?php echo JText::_('Missing or Removed'); ?>)
						<?php } ?>
					</div>

				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_INVOICE_EDIT_STATUS', 'status'); ?>

					<div class="flex-grow col-md-7">
						<?php echo $this->fd->html('label.standard', $invoice->getStatusName(), $invoice->getStatusLabelClass()); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_INVOICE_EDIT_CREATED_DATE', 'created_date'); ?>

					<div class="flex-grow col-md-7">
						<?php echo PP::date($invoice->getCreatedDate(), true)->toSql(true); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_INVOICE_PAID_DATE', 'paid_date'); ?>

					<div class="flex-grow col-md-7">
						<?php echo $invoice->getPaidDate(false) !== '0000-00-00 00:00:00' ? PP::date($invoice->getPaidDate(), true)->toSql(true) : JText::_('COM_PAYPLANS_NEVER'); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_INVOICE_INVOICE_SERIAL', 'serial'); ?>

					<div class="flex-grow col-md-7">
						<?php echo $invoice->getSerial(); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_INVOICE_EDIT_BUYER', 'buyer'); ?>

					<div class="flex-grow col-md-7">
						<a href="index.php?option=com_payplans&view=user&layout=form&id=<?php echo $invoice->getBuyer()->user_id; ?>"><?php echo $invoice->getBuyer()->getUsername();?></a>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_INVOICE_EDIT_SUBTOTAL', 'subtotal'); ?>

					<div class="flex-grow col-md-7">
						<div class="">
							<?php echo $this->html('form.amount', $invoice->getSubtotal(), $invoice->getCurrency()); ?>
						</div>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_INVOICE_EDIT_DISCOUNTABLE', 'discount'); ?>

					<div class="flex-grow col-md-7">
						<div class="">
							<?php echo $this->html('form.amount', $invoice->getDiscountable(), $invoice->getCurrency()); ?>
						</div>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_INVOICE_EDIT_DISCOUNT', 'edit_discount'); ?>

					<div class="flex-grow col-md-7">
						<div class="">
							<?php echo $this->html('form.amount', $invoice->getDiscount(), $invoice->getCurrency()); ?>
						</div>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_INVOICE_EDIT_TAXABLE', 'tax'); ?>

					<div class="flex-grow col-md-7">
						<div class="">
							<?php echo $this->html('form.amount', $invoice->getTaxableAmount(), $invoice->getCurrency()); ?>
						</div>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_INVOICE_EDIT_TAX', 'edit_tax'); ?>

					<div class="flex-grow col-md-7">
						<div class="">
							<?php echo $this->html('form.amount', $invoice->getTaxAmount(), $invoice->getCurrency()); ?>
						</div>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_INVOICE_EDIT_NON_TAXABLE', 'non_taxable'); ?>

					<div class="flex-grow col-md-7">
						<div class="">
							<?php echo $this->html('form.amount', $invoice->getNontaxableAmount(), $invoice->getCurrency()); ?>
						</div>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_INVOICE_EDIT_TOTAL', 'edit_total'); ?>

					<div class="flex-grow col-md-7">
						<div class="">
							<?php echo $this->html('form.amount', $invoice->getTotal(), $invoice->getCurrency()); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>