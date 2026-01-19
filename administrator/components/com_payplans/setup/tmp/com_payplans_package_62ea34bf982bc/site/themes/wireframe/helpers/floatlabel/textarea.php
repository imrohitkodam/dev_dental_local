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
<div class="o-form-group o-form-group--ifta" data-pp-form-group>
	<?php echo $this->html('form.textarea', $name, $value, '', $attributes); ?>

	<label for="<?php echo $id;?>" class="o-form-label"><?php echo isset($attributes['required']) ? '*' : ''; ?><?php echo $label;?></label>

	<div class="t-hidden" data-error-message>
		<div class="help-block text-danger"><?php echo JText::_('COM_PP_FIELD_REQUIRED_MESSAGE'); ?></div>
	</div>
</div>