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
<?php if ($conversations) { ?>
	<?php foreach ($conversations as $converse) { ?>
		<?php if ($ck) { ?>
			<a href="javascript:void(0);" class="flex hover:bg-gray-100 px-md py-md hover:no-underline text-gray-800 <?= $converse->isNew() ? 'is-unread' : '';?>" data-ck-chat data-conversation-id="<?php echo $converse->id; ?>">
		<?php } else { ?>
			<a href="<?php echo $converse->getPermalink(); ?>" class="flex hover:bg-gray-100 px-md py-md hover:no-underline text-gray-800 <?= $converse->isNew() ? 'is-unread' : '';?>" data-fd-notification-items>
		<?php } ?>
			<div class="pr-md">
				<?php echo FDT::themes()->html('html.avatar', ['user' => $converse->participant]); ?>
			</div>
			<div class="flex-grow space-y-2xs">
				<div class="font-bold"><?php echo $converse->title; ?></div>

				<div class="text-gray-500 text-xs">
					<?php echo FDT::themes()->output('ajax/conversations/' . $converse->lastMessageType, ['conversation' => $converse, 'my' => $my]); ?>
				</div>
				<div class="text-gray-500 text-xs">
					<?php echo $converse->elaped; ?>
				</div>
			</div>

			<div class="flex-shrink-0">
				
			</div>
		</a>
	<?php } ?>
<?php } ?>

<?php echo $this->fd->html('html.emptyList', 'MOD_SI_TOOLBAR_CONVERSATIONS_NO_CONVERSATIONS_YET', [
	'icon' => 'fdi fa fa fa-envelope', 
	'class' => (!$conversations) ? 'block' : '', 
	'attributes' => 'data-fd-empty'
]); ?>