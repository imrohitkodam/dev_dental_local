EasySocial.require()
.done(function($) {

var getItem = function(element) {
	var item = $(element).parents('[data-item]');

	return item;
};

var toggleForm = function(row) {
	var form = row.find('[data-form]');

	row.find('[data-value]').toggleClass('t-hidden');
	form.toggleClass('t-hidden');

	if (form.hasClass('t-hidden')) {
		resetError(row);
	}
};

var resetError = function(row) {
	var error = row.find('[data-fitbit-error]');

	error.html('')
		.addClass('t-hidden');
};

var showError = function(row, message) {
	var error = row.find('[data-fitbit-error]');
	error.html(message);

	error.removeClass('t-hidden');
};

var editButton = $('[data-fitbit-edit]');

editButton.on('click', function() {
	var row = getItem(this);
	toggleForm(row);
});

var deleteButton = $('[data-fitbit-delete]');

deleteButton.on('click', function() {
	var row = getItem(this);
	var id = row.data('id');

	var confirmed = confirm('<?php echo JText::_('APP_FITBIT_CONFIRM_DELETE', true);?>');

	if (confirmed) {
		EasySocial.ajax('apps/user/fitbit/controllers/fitbit/delete', {
			"appId": "<?php echo $appId;?>",
			"recordId": id
		}).always(function() {
			row.remove();
		});
	}
});

// Save
var saveButton = $('[data-save-item]');
saveButton.on('click', function() {
	var row = getItem(this);
	var input = row.find('[data-value-input]');
	var value = input.val();

	EasySocial.ajax('apps/user/fitbit/controllers/fitbit/edit', {
		"id": "<?php echo $appId;?>",
		"recordId": row.data('id'),
		"value": value
	}).done(function(newValue) {
		row.find('[data-value]').html(newValue);

		toggleForm(row);
	}).fail(function(message) {
		showError(row, message);
	});

});


var toggler = $('[data-stats-toggler]');

toggler.on('change', function() {
	var checked = $(this).is(':checked');

	EasySocial.ajax('apps/user/fitbit/controllers/fitbit/savePrivacy', {
		"id": "<?php echo $appId;?>",
		"access": checked ? 1 : 0
	}).done(function() {
	});
});

var unlinkFitbit = $('[data-unlink-fitbit]');

unlinkFitbit.on('click', function() {
	var agree = confirm('<?php echo JText::_('APP_FITBIT_CONFIRM_UNLINK');?>');

	if (agree) {
		window.location = '<?php echo $unlinkUrl;?>';
	}
});


$('[data-insert-log]').on('click', function() {
	EasySocial.dialog({
		'content': EasySocial.ajax('apps/user/fitbit/controllers/fitbit/form', {"appId": "<?php echo $appId;?>"})
	});
});

$('[data-purge-logs]').on('click', function() {
	EasySocial.dialog({
		'content': EasySocial.ajax('apps/user/fitbit/controllers/fitbit/purgeConfirmation', {"appId": "<?php echo $appId;?>"})
	});
});

});
