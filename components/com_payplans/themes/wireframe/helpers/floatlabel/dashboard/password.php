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

$label = isset($attributes['required']) ? '*' : '' . $label;
?>
<div class="o-form-group space-y-xs">
	<?php echo $this->fd->html('form.label', $label, $id, '', '', false, ['columns' => 12]); ?>

	<?php echo $this->fd->html('form.password', $name, $value, $id, ['attributes' => $attributes]);?>
</div>

<div class="o-form-helper-text text-xs text-danger t-hidden" data-error-message>
	<i class="fdi fa fa-exclamation-circle"></i> <?php echo JText::_('COM_PP_FIELD_REQUIRED_MESSAGE'); ?>
</div>