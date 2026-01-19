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
<div class="flex items-center" data-pp-cd-attachment-item 
	data-saved="<?php echo $saved ? 1 : 0 ?>"
	data-type="<?php echo $type; ?>"
	data-container="<?php echo $container ?>"
	data-group="<?php echo $group; ?>"
	data-obj-id="<?php echo $objId; ?>"
>
	<div class="flex-grow" data-pp-cd-attachment-name>
		<?php echo $name; ?>
	</div>
	<div class="flex-shrink-0 <?php echo !$action ? 't-hidden' : ''; ?>" data-pp-cd-attachment-action>
		<?php if ($download) { ?>
		<a href="<?php echo $downloadLink; ?>" target="_blank">
			<?php echo JText::_('COM_PP_CUSTOM_FIELD_FILE_DOWNLOAD_BUTTON'); ?>
		</a>

		&middot;
		<?php } ?>

		<a href="javascript:void(0);" data-pp-cd-attachment-remove>
			<?php echo JText::_('COM_PP_CUSTOM_FIELD_FILE_REMOVE_BUTTON'); ?>
		</a>
	</div>
</div>