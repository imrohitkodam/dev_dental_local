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
<div id="fd">
	<div id="pp" class="pp-frontend pp-main <?php echo $modules->isMobile() ? 'is-mobile' : 'is-desktop';?>" data-pp-structure>
		<?php if ($groups || $plans) { ?>
		<form action="<?php echo JRoute::_('index.php');?>" method="post">

			<?php echo $renderBadgeStyleCss; ?>
			<div class="pp-plans pp-plans--<?php echo $columns;?> t-lg-mt--xl">
				<?php if ($plans) { ?>
					<?php foreach ($plans as $plan) { 
						$suffix = 'plan-id-' . $plan->getId(); ?>

						<div class="pp-plans__item">
							<div class="pp-plan-card<?php echo $plan->isHighlighted() ? ' is-highlight' : '';?><?php echo $plan->hasBadge() ? ' has-badges' : ''; ?>">
								<div class="pp-plan-card__hd">
									<div class="pp-plan-card__label pp-plan-card__label--<?php echo $plan->getBadgePosition(); ?>">
										<div class="pp-plan-pop-label <?php echo $suffix; ?>">
											<span class="pp-plan-pop-label__txt <?php echo $suffix; ?>">
												<?php echo JText::_($plan->getBadgeTitle()); ?>
											</span>
										</div>
									</div>
									<div class="pp-plan-card__title">
										<?php echo PPJString::ucfirst(JText::_($plan->getTitle()));?>
									</div>

									<div class="pp-plan-card__desc">
										<?php echo PPJString::ucfirst(JText::_($plan->getTeaser()));?>
									</div>
									
									<div class="pp-plan-card__price">
										<?php if ($plan->isFree()) { ?>
											<?php echo JText::_('COM_PAYPLANS_PLAN_PRICE_FREE');?>
										<?php } else { ?>

											<?php $currency = $plan->getCurrency();
												  $amount = $plan->getPrice();?>
											
											<?php if (PP::config()->get('show_currency_at') == 'before') { ?>
												<span class="pp-currency"><?php echo $currency;?>&nbsp;</span><span class="pp-amount"><?php echo $amount;?></span>
											<?php } else { ?>
												<span class="pp-amount"><?php echo $amount;?></span>&nbsp;<span class="pp-currency"><?php echo $currency;?></span>
											<?php } ?>

										<?php } ?>
									</div>

									<div class="pp-plan-card__period">
										<?php if ($plan->isRecurring()) { ?>
											<?php echo JText::_('COM_PAYPLANS_PLAN_PRICE_TIME_SEPERATOR'); ?>
										<?php } else { ?>
											<?php echo JText::_('COM_PAYPLANS_PLAN_PRICE_TIME_SEPERATOR_FOR'); ?>
										<?php } ?>

										<?php 	$lifetime = true;
												$count = 0;
												$timer = $plan->getExpiration();
												
												foreach ($timer as $key => $value) {
													$value = (int) $value;

													if ($value > 0) {
														$lifetime = false;
													}

													$count += $value ? 1 : 0;
												}

												if ($lifetime) {
													echo JText::_('COM_PAYPLANS_PLAN_LIFE_TIME');
												}

												$counter = 0;
												$str = '';

												foreach ($timer as $key => $value) {
													$value = (int) $value;
													$key = PPJString::strtoupper($key);
													
													// show values if they are greater than zero only
													if (!$value) {
														continue;
													}
														
													$key .= ($value > 1) ? 'S':'';
													$valueStr = $value ." ";
													
													$concatStr = $counter ? ' ' . JText::_('COM_PAYPLANS_PLANTIME_CONCATE_STRING_AND') . ' ' : '';
													$str .= $concatStr.$valueStr . JText::_("COM_PAYPLANS_PLAN_" . $key); 
													
													$counter++;
												}

												echo $str; ?>
									</div>
								</div>

								<?php if ($plan->getDescription(true)) { ?>
								<div class="pp-plan-card__bd">
									<div class="pp-plan-card__features">
										<?php echo JText::_($plan->getDescription(true));?>
									</div>
								</div>
								<?php } ?>

								<div class="pp-plan-card__ft" data-plan-footer>
									<div class="pp-plan-card__forms">
										<?php if ($columns == 1) { ?>
											<?php if ($plan->advancedpricing) { ?>
												<div class="t-border-radius--lg t-bg--shade t-lg-p--lg t-text--left t-lg-mb--lg" data-adv-pricing>
													<?php require JModuleHelper::getLayoutPath('mod_payplans_plan', 'advancedpricing'); ?>
												</div>
											<?php } ?>
										<?php } ?>

										<?php if ($plan->priceVariations) { ?>
											<div class="t-border-radius--lg t-bg--shade t-lg-p--lg t-text--left t-lg-mb--lg" data-modifier>
												<?php require JModuleHelper::getLayoutPath('mod_payplans_plan', 'pricevariation'); ?>
											</div>
										<?php } ?>

										<?php echo $fd->html('button.link', $plan->getSelectPermalink() . "&return_url=" . $returnUrl, 'COM_PAYPLANS_PLAN_SUBSCRIBE_BUTTON', 'primary', 'default', [
											'class' => 'mt-sm',
											'attributes' => 'data-subscribe-button data-default-link="' . $plan->getSelectPermalink() . "&return_url=" . $returnUrl . '"'
										]); ?>
									</div>
								</div>
							</div>
						</div>

					<?php } ?>
				<?php } ?>
			</div>
		</form>
	<?php } else { ?>
		<div class="pp-access-alert pp-access-alert--warning">
			<div class="pp-access-alert__icon">
				<i class="fdi fas fa-exclamation-circle"></i>
			</div>

			<div class="pp-access-alert__content">
				<div class="pp-access-alert__title t-lg-mb--xl">
					<?php echo JText::_('COM_PP_NO_PLANS_CURRENTLY'); ?>
				</div>

				<div class="pp-access-alert__desc">
					<?php if (!PP::config()->get('displayExistingSubscribedPlans')) { ?>
						<?php echo JText::_('COM_PP_NO_OTHER_PLANS_CURRENTLY_INFO'); ?>
					<?php } else { ?>
						<?php echo JText::_('COM_PP_NO_PLANS_CURRENTLY_INFO'); ?>
					<?php } ?>
				</div>
			</div>

			<div class="pp-access-alert__action">
				<?php echo $fd->html('button.link', PPR::_('index.php?option=com_payplans&view=dashboard'), 'COM_PP_PROCEED_TO_DASHBOARD_BUTTON', 'primary', 'default', [
					'icon' => 'fdi fa fa-briefcase'
				]); ?>
			</div>
		</div>
	<?php } ?>
	</div>
</div>