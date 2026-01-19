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
<div class="o-form-group space-y-sm" data-pp-form-group <?php echo $attributes; ?>>
	<div class="o-form-label"><?php echo $label; ?></div>

	<?php foreach ($options as $option) { ?>
		<?php echo $this->fd->html('form.radio', $name, $value === $option->value, $option->value, $id . '-' . $option->value, $option->title); ?>
	<?php } ?>

	<div class="t-hidden" data-error-message>
		<div class="text-danger"><?php echo JText::_('COM_PP_FIELD_REQUIRED_MESSAGE'); ?></div>
	</div>
</div>