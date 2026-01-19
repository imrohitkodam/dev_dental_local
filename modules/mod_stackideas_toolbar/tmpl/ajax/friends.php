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
<?php if ($requests) { ?>
	<?php foreach ($requests as $request) { ?>
		<div class="flex hover:bg-gray-100 px-md py-md hover:no-underline text-gray-800" data-item data-id="<?php echo $request->getRequester()->id;?>">
			<div class="pr-md">
				<?php echo FDT::themes()->html('html.avatar', ['user' => $request->getRequester()]); ?>
			</div>
			<div class="flex-grow space-y-2xs">
				<div class="font-bold">
					<?php echo FDT::themes()->html('html.name', [
						'user' => $request->getRequester(),
						'profileStyling' => false
					]); ?>
				</div>
				<div class="text-gray-500 text-xs">
					<?php if ($request->getRequester()->getTotalMutualFriends($my->id)) { ?>
						<?php echo JText::sprintf('MOD_SI_TOOLBAR_FRIENDS_MUTUAL_FRIENDS_TOTAL', $request->getRequester()->getTotalMutualFriends($my->id)); ?>
					<?php } else { ?>
						<?php echo JText::_('MOD_SI_TOOLBAR_FRIENDS_NO_MUTUAL_FRIENDS'); ?>
					<?php } ?>
				</div>
				<div class="flex space-x-xs">
					<a href="javascript:void(0);" class="o-btn o-btn--primary o-btn--sm" data-fd-friend-request data-action="accept">
						<?php echo JText::_('MOD_SI_TOOLBAR_ACCEPT_BUTTON');?>
					</a>

					<a href="javascript:void(0);" class="o-btn o-btn--default o-btn--sm" data-fd-friend-request data-action="reject">
						<?php echo JText::_('MOD_SI_TOOLBAR_REJECT_BUTTON');?>
					</a>

					<div class="t-hidden text-xs text-gray-500" data-fd-friend-request-status></div>
				</div>
			</div>
		</div>
	<?php } ?>

<?php } ?>

<?php echo $this->fd->html('html.emptyList', 'MOD_SI_TOOLBAR_FRIENDS_NO_FRIENDS_YET', [
	'icon' => 'fdi fa fa-user-friends', 
	'class' => (!$requests) ? 'block' : '', 
	'attributes' => 'data-fd-empty'
]); ?>