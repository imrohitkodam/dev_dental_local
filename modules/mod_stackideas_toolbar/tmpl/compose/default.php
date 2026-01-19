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
<?php if (!empty($composeButtons)) { ?>
	<div class="fd-toolbar__o-nav-item md:flex">
		<a href="javascript:void(0);" class="fd-toolbar__link is-composer" 
			data-fd-dropdown="toolbar"
			data-fd-dropdown-placement="bottom" 
			data-fd-dropdown-offset="[0, 0]"
			data-fd-dropdown-trigger="click"
			data-fd-dropdown-max-width="">
			<i class="fdi fas fa-plus"></i>
		</a>

		<div class="hidden" data-fd-toolbar-dropdown="">
			<div id="fd">
				<div class="<?php echo FDT::getAppearance();?> <?php echo FDT::getAccent();?>">
					<div class="o-dropdown divide-y divide-gray-200 md:w-[320px]">
						<div class="o-dropdown__hd px-md py-sm">
							<div class="font-bold text-sm text-gray-800">
								<?php echo JText::_('MOD_SI_TOOLBAR_COMPOSER_HEADING');?>
							</div>
						</div>
						<div class="o-dropdown__bd px-xs py-xs" data-fd-toolbar-dropdown-menus>
							<ul class="o-dropdown-nav">
								<?php foreach ($composeButtons as $button) { ?>
									<li class="o-dropdown-nav__item">
										<a href="<?php echo $button['link']; ?>" class="o-dropdown-nav__link">
											<div class="o-dropdown-nav__media">
												<i class="<?php echo $button['icon'];?>"></i>
											</div>
											<?php echo $button['title'];?>
										</a>
									</li>
								<?php } ?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php } ?>
