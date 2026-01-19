<?php
/**
* @package      StackIdeas
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* StackIdeas Toolbar is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<select id="<?php echo $id;?>" name="<?php echo $name;?>" class="form-select">
	<option value="disabled" <?php echo ($value === 'disabled') ? 'selected="selected"': '';?>>
		<b><?php echo JText::_('MOD_SI_TOOLBAR_DISABLE_TOOLBAR');?></b>
	</option>
	<optgroup label="<?php echo JText::_('MOD_SI_TOOLBAR_DEFAULT_EXT_TOOLBAR');?>">
		<?php foreach ($toolbars as $toolbar) { ?>
		<option value="<?php echo $toolbar->value;?>" <?php echo ($value === $toolbar->value) ? 'selected="selected"': '';?>>
			<b><?php echo JText::_($toolbar->title);?></b>
		</option>
		<?php } ?>
	</optgroup>

	<optgroup label="<?php echo JText::_('MOD_SI_TOOLBAR_JOOMLA_MENU');?>">
		<?php foreach ($menus as $menu) { ?>
		<option value="<?php echo $menu->menutype;?>" <?php echo ($value === $menu->menutype) ? 'selected="selected"': '';?>><?php echo $menu->title;?></option>
		<?php } ?>
	</optgroup>
</select>
