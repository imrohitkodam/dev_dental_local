PayPlans.ready(function($) {

	var form = $('[data-payflow-form]');
	var isSubmitting = false;

	$('[data-submit-payment]').on('click', function(event) {
		
		if (isSubmitting) {
			return;
		}

		event.preventDefault();

		// current button
		var button = $(this);

		// lock the submit button
		isSubmitting = true;

		// Ensure that the submit button is disabled
		$('[data-submit-payment]').attr('disabled', 'disabled');

		// show loading button
		button.addClass('is-loading');

		var cardType = $('[data-payflow-card-type]');
		var card = form.find('.card-js');

		cardType.val(card.CardJs('cardType'));
		form.submit();
	});
});