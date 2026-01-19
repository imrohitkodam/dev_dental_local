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
<div class="pp-frontend__overlay"><div class="o-loader is-active"></div></div>

<div class="pp-checkout-container">
	<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="checkout" novalidate autocomplete="off" class="pp-checkout-container__form" data-pp-checkout-form enctype="multipart/form-data">	
		<div class="pp-checkout-container__hd">
			<?php echo $this->output('site/checkout/default/header'); ?>

			<div class="pp-checkout-wrapper">
				<div class="pp-checkout-container__title">
					<?php echo JText::sprintf('COM_PP_ORDER_CONFIRMATION', $invoice->getKey());?>
				</div>

				<?php echo PP::info()->html(array('t-lg-mb--xl')); ?>

				<div class="text-center">
					<div class="o-loader o-loader--inline" data-pp-checkout-loader="" style="margin: 0 auto;"></div>
				</div>
				<div class="pp-checkout-menu">
					<div class="pp-checkout-item">
						<div class="pp-checkout-item__title">
							<?php echo JText::_('COM_PP_CHECKOUT_ITEM'); ?>
						</div>

						<div class="pp-checkout-item__content">
							<table class="pp-checkout-table">
								<tbody>
									<tr>
										<td class="text-left">
											<div class="pp-checkout-table__title">
												<?php echo JText::_($invoice->getTitle()); ?>
											</div>

											<div class="pp-checkout-table__desc">
												<?php echo JText::_($plan->getDescription(true));?>
											</div>
											<div class="pp-checkout-table__note">
												<?php echo $this->output('site/checkout/default/plandetails', array('invoice' => $invoice, 'recurring' => $invoice->isRecurring())); ?>
											</div>
										</td>

										<td class="pp-checkout-table__last-col text-right align-top">
											<div class="pp-checkout-table__price">
												<?php if ($plan->isFree()) { ?>
													<?php echo JText::_('COM_PAYPLANS_PLAN_PRICE_FREE');?>
												<?php } else { ?>
													<?php echo $this->html('html.amount', $invoice->getSubtotal(), $invoice->getCurrency()); ?>
												<?php } ?>
											</div>
										</td>
									</tr>
								</tbody>
							</table>

							<hr class="flex h-[1px] border-none bg-gray-300">
							<div class="<?php echo $skipInvoice ? 't-hidden' : ''; ?>">
								<table class="pp-checkout-table md:w-6/12 md:ml-auto md:mr-no">

									<tbody data-pp-modifiers>
										<div data-pp-registration-wrapper class="<?php echo $registrationOnly ? 't-hidden' : ''; ?>">
											<?php echo $this->output('site/checkout/default/modifier'); ?>
										</div>
									</tbody>
								</table>
								<hr class="flex h-[1px] border-none bg-gray-300 md:w-6/12 md:ml-auto md:mr-no <?php echo $modifiers ? '' : 't-hidden'; ?>" data-modifiers-separator>

								<?php if ($this->config->get('enableDiscount')) { ?>
								<div data-pp-registration-wrapper class="<?php echo $registrationOnly ? 't-hidden' : ''; ?>">
									<?php echo $this->output('site/checkout/default/discounts'); ?>
								</div>
								<?php } ?>

								<?php echo $this->renderPlugins($pluginResult, 'payplans_order_confirm_payment'); ?>

								<table class="pp-checkout-table md:w-6/12 md:ml-auto md:mr-no">
									<tbody>
										<tr data-pp-payable data-pp-registration-wrapper class="<?php echo $registrationOnly ? 't-hidden' : ''; ?>">
											<td class="text-left">
												<div class="pp-checkout-table__title"><?php echo JText::_('COM_PAYPLANS_ORDER_CONFIRM_AMOUNT_PAYABLE');?></div>
											</td>
											<td class="pp-checkout-table__last-col text-right">
												<div class="pp-checkout-table__price pp-checkout-table__price--total" data-pp-payable-label><?php echo $this->html('html.amount', $invoice->getTotal(), $invoice->getCurrency()); ?></div>
											</td>
										</tr>
										
									</tbody>
								</table>
								<hr data-pp-registration-wrapper class="flex h-[1px] border-none bg-gray-300 md:w-6/12 md:ml-auto md:mr-no <?php echo $registrationOnly ? 't-hidden' : ''; ?>">

							</div>
						</div>
					</div>
				</div>
				<div class="<?php echo $skipInvoice ? 't-hidden' : ''; ?>">
					<div data-pp-registration-wrapper class="<?php echo $registrationOnly ? 't-hidden' : ''; ?>">
						<!-- Social Discounts -->
						<?php if ($socialDiscount->isEnabled()) { ?>
							<?php echo $socialDiscount->html($invoice);?>
						<?php } ?>

						<?php echo $this->output('site/checkout/default/referrals'); ?>

						<?php echo $this->renderPlugins($pluginResult, 'pp-checkout-options'); ?>

						<?php echo $this->output('site/checkout/default/addons'); ?>
					</div>
				</div>
			</div>
		</div>

		<?php $hideBody = !$invoice->isPaymentNeeded() && $this->my->id && !$this->config->get('show_billing_details')
		 && !$userCustomDetails && !$subsCustomDetails && !isset($pluginResult['pp-before-actions']) && !isset($pluginResult['pp-user-details']) ? true : false; ?>
		<div class="pp-checkout-container__bd <?php echo $hideBody ? 't-hidden' : ''; ?>" data-pp-checkout-body data-hide="<?php echo $hideBody ? 1 : 0; ?>">
			<div class="pp-checkout-wrapper">					
				<div class="pp-checkout-menu">
					<div class="<?php echo $skipInvoice ? 't-hidden' : ''; ?>">
						<div data-pp-registration-wrapper class="<?php echo $registrationOnly ? 't-hidden' : ''; ?>">
							<div class="pp-checkout-item <?php echo $invoice->isPaymentNeeded() ? '' : 't-hidden'; ?>" data-pp-payment-form>
								<div class="pp-checkout-item__title">
									<?php echo mb_strtoupper(JText::_('COM_PAYPLANS_ORDER_MAKE_PAYMENT_FROM'));?>
								</div>
								<div class="pp-checkout-item__content space-y-sm">
									<?php if ($plan->isFree() && !$provider && !$invoice->isPaymentNeeded()) { ?>
										<div data-pp-no-payment>
											<?php echo JText::_('COM_PAYPLANS_ORDER_NO_PAYMENT_METHOD_NEEDED')?>
										</div>
									<?php } ?>

									<?php if ($provider) { ?>
										<div>
											<?php echo JText::sprintf('COM_PP_PAYMENT_METHOD_DEFAULT', $provider->getTitle()); ?>
											<?php echo $this->html('form.hidden', 'app_id', $provider->getId()); ?>
										</div>
									<?php } else { ?>

										<div class="<?php echo !$provider && $invoice->isPaymentNeeded() ? '' : 't-hidden'; ?>" data-pp-form-group>
											<?php if ($paymentMethodLayout === 'dropdown') {
												$providerOptions = [];

												foreach ($providers as $provider) {
													$providerOptions[$provider->getId()] = JText::_($provider->getTitle());
												}
											?>
												<?php echo $this->fd->html('form.floatingLabel', 'COM_PP_CHECKOUT_SELECT_PAYMENT_PROVIDER', 'app_id', 'dropdown', '', 'payment_provider', [
													'class' => 'is-filled',
													'html' => $this->fd->html('form.dropdown', 'app_id', '', $providerOptions)
												]); ?>
											<?php } else { ?>
												<div class="border border-gray-300 rounded-md  divide-y divide-solid divide-gray-300 overflow-hidden">
													<?php foreach ($providers as $key => $provider) { ?>
														<?php echo $this->fd->html('radio.image', PP::getPaymentProviderLogo($provider->type), 'app_id', $key == 0, $provider->getId(), 'item-checkbox-' .$provider->getId(), JText::_($provider->getTitle()), [
															'class' => ''
														]); ?>
													<?php } ?>
												</div>
											<?php } ?>
										</div>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>

					<?php if (!$this->my->id) { ?>
						<?php echo $registration->html($invoice);?>
					<?php } ?>

					<div data-pp-registration-wrapper class="<?php echo $registrationOnly ? 't-hidden' : ''; ?>">

						<?php if ($this->config->get('show_billing_details') && $plan->canShowBillingDetails()) { ?>
							<?php echo $this->output('site/checkout/default/company'); ?>
						<?php } ?>

						<?php $position = 'pp-subscription-details';?>
						<?php //echo $this->loadTemplate('partial_position',compact('plugin_result','position'));?>

						<?php $position = 'pp-user-mobile-number';?>
						<?php //echo $this->loadTemplate('partial_position',compact('plugin_result','position'));?>

						<?php echo $this->renderPlugins($pluginResult, 'pp-user-details'); ?>

						<?php if ($userCustomDetails) { ?>
							<?php foreach ($userCustomDetails as $customDetail) { ?>
								<?php echo $customDetail->renderForm($user, true, 'userparams'); ?>
							<?php } ?>
						<?php } ?>

						<?php if ($subsCustomDetails) { ?>
							<?php foreach ($subsCustomDetails as $customDetail) { ?>
								<?php echo $customDetail->renderForm($subscription, true, 'subscriptionparams'); ?>
							<?php } ?>
						<?php } ?>
					</div>
					
					<?php echo $this->renderPlugins($pluginResult, 'pp-before-actions'); ?>

					<?php $position = 'order-confirm-footer';?>
					<?php //echo $this->loadTemplate('partial_position',compact('plugin_result','position'));?>
				</div>
			</div>
		</div>

		<div class="pp-checkout-container__ft">
			<div class="pp-checkout-wrapper">
				<div class="flex items-center">
					<div class="flex-grow">
						<a href="<?php echo $returnUrl;?>" class="no-underline">
							&larr; <?php echo JText::_('COM_PP_CANCEL_AND_RETURN');?>
						</a>
					</div>

					<div class="flex-shrink-0">
						<button type="submit" class="o-btn o-btn--primary" data-pp-submit>
							<?php if ($this->my->id || (!$registration->isBuiltIn() && $registration->getNewUserId())) { ?>
								<span>
									<?php echo JText::_('COM_PAYPLANS_ORDER_CONFIRM_BTN');?>
								</span>
							<?php } else { ?>
								<span data-pp-submit-login data-pp-registration-wrapper class="<?php echo $registrationOnly || ($registration->isBuiltIn() && $this->config->get('default_form_order') == 'register') ? ' t-hidden' : ''; ?>">
									<?php echo JText::_('COM_PP_LOGIN_ORDER_CONFIRM_BTN'); ?>
								</span>

								<?php if ($registration->isBuiltIn()) { ?>
								<span data-pp-submit-register class="<?php echo $this->config->get('default_form_order') != 'register' ? 't-hidden' : ''; ?>">
									<?php echo JText::_('COM_PP_REGISTER_ORDER_CONFIRM_BTN'); ?>
								</span>
								<?php } ?>
							<?php } ?>
						</button>
					</div>
				</div>
			</div>
		</div>

		<?php echo $this->html('form.hidden', 'account_type', $accountType, array('data-pp-account-type' => '')); ?>
		<?php echo $this->html('form.action', '', 'checkout.confirm'); ?>
		<?php echo $this->html('form.hidden', 'invoice_key', $invoice->getKey(), array('data-pp-invoice-key' => '')); ?>
	</form>
</div>