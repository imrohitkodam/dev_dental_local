<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access'); 
?>
<div class="o-form-group space-y-sm" data-pp-form-group>
	<div class="o-form-check">
		<input id="<?php echo $id; ?>" class="fd-custom-check is-switch" type="checkbox" data-pp-floatlabel-toggler <?php echo $value ? 'checked="checked"' : '';?> <?php echo $attributes; ?>>
		<label class="o-form-check__text" for="<?php echo $name; ?>"><?php echo $label; ?></label>

		<?php echo $this->fd->html('form.hidden', $name, $value ? 1 : 0, $id, 'data-pp-floatlabel-toggler-input'); ?>
	</div>

	<div class="t-hidden" data-error-message>
		<div class="help-block text-danger"><?php echo JText::_('COM_PP_FIELD_REQUIRED_MESSAGE'); ?></div>
	</div>
</div>
<script>
	PayPlans.ready(function($) {
		$('[data-pp-floatlabel-toggler]').on('change', function() {
			const checked = $(this).is(':checked');

			$(this).closest('[data-pp-form-group]').find('[data-pp-floatlabel-toggler-input]').val(checked ? 1 : 0);
		});
	});
</script>