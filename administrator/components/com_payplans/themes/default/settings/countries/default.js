PayPlans.ready(function($) {

	<?php if ($this->tmpl == 'component') { ?>
		$('[data-pp-plan-item]').on('click', function(event) {
			event.preventDefault();


			var item = $(this);
			var obj = {
				'id': item.data('id'),
				'title': item.data('title'),
				'permalink': item.data('permalink')
			};

			window.parent['<?php echo $this->app->input('jscallback', '', 'cmd');?>'].apply(null, [obj]);
		});
	<?php } ?>
});
