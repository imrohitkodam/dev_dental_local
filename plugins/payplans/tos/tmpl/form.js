PayPlans.ready(function($) {

	// Ensure that the submit button is disabled
	$('[data-pp-submit]').attr('disabled', 'disabled');

	$(document).on('click.tos', '[data-tos-link]', function(event) {
		event.stopPropagation();
		event.preventDefault();

		$('[data-pp-tos-message]').hide();

		var appId = $(this).data('id');
		var checkboxInputWrapper = $('[data-tos-checkbox-' + appId + ']');

		PayPlans.dialog({
			"content": PayPlans.ajax('plugins/tos/show', {"appId" : appId}),
			"bindings": {
				"{closeButton} click": function() {

					checkboxInputWrapper.prop('checked', false);
					validate();
					PayPlans.dialog().close();
				},

				"{submitButton} click": function() {
					checkboxInputWrapper.prop('checked', true);
					validate()
					PayPlans.dialog().close();
				}
			}
		});
	});

	$(document).on('change.tos', '[data-tos-checkbox]', function() {
		// Go through each checkbox and ensure that they are all checked
		validate();
	});

	$('[data-pp-checkout-form]').on('submit', function(ev) {
		// Go through each checkbox and ensure that they are all checked
		if (!validate()) {

			var errorMsg = "<?php echo JText::_('COM_PP_APP_TOS_SELECT_TERMS_AND_CONDITIONS', true); ?>";

			$('[data-pp-tos-message]').html(errorMsg);
			$('[data-pp-tos-message]').show();

			return false;
		}
	});

	function validate() {
		var checked = true;

		$('[data-pp-tos-message]').hide();
		
		$('[data-tos-checkbox]').each(function() {
			var isChecked = $(this).is(':checked');

			if (!isChecked) {
				checked = false;
			}
		});

		if (checked) {
			$('[data-pp-submit]').removeAttr('disabled');
			return true;
		}

		$('[data-pp-submit]').attr('disabled', 'disabled');

		return false;
	}
});