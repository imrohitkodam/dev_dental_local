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
<?php 
JHtml::_('formbehavior.chosen', '.pp-autocomplete', null);

$esLib = PP::easysocial();
$esExist = 	$esLib->exists();
?>
<select name="<?php echo $name;?>" class="pp-autocomplete o-form-control"  <?php echo $attributes;?>>
	<option value="everyone" <?php echo $value == 'everyone' ? 'selected="selected"' : '';?>><?php echo JText::_('COM_PP_FRIENDSUBSCRIPTION_LIST_EVERYONE');?></option>
	<?php if ($esExist) { ?>
		<option value="friends" <?php echo $value == 'friends' ? 'selected="selected"' : '';?>><?php echo JText::_('COM_PP_FRIENDSUBSCRIPTION_LIST_FRIENDS');?></option>
		<option value="followers" <?php echo $value == 'followers' ? 'selected="selected"' : '';?>><?php echo JText::_('COM_PP_FRIENDSUBSCRIPTION_LIST_FOLLOWERS');?></option>
	<?php } ?>
	
</select>

