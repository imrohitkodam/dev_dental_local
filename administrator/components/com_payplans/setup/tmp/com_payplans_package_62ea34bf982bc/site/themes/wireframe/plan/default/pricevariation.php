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
	<div class="o-form-group">
		<div class="o-form-check">
			<input class="fd-custom-radio" id="plan-radio<?php echo $plan->getId(); ?>" type="radio" value="pricevariation" name="plan-extra<?php echo $plan->getId(); ?>" data-pricevariation-radio>

			<span class="o-form-check__text" for="pricevariation-radio<?php echo $plan->getId(); ?>">
				<?php echo JText::_('COM_PP_PLAN_SUBSCRIBE_FOR'); ?>
			</span>
		</div>
	</div>
	<?php } ?>

	<div class="o-form-group" data-plan-pricevariation>
		<div class="o-control-input">
			<div class="o-select-group">
				<select name="priceVariation" id="planPriceVariation" class="o-form-control" data-pricevariation-selection data-id="<?php echo $pricevariation->getId(); ?>" >
					<option value="default">
						<?php echo $plan->getTitle(); ?> <?php echo $this->html('html.amount', $plan->getPrice(), $plan->getCurrency()); ?> <?php echo $plan->separator; ?> <?php echo $this->html('html.plantime', PPHelperPlan::convertIntoTimeArray($plan->getRawExpiration()), ['isRecurring' => $plan->isRecurring()]); ?>
					</option>

					<?php foreach ($pricevariation->options as $option) { ?>
						<option value="<?php echo $option->title;?>_<?php echo $option->price;?>_<?php echo $option->time; ?>_<?php echo $pricevariation->getId();?>">
							<?php echo $option->title; ?> <?php echo $this->html('html.amount', $option->price, $plan->getCurrency()); ?> <?php echo $plan->separator; ?> <?php echo $this->html('html.plantime', PPHelperPlan::convertIntoTimeArray($option->time), ['isRecurring' => $plan->isRecurring()]); ?>
						</option>
					<?php } ?>
				</select>
				<label for="" class="o-select-group__drop"></label>
			</div>
		</div>
	</div>
</div>