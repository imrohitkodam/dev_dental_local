EasySocial
	.require()
	.app('fields/user/easyblog_biography/content')
	.done(function($) {
		$('[data-field-<?php echo $field->id; ?>]').addController('EasySocial.Controller.Field.Easyblog_Biography', {
			required: <?php echo $field->required ? 1 : 0; ?>,

			"editor": {
				getContent: function() {
					<?php if (ES::isJoomla4()) { ?>
						var editorId = '<?php echo ES::editor()->formatId($inputName, $editorName); ?>';

						return Joomla.editors.instances[editorId].getValue();
					<?php } else { ?>
						<?php echo 'return ' . ES::editor()->getContent($editor, $inputName); ?>
					<?php } ?>
				}
			}
		});
	});