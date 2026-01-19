PayPlans.ready(function($) {
	$.Joomla('submitbutton', function(task) {

		if (task == 'add') {
			window.location = '<?php echo rtrim(JURI::root(), '/');?>/administrator/index.php?option=com_payplans&view=planpricevariations&layout=form';
			return;
		}

		if (task == 'planpricevariations.cancel') {
			window.location = "<?php echo JRoute::_('index.php?option=com_payplans&view=planpricevariations', false);?>";
			return;
		}

		$.Joomla('submitform', [task]);
	});
});
