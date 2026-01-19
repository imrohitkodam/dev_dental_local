PayPlans.ready(function($) {

	var isSubmitting = false;

	var stripePublicKey = '<?php echo $publicKey; ?>';
	var stripe = Stripe(stripePublicKey);

	$('[data-pp-stripe-alipay-submit]').on('click', function() {
		
		if (isSubmitting) {
			return;
		}

		// current button
		var button = $(this);

		// lock the submit button
		isSubmitting = true;

		// show loading button
		button.addClass('is-loading');

		//var elements = $(this);
		var form = $('[data-pp-stripe-alipay-form]');
		var result = $('[data-pp-stripe-result]');
		var paymentIntent = '<?php echo $paymentIntentSecret; ?>';
		var publicKey = '<?php echo $publicKey; ?>';
		var redirectUrl = '<?php echo $returnUrl?>';

		result.html('');
		result.removeClass('o-alert o-alert--danger');

		 // Set the clientSecret of the PaymentIntent
  		stripe.confirmAlipayPayment(paymentIntent, {
  	
  	 	// Return URL where the customer should be redirected to after payment
  	 	return_url: redirectUrl,
  
   	 }).then(function(response) {
			if (response.error) {
				result.html(response.error.message);
				result.addClass('o-alert o-alert--danger t-lg-mb--lg');


				isSubmitting = false;
				button.removeClass('is-loading');

				return;
			}

			form.submit();
		});
	});
});
