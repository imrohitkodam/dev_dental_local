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
<?php foreach ($categories as $category) { ?>
	<li class="o-dropdown-nav__item">
		<a href="javascript:void(0);" class="o-dropdown-nav__link" 
			data-fd-search-filter-item
			data-fd-search-filter-value="<?php echo JText::_($category->title); ?>"
			data-fd-search-filter-id="<?php echo $category->id;?>"
			data-fd-toolbar-dropdown-item
			>
			<div class="o-dropdown-nav__text">
				<?php echo JText::_($category->title); ?>
			</div>
		</a>

		<?php if ($category->childs) { ?>
			<a href="javascript:void(0);" class="o-dropdown-nav__toggle" data-fd-menu-nav>
				<i class="fdi fa fa-angle-right"></i>
			</a>

			<ul class="o-dropdown-nav o-dropdown-nav--nested hidden">
				<li class="o-dropdown-nav__item o-dropdown-nav__item-back">
					<a href="javascript:void(0);" class="o-dropdown-nav__link" data-fd-menu-nav-back>
						<div class="o-dropdown-nav__media">
							<i class="fdi fa fa-angle-left"></i>
						</div>
						<div class="o-dropdown-nav__text">
							<?php echo JText::_('MOD_SI_TOOLBAR_BACK'); ?>
						</div>
					</a>
				</li>

				<?php echo FDT::themes()->html('search.categoriesItems', $category->id, $adapter); ?>
			</ul>
		<?php } ?>
	</li>
<?php } ?>