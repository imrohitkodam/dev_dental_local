PayPlans.ready(function($) {
	var name = '<?php echo $name; ?>';
	var formGroup = $('[data-pp-form-group][data-name="' + name + '"]');
	var customDetailWrapper = formGroup.closest('[data-pp-cd-file-wrapper]');
	var fileInputWrapper = customDetailWrapper.find('[data-pp-cd-file-form]');
	var fileInputButton = fileInputWrapper.find('[data-pp-file-input]');
	var clonedFileInput = fileInputWrapper.clone();
	var list = customDetailWrapper.find('[data-pp-cd-file-list]');
	var requiredMessage = '<?php echo JText::_('COM_PP_FIELD_REQUIRED_MESSAGE'); ?>';
	var maxSize = <?php echo $this->config->get('custom_details_file_maxsize'); ?>;
	var maxSizeInByte = maxSize * 1024 * 1024;
	var limit = <?php echo $this->config->get('custom_details_file_limit'); ?>;

	var resetAttachmentForm = function(formGroup, cloned) {
		var wrapper = formGroup.closest('[data-pp-cd-file-wrapper]');

		// Reset the data
		var clonedItem = cloned.find('[data-pp-cd-attachment-item]');
		clonedItem.find('[data-pp-cd-attachment-name]').html('');
		clonedItem.find('[data-pp-cd-attachment-action]').addClass('t-hidden');

		cloned.find('[data-pp-file-input]').val('');

		// Add back the class to render the input nicely
		cloned.addClass('o-form-custom-file');

		var list = wrapper.find('[data-pp-cd-file-list]');
		var totalItems = getTotalAttachmentItems(list);

		console.log(formGroup);

		wrapper.find('[data-pp-cd-file-attachment-wrapper]').append(cloned);

		if (limit && totalItems >= limit) {
			cloned.find('[data-pp-cd-attachment-input]').addClass('t-hidden');
			return;
		}

		// Ensure that it is not hidden
		cloned.removeClass('t-hidden');
		cloned.find('[data-pp-cd-attachment-input]').removeClass('t-hidden');
	};

	var getTotalAttachmentItems = function(list) {
		var total = list.find('[data-pp-cd-attachment-item]').length;

		return total;
	};

	// Remove the file input if reached the limit when the document is ready
	if (limit && getTotalAttachmentItems(list) >= limit) {
		fileInputWrapper.addClass('t-hidden');
	}

	var insertAttachment = function(form) {
		var input = form.find("input:not(:hidden)");
		var file = {
			title: input.val(),
			size: input[0].files[0].size
		};

		var formGroup = form.closest('[data-pp-form-group]');
		var errorWrapper = formGroup.find('[data-error-message]');
		var errorContent = errorWrapper.find('[data-error-content]');

		// Always hide the error first
		formGroup.removeClass('has-error');
		errorWrapper.addClass('t-hidden');

		// Clone first
		var cloned = form.clone();

		// Chrome fix
		if (file.title.match(/fakepath/)) {
			file.title = file.title.replace(/C:\\fakepath\\/i, '');
		}

		if (file.size && maxSizeInByte && file.size > maxSizeInByte) {
			form.remove();

			resetAttachmentForm(formGroup, cloned);

			// Show the error message
			var exceedMaxSizeError = '<?php echo JText::_('COM_PP_CUSTOM_DETAILS_FILE_FIELD_MAXSIZE_EXCEEDED'); ?>'.replace('%2s', maxSize);
			exceedMaxSizeError = exceedMaxSizeError.replace('%1s', file.title);

			errorContent.html(exceedMaxSizeError);
			errorWrapper.removeClass('t-hidden');

			formGroup.addClass('has-error');

			return;
		}

		// Set the file title
		var title = form.find('[data-pp-cd-attachment-name]');
		title.html(file.title);

		// Display the actions
		var actions = form.find('[data-pp-cd-attachment-action]');
		actions.removeClass('t-hidden');

		form.find('[data-pp-cd-attachment-input]').addClass('t-hidden');

		// Remove this class to remove additional space and it is not needed after moving to the list
		form.removeClass('o-form-custom-file');

		var list = form.closest('[data-pp-cd-file-wrapper]').find('[data-pp-cd-file-list]');

		// Add it into the list
		form.appendTo(list);

		resetAttachmentForm(formGroup, cloned);

		// Reset back the error message
		errorContent.html(requiredMessage);
	};

	$(document).on('change', fileInputButton.selector, function() {
		var el = $(this);
		var fileForm = el.closest('[data-pp-cd-file-form]');

		// Insert a new item into the list
		insertAttachment(fileForm);
	});

	$(document).on('attachment.after.deleted', function(event, formGroupWrapper) {
		var attachmentForm = formGroupWrapper.find('[data-pp-cd-attachment-input]');
		var formGroup = formGroupWrapper.find('[data-pp-form-group]');
		var fileInputWrapper = formGroupWrapper.find('[data-pp-cd-file-form]');
		var list = formGroupWrapper.find('[data-pp-cd-file-list]');

		if (attachmentForm.length < 1) {
			var totalItems = getTotalAttachmentItems(list);

			if (limit && totalItems >= limit) {
				return;
			}

			var cloned = fileInputWrapper.clone();

			resetAttachmentForm(formGroup, cloned);

			return;
		}

		attachmentForm.removeClass('t-hidden');
		fileInputWrapper.removeClass('t-hidden');
	});
});