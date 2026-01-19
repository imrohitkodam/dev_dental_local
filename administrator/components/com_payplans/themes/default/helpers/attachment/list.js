PayPlans.ready(function($) {
	$(document).on('click', '[data-pp-cd-attachment-remove]', function() {
		var el = $(this);
		var row = el.closest('[data-pp-cd-attachment-item]');
		var saved = parseInt(row.data('saved'));
		var formGroupWrapper = row.closest('[data-pp-cd-file-wrapper]');

		if (!saved) {
			row = el.closest('[data-pp-cd-file-form]');
			row.remove();

			$(document).trigger('attachment.after.deleted', [formGroupWrapper]);

			return;
		}

		var type = row.data('type');
		var group = row.data('group');
		var objId = row.data('obj-id');
		var container = row.data('container');
		var name = row.find('[data-pp-cd-attachment-name]').text().trim();

		PayPlans.dialog({
			"content": PayPlans.ajax('site/views/attachment/confirmDelete', {
			}),
			"bindings": {
				"{submitButton} click": function(el) {
					$(el).addClass('is-loading');

					// Remove the item
					PayPlans.ajax('site/views/attachment/delete', {
						'type': type,
						'group': group,
						'objId': objId,
						'container': container,
						'name': name
					}).done(function() {
						PayPlans.dialog().close();

						row.remove();

						$(document).trigger('attachment.after.deleted', [formGroupWrapper]);
					});
				}
			}
		});
	});
});