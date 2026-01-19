PayPlans.ready(function($) {
	var businessType = '<?php echo PP_EUVAT_PURPOSE_BUSINESS; ?>';
	var personalType = '<?php echo PP_EUVAT_PURPOSE_PERSONAL; ?>';
	var errorMsg = '<?php echo JText::_('COM_PP_FIELD_REQUIRED_MESSAGE'); ?>';
	var formatErrorMsg = '<?php echo JText::_('COM_PP_FIELD_INVALID_FORMAT'); ?>';

	var hasError = function(el) {
		return el.parent('[data-pp-form-group]').find('o-form-group').hasClass('has-error');
	};

	var showError = function(el, message) {
		var elParent = el.closest('[data-pp-form-group]');

		elParent.find('o-form-group').addClass('has-error');
		elParent.find('[data-error-message]').removeClass('t-hidden');

		var error = message === undefined ? errorMsg : message;

		// Override back the required message as it might be overridden by the validation
		elParent.find('.text-danger').html(error);
	};

	var hideError = function(el) {
		el.find('o-form-group').removeClass('has-error');
		el.find('[data-error-message]').addClass('t-hidden');
	};

	<?php if ($this->config->get('discounts_referral')) { ?>
	$('[data-pp-referral-copy]').on('click', function() {
		var temp = $('<input>');
		var code = $('[data-pp-referral-code]').val();

		$('body').append(temp);
		temp.val(code).select();

		document.execCommand('copy');
		temp.remove();
	});
	<?php } ?>

	$('[data-pp-transaction-purpose]').on('change', function() {
		var selected = $(this).val();

		$('[data-userpreference-business]').addClass('t-hidden');

		if (selected == businessType) {
			$('[data-userpreference-business]').removeClass('t-hidden');
		}
	});

	$('[data-preference-submit]').on('click', function() {
		var form = $('[data-preference-form]');
		var required = false;
		var valid = true;

		form.find('o-form-group').removeClass('has-error');
		form.find('[data-error-message]').addClass('t-hidden');

		// Selects all input, textarea, select and button elements.
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

			// Check for required <input>
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

		if (required || !valid) {
			return;
		}

		$('[data-preference-form]').submit();
	});

	$(document).on('click.delete.user', '[data-delete-user]', function() {
		var element = $(this);
		var userId = element.data('user-id');

		PayPlans.dialog({
			"content": PayPlans.ajax('site/views/user/confirmDeleteion', {
				"user_id": userId
			})
		})
	});
});