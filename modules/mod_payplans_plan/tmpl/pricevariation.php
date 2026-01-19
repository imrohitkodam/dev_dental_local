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
<script type="text/javascript">
	PayPlans.require()
	.done(function($) {
		$('[data-pricevariation-selection]').on('change', function() {
		var wrapper = $(this).parents('[data-plan-footer]');

		// if the pricevariation radio is not check, check it.
		wrapper.find('[data-pricevariation-radio]').prop("checked", true);
		wrapper.find('[data-priceset-selection]').prop("checked", false);

		var subscribeButton = $(this).parents('[data-plan-footer]').find('[data-subscribe-button]');
		var value = $(this).val();

		resetLink(subscribeButton, value);
		});

		$('[data-pricevariation-radio]').on('change', function() {
			$('[data-priceset-selection]').prop("checked", false);

			var subscribeButton = $(this).parents('[data-plan-footer]').find('[data-subscribe-button]');
			var priceVariation = $(this).parents('[data-pricevariation]').find('[data-pricevariation-selection]');

			resetLink(subscribeButton, priceVariation.val());
		});

		var resetLink = function(button, value) {
			
			var defaultLink = button.data('default-link');
			
			if (value.length > 0) {
				var separator = defaultLink.indexOf("?") == -1 ? '?' : '&';
				defaultLink += separator + 'pricevariation=' + value;
			}

			button.attr("href", defaultLink);
		}
	});
</script>
<?php foreach ($plan->priceVariations as $priceVariation) { ?>
	<?php if ($priceVariation->options) { ?>

		<div class="o-form-group">
			<div class="o-radio">
				<?php if ($plan->advancedpricing) { ?>
					<input id="modifier-radio<?php echo $plan->getId(); ?>" type="radio" value="modifier" name="plan-extra<?php echo $plan->getId(); ?>" data-modifier-radio>
					<label for="modifier-radio<?php echo $plan->getId(); ?>">
						<?php echo JText::_('COM_PP_PLAN_SUBSCRIBE_FOR'); ?>
					</label>
				<?php } ?>
			</div>
		</div>

		<div class="o-form-group" data-plan-pricevariation>
			<div class="o-select-group">
				<select name="priceVariation" id="planPriceVariation" class="o-form-control" data-pricevariation-selection data-id="<?php echo $priceVariation->getId(); ?>" >
					<option value="default">
						<?php $currency = $plan->getCurrency();
							  $amount = $plan->getPrice();?>

						<?php echo $plan->getTitle(); ?>
							<?php if (PP::config()->get('show_currency_at') == 'before') { ?>
								<span class="pp-currency"><?php echo $currency;?>&nbsp;</span><span class="pp-amount"><?php echo $amount;?></span>
							<?php } else { ?>
								<span class="pp-amount"><?php echo $amount;?></span>&nbsp;<span class="pp-currency"><?php echo $currency;?></span>
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
					</option>
					<?php foreach ($priceVariation->options as $option) { ?>
						<option value="<?php echo $option->title;?>_<?php echo $option->price;?>_<?php echo $option->time; ?>_<?php echo $priceVariation->getId();?>">

							<?php $currency = $plan->getCurrency();
								  $amount = $option->price;?>
										
							<?php echo $option->title; ?> 
							<?php if (PP::config()->get('show_currency_at') == 'before') { ?>
								<span class="pp-currency"><?php echo $currency;?>&nbsp;</span><span class="pp-amount"><?php echo $amount;?></span>
							<?php } else { ?>
								<span class="pp-amount"><?php echo $amount;?></span>&nbsp;<span class="pp-currency"><?php echo $currency;?></span>
							<?php } ?>

									<?php 	$lifetime = true;
											$count = 0;
											$timer = PPHelperPlan::convertIntoTimeArray($option->time);
											
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
						</option>
					<?php } ?>
				</select>
				<label for="" class="o-select-group__drop"></label>
			</div>
		</div>
	<?php } ?>
<?php } ?>
