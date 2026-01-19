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

$suffix = 'plan-id-' . $plan->getId();
?>
<div class="pp-plans__item" data-plans-item data-plan-id="<?php echo $plan->getId(); ?>">
	<div class="pp-plan-card<?php echo $plan->isHighlighted() ? ' is-highlight' : '';?><?php echo $plan->hasBadge() ? ' has-badges' : ''; ?>" data-plans-item-card>
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
					<?php 
						$displayPrice = $plan->getPrice();
						if ($plan->basictax) {
							$displayPrice += $plan->basictax;
						}
					?>
					<?php echo $this->html('html.amount', $displayPrice, $plan->getCurrency()); ?>
				<?php } ?>
			</div>

			<div class="pp-plan-card__period">
				<?php if ($plan->isRecurring()) { ?>
						<?php echo JText::_('COM_PAYPLANS_PLAN_PRICE_TIME_SEPERATOR'); ?>
						<?php echo $this->html('html.plantime', $plan->getExpiration(), ['isRecurring' => true]); ?>
				<?php } else { ?>
					<?php if ($plan->fixedExpirationDate) { ?>
						<?php echo JText::sprintf('COM_PP_FIXEDDATEEXPIRATION_SEPARATOR', $plan->fixedExpirationDate->toDisplay($this->config->get('date_format')));?>
					<?php } else { ?>
							<?php echo JText::_('COM_PAYPLANS_PLAN_PRICE_TIME_SEPERATOR_FOR'); ?>
							<?php echo $this->html('html.plantime', $plan->getExpiration()); ?>
					<?php } ?>
				<?php } ?>
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
						<?php echo $this->output('site/plan/default/advancedpricing', ['advancedpricing' => $plan->advancedpricing, 'plan' => $plan]); ?>
						</div>
					<?php } ?>
				<?php } ?>
				
				<?php if ($plan->pricevariations) { ?>
					<div class="t-border-radius--lg t-bg--shade t-lg-p--lg t-text--left t-lg-mb--lg" data-modifier>
					<?php foreach ($plan->pricevariations as $priceVariation) { ?>
						<?php if ($priceVariation->options) { ?>
							<?php echo $this->output('site/plan/default/pricevariation', ['pricevariation' => $priceVariation, 'plan' => $plan]); ?>
						<?php } ?>
					<?php } ?>
					</div>
				<?php } ?>

				<?php
				//@TODO: Figure out a way to generate output from plugins
				//$position = 'plan-block-bottom_'.$plan->getId();
				//echo $this->output('site/partials/position',compact('plugin_result','position'));
				?>
				<?php echo $this->fd->html('button.link', $plan->getSelectPermalink(), 'COM_PAYPLANS_PLAN_SUBSCRIBE_BUTTON', 'primary', 'md', ['attributes' => 'data-subscribe-button data-default-link="' . $plan->getSelectPermalink() . '"']); ?>
			</div>
		</div>
	</div>
</div>