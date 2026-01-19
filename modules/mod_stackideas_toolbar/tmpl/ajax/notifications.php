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
<?php if ($items) { ?>
	<?php foreach ($items as $item) { ?>
		<a href="<?php echo $item->permalink; ?>" class="flex hover:bg-gray-100 px-md py-md hover:no-underline text-gray-800 type-<?php echo $item->type;?> is-unread" data-fd-notification-items>
			<div class="pr-md">
				<?php echo FDT::themes()->html('html.avatar', ['user' => $item->user]); ?>
			</div>
			<div class="flex-grow space-y-2xs">
				<?php echo $item->title; ?>
				<div class="text-gray-500">
					<?php if ($item->content) { ?>
						<div>
							"<?php echo $item->content; ?>"
						</div>
					<?php } ?>
				</div>
				<div class="text-gray-500 text-xs">
					<?php echo $item->lapsed; ?>
				</div>
			</div>

			<?php if ($item->image) { ?>
			<div class="flex-shrink-0">
				<div class="o-aspect-ratio min-w-[64px] rounded-md overflow-hidden" style="--aspect-ratio: 1/1;">
					<img src="<?php echo $item->image; ?>" alt="">
				</div>
			</div>
			<?php } ?>
		</a>
	<?php } ?>
<?php } ?>

<?php echo $this->fd->html('html.emptyList', 'MOD_SI_TOOLBAR_NO_NEW_NOTIFICATIONS_YET', [
	'icon' => 'fdi fa fa-bell', 
	'class' => (!$items) ? 'block' : '', 
	'attributes' => 'data-fd-empty'
]); ?>