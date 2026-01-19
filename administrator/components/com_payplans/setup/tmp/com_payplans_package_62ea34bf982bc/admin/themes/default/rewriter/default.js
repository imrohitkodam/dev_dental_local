PayPlans.ready(function($) {
	
	var clipboardMsg = $('[data-clipboard-message]');

	$('[data-copy-clipboard]').on('click', function(element) {
		var temp = PayPlans.$('<input>');
		var button = $(this);
		var value = button.data('value');

		PayPlans.$('body').append(temp);
		temp.val(value).select();

		document.execCommand('copy');
		temp.remove();

		clipboardMsg.show();

		clipboardMsg
			.delay(500)
			.fadeOut('slow');
	});
});