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
<?php if ($showFriendRequests) { ?>
<div class="fd-toolbar__o-nav-item">
	<a href="javascript:void(0);" class="fd-toolbar__link no-active-state <?php echo ($newFriendRequests > 0) ? 'has-new' : ''; ?>"
		data-fd-tooltip="toolbar"
		data-fd-tooltip-title="<?php echo JText::_('MOD_SI_TOOLBAR_FRIEND_REQUESTS'); ?>"
		data-fd-tooltip-placement="top" 

		data-fd-dropdown="toolbar"
		data-fd-dropdown-offset="[0, 0]"
		data-fd-dropdown-trigger="click"
		data-fd-dropdown-placement="bottom"
		data-fd-dropdown-content="action/friends"
		data-module-id="<?php echo $moduleId; ?>"
		data-module-currentview="<?php echo $currentView; ?>"
		>
		<i class="fdi fa fa-user-friends"></i>
		<span class="fd-toolbar__link-bubble" data-fd-notifications-counter><?php echo $newFriendRequests;?></span>
	</a>
	<div class="t-hidden">
		<div id="fd" class="">
			<div class="<?php echo FDT::getAppearance();?> <?php echo FDT::getAccent();?>">
				<div class="o-dropdown divide-y divide-gray-200 md:w-[400px] " data-fd-dropdown-wrapper>
					<div class="o-dropdown__hd px-md py-sm">
						<div class="flex">
							<div class="flex-grow font-bold text-sm text-gray-800">
								<?php echo JText::_('MOD_SI_TOOLBAR_FRIEND_REQUESTS'); ?>
							</div>
						</div>
					</div>

					<div class="o-dropdown__bd xpy-sm xpx-xs overflow-y-auto max-h-[380px] divide-y divide-gray-200 space-y-smx" data-fd-dropdown-body data-fd-toolbar-dropdown-menus>
						<div class="px-sm py-sm hover:no-underline text-gray-800">
							<?php echo $this->fd->html('placeholder.standard', FDT::config()->get('avatar_style', 'rounded')); ?>
						</div>
					</div>

					<div class="o-dropdown__ft px-md py-sm">
						<div class="text-center">
							<a href="<?php echo $viewAllFriendRequestLink;?>" class="fd-link"><?php echo JText::_('MOD_SI_TOOLBAR_VIEW_ALL');?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php } ?>

<?php if ($showConversations) { ?>
<div class="fd-toolbar__o-nav-item">
	<a href="javascript:void(0);" class="fd-toolbar__link no-active-state <?php echo ($newConversations > 0) ? 'has-new' : ''; ?>"
		data-fd-tooltip="toolbar"
		data-fd-tooltip-title="<?php echo JText::_('MOD_SI_TOOLBAR_CONVERSATIONS'); ?>"
		data-fd-tooltip-placement="top" 

		data-fd-dropdown="toolbar"
		data-fd-dropdown-offset="[0, 0]"
		data-fd-dropdown-trigger="click"
		data-fd-dropdown-placement="bottom" 
		data-fd-dropdown-content="action/conversations"
		data-module-id="<?php echo $moduleId; ?>"
		data-module-currentview="<?php echo $currentView; ?>"
		>
		<i class="fdi fa fa-comment-alt"></i>
		<span class="fd-toolbar__link-bubble" data-fd-notifications-counter><?php echo $newConversations;?></span>
	</a>
	<div class="t-hidden">
		<div id="fd" class="">
			<div class="<?php echo FDT::getAppearance();?> <?php echo FDT::getAccent();?>">
				<div class="o-dropdown divide-y divide-gray-200  md:w-[400px]" data-fd-dropdown-wrapper>
					<div class="o-dropdown__hd px-md py-sm">
						<div class="flex">
							<div class="flex-grow font-bold text-sm text-gray-800">
								<?php echo JText::_('MOD_SI_TOOLBAR_CONVERSATIONS'); ?>
							</div>

							<?php if ($canCreateConversations) { ?>
							<div>
								<a href="<?php echo $createConversationLink;?>" class="fd-link">
									<?php echo JText::_('MOD_SI_TOOLBAR_CONVERSATIONS_COMPOSE'); ?>
								</a>
							</div>
							<?php } ?>
						</div>
					</div>

					<div class="o-dropdown__bd overflow-y-auto max-h-[380px] divide-y divide-gray-200" data-fd-dropdown-body data-fd-toolbar-dropdown-menus>
						<div class="px-sm py-sm hover:no-underline text-gray-800">
							<?php echo $this->fd->html('placeholder.standard', FDT::config()->get('avatar_style', 'rounded')); ?>
						</div>
					</div>

					<div class="o-dropdown__ft px-md py-sm">
						<div class="text-center">
							<a href="<?php echo $viewAllConversationsLink; ?>" class="fd-link"><?php echo JText::_('MOD_SI_TOOLBAR_VIEW_ALL'); ?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php } ?>

<?php if ($showNotifications) { ?>
<div class="fd-toolbar__o-nav-item">
	<a href="javascript:void(0);" class="fd-toolbar__link no-active-state <?php echo ($newNotifications > 0) ? 'has-new' : ''; ?>"
		data-fd-tooltip="toolbar"
		data-fd-tooltip-title="<?php echo JText::_('MOD_SI_TOOLBAR_NOTIFICATIONS'); ?>"
		data-fd-tooltip-placement="top" 

		data-fd-dropdown="toolbar"
		data-fd-dropdown-offset="[0, 0]"
		data-fd-dropdown-trigger="click"
		data-fd-dropdown-placement="bottom" 
		data-fd-dropdown-content="action/notifications"
		data-module-id="<?php echo $moduleId; ?>"
		data-module-currentview="<?php echo $currentView; ?>"
		>
		<i class="fdi fa fa-bell"></i>
		<span class="fd-toolbar__link-bubble" data-fd-notifications-counter><?php echo $newNotifications;?></span>
	</a>
	<div class="t-hidden">
		<div id="fd" class="">
			<div class="<?php echo FDT::getAppearance();?> <?php echo FDT::getAccent();?>">
				<div class="o-dropdown divide-y divide-gray-200  md:w-[400px]" data-fd-dropdown-wrapper>
					<div class="o-dropdown__hd px-md py-sm">
						<div class="flex">
							<div class="flex-grow font-bold text-sm text-gray-800">
								<?php echo JText::_('MOD_SI_TOOLBAR_NOTIFICATIONS'); ?>
							</div>
							<div class="">
								<a href="javascript:void(0)" class="fd-link" data-fd-notifications-read-all>
									<?php echo Jtext::_('MOD_SI_TOOLBAR_MARK_ALL_AS_READ'); ?>
								</a>
							</div>
						</div>
					</div>

					<div class="o-dropdown__bd overflow-y-auto max-h-[380px] divide-y divide-gray-200" data-fd-dropdown-body data-fd-toolbar-dropdown-menus>
						<div class="px-sm py-sm hover:no-underline text-gray-800">
							<?php echo $this->fd->html('placeholder.standard', FDT::config()->get('avatar_style', 'rounded')); ?>
						</div>
					</div>
					
					<div class="o-dropdown__ft px-md py-sm">
						<div class="text-center">
							<a href="<?php echo $viewAllNotificationsLink; ?>" class="fd-link"><?php echo JText::_('MOD_SI_TOOLBAR_VIEW_ALL_NOTIFICATIONS'); ?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php } ?>

