PayPlans.ready(function($) {

	$(document).on('click.close.button', '[data-pp-close-toolbarnotice]', function(event) {
		PayPlans.ajax('admin/controllers/system/dismissToolbarNotice');

		$('[data-pp-toolbar-notice]').addClass('t-hidden');
	});

	<?php if (FH::isJoomla4()) { ?>
	$('[data-fd-structure]')
		.removeClass('is-loading')
		.addClass('is-done-loading');

	$('[data-fd-body]').removeClass('t-hidden');
	<?php } ?>

	$(document).on('click.close.button', '[data-pp-close-overridenotice]', function(event) {
		PayPlans.ajax('admin/controllers/system/dismissOutdatedOverrideNotice');

		$('[data-pp-override-notice]').addClass('t-hidden');
	});
});