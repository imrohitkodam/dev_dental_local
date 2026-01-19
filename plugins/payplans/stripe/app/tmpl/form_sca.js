PayPlans.ready(function($) {

	var isSubmitting = false;

	var stripePublicKey = '<?php echo $publicKey; ?>';
	var stripe = Stripe(stripePublicKey);

	// Create an instance of Elements.
	var elements = stripe.elements();
	var cardElement = elements.create('card',{
		style: {
			base: {
				color: '#888',
				lineHeight: '40px',
				fontWeight: 300,
				fontFamily: 'Helvetica Neue',
				fontSize: '1rem',

				'::placeholder': {
					color: '#999',
				},
			},
		}
	});

	

	// Add an instance of the card Element into the `card-element` <div>.
	cardElement.mount('#card-element');

	$('[data-pp-stripe-submit]').on('click', function() {
		
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
		var form = $('[data-pp-stripe-form]');
		var result = $('[data-pp-stripe-result]');
		var paymentIntent = '<?php echo $paymentIntentSecret; ?>';
		var publicKey = '<?php echo $publicKey; ?>';
		var cardholderName = $('#cardholder-name').val();

		var city = $('[data-pp-city]').val();
		var country = $('[data-pp-country]').val();
		var line1 = $('[data-pp-address]').val();
		var postalcode = $('[data-pp-zip]').val();
		var state = $('[data-pp-state]').val();


		result.html('');
		result.removeClass('o-alert o-alert--danger');

		 stripe.confirmCardPayment(paymentIntent, {
    		payment_method: {
      		card: cardElement,
      		billing_details: {name: cardholderName,
      						  address: { city: city,
	      						  country: country,
	      						  line1: line1,
	      						  postal_code: postalcode,
	      						  state: state
      							 }
      						}
    	}

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
