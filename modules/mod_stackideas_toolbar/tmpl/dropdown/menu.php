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
<?php foreach ($menus as $title => $item) { ?>
<li class="o-dropdown-nav__item" data-fd-filter-menu-item>
	<?php if (isset($item['menus']) && is_array($item['menus']) && count($item['menus'])) { ?>
		<a href="javascript:void(0);" class="o-dropdown-nav__link" data-fd-menu-nav>
			<div class="o-dropdown-nav__media">
				<i class="<?php echo $item['icon']; ?>"></i>
			</div>
			<div class="o-dropdown-nav__text">
				<?php echo JText::_($title);?>
			</div>
		</a>

		<a href="javascript:void(0);" class="o-dropdown-nav__toggle" data-fd-menu-nav>
			<i class="fdi fa fa-angle-right"></i>
		</a>

		<ul class="o-dropdown-nav o-dropdown-nav--nested hidden" data-fd-menu-nav-nested>
			<li class="o-dropdown-nav__item o-dropdown-nav__item-back" data-fd-menu-nav-back>
				<a href="javascript:void(0);" class="o-dropdown-nav__link">
					<div class="o-dropdown-nav__media">
						<i class="fdi fa fa-angle-left"></i>
					</div>
					<div class="o-dropdown-nav__text font-bold">
						<?php echo JText::_($title); ?>
					</div>
				</a>
			</li>

			<?php echo FDT::themes()->html('dropdown.menu', ['menus' => $item['menus']]);?>
		</ul>
	<?php } elseif (isset($item['link'])) { ?>
		<a href="<?php echo $item['link'];?>" class="o-dropdown-nav__link" <?php echo isset($item['attributes']) ? $item['attributes'] : ''; ?>>
			<div class="o-dropdown-nav__media">
				<i class="<?php echo $item['icon']; ?>"></i>
			</div>
			<div class="o-dropdown-nav__text">
				<?php echo JText::_($title); ?>
			</div>
		</a>
	<?php } ?>
</li>
<?php } ?>