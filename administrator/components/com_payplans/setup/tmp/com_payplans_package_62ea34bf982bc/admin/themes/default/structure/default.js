PayPlans.ready(function($) {
	<?php if (FH::isJoomla4()) { ?>
	$('[data-fd-structure]')
		.removeClass('is-loading')
		.addClass('is-done-loading');

	$('[data-fd-body]').removeClass('t-hidden');
	<?php } ?>

	$(document).on('click.close.button', '[data-pp-close-toolbarnotice]', function(event) {
		PayPlans.ajax('admin/controllers/system/dismissOutdatedOverrideNotice');

		$('[data-pp-toolbar-notice]').addClass('t-hidden');
	});
});