EasySocial.require()
.script('admin/discovery/discovery')
.done(function($){

	// Implement discover controller.
	$('[data-access-discover]').implement(EasySocial.Controller.Admin.Discovery, {
		"namespaces": {
			"discover": "admin/controllers/access/scanFiles",
			"install": "admin/controllers/access/installFile"
		},
		"messages" : {
			"completed": "<?php echo JText::_('COM_EASYSOCIAL_SCAN_COMPLETED', true);?>"
		}
	});
	
});