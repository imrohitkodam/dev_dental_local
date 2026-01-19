PayPlans.ready(function($) {

	$.Joomla('submitbutton', function(task) {
		if (task == 'add') {
			window.location = '<?php echo JURI::root();?>administrator/index.php?option=com_payplans&view=notifications&layout=create';
			return;
		}

		$.Joomla('submitform', [task]);
	});


	$('[data-pp-preview]').on('click', function() {
		var id = $(this).data("id");

		PayPlans.dialog({
			"content": PayPlans.ajax('admin/views/notifications/preview', {
				"id": id
			})
		});
	});
});