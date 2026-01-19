EasySocial.require()
.script('admin/fields/editor')
.done(function($){

	$('[data-fields-form]').addController('EasySocial.Controller.Fields', {
		group: '<?php echo $fieldGroup; ?>'
	});

});
