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

	<optgroup label="<?php echo JText::_('MOD_SI_TOOLBAR_DEFAULT_EXT_SEARCH');?>">
		<option value="search-default" <?php echo ($value === 'search-default') ? 'selected="selected"': '';?>>
			<b><?php echo JText::_('MOD_SI_TOOLBAR_DEFAULT_EXT_SEARCH');?></b>
		</option>
	</optgroup>

	<optgroup label="<?php echo JText::_('MOD_SI_TOOLBAR_SPECIFIC_EXT_SEARCH');?>">
		<?php foreach ($search as $item) { ?>
		<option value="<?php echo $item->value;?>" <?php echo ($value === $item->value) ? 'selected="selected"': '';?>><?php echo JText::_($item->title);?></option>
		<?php } ?>
	</optgroup>
</select>