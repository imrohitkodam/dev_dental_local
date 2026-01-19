PayPlans.ready(function($) {
	var name = $('[data-register-name]');
	var username = $('[data-register-username]');
	var password = $('[data-register-password]');
	var password2 = $('[data-register-password2]');
	var email = $('[data-register-email]');
	var submitButton = $('[data-pp-submit]');

	var nameWrapper =  name.parent('[data-pp-form-group]');
	var usernameWrapper =  username.parent('[data-pp-form-group]');
	var pwdWrapper =  password.parent('[data-pp-form-group]');
	var pwd2Wrapper =  password2.parent('[data-pp-form-group]');
	var emailWrapper =  email.parent('[data-pp-form-group]');
	var confirm = <?php echo $this->config->get('show_confirmpassword') ? 1 : 0; ?>;

	var showError = function(element, message) {
		element.addClass('has-error');
		element.find('[data-error-message]').removeClass('t-hidden');
		element.find('.text-danger').html(message);
	};

	var hideError = function(element) {
		element.removeClass('has-error');
		element.find('[data-error-message]').addClass('t-hidden');
	};

	// Joomla strong password validation
	var joomlaPasswordValidation = function() {

		PayPlans.ajax('site/views/checkout/validatePassword', {
		"password": password.val()
		}).done(function(isValid, message) {
			if (!isValid) {
				pwdWrapper.addClass('has-error');

				// Hide the password field error message manually just in case the required message of it is shown
				pwdWrapper.find('[data-error-message]').addClass('t-hidden');
				showError(pwdWrapper, message);

				return false;
			}
		});

		hideError(pwdWrapper);
		hideError(pwd2Wrapper);
		return true;
	}

	var validatePassword = function() {

		<?php if ($this->config->get('joomla_password_validation')) { ?>
			var joomlaPasswordValidate =  joomlaPasswordValidation();

			if (!joomlaPasswordValidate) {
				return;
			}
		<?php } ?>

		if (!confirm) {
			return;
		}

		if (!password.val() || !password2.val()) {
			return;
		}

		var message = '<?php echo JText::_('COM_PP_PASSWORD_DOES_NOT_MATCH'); ?>';

		if (password.val() != password2.val()) {
			pwdWrapper.addClass('has-error');

			// Hide the password field error message manually just in case the required message of it is shown
			pwdWrapper.find('[data-error-message]').addClass('t-hidden');

			// Show the error message on the pwd2 field only
			showError(pwd2Wrapper, message);

			return;
		}

		hideError(pwdWrapper);
		hideError(pwd2Wrapper);
	};

	name.on('keyup', $.debounce(function() {
		hideError(nameWrapper);
	}, 100));

	password.on('keyup', $.debounce(function() {
		validatePassword();
	}, 100));

	password2.on('keyup', $.debounce(function() {
		validatePassword();
	}, 100));

	email.on('keyup', $.debounce(function() {
		const el = $(this);
		const value = el.val();

		if (!value) {
			return;
		}

		PayPlans.ajax('site/views/checkout/validateEmail', {
			"email": value
		}).done(function(isValid, message) {
			if (!isValid) {
				showError(emailWrapper, message);

				return;
			}

			hideError(emailWrapper);
		});
	}, 600));

	username.on('keyup', $.debounce(function() {
		PayPlans.ajax('site/views/checkout/validateUsername', {
			"username": $(this).val()
		}).done(function(isValid, message) {
			if (!isValid) {
				showError(usernameWrapper, message);

				return;
			}

			hideError(usernameWrapper);
		});
	}, 600));
});