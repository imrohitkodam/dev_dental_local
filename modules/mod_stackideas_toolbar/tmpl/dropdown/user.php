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
<div class="fd-toolbar__o-nav-item is-user">
	<a href="javascript:void(0);" class="fd-toolbar__link has-avatar" 
	data-fd-dropdown="toolbar"
	data-fd-dropdown-placement="<?php echo FH::isRTL() ? 'bottom-start' : 'bottom-end'; ?>" 
	data-fd-dropdown-offset="[0, 0]"
	data-fd-dropdown-trigger="click"
	data-fd-dropdown-max-width=""
	>
		<div class="fd-toolbar__avatar">
			<?php echo FDT::themes()->html('html.avatar');?>
		</div>
	</a>

	<div class="t-hidden" data-fd-toolbar-dropdown>
		<div id="fd">
			<div class="<?php echo FDT::getAppearance();?> <?php echo FDT::getAccent();?>">
				<div class="o-dropdown divide-y divide-gray-200 w-[320px]">
					<div class="o-dropdown__hd px-md py-md">
						<div class="flex items-center">
							<div class="flex-grow">
								<div class=" space-y-2xs">
									<div class="font-bold text-sm text-gray-800">
										<?php echo FDT::themes()->html('html.name', [
											'useAnchorTag' => false,
											'showVerified' => $showVerified
										]);?>
									</div>

									<?php if ($showProfileMeta && !empty($profileMeta)) { ?>
									<div class="text-xs leading-xs text-gray-500 items-center <?php echo $profileMeta['type'] == 'image' ? 'flex' : ''; ?>">
										<?php if ($profileMeta['type'] == 'icon') { ?>
											<i class="fdi <?php echo $profileMeta['item']; ?>"></i>
										<?php } ?>

										<?php if ($profileMeta['type'] == 'image') { ?>
											<div class="flex-shrink-0 flex">
												<div class="o-aspect-ratio w-[16px] mr-2xs" style="--aspect-ratio: 1/1;">
													<img src="<?php echo $profileMeta['item']; ?>" alt="<?php echo $profileMeta['title']; ?>" class="rounded-full">
												</div>
											</div>
										<?php } ?>

										<span>
											<?php echo $profileMeta['title']; ?>
										</span>
									</div>
									<?php } ?>

									<?php if (!empty($badges)) { ?>
									<div class="text-xs text-gray-500 flex">
										<div>
											<?php foreach($badges as $badge) { ?>
												<a href="<?php echo $badge->getPermalink();?>" alt="<?php echo FH::escape($badge->getTitle());?>" class="o-aspect-ratio w-[16px] mr-2xs" style="--aspect-ratio: 1/1;">
													<img src="<?php echo $badge->getAvatar();?>" alt="" class="rounded-full">
												</a>
											<?php } ?>
										</div>
									</div>
									<?php } ?>
								</div>
							</div>

							<?php if ($permaLink) { ?>
								<div class="pl-md flex-shrink-0">
									<a href="<?php echo $permaLink;?>" class="no-underline">
										<?php echo FDT::themes()->html('html.avatar');?>
									</a>
								</div>
							<?php } ?>
						</div>
					</div>

					<?php echo FDT::themes()->html('html.qrcode');?>

					<div class="o-dropdown__bd">
						<div class="py-xs px-xs overflow-y-auto max-h-[380px]" data-fd-toolbar-dropdown-menus>
							<ul	class="o-dropdown-nav o-dropdown-nav--parent">
								<?php echo FDT::themes()->html('dropdown.menu');?>
							</ul>
						</div>
					</div>
					
					<div class="o-dropdown__ft py-xs px-xs">
						<ul class="o-dropdown-nav">
							<li class="o-dropdown-nav__item">
								<a class="o-dropdown-nav__link" href="javascript:void(0);" data-fd-toolbar-logout-button>
									<div class="o-dropdown-nav__media">
										<i class="fdi fas fa-sign-out-alt fa-fw"></i>
									</div>
									<div class="o-dropdown-nav__text">
										&nbsp; <?php echo JText::_('MOD_SI_TOOLBAR_LOGOUT'); ?>
									</div>
								</a>

								<?php echo $this->html('form.logout'); ?>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
