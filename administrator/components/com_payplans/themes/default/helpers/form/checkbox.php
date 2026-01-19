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
<?php foreach ($options as $option) { ?>
	<?php echo $this->fd->html('form.checkbox', '', !empty($value) && in_array($option->value, $value), '', $name, $option->title, ['attributes' => 'data-pp-form-checkbox data-value="' . $option->value . '"']); ?>

	<?php echo $this->fd->html('form.hidden', $name, !empty($value) && in_array($option->value, $value) ? $option->value : '', $id, 'data-pp-form-checkbox-input'); ?>
<?php } ?>
<script>
	PayPlans.ready(function($) {
		$('[data-pp-form-checkbox]').on('change', function() {
			const el = $(this);
			const checked = el.is(':checked');
			let value = el.data('value');
			value = checked ? value : '';

			$(this).closest('.o-form-check').next('[data-pp-form-checkbox-input]').val(value);
		});
	});
</script>