PayPlans.require()
.done(function($) {
	// listening to euvat.showerror trigger
	$(window).on('euvat.showerror', function(event, errMsg) {
		$('[data-pp-company-message]').addClass('text-danger');
		$('[data-pp-company-message]').show();
		$('[data-pp-company-message]').html(errMsg);
	});

	$(window).on('euvat.showsuccess', function(event, successMsg) {
		$('[data-pp-company-message]').addClass('text-success');
		$('[data-pp-company-message]').show();
		$('[data-pp-company-message]').html(successMsg);
	});

	$(window).on('euvat.processing', function(event) {
		// show loader
		$('[data-pp-company-loader]').addClass('is-active');

		// show proper message
		$('[data-pp-company-message]').removeClass('text-danger');
		$('[data-pp-company-message]').removeClass('text-success');
		$('[data-pp-company-message]').show();
		$('[data-pp-company-message]').html('<?php echo JText::_('COM_PP_APP_EUVAT_PROCESSIN_TAX', true); ?>');
	});

	// listening to euvat.always trigger from ajax
	$(window).on('euvat.complete', function(event, err) {

		//update message
		if (err == '') {
			$('[data-pp-company-message]').hide();
			$('[data-pp-company-message]').html('');
		}

	});

	// listening to euvat.always trigger from ajax
	$(window).on('euvat.always', function(event) {
		// turn off the loader
		$('[data-pp-company-loader]').removeClass('is-active');
	});

	$('[data-pp-company-bizname]').on('keyup', function() {
		$(window).trigger('euvat.updateBizName', $(this).val());

		delayedCheck();
	});

	$('[data-pp-company-vatno]').on('keyup', $.debounce(function() {
		$(window).trigger('euvat.updateVatNo', $(this).val());

		validateVatNumber();
	}, 400));

	$('[data-pp-company-country]').on('change', function() {
		$(window).trigger('euvat.updateCountry', $(this).val());

		processVAT();
	});

	var delayedCheck = $.debounce(function() {
		processVAT();
	}, 400);


	var validateVatNumber = $.debounce(function() {
		validateVatNumber();
	}, 400);

	var validateVatNumber = function () {

		// lets check if euvat is required or not.
		// check based on the euvat update button
		var updateTaxButton = $('[data-pp-euvat-update]');

		if (updateTaxButton.length == 0) {
			return false;
		}

		if ($('[data-pp-company-vatno]').val() != '') {

			var vatId = $('[data-pp-company-vatno').val();
			
			if (vatId.length >= 2) {

				// check first 2 character are country code in vat id or not
				var isoCode2 = vatId.slice(0, 2);

				var pattern = /^[A-Za-z]+$/;
				if(isoCode2.match(pattern)) {

					PayPlans.ajax('site/views/checkout/getCountryFromVatNo', {
						"isoCode2": isoCode2
					})
					.done(function(countryId) {
						// auto select the country if vat number consist the country isocode
						if (countryId) {
							$('[data-pp-company-country]').val(countryId);
							$('[data-pp-company-country]').trigger('change');
						}
					});
				 }
			}
		} else {
			// clear message if any
			$('[data-pp-company-message]').hide();
			$('[data-pp-company-message]').html('');

			processVAT();
		}

	};

	var processVAT = function () {

		// lets check if euvat is required or not.
		// check based on the euvat update button
		var updateTaxButton = $('[data-pp-euvat-update]');

		if (updateTaxButton.length == 0) {
			return false;
		}

		var run = true;

		if ($('[data-pp-company-country]').val() == '') {
			run = false;
		}

		if (run) {

			// clear message if any
			$('[data-pp-company-message]').hide();
			$('[data-pp-company-message]').html('');

			// purpose id 1 for personal tax , 2 for business tax and 3 for business tax without vat

			var purposeId = 1;

			if ($('[data-pp-company-vatno]').val()) {
				purposeId = 2;
			}

			// If company name if gived and vat no is blank then set the purpose id to 3 (business tax rate without vat)
			if ($('[data-pp-company-bizname]').val() && $('[data-pp-company-vatno]').val() == '') {
				purposeId = 3;
			}

			var data = {
				isTrigger: true,
				country: $('[data-pp-company-country]').val(),
				purpose: purposeId,
				bizname: $('[data-pp-company-bizname]').val(),
				vatno: $('[data-pp-company-vatno]').val()
			}

			$(window).trigger('euvat.process', data);
		}
	};

	// When there is a default country, we need to trigger the change
	var countryValue = parseInt($('[data-pp-company-country]').val());

	if (countryValue) {
		$('[data-pp-company-country]').trigger('change');
	}
});
