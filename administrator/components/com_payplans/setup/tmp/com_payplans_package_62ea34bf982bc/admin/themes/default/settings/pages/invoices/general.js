PayPlans.ready(function($) {

	$('[data-remove-image]').on('click', function() {

		$.Joomla('submitform', ['config.removeLogo']);
	});

	$('[data-pp-invoice-source]').on('change', function() {
		var selected = $(this).val();

		$('[data-pp-invoice-setting]').toggleClass('t-hidden', selected);
	});		
});