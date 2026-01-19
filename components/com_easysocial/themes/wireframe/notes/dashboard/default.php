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
<div class="app-notes" data-app-notes data-app-id="<?php echo $app->id; ?>">
	<div class="t-text--right">
		<a class="btn btn-es-primary btn-sm" href="javascript:void(0);" data-notes-create><?php echo JText::_('APP_NOTES_NEW_NOTE_BUTTON'); ?></a>
	</div>

	<div class="app-contents<?php echo !$notes ? ' is-empty' : '';?>" data-app-contents>
		<p class="app-info">
			<?php echo JText::_('APP_USER_NOTES_DASHBOARD_INFO'); ?>
		</p>

		<div class="app-contents-data" data-notes-list>
			<?php if ($notes) { ?>
				<?php foreach ($notes as $note) { ?>
					<?php echo $this->loadTemplate('themes:/site/notes/dashboard/item' , array('app' => $app, 'note' => $note, 'appId' => $app->id , 'user' => $user)); ?>
				<?php } ?>
			<?php } ?>
		</div>

		<?php echo $this->html('html.emptyBlock', 'APP_NOTES_EMPTY_NOTES', 'fa-database'); ?>
	</div>

</div>
