PayPlans.ready(function($) {

	$.Joomla('submitbutton', function(task) {

		if (task == 'group.cancel') {
			window.location = "<?php echo JRoute::_('index.php?option=com_payplans&view=group', false);?>";
			return;
		}

		$.Joomla('submitform', [task]);
	});

	<?php if (PP::isJoomla4() && $renderEditor) { ?>
    	// Need to move out those editor-xtd button markup to outside in order to prevent the popup modal styling issue
		$("[data-pp-legacy-editor] .joomla-modal").prependTo('body');
	<?php } ?>		
});