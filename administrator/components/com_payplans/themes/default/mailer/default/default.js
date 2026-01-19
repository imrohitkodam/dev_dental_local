PayPlans.ready(function($) {



$('[data-preview]').on('click', function() {

	var file = $(this).data('preview');
	console.log(file);
	
	PayPlans.dialog({
		"content": PayPlans.ajax('admin/views/mailer/preview', {
			"file": file
		})
	});
});



});