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
	$('[data-number-of-purchase]').on('click', function() {
		$('[data-pp-advancepricing-message]').html('');

		var unit = $('[data-number-of-unit]').val();
		var minLimit = $('[data-unit-min-limit]').val();
		var maxLimit = $('[data-unit-max-limit]').val();

		if (unit < minLimit || unit > maxLimit) {
			$('[data-pp-advancepricing-message]').html('<?php echo JText::_("COM_PAYPLANS_APP_ADVANCED_PRICING_RANGE_NOT_AVAILABLE", true); ?>');
			return false;
		}



		var wrapper = $(this).parents('[data-plan-footer]');

		// Reset the priceset selection
		wrapper.find('[data-priceset-selection]').prop("checked", false);

		var n = $('[data-number-of-unit]').val(),
			pricePerDay = wrapper.find('[data-plan-advancedpricing]').data('price-perday'),
			rows = $('[data-price-set]');

		wrapper.find('[data-price-set]').each(function(i, el) {
			var days = $(el).data('days'),
				pricePerUnit = $(el).data('price');

			var actualPrice = (pricePerDay * days) * n;
			$(el).find('[data-actual-price] .pp-amount').html(actualPrice.toFixed(2));

			var priceToPay = pricePerUnit * n;
			$(el).find('[data-price-topay] .pp-amount').html(priceToPay.toFixed(2));

			var savings = actualPrice - priceToPay;
			$(el).find('[data-savings] .pp-amount').html(savings.toFixed(2));

			$(el).find('[data-priceset-selection]').val(priceToPay);
		});
	});

	$('[data-priceset-selection]').on('change', function() {
		var wrapper = $(this).parents('[data-plan-footer]'),
			price = $(this).val(),
			duration = $(this).data('duration'),
			unit = wrapper.find('[ data-number-of-unit]').val(),
			subscribeButton = wrapper.find('[data-subscribe-button]'),
			value = unit + '_' + price + '_' + duration;

		// if the advance pricing radio is not check, check it.
		wrapper.find('[data-advancedpricing-radio]').prop("checked", true);

		resetLink(subscribeButton, value);
	});

	$('[data-advancedpricing-radio]').on('change', function() {
		var subscribeButton = $(this).parents('[data-plan-footer]').find('[data-subscribe-button]');
		resetLink(subscribeButton, '');
	});

	var resetLink = function(button, value) {
		
		var defaultLink = button.data('default-link');
		
		if (value.length > 0) {
			var separator = defaultLink.indexOf("?") == -1 ? '?' : '&';
			defaultLink += separator + 'advpricing=' + value;
		}

		button.attr("href", defaultLink);
	}
	});
</script>

<div class="o-form-group" data-plan-advancedpricing data-price-perday="<?php echo $plan->getPricePerDay(false); ?>">
	<div class="o-radio">
		<?php if ($plan->advancedpricing) { ?>
			<input id="advancedpricing<?php echo $plan->getId(); ?>" type="radio" value="advancedpricing" name="plan-extra<?php echo $plan->getId(); ?>" data-advancedpricing-radio>
			<label for="advancedpricing<?php echo $plan->getId(); ?>">
				<?php echo JText::_('COM_PP_PLAN_SUBSCRIBE_FOR'); ?>
			</label>
		<?php } ?>
	</div>
</div>

<div class="o-form-group">
	<label class="o-control-label" for="pp-mod-advancedpricing-field"><?php echo $plan->advancedpricing->units_title; ?></label>
	<div class="o-input-group">
		<input name="total" id="pp-mod-advancedpricing-field" type="int" class="o-form-control" value="" min="<?php echo $plan->advancedpricing->units_min;?>" max="<?php echo $plan->advancedpricing->units_max; ?>" data-number-of-unit/>

		<span class="o-input-group__append">

		<button class="btn btn-pp-default-o" type="button" data-number-of-purchase>
			<?php echo JText::_('COM_PAYPLANS_APP_ADVANCED_PRICING_CALCULATE_PRICE');?>
		</button>
	</div>
	<input name="unit_min" type="hidden" value="<?php echo $plan->advancedpricing->units_min;?>" data-unit-min-limit>
	<input name="unit_max" type="hidden" value="<?php echo $plan->advancedpricing->units_max;?>" data-unit-max-limit>

	<div class="t-text--center t-lg-mt--xl">
		<div class="t-text--danger" data-pp-advancepricing-message></div>
	</div>

	<div class="t-text--center t-lg-mt--xl">
		<span>
			<?php echo JText::_('COM_PAYPLANS_APP_ADVANCED_PRICING_AVAILABLE_RANGE');?> (<?php echo $plan->advancedpricing->units_min .'-'. $plan->advancedpricing->units_max;?>) 
		</span>
	</div>
</div>
<div class="">
	<table class="table table--bordered-horizontal t-bg--default">
		<thead>
			<tr class="t-bg--shade">
				<th>
				</th>
				<th>
					<?php echo JText::_('COM_PP_PLAN_TIME'); ?>
				</th>
				<th>
					<?php echo JText::_('COM_PP_PLAN_PRICE_UNIT'); ?>
				</th>
				<th>
					<?php echo JText::_('COM_PP_PLAN_ACTUAL_PRICE'); ?>
				</th>
				<th>
					<?php echo JText::_('COM_PP_PLAN_PRICE_TO_PAY'); ?>
				</th>
				<th>
					<?php echo JText::_('COM_PP_PLAN_SAVINGS'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($plan->advancedpricing->priceset as $set) { ?>
				<?php $actualPrice = $plan->getPricePerDay(false) * PPHelperPlan::convertAdvancePricingTimeArrayToDays($set['duration']);?>
				<?php $priceToPay = $set['price'] * 1; ?>
				<?php $savings = $actualPrice - $priceToPay; ?>

				<tr data-price-set data-price="<?php echo $set['price']; ?>" data-days="<?php echo PPHelperPlan::convertAdvancePricingTimeArrayToDays($set['duration']); ?>">
					<td>
						<input id="priceset-select" type="radio" name="priceset" value="<?php echo $priceToPay ?>" data-duration="<?php echo $set['duration']; ?>" data-priceset-selection />
					</td>
					<td>
						<?php echo PP::string()->formatTimer($set['duration']); ?>
					</td>
					<td>
						<?php $currency = $plan->getCurrency();?>
						
						<?php if (PP::config()->get('show_currency_at') == 'before') { ?>
							<span class="pp-currency"><?php echo $currency;?>&nbsp;</span><span class="pp-amount"><?php echo $set['price'];?></span>
						<?php } else { ?>
							<span class="pp-amount"><?php echo $set['price'];?></span>&nbsp;<span class="pp-currency"><?php echo $currency;?></span>
						<?php } ?>

					</td>

					<td data-actual-price>
						<?php $currency = $plan->getCurrency(); ?>
						
						<?php if (PP::config()->get('show_currency_at') == 'before') { ?>
							<span class="pp-currency"><?php echo $currency;?>&nbsp;</span><span class="pp-amount"><?php echo $actualPrice;?></span>
						<?php } else { ?>
							<span class="pp-amount"><?php echo $actualPrice;?></span>&nbsp;<span class="pp-currency"><?php echo $currency;?></span>
						<?php } ?>
					</td>

					<td data-price-topay>
						<?php $currency = $plan->getCurrency(); ?>
						
						<?php if (PP::config()->get('show_currency_at') == 'before') { ?>
							<span class="pp-currency"><?php echo $currency;?>&nbsp;</span><span class="pp-amount"><?php echo $priceToPay;?></span>
						<?php } else { ?>
							<span class="pp-amount"><?php echo $priceToPay;?></span>&nbsp;<span class="pp-currency"><?php echo $currency;?></span>
						<?php } ?>
					</td>

					<td data-savings>
						<?php $currency = $plan->getCurrency(); ?>
						
						<?php if (PP::config()->get('show_currency_at') == 'before') { ?>
							<span class="pp-currency"><?php echo $currency;?>&nbsp;</span><span class="pp-amount"><?php echo $savings;?></span>
						<?php } else { ?>
							<span class="pp-amount"><?php echo $savings;?></span>&nbsp;<span class="pp-currency"><?php echo $currency;?></span>
						<?php } ?>
					</td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
</div>



