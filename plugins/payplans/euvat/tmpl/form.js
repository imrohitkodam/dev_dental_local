PayPlans.ready(function($) {

	var euvat = {
		"lock" : false,
		"process" : function (data) {

			if (euvat.lock) {
				return;
			}

			var isTrigger = null;

			if (data != undefined) {
				isTrigger = data.isTrigger;
			}

			var invoiceKey = $('[data-pp-invoice-key]').val();
			var country = $('[data-pp-euvat-country]').val();
			var bizname = $('[data-pp-euvat-businessname]').val();
			var vatno = $('[data-pp-euvat-vatnumber]').val();

			if (isTrigger) {

				var country = data.country;
				var purpose = data.purpose;
				var bizname = data.bizname;
				var vatno = data.vatno;
			}

			$('[data-pp-euvat-message]').html('');
			
			// disable the update button
			$('[data-pp-euvat-update]').attr('disabled', 'disabled');
			$('[data-pp-euvat-update]').prop('disabled', true);

			euvat.lock = true;

			// trigger euvat.processing
			$(window).trigger('euvat.processing');

			PayPlans.ajax('site/controllers/app/trigger', {
				"event": "onPayplansTaxRequest",
				"event_args": {
					"invoice_key": invoiceKey,
					"country": country,
					"purpose": purpose,
					"businessVat": vatno,
					"businessName": bizname
				}
			}).done(function(html, total, err, success){

				// remove all
				$('[data-pp-modifier-discount]').remove();

				// now repopulate with the udpates
				$('[data-pp-modifiers]').prepend(html);
				$('[data-pp-payable-label]').html(total);

				if (err) {
					$('[data-pp-euvat-message]').html(err);

					// trigger showerror
					$(window).trigger('euvat.showerror', err);

				}

				if (success) {
					$('[data-pp-euvat-message').html(success);

					$(window).trigger('euvat.showsuccess', success);
				}

				$('[data-pp-submit]').removeAttr('disabled');
				
				// trigger euvat.complete
				if (success) {
					$(window).trigger('euvat.complete', success);
				} else {
					$(window).trigger('euvat.complete', err);
				}

			}).fail(function(message) {
				// $('[data-pp-euvat-message]').html(message);
			}).always(function() {

				euvat.lock = false;

				// enable the update button
				$('[data-pp-euvat-update]').removeAttr('disabled');
				$('[data-pp-euvat-update]').prop('disabled', false);

				$(window).trigger('euvat.always');

			});

		},
		"validate" : function () {

			$('[data-pp-euvat-message]').html('');

			// check if country selected or not.
			if ($('[data-pp-euvat-country]').val() == '0') {
				var errorMsg = "<?php echo JText::_('COM_PP_APP_EUVAT_PLEASE_SELECT_COUNTRY', true); ?>";

				$('[data-pp-euvat-message]').html(errorMsg);

				// trigger showerror
				$(window).trigger('euvat.showerror', errorMsg);

				// focus on country selection
				$('[data-pp-euvat-country]').focus();

				return false;
			}

			<?php if ($this->config->get('required_company_name')) { ?>
					if ($('[data-pp-euvat-businessname]').val() == '') {

						var errorMsg = "<?php echo JText::_('COM_PP_APP_EUVAT_PLEASE_ENTER_COMPANY_NAME', true); ?>";

						$('[data-pp-euvat-message]').html(errorMsg);

						// trigger showerror
						$(window).trigger('euvat.showerror', errorMsg);

						// focus on country selection
						$('[data-pp-euvat-businessname]').focus();

						return false;
					}

					if ($('[data-pp-euvat-vatnumber]').val() == '') {

						var errorMsg = "<?php echo JText::_('COM_PP_APP_EUVAT_PLEASE_ENTER_VAT', true); ?>";

						$('[data-pp-euvat-message]').html(errorMsg);

						// trigger showerror
						$(window).trigger('euvat.showerror', errorMsg);

						// focus on country selection
						$('[data-pp-euvat-vatnumber]').focus();

						return false;
					}

			<?php } ?>

			return true;
		}
	};

	$('[data-pp-euvat-update]').on('click', function() {
		if (euvat.validate()) {
			euvat.process();
		}
	});

	<?php if ($this->my->id) { ?>
	$('[data-pp-checkout-form]').on('submit', function(ev) {
		if (!euvat.validate()) {
			return false;
		}
	});
	<?php } ?>

	// listening to euvat.always trigger from ajax
	$(window).on('euvat.process', function(event, data) {
		euvat.process(data);
	});

	$(window).on('euvat.updateBizName', function(event, data) {
		$('[data-pp-euvat-businessname]').val(data);
	});


	$(window).on('euvat.updateVatNo', function(event, data) {
		$('[data-pp-euvat-vatnumber]').val(data);
	});

	$(window).on('euvat.updateCountry', function(event, data) {
		$('[data-pp-euvat-country]').val(data);
	});

});
