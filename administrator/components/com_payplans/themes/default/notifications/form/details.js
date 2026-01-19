PayPlans.ready(function($) {

	$('select[data-email-template]').on('change', function() {
		var selected = $(this).val();

		$('[data-custom-content]').toggleClass('t-hidden', selected != 'custom');
		$('[data-email-templates]').toggleClass('t-hidden', selected != 'choose_template');
		$('[data-content-joomlaarticle]').toggleClass('t-hidden', selected != 'choose_joomla_article');
	});
});