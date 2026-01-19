<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="o-box t-lg-mb--md" data-notes-item data-id="<?php echo $note->id;?>">
	<div>
		<a href="<?php echo $note->permalink; ?>" class="t-fs--sm t-text--bold t-lg-pl--no t-lg-pull-left">
			<i class="fa fa-file-text-o"></i>&nbsp; <?php echo $note->title;?>
		</a>
		<div class="t-lg-pull-right">
			<ol class="g-list--horizontal has-dividers--right">
				<li class="g-list__item"><?php echo $this->html('string.date', $note->created, JText::_('DATE_FORMAT_LC1')); ?></li>
				<li class="g-list__item">
					<a href="javascript:void(0);" class="btn btn-es-default btn-sm" data-edit><?php echo JText::_('APP_NOTES_EDIT_BUTTON');?></a>
					<a href="javascript:void(0);" class="btn btn-es-danger btn-sm" data-delete><?php echo JText::_('APP_NOTES_DELETE_BUTTON');?></a>
				</li>
			</ol>    
		</div>
	</div>
</div>