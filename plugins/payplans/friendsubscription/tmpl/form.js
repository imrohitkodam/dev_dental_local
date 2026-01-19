PayPlans.ready(function($) {

	var titleField = $('[data-pp-form-user-preview]');
	var valueField = $('[data-pp-friend-user-id]');
	var browseButton = $('[data-pp-form-user-browse]');
	var cancelButton = $('[data-pp-form-user-clear]');
	var listOption = $('[data-pp-friend_userlist_option]').val();

	window.selectUser = function(obj) {
		
		titleField.val(obj.title);
		valueField.val(obj.id);

		// Close the dialog when done
		PayPlans.dialog().close();
	};

	cancelButton.on('click', function() {
		valueField.val('');
		titleField.val('');
	});

	browseButton.on('click', function() {
		PayPlans.dialog({
			"content": PayPlans.ajax('plugins/friendsubscription/browse', {"jscallback": "selectUser", "listOption" : listOption})
		});
	});
});
