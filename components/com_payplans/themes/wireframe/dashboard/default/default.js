PayPlans.ready(function($) {

	$(document).on('click.cancel.subs', '[data-cancel-subscription]', function() {
		var element = $(this);
		var key = element.data('key');

		PayPlans.dialog({
			"content": PayPlans.ajax('site/views/order/confirmCancellation', {
				"order_key": key
			})
		})
	});

	$(document).on('click.delete.subs', '[data-delete-subscription]', function() {
		var element = $(this);
		var key = element.data('key');

		PayPlans.dialog({
			"content": PayPlans.ajax('site/views/order/confirmDeleteion', {
				"order_key": key
			})
		})
	});


	$(document).on('click.upgrade.subs', '[data-upgrade-button]', function() {
		var element = $(this);
		var key = element.data('key');

		PayPlans.dialog({
			content: PayPlans.ajax('site/views/order/confirmUpgrade', {
				'key' : key
			}),
			bindings: {
				"{priceVariationSelection} change": function(element, event) {
					var selectedPlan = this.planSelection().val();
					var selectedPricevariation = this.priceVariationSelection().val();

					// we need to recalculate the un-utilized amount.
					PayPlans.ajax('site/views/order/calculatePriceVariation', {
						"key" : key,
						"id" : selectedPlan,
						"priceVariation": selectedPricevariation
					}).done(function(item) {

						var unutilizedAmount = $('[data-ununtilized-amount-label]');
						var unutilizedTax = $('[data-ununtilized-tax-label]');

						// Always reset to show first
						unutilizedAmount.removeClass('t-hidden');
						unutilizedTax.removeClass('t-hidden');

						// show the pricing accordingly.
						$('[data-upgrade-amount]').html(item.price);
						$('[data-ununtilized-amount]').html(item.unutilized);
						$('[data-ununtilized-tax]').html(item.unutilizedTax);
						$('[data-discounts]').html(item.discounts);
						$('[data-taxes]').html(item.taxes);
						$('[data-payable-amount]').html('<b>' + item.payableAmount + '</b>');

						var isActivateProration = item.isActivateProration;

						// Do not show these unutilized amount during upgrade form if deactivate the upgrade proration
						if (!isActivateProration) {
							unutilizedAmount.addClass('t-hidden');
							unutilizedTax.addClass('t-hidden');
						}
					});
				},

				'{planSelection} change' : function(element, e) {

					var selectedPlan = $(element).val();
					var priceVariationPlaceholder = this.priceVariationPlaceholder();
					var priceVariation = this.priceVariation();

					// we need to recalculate the un-utilized amount.
					PayPlans.ajax('site/views/order/showUpgradeDetails', {
						"key" : key,
						"id" : selectedPlan
					})
					.done(function(item) {

						var unutilizedAmount = $('[data-ununtilized-amount-label]');
						var unutilizedTax = $('[data-ununtilized-tax-label]');

						// Always reset to show first
						unutilizedAmount.removeClass('t-hidden');
						unutilizedTax.removeClass('t-hidden');

						// show the pricing accordingly.
						$('[data-upgrade-amount]').html(item.price);
						$('[data-ununtilized-amount]').html(item.unutilized);
						$('[data-ununtilized-tax]').html(item.unutilizedTax);
						$('[data-discounts]').html(item.discounts);
						$('[data-taxes]').html(item.taxes);
						$('[data-payable-amount]').html('<b>' + item.payableAmount + '</b>');

						var isActivateProration = item.isActivateProration;
						
						// Do not show these unutilized amount during upgrade form if deactivate the upgrade proration
						if (!isActivateProration) {
							unutilizedAmount.addClass('t-hidden');
							unutilizedTax.addClass('t-hidden');
						}

						if (item.planpricevariation) {
							priceVariationPlaceholder.removeClass('t-hidden');							
							priceVariation.html(item.planpricevariation);
							return;
						}

						// Always reset the placeholder when there's nothing to load
						priceVariation.html('');
						priceVariationPlaceholder.addClass('t-hidden');
					});
				}
			}
		});

	});

});
