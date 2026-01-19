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
<div class="pp-orders ">
	<?php if ($subscriptions) { ?>
	<div class="flex flex-col gap-md">
		<?php foreach ($subscriptions as $subscription) { ?>
			<div class="o-card bg-white text-gray-600">
				<div class="o-card__body space-y-sm">
					<div class="o-alert o-alert--danger <?php echo !$subscription->isExpired() ? 't-hidden' : '';?>">
						<?php echo JText::_('COM_PP_SUBSCRIPTION_EXPIRED_PLEASE_RENEW'); ?>
					</div>
					<div class="flex">
						<div class="flex-grow min-w-0 space-y-xs">
							<div class="o-card__title">
								<a href="<?php echo $subscription->getPermalink();?>" class="no-underline">
									<?php echo JText::_($subscription->getTitle());?>
								</a>
							</div>
							<div class="o-card__desc">
								<div class="flex flex-wrap gap-sm">
									<span>
										#<?php echo $subscription->getKey();?>
									</span>
									<?php $showInvoice = true; ?>
									<?php $plan = $subscription->getPlan();?>

									<?php if ($this->config->get('layout_free_invoices') && !$plan->isRecurring()) {
											if ($plan->isFree()) { 
													$showInvoice = false;
											} else {
												$order = $subscription->getOrder();
												$invoice = $order->getLastMasterInvoice();

												if ($invoice instanceof PPInvoice) {
													$payment = $invoice->getPayment();
													 if (!$payment instanceof PPPayment) {
														$showInvoice = false;
													 }
												}
											}
										} 
									?>

									<span class="<?php echo $showInvoice ? '' : 't-hidden';?>">
										<a href="<?php echo $subscription->getPermalink();?>" class="no-underline">
											<i class="fdi far fa-file"></i>&nbsp; <?php echo JText::_('COM_PP_INVOICES');?>
										</a>
									</span>

									<?php if ($subscription->getSubscriptionDate()) { ?>
									<span class="pp-subscription-date">
										<span data-fd-tooltip data-fd-tooltip-placement="top" data-fd-tooltip-title="<?php echo JText::sprintf('COM_PP_SUBSCRIPTION_CREATED_TOOLTIP', $subscription->getSubscriptionDate(true)->toDisplay(PP::getDateFormat()));?>" class="no-underline">
											<i class="fdi far fa-calendar"></i>&nbsp; <?php echo $subscription->getSubscriptionDate(true)->toDisplay(PP::getDateFormat());?>
										</span>
									</span>
									<?php } ?>
								</div>

							</div>

							<?php if ($subscription->isRecurring() && $subscription->order->isCancelled()) { ?>
							<div class="o-card__desc">
								<span><?php echo JText::_('COM_PP_SUBSCRIPTION_CANCELLED_AND_WILL_NOT_BE_REBILLED');?></span>
							</div>
							<?php } ?>
						</div>
						<div class="flex-shrink-0">
							<?php if ($subscription->isExpired() && $subscription->getExpirationDate()) { ?>
								<?php echo $this->html('subscription.status', $subscription, array('postfix' => '(' . $subscription->getExpirationDate(true)->toDisplay(PP::getDateFormat()) . ')')); ?>
							<?php } else { ?>
								<?php if (!$subscription->isNotActive()) { ?>
									<?php echo $this->html('subscription.status', $subscription); ?>
								<?php } ?>
							<?php } ?>
						</div>
					</div>
				</div>
				<div class="o-card-list-group">
					<div class="o-card-list-group__item bg-gray-50 border-t border-gray-300">
						<div class="o-card--meta">
							<div class="flex">
								<div class="flex-grow">
									<div><?php echo JText::_('COM_PP_SUBSCRIPTION_PRICE');?></div>
								</div>
								<div class="flex-shrink-0">
									<div class="">
										<div class="">
											<?php $order = $subscription->getOrder();
												  $invoice = $order->getInvoice();
												  $addons = false;
												  if ($invoice instanceof PPInvoice) {
														  $addonModifier = $invoice->getModifiers(array('type' => 'plan_addons', 'frequency' => PP_MODIFIER_FREQUENCY_EACH_TIME));

														  if ($addonModifier && $this->config->get('show_addonprice_dashbaord')) {

															$addons = true;
															$eachTimePrice = 0;
															$trial = false;

															foreach ($addonModifier as $modifier) {

																$addonPrice = $modifier->getAmount();
																if ($modifier->isPercentage()) {
																	$planPrice = $invoice->getSubtotal();
																	
																	if ($addonPrice) {
																		$addonPrice = ($planPrice * $addonPrice ) / 100;
																	}
																}
																$eachTimePrice += $addonPrice;
															}
														}
													}	?>
											<?php if ($subscription->isRecurring()) { ?>
												<?php if (in_array($subscription->expirationType, array(PP_RECURRING_TRIAL_1, PP_RECURRING_TRIAL_2))) { ?>

													<div>
														<?php $amount = $subscription->getPrice(PP_PRICE_RECURRING_TRIAL_1);?>
														<?php if ($addons) { ?>
															<?php $trial = true; ?>
															<?php $amount = $amount + $eachTimePrice; ?>
														<?php } ?>

														<?php echo $this->html('html.amount', $amount, $subscription->currency); ?>

														<?php echo JText::sprintf('COM_PAYPLANS_DASHBOARD_SUBSCRIPTION_CONFIRM_FIRST_CHARGABLE_AMOUNT', $this->html('html.plantime', $subscription->getExpiration(PP_RECURRING_TRIAL_1), ['isRecurring' => true])); ?>
													</div>

													<?php if ($subscription->expirationType == PP_RECURRING_TRIAL_2) { ?>
													<div>

														<?php $amount = $subscription->getPrice(PP_PRICE_RECURRING_TRIAL_2); ?>
														<?php if ($addons) { ?>
															<?php $amount = $amount + $eachTimePrice; ?>
														<?php } ?>

														<?php echo $this->html('html.amount', $amount,  $subscription->currency);?>

														<?php echo JText::sprintf('COM_PAYPLANS_DASHBOARD_SUBSCRIPTION_CONFIRM_SECOND_CHARGABLE_AMOUNT', $this->html('html.plantime', $subscription->getExpiration(PP_RECURRING_TRIAL_2), ['isRecurring' => true]));?>
													</div>
													<?php } ?>
												<?php } ?>

												<?php $amount = $subscription->getPrice(); ?>

												<?php if ($addons) { ?>
														<?php $amount = $amount + $eachTimePrice; ?>
												<?php } ?>

												<?php $amountHtml = $this->html('html.amount', $amount, $subscription->currency); ?>
												<?php if ($subscription->getRecurrenceCount() <= 0) { ?>
												<div>
													<?php echo JText::sprintf('COM_PAYPLANS_DASHBOARD_SUBSCRIPTION_CONFIRM_FIRST_RECURRENCE_COUNT_ZERO_RECURRENCE_COUNT', $amountHtml, $this->html('html.plantime', $subscription->getExpiration(), ['isRecurring' => true]));?>
												</div>
												<?php } else { ?>
												<div>
													<?php echo JText::sprintf('COM_PAYPLANS_DASHBOARD_SUBSCRIPTION_CONFIRM_FIRST_RECURRENCE_COUNT', $amountHtml, $this->html('html.plantime', $subscription->getExpiration(), ['isRecurring' => true]), $subscription->getRecurrenceCount());?>
												</div>
												<?php } ?>

											<?php } else { ?>
												<b><?php echo $this->html('html.amount', $subscription->getTotal(), $subscription->currency); ?></b>
											<?php } ?>
										</div>
										
									</div>
								</div>
							</div>
						</div>

					</div>
					

				</div>
				<div class="o-card-list-group">
					<div class="o-card-list-group__item bg-gray-50 border-t border-gray-300">
						<div class="o-card--meta">
							<div class="flex gap-sm">
								<div class="flex-grow">
									<div><?php echo JText::_('COM_PP_SUBSCRIPTION_PERIOD');?></div>
								</div>
								<div class="">
									<div>
										<?php if ($subscription->isOnHold()) { ?>
											&mdash;
										<?php } ?>

										<?php if ($subscription->isActive()) { ?>
											<?php if ($subscription->expirationDate) { ?>
												<i class="fdi far fa-calendar"></i>&nbsp;
												<b>
													<?php echo $subscription->getSubscriptionDate(true)->toDisplay(PP::getDateFormat());?>
												</b>

												<span class="separator t-lg-ml--md t-lg-mr--md">&mdash;</span>

												<b>
													<?php echo $subscription->getExpirationDate(true)->toDisplay(PP::getDateFormat());?>
												</b>
											<?php } else { ?>
												<b><?php echo JText::_('COM_PAYPLANS_ORDER_SUBSCRIPTION_TIME_LIFETIME'); ?></b>
											<?php } ?>
										<?php } ?>

										<?php if ($subscription->isExpired()) { ?>
											<b class="text-danger"><?php echo JText::_('COM_PP_EXPIRED'); ?></b>
										<?php } ?>

										<?php if ($subscription->isNotActive()) { ?>
											<b class="text-danger"><?php echo JText::_('COM_PAYPLANS_ORDER_SUBSCRIPTION_NOT_ACTIVATED'); ?></b>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>
					</div>

				</div>
				<?php if (!$subscription->isOnHold() || $subscription->canCancel() || $subscription->actions) { ?>
					<div class="o-card__footer px-sm py-md border-t border-gray-300">
						<div class="flex justify-end">
							<?php if ($subscription->canCancel()) { ?>
								<div class="flex-grow min-w-0">
									<a href="javascript:void(0);" class="o-btn o-btn--danger-ghost" data-cancel-subscription data-key="<?php echo $subscription->order->getKey();?>">
										<?php echo JText::_('COM_PP_CANCEL_SUBSCRIPTION');?>
									</a>
								</div>
							<?php } ?>
							<?php if (!$subscription->isOnHold() || $subscription->actions) { ?>
								<div class="">
									<div class="o-btn-toolbar flex gap-sm">
										<?php if ($subscription->actions) { ?>
											<?php foreach ($subscription->actions as $action) { ?>
												<?php echo $action;?>
											<?php } ?>
										<?php } ?>

										<?php if ($subscription->isNotActive() && $subscription->pendingInvoice && !$subscription->pendingInvoice->hasTransaction()) { ?>
											<?php if ($this->config->get('user_delete_orders')) { ?>
												<div class="o-btn-group">
													<button type="button" class="o-btn o-btn--default-ghost" data-delete-subscription data-key="<?php echo $subscription->order->getKey();?>">
														<?php echo JText::_('COM_PP_DELETE_ORDER');?> 
													</button>
												</div>
											<?php } ?>

												<div class="o-btn-group">
													<a href="<?php echo PPR::_('index.php?option=com_payplans&view=checkout&invoice_key=' . $subscription->pendingInvoice->getKey() . '&tmpl=component'); ?>" class="o-btn o-btn--primary">
														<?php echo JText::_('COM_PP_COMPLETE_ORDER_NOW');?>
													</a>
												</div>
											<?php } ?>

											<?php if ($subscription->isRenewable()) { ?>
											<div class="o-btn-group">
												<a href="<?php echo PPR::_('index.php?option=com_payplans&view=order&layout=processRenew&subscription_key=' . $subscription->getKey() . '&tmpl=component'); ?>" class="o-btn o-btn--primary">
													<?php echo JText::_('COM_PP_APP_RENEW_BUTTON'); ?>
												</a>
											</div>
											<?php } ?>

											<?php if ($subscription->isUpgradable()) { ?>
											<div class="o-btn-group">
												<button type="button" class="o-btn o-btn--default" data-upgrade-button data-key="<?php echo $subscription->order->getKey(); ?>">
													<?php echo JText::_('COM_PP__APP_UPGRADE_BUTTON'); ?>
												</button>
											</div>
											<?php } ?>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
	</div>
	<?php } ?>

	<?php if (!$subscriptions) { ?>
	<div class="pp-access-alert pp-access-alert--warning">
		<div class="pp-access-alert__icon"><i class="fdi fas fa-exclamation-circle"></i></div>
		<div class="pp-access-alert__content">
			<div class="pp-access-alert__title t-lg-mb--xl">
				<?php echo JText::_('COM_PP_NO_SUBSCRIPTIONS_CURRENTLY'); ?>
			</div>
			<div class="pp-access-alert__desc">
				<?php echo JText::_('COM_PP_NO_SUBSCRIPTIONS_CURRENTLY_INFO'); ?>
			</div>
		</div>
		<div class="pp-access-alert__action">
			<?php echo $this->fd->html('button.link', PPR::_('index.php?option=com_payplans&view=plan&from=dashboard'), 'COM_PP_VIEW_AVAILABLE_PLANS', 'primary', 'default', ['icon' => 'fdi fa fa-shopping-basket']); ?>
		</div>
	</div>
	<?php } ?>
</div>