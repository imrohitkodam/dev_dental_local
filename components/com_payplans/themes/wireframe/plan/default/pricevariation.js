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