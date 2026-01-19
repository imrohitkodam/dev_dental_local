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
<?php if ($home) { ?>
<div class="fd-toolbar__item fd-toolbar__item--home mr-3xs">
	<nav class="fd-toolbar__o-nav">
		<div class="fd-toolbar__o-nav-item <?php echo $home->id === $active ? 'is-active' : '';?>">
			<a href="<?php echo $home->permalink;?>" class="fd-toolbar__link">
				<i aria-hidden="true" class="fdi fa fa-home"></i>
				<span class="sr-only"><?php echo JText::_('MOD_SI_TOOLBAR_HOME'); ?></span>
			</a>
		</div>
	</nav>
</div>
<?php } ?>

<?php if ($menus) { ?>
<div class="fd-toolbar__item fd-toolbar__item--submenu" data-fd-toolbar-menu="">
	<div class="fd-toolbar__o-nav">
		<?php if ($menus->visible) { ?>
			<?php foreach ($menus->visible as $key => $menu) { ?>
			<div class="fd-toolbar__o-nav-item <?php echo $menu->view === $active ? 'is-active' : '';?>">
				<a href="<?php echo $menu->permalink;?>" class="fd-toolbar__link" title="<?php echo JText::_($menu->title); ?>">
					<span><?php echo JText::_($menu->title); ?></span>
				</a>
			</div>
			<?php } ?>
		<?php } ?>

		<?php if ($menus->hidden) { ?>
		<div class="fd-toolbar__o-nav-item" 
			data-fd-dropdown="toolbar"
			data-fd-dropdown-placement="bottom-start" 
			data-fd-dropdown-offset="[0, 10]" 
			aria-expanded="false"
			role="button"
			>
			<a href="javascript:void(0);" class="fd-toolbar__link">
				<span><?php echo JText::_('MOD_SI_TOOLBAR_MORE'); ?> <i class="fdi fas fa-chevron-down"></i></span>
			</a>
		</div>

		<div class="hidden" data-fd-toolbar-dropdown="">
			<div id="fd">
				<div class="<?php echo FDT::getAppearance();?> <?php echo FDT::getAccent();?>">
					<div class="o-dropdown divide-y divide-gray-200 w-[280px]">
						<div class="o-dropdown__hd px-md py-sm">
							<div class="font-bold text-sm text-gray-800"><?php echo JText::_('MOD_SI_TOOLBAR_MORE');?></div>
						</div>
						<div class="o-dropdown__bd px-xs py-xs" data-fd-toolbar-dropdown-menus>
							<ul class="o-dropdown-nav">
								<?php foreach ($menus->hidden as $menu) { ?>
								<li class="o-dropdown-nav__item <?php echo $menu->id === $active ? 'is-active' : '';?>">
									<a href="<?php echo $menu->permalink;?>" class="o-dropdown-nav__link" title="<?php echo JText::_($menu->title); ?>">
										<span class="o-dropdown-nav__text"><?php echo JText::_($menu->title); ?></span>
									</a>
								</li>
								<?php } ?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php } ?>
	</div>
</div>
<?php } ?>