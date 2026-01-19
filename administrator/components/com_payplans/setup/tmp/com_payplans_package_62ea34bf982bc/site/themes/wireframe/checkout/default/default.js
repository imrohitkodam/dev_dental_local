PayPlans.require()
.script('site/floatlabels')
.done(function($) {

	// use to keep the addon adding state
	var addonLock = false;

	var registerLink = $('[data-pp-register-link]');
	var loginLink = $('[data-pp-login-link]');
	var accountType = $('[data-pp-account-type]');
	var guest = <?php echo !$this->my->id ? 1 : 0; ?>;
	var isBuiltinRegistrationType = <?php echo $this->config->get('registrationType') == "auto" ? 1 : 0; ?>;
	var errorMsg = '<?php echo JText::_('COM_PP_FIELD_REQUIRED_MESSAGE'); ?>';
	var formatErrorMsg = '<?php echo JText::_('COM_PP_FIELD_INVALID_FORMAT'); ?>';

	// Transaction billing details data attributes
	// These 2 fields for business transaction details
	var transactionPurpose = $('[data-pp-transaction-purpose]');
	var transactionAddress = $('[data-pp-company-address]');
	var transactionCity = $('[data-pp-company-city]');
	var transactionState = $('[data-pp-company-state]');
	var transactionZipCode = $('[data-pp-company-zip]');
	var transactionCountry = $('[data-pp-company-country]');

	var transactionCompanyName = $('[data-pp-company-bizname]');
	var transactionCompanyTaxId = $('[data-pp-company-vatno]');

	// Each of the billing details wrapper fields
	//var transactionPurposeWrapper = transactionPurpose.closest('[data-pp-form-group]');
	var transactionAddressWrapper = transactionAddress.closest('[data-pp-form-group]');
	var transactionCityWrapper = transactionCity.closest('[data-pp-form-group]');
	var transactionStateWrapper = transactionState.closest('[data-pp-form-group]');
	var transactionZipCodeWrapper = transactionZipCode.closest('[data-pp-form-group]');
	var transactionCountryWrapper = transactionCountry.closest('[data-pp-form-group]');

	//var transactionCompanyNameWrapper = transactionCompanyName.closest('[data-pp-form-group]');
	//var transactionCompanyTaxIdWrapper = transactionCompanyTaxId.closest('[data-pp-form-group]');	

	// login fields
	var loginName = $('[data-pp-login-username]');
	var loginPwd = $('[data-pp-login-password]');

	var loginNameWrapper = loginName.closest('[data-pp-form-group]');	
	var loginPwdWrapper = loginPwd.closest('[data-pp-form-group]');	

	<?php if ($accountType == 'register') { ?>
		$('[data-pp-login]').addClass('t-hidden');
		$('[data-pp-register]').removeClass('t-hidden');

		<?php if($this->config->get('registrationType') != 'auto' && (!$this->my->id && !$session->get('REGISTRATION_NEW_USER_ID', 0))) { ?>
			$('[data-pp-submit]').addClass('t-hidden');
		<?php } ?>
	<?php } ?>

	var showError = function(el, message) {
		var elParent = el.closest('[data-pp-form-group]');

		elParent.addClass('has-error');
		elParent.find('[data-error-message]').removeClass('t-hidden');

		var error = message === undefined ? errorMsg : message;

		// Override back the required message as it might be overridden by the validation
		elParent.find('[data-error-message]').find('.text-danger').html(error);
	};

	var hasError = function(el) {
		return el.parent('[data-pp-form-group]').hasClass('has-error');
	};

	var hideError = function(el) {
		el.removeClass('has-error');
		el.find('[data-error-message]').addClass('t-hidden');	
	};

	// fieldWrapper - field wrapper
	// fieldInput - input field wrapper
	// fieldValue - input value
	var validateOnLive = function(fieldWrapper, fieldInput, fieldValue) {

		if (fieldValue == "none" || fieldValue == '' || fieldValue == 0) {
			showError(fieldInput);
		} else {
			hideError(fieldWrapper);
		}
	};

	var validateFields = function() {

		var hasErrorField = false;
		var isRenderRegistrationForm = true;

		// Determine whether the current form is render registration form or not
		if (accountType.val() != 'register') {
			var isRenderRegistrationForm = false;
		}

		if (guest) {

			// if the checkout form processing for the user login via subscribe
			if (!isRenderRegistrationForm) {

				if (!loginName.val()) {
					hasErrorField = true;
					showError(loginName);
				}

				if (!loginPwd.val()) {
					hasErrorField = true;
					showError(loginPwd);
				}
			}

			// Do not submit the form if the necessary fields are empty or has error when they are about to checkout
			if (isRenderRegistrationForm && isBuiltinRegistrationType) {

				var name = $('[data-register-name]');
				var username = $('[data-register-username]');
				var password = $('[data-register-password]');
				var password2 = $('[data-register-password2]');
				var email = $('[data-register-email]');
				var confirm = <?php echo $this->config->get('show_confirmpassword') ? 1 : 0; ?>;
				var showName = <?php echo $this->config->get('show_fullname') ? 1 : 0; ?>;

				if (!name.val() && showName) {
					showError(name);
				}

				<?php if($this->config->get('show_username')) { ?>
					if (!username.val()) {
						showError(username);
					}
				<?php } ?>

				if (!password.val()) {
					showError(password);
				}

				if (!password2.val() && confirm) {
					showError(password2);
				}

				if (!email.val()) {
					showError(email);
				}

				<?php if($this->config->get('show_username')) { ?>
						if ((!name.val() && showName) || !username.val() || !password.val() || !email.val() || (!password2.val() && confirm)) {
							hasErrorField = true;
						}
						
						if ((hasError(name) && showName) || hasError(username) || hasError(email) || hasError(password) || (hasError(password2) && confirm)) {
							hasErrorField = true;
						}
				<?php } else { ?>
						if ((!name.val() && showName) || !password.val() || !email.val() || (!password2.val() && confirm)) {
							hasErrorField = true;
						}
						
						if ((hasError(name) && showName) || hasError(email) || hasError(password) || (hasError(password2) && confirm)) {
							hasErrorField = true;
						}
				<?php } ?>
			}
		}

		// Validate for the billing details input fields if the user 
		// And one of the exception is if the user subscribe using login type, or the plan is free without displaying business details, it shouldn't validate for this
		<?php if ($this->config->get('show_billing_details') && $plan->canShowBillingDetails()) { ?>
			if (isRenderRegistrationForm || !guest) {

				//var isSelectedTransactionPurpose = transactionPurpose.val();
				var hasAddress = transactionAddress.val();
				var hasCity = transactionCity.val();
				var hasState = transactionState.val();
				var hasZipCode = transactionZipCode.val();
				var hasCountry = transactionCountry.val();
				var hasCompanyName = transactionCompanyName.val();

				var isCompanyNameRequired = <?php echo $this->config->get('required_company_name'); ?>;

				if(isCompanyNameRequired && !hasCompanyName){
					hasErrorField = true;
					showError(transactionCompanyName);
				}

				if (!hasAddress) {
					hasErrorField = true;
					showError(transactionAddress);
				}

				if (!hasCity) {
					hasErrorField = true;
					showError(transactionCity);
				}

				if (!hasState) {
					hasErrorField = true;
					showError(transactionState);
				}

				if (!hasZipCode) {
					hasErrorField = true;
					showError(transactionZipCode);
				}

				if (hasCountry == 0) {
					hasErrorField = true;
					showError(transactionCountry);
				}
			}
		<?php } ?>

		if (hasErrorField) {
			return false;
		}

		return true;
	};

	// Auto validate again after user key in
	/*transactionPurpose.on('change', $.debounce(function() {
		var transactionPurposeVal = $(this).val();
		validateOnLive(transactionPurposeWrapper, transactionPurpose, transactionPurposeVal);
	}, 100));*/

	transactionAddress.on('keyup', $.debounce(function() {
		var transactionAddressVal = $(this).val();
		validateOnLive(transactionAddressWrapper, transactionAddress, transactionAddressVal);
	}, 100));

	transactionCity.on('keyup', $.debounce(function() {
		var transactionCityVal = $(this).val();
		validateOnLive(transactionCityWrapper, transactionCity, transactionCityVal);
	}, 100));

	transactionState.on('keyup', $.debounce(function() {
		var transactionStateVal = $(this).val();
		validateOnLive(transactionStateWrapper, transactionState, transactionStateVal);
	}, 100));

	transactionZipCode.on('keyup', $.debounce(function() {
		var transactionZipCodeVal = $(this).val();
		validateOnLive(transactionZipCodeWrapper, transactionZipCode, transactionZipCodeVal);
	}, 100));

	transactionCountry.on('change', $.debounce(function() {
		var transactionCountryVal = $(this).val();
		validateOnLive(transactionCountryWrapper, transactionCountry, transactionCountryVal);
	}, 100));

	/*transactionCompanyName.on('keyup', $.debounce(function() {
		var transactionCompanyNameVal = $(this).val();
		validateOnLive(transactionCompanyNameWrapper, transactionCompanyName, transactionCompanyNameVal);
	}, 100));

	transactionCompanyTaxId.on('keyup', $.debounce(function() {
		var transactionCompanyTaxIdVal = $(this).val();
		validateOnLive(transactionCompanyTaxIdWrapper, transactionCompanyTaxId, transactionCompanyTaxIdVal);
	}, 100));*/

	loginName.on('keyup', $.debounce(function() {
		var loginNameVal = $(this).val();
		validateOnLive(loginNameWrapper, loginName, loginNameVal);
	}, 100));

	loginPwd.on('keyup', $.debounce(function() {
		var loginPwdVal = $(this).val();
		validateOnLive(loginPwdWrapper, loginPwd, loginPwdVal);
	}, 100));

	// Submit button
	$('[data-pp-submit]').on('click', function(event) {
		event.preventDefault();

		// validate all the order confirmation fields
		var isValid = validateFields();

		if (!isValid) {
			return;
		}

		var form = $('[data-pp-checkout-form]');
		var required = false;
		var valid = true;

		form.find('[data-pp-form-group]').removeClass('has-error');
		form.find('[data-error-message]').addClass('t-hidden');

		// Check for required <input>
		form.find(':input').each(function() {
			var el = $(this);
			var value = el.val();

			// If this is a telephone input field
			if (el.prop('type') === 'tel') {
				var pattern = el.prop('pattern');

				if (pattern && value) {
					var regex = new RegExp('^' + pattern + '$');
					var result = regex.test(value);

					if (!result) {
						showError(el, '<?php echo JText::_('COM_PP_FIELD_TELEPHONE_INVALID_PATTERN'); ?>');
					}

					valid = result;
				}
			}

			if (el.prop('required')) {
				// If this is a file input field
				if (el.prop('type') === 'file') {
					var formGroup = el.closest('[data-pp-cd-file-wrapper]');
					var list = formGroup.find('[data-pp-cd-file-list]');
					var attachments = list.find('[data-pp-cd-attachment-item]');

					// The total of the attachments
					value = attachments.length;
				}

				if (!value) {
					showError(el);

					required = true;
				}
			}
		});

		form.find('[data-pp-form-group][data-type="checkbox"]').each(function() {
			const checkboxWrapper = $(this);

			if (checkboxWrapper[0].hasAttribute('required')) {
				let hasChecked = false;

				checkboxWrapper.find('input[type="checkbox"]').each(function() {
					const checkbox = $(this);

					if (checkbox.is(':checked')) {
						hasChecked = true;

						return false;
					}
				});

				if (!hasChecked) {
					showError(checkboxWrapper);

					required = true;
				}
			}
		});

		// Check for required <select>
		form.find('select').each(function() {
			var select = $(this);

			if (select.prop('required')) {
				var value = select.find('option:selected').val();

				if (select[0].hasAttribute('data-country-select')) {
					value = parseInt(value);
				}

				if (!value) {
					showError(select);

					required = true;
				}
			}
		});

		if (required || !valid) {
			return;
		}

		var deferredObjects = [];

		form.trigger('onSubmit', [deferredObjects]);

		// disable once clicked 
		$('[data-pp-submit]').attr('disabled', 'disabled');
		$('#pp').addClass('is-loading');

		if (deferredObjects.length <= 0) {
			form.submit();
			return;
		}

		$.when.apply(null, deferredObjects)
			.done(function() {
				form.submit();
			})
			.fail(function() {
			});
	});

	// Login link
	loginLink.on('click', function() {

		$('[data-pp-register]').addClass('t-hidden');
		$('[data-pp-submit-register]').addClass('t-hidden');

		$('[data-pp-login]').removeClass('t-hidden');
		$('[data-pp-submit]').removeClass('t-hidden');
		$('[data-pp-submit-login]').removeClass('t-hidden');
		$('[data-pp-registration-wrapper]').removeClass('t-hidden');

		accountType.val('login');
	});

	// Registration link
	registerLink.on('click', function() {
		$('[data-pp-login]').addClass('t-hidden');
		$('[data-pp-register]').removeClass('t-hidden');
		
		<?php if($this->config->get('registrationType') != 'auto') { ?>
			$('[data-pp-submit]').addClass('t-hidden');
			$('[data-pp-registration-wrapper]').addClass('t-hidden');
		<?php } else { ?>
			$('[data-pp-submit-login]').addClass('t-hidden');
			$('[data-pp-submit-register]').removeClass('t-hidden');
		<?php } ?>

		accountType.val('register');
	});

	// Handle enter key for coupon code
	var couponInput = $('[data-pp-discount-code]');
	var applyCouponButton = $('[data-pp-discount-apply]');

	couponInput.on('keydown', function(event) {
		if (event.keyCode == 13) {
			event.preventDefault();
			applyCouponButton.click();
		}
	});

	// Apply discount codes
	applyCouponButton.on('click', function() {
		var button = $(this);
		var loader = $('[data-pp-checkout-loader]');
		var discountWrapper = $('[data-pp-discount-wrapper]');
		var discountMessage = $('[data-pp-discount-message]');
		var input = $('[data-pp-discount-code]');
		var coupon = input.val();
		var invoiceKey = $('[data-pp-invoice-key]').val();

		discountWrapper.removeClass('has-error');
		discountMessage.html('');

		PayPlans.ajax('site/views/discounts/check', {
			"code": coupon,
			"invoice_key": invoiceKey
		}).done(function(discount, totalHtml, total, isRecurring) {
			// remove all
			$('[data-pp-modifier-discount]').remove();

			$('[data-pp-modifiers]').prepend(discount);
			$('[data-pp-payable-label]').html(totalHtml);

			var hide = total <= 0.00 && !isRecurring;
			$('[data-pp-payment-form]').toggleClass('t-hidden', hide);

		}).fail(function(message) {
			discountWrapper.addClass('has-error');
			discountMessage.html(message);
		}).always(function() {
			button.removeClass('is-loading');
		});
	});

	// Business preferences
	transactionPurpose.on('change', function() {
		var element = $(this);
		var value = element.val();

		if (value == 'business') {
			$('[data-pp-business]').removeClass('t-hidden');
			return;
		}

		$('[data-pp-business]').addClass('t-hidden');
	});


	// Addons
	$(document).on('click', '[data-addons-item]', function(ev) {
		if (addonLock) {
			// do not let user click
            ev.preventDefault();
			return;
		}

		// to prevent user to click on other items before the current one finish.
		addonLock = true;

		var multiple = <?php echo $this->config->get('addons_select_multiple', 1) ? 1 : 0; ?>;
		var button = $(this);
		var invoiceKey = $('[data-pp-invoice-key]').val();
		var addonId = button.data('id');
		var type = button.data('type');
		var updateType = (type == 'add') ? 'add' : 'remove';

		// Disable other add buttons if multiple addon is not allowed
		if (!multiple && type == 'add') {
			$('[data-addons-add-button]').attr('disabled', true);
		}

		// Always make sure all the add buttons are able to being clicked
		if (!multiple && type == 'remove') {
			$('[data-addons-add-button]').removeAttr('disabled');
		}

		PayPlans.ajax('site/views/addons/updateCharges', {
			"plan_addons": addonId,
			"update_type": updateType,
			"invoice_key": invoiceKey
		}).done(function(html, totalHtml, total, isRecurring) {
			var addButton = $('[data-addons-add-button][data-id=' + addonId + ']');
			var modifiers = $('[data-pp-modifiers]');
			var items = $('[data-pp-modifier-discount]');
			var separator = $('[data-modifiers-separator]');
			var paymentForm = $('[data-pp-payment-form]');

			// The pp-checkout-container__bd wrapper
			var body = $('[data-pp-checkout-body]');

			// remove all
			items.remove();

			// now repopulate with the udpates
			modifiers.prepend(html);
			$('[data-pp-payable-label]').html(totalHtml);

			var hide = total <= 0.00 && !isRecurring;
			
			paymentForm.toggleClass('t-hidden', hide);
			body.find(paymentForm).find('[data-pp-form-group]').toggleClass('t-hidden', hide);
			body.find('[data-pp-no-payment]').toggleClass('t-hidden', !hide);

			if (!hide) {
				// Make sure the body is not hidden
				body.removeClass('t-hidden');
			} 

			// Hide back the body if it is supposed to be hidden from the start
			if (hide && body.data('hide')) {
				body.addClass('t-hidden');
			}

			if (type == 'add') {
				// The added item on the modifier
				var item = modifiers.find('[data-addons-item][data-id=' + addonId + ']');
				var itemWrapper = item.parent().closest('tr');
				var position = item.offset().top;			

				if (separator.hasClass('t-hidden')) {
					separator.removeClass('t-hidden');
				}

				// Let's highlight the itemWrapper after the user added an addon
				itemWrapper.css({
					'background': '#fff9d7',
					'transition': 'background 1.0s ease-in-out'
				});

				// Scroll back to the position of the item to show it to the user
				item[0].scrollIntoView({
					behavior: 'smooth',
					block: 'center'
				});

				addButton.addClass('t-hidden');

				setTimeout(function(){
					itemWrapper.css({
						'background': '#ffffff'
					});
				},3000);
			}

			if (type == 'remove') {
				addButton.removeClass('t-hidden');

				// Get the updated items
				items = $('[data-pp-modifier-discount]');

				// If there is no modifier left, hide the separator back
				if (items.length < 1) {
					separator.addClass('t-hidden');
				}
			}
		}).fail(function(message) {

			PayPlans.dialog({
				"content": message
			});

		}).always(function() {
			addonLock = false;
		});
	});

	<?php if ($accountType == 'register') { ?>
		registerLink.trigger('click');
	<?php } ?>
});
