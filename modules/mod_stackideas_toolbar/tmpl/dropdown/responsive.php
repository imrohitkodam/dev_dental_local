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
<div class="fd-toolbar__o-nav-item fd-toolbar-mobile-toggle">
	<a href="#fd-canvas" class="fd-toolbar__link">
		<i class="fdi fa fa-bars"></i>
	</a>
</div>

<nav id="fd-canvas" 
	data-placeholder="<?php echo JText::_('MOD_SI_TOOLBAR_SEARCH', true);?>" 
	data-no-result="<?php echo JText::_('MOD_SI_TOOLBAR_NO_SEARCH_RESULTS', true);?>"
	data-title="<?php echo JText::_("MOD_SI_TOOLBAR_MENU", true);?>"
>
	<ul>
		<?php if ($home) { ?>
			<li>
				<a href="<?php echo $home->permalink;?>">
					<?php echo JText::_('MOD_SI_TOOLBAR_HOME');?>
				</a>
			</li>
		<?php } ?>

		<?php foreach ($menus->visible as $key => $menu) { ?>
			<li>
				<a href="<?php echo $menu->permalink; ?>">
					<?php echo JText::_($menu->title); ?>
				</a>
			</li>
		<?php } ?>

		<?php if ($menus->hidden) { ?>
			<li>
				<span><?php echo JText::_('MOD_SI_TOOLBAR_MORE'); ?></span>

				<ul>
					<?php foreach ($menus->hidden as $menu) { ?>
					<li>
						<a href="<?php echo $menu->permalink; ?>">
							<?php echo JText::_($menu->title); ?>
						</a>
					</li>
					<?php } ?>
				</ul>
			</li>
		<?php } ?>

		<?php if ($sections || $user->id) { ?>
			<li class="mm-divider">
				<?php echo JText::_("MOD_SI_TOOLBAR_NAVIGATION");?>
			</li>
		<?php } ?>

		<?php foreach ($sections as $section => $value) { ?>
			<li>
				<?php if (empty($value['menus'])) { ?>
					<a href="<?php echo $value['link']; ?>">
						<?php echo JText::_($section); ?>
					</a>
				<?php } ?>

				<?php if (!empty($value['menus'])) { ?>
					<span>
						<?php echo JText::_($section);?>
					</span>

					<ul>
						<?php foreach ($value['menus'] as $group => $item) { ?>
							<?php if (!empty($item)) { ?>
								<?php if (!isset($item['menus'])) { ?>
								<li class="o-dropdown-nav__item">
									<a href="<?php echo $item['link']; ?>">
										<?php echo JText::_($group);?>
									</a>
								</li>
								<?php } ?>

								<?php if (isset($item['menus'])) { ?>
								<li>
									<span>
										<?php echo JText::_($group);?>
									</span>

									<ul>
										<?php foreach ($item['menus'] as $group => $item) { ?>
											<li class="o-dropdown-nav__item">
												<a href="<?php echo $item['link']; ?>">
													<?php echo JText::_($group);?>
												</a>
											</li>
										<?php } ?>
									</ul>
								</li>
								<?php } ?>
							<?php } ?>
						<?php } ?>
					</ul>
				<?php } ?>
			</li>
		<?php } ?>

		<?php if ($user->id) { ?>
		<li class="o-dropdown-nav__item" data-fd-toolbar-logout-button>
			<a href="#">
				<?php echo JText::_('MOD_SI_TOOLBAR_LOGOUT'); ?>
			</a>

			<?php echo $this->html('form.logout'); ?>
		</li>
		<?php } ?>
	</ul>
</nav>