EasySocial.ready(function($){

	// Implement active tab
	$.Joomla('submitbutton', function(task) {

		if (task == 'cancel') {
			window.location	= 'index.php?option=com_easysocial&view=groups&layout=categories';
			return false;
		}

		<?php if( $category->id ) { ?>
		var performSave = function(id) {
			var result = [];

			// Prepare data to save fields
			var controller = $('[data-fields-form]').controller();

			result.push(controller.save(task));

			if (result.length > 0) {
				$.when.apply(null, result).done(function() {
					$.Joomla('submitform', [task]);
				});

				return;
			}

			$.Joomla('submitform', [task]);

			return;
		}

		var validateUploadSize = function() {

			var hasError = false;

			$('[data-maxupload-check]').each(function(idx, ele) {
				var maxvalue = $(this).data('maxupload');
				var key = $(this).data('maxupload-key');
				var curvalue = $(this).val();

				if (curvalue > maxvalue) {
					// console.log('invalid value for ' + label);

					hasError = true;
					var errorText = '';

					if (key == 'PHOTOS_MAXSIZE') {
						errorText = $('[data-error-maxsize-photo]').text();
					} else if (key == 'FILES_MAXSIZE') {
						errorText = $('[data-error-maxsize-file]').text();
					} else if (key == 'VIDEOS_MAXSIZE') {
						errorText = $('[data-error-maxsize-video]').text();
					} else if (key == 'PHOTOS_UPLOADER_MAXSIZE') {
						errorText = $('[data-error-maxsize-uploader]').text();
					}

					EasySocial.dialog({
						content: errorText
					});
				}
			});

			if (hasError) {
				return false;
			} else {
				return true;
			}

		}

		if( task == 'applyCategory' || task == 'saveCategory' || task == 'saveCategoryNew' )
		{
			if (validateUploadSize()) {
				performSave(<?php echo $category->id; ?>);
			}

			return false;
		}

		if (task == 'saveCategoryCopy') {
			// Make ajax call to create copy of category
			EasySocial.ajax('admin/controllers/groups/createBlankCategory')
				.done(function(id) {

					// lets update the form element cid value.
					var input = $('input[name="cid"]');
					input.attr('value', id);
					performSave(id);
				});

			return false;
		}

		<?php } ?>

		$.Joomla('submitform', [task]);
	});

	$('[data-category-avatar-remove-button]' ).on( 'click' , function() {
		var id 		= $( this ).data( 'id' ),
			button	= $( this );

		EasySocial.dialog({
			content 	: EasySocial.ajax( 'admin/views/groups/confirmRemoveCategoryAvatar' , { "id" : id }),
			bindings 	:
			{
				"{deleteButton} click" : function()
				{
					EasySocial.ajax( 'admin/controllers/groups/removeCategoryAvatar' ,
					{
						"id" : id
					})
					.done(function()
					{
						$( '[data-category-avatar-image]' ).remove();

						button.remove();

						EasySocial.dialog().close();
					});
				}
			}
		});
	});
});
