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
<select data-language-select class="o-form-control" id="<?php echo $id;?>" name="<?php echo $name;?>" style="min-height: 100px;"' : '';?> <?php echo $attributes; ?> <?php echo $disabled ? 'disabled="disabled"' : ''; ?>>
	
	<option value="0"><?php echo JText::_('JOPTION_USE_DEFAULT'); ?></option>

	<?php foreach ($languages as $language) { ?>
		<?php
			$selected = false;
			if (is_array($value)) {
				$selected = in_array($language['value'], $value);
			} else {
				$selected = $language['value'] == $value;
			}
		?>

		<option value="<?php echo $language['value'];?>"<?php echo $selected ? ' selected="selected"' : ''; ?>>
			<?php echo JText::_($language['text']);?>
		</option>
	<?php } ?>
</select>
<input data-country-hidden type="hidden" value="<?php echo is_array($value) ? implode(',', $value) : $value; ?>">
