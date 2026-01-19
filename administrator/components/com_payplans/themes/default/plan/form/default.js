PayPlans.ready(function($) {

	$.Joomla('submitbutton', function(task) {

		if (task == 'plan.apply' || task == 'plan.save' || task == 'plan.saveNew') {
			var hasErrors = $('[data-pp-form]').validateForm();

			if (!hasErrors) {
				$.Joomla('submitform', [task]);
				return true;
			}

			return false;
		}

		if (task == 'plan.cancel') {
			window.location = "<?php echo JRoute::_('index.php?option=com_payplans&view=plan', false);?>";
			return;
		}
	});


	// Toggle options between achieving type
	$('[data-recurr-validate]').on('click', function() {

		var id = $('input[name="plan_id"]').val();

		PayPlans.dialog({
			content: PayPlans.ajax('admin/views/plan/recurrencevalidation', {
				'id' : id
			})
		});

	})

	$('[fixed-expiration-type]').on('change', function() {
		var type = $(this).val();
		var value = type == 'fixed';

		$('[data-fixed-expiration-wrapper]').toggleClass('t-hidden', !value);
	});

	$('[data-pp-plan-price]').on('keyup', function() {
		const el = $(this);
		const value = el.val().trim();
		const showBillingToggler = $('[data-pp-plan-show-billing]').parents('[data-fd-toggler="payplans"]').find('[data-fd-toggler-checkbox="payplans"]');

		// Ensure that the show billing setting is disabled first
		showBillingToggler.disabled(true);

		// Enable back the show billing setting on the spot if the user set it for free
		if (value.charAt(0) == 0 || value === '') {
			showBillingToggler.disabled(false);

			return;
		}

		// Always switch back to checked if this is not free
		showBillingToggler.prop('checked', true);
	});

	<?php if (PP::isJoomla4() && $renderEditor) { ?>
    	// Need to move out those editor-xtd button markup to outside in order to prevent the popup modal styling issue
		$("[data-pp-legacy-editor] .joomla-modal").prependTo('body');
	<?php } ?>
});