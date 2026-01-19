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
<div class="rounded-md bg-gray-50 p-md text-left mb-sm space-y-sm">
	<?php if ($plan->advancedpricing) { ?>
	<div class="o-form-group" data-plan-advancedpricing data-price-perday="<?php echo $plan->getPricePerDay(false); ?>">
		<div class="o-form-check">
			<input class="fd-custom-radio" id="advancedpricing<?php echo $plan->getId(); ?>" type="radio" value="advancedpricing" name="plan-extra<?php echo $plan->getId(); ?>" data-advancedpricing-radio>

			<span class="o-form-check__text" for="advancedpricing<?php echo $plan->getId(); ?>">
				<?php echo JText::_('COM_PP_PLAN_SUBSCRIBE_FOR'); ?>
			</span>
		</div>
	</div>
	<?php } ?>

	<div class="o-form-group">
		<label class="o-form-label text-left" for="pp-advancedpricing-field">
			<?php echo $plan->advancedpricing->units_title; ?>
		</label>
		<div class="o-input-group">
			<input name="total" id="pp-advancedpricing-field" type="int" class="o-form-control" value="" min="<?php echo $advancedpricing->units_min;?>" max="<?php echo $advancedpricing->units_max; ?>" data-number-of-unit/>

			<?php echo $this->fd->html('button.standard', 'COM_PAYPLANS_APP_ADVANCED_PRICING_CALCULATE_PRICE', 'default', 'default', ['outline' => true, 'attributes' => 'data-number-of-purchase']); ?>
		</div>
		<input name="unit_min" type="hidden" value="<?php echo $advancedpricing->units_min;?>" data-unit-min-limit>
		<input name="unit_max" type="hidden" value="<?php echo $advancedpricing->units_max;?>" data-unit-max-limit>

		<div class="text-center mt-xl">
			<div class="text-danger" data-pp-advancepricing-message></div>
		</div>

		<div class="text-center mt-xl">
			<span>
				<?php echo JText::_('COM_PAYPLANS_APP_ADVANCED_PRICING_AVAILABLE_RANGE');?> (<?php echo $advancedpricing->units_min .'-'. $advancedpricing->units_max;?>) 
			</span>
		</div>
	</div>
	<div class="mt-md">
		<table class="o-table o-table--borderless rounded-md">
			<thead>
				<tr class="text-sm text-gray-800">
					<th class="py-xs">
					</th>
					<th class="py-xs">
						<?php echo JText::_('COM_PP_PLAN_TIME'); ?>
					</th>
					<th class="py-xs">
						<?php echo JText::_('COM_PP_PLAN_PRICE_UNIT'); ?>
					</th>
					<th class="py-xs">
						<?php echo JText::_('COM_PP_PLAN_ACTUAL_PRICE'); ?>
					</th>
					<th class="py-xs">
						<?php echo JText::_('COM_PP_PLAN_PRICE_TO_PAY'); ?>
					</th>
					<th class="py-xs">
						<?php echo JText::_('COM_PP_PLAN_SAVINGS'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($advancedpricing->priceset as $set) { ?>
					<?php $actualPrice = $plan->getPricePerDay(false) * PPHelperPlan::convertAdvancePricingTimeArrayToDays($set['duration']);?>
					<?php
						$set['price'] = !$set['price'] ? 0 : $set['price'];
						$priceToPay = $set['price'] * 1;
					?>
					<?php $savings = $actualPrice - $priceToPay; ?>

					<tr class="text-sm text-gray-800" data-price-set data-price="<?php echo $set['price']; ?>" data-days="<?php echo PPHelperPlan::convertAdvancePricingTimeArrayToDays($set['duration']); ?>">
						<td class="py-xs pl-sm pr-md">
							<input id="priceset-select" type="radio" name="priceset" value="<?php echo $priceToPay ?>" data-duration="<?php echo $set['duration']; ?>" data-priceset-selection />
						</td>
						<td class="py-xs">
							<?php echo PP::string()->formatTimer($set['duration']); ?>
						</td>
						<td class="py-xs">
							<?php echo $this->html('html.amount', $set['price'], $this->config->get('currency')); ?>
						</td>
						<td class="py-xs" data-actual-price>
							<?php echo $this->html('html.amount', $actualPrice, $this->config->get('currency')); ?>
						</td>
						<td class="py-xs" data-price-topay>
							<?php echo $this->html('html.amount', $priceToPay, $this->config->get('currency')); ?>
						</td>
						<td class="py-xs" data-savings>
							<?php echo $this->html('html.amount', $savings, $this->config->get('currency')); ?>
						</td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>