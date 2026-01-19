<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="hidden" data-j4-sidebar>
	<li class="item item-level-1">
		<a class="has-arrow" href="javascript:void(0);" aria-expanded="false" data-back-payplans>
			<span style="padding: .2rem 0;margin: 0 .6rem;">
				<img src="/media/com_payplans/images/logo-payplans.png" style="width: 18px;height: 18px;/*! padding: 1rem 0; */">
			</span>

			<span class="sidebar-item-title">Payplans</span>
		</a>
	</li>
</div>

<div class="app-sidebar app-sidebar-collapse <?php echo PP::isJoomla4() ? 't-hidden' : '';?>" data-sidebar>

	<ul class="app-sidebar-nav list-unstyled">
		<li class="sidebar-item sidebar-item--joomla-4-btn">
			<a href="javascript:void(0);" data-back-joomla>
				<i class="fa fa-chevron-left"></i> <span class="app-sidebar-item-title"><?php echo JText::_('Back');?></span>
			</a>
		</li>
		<?php foreach ($menus as $item) { ?>
		<li class="sidebar-item <?php echo isset($item->childs) && $item->childs ? 'dropdown' : '';?> <?php echo $item->view == $view ? 'active' : '';?>" data-sidebar-item>
			<a href="<?php echo isset($item->childs) && $item->childs ? 'javascript:void(0);' : $item->link;?>" data-sidebar-parent data-childs="<?php echo isset($item->childs) ? count($item->childs) : 0;?>">
				<i class="fa <?php echo $item->class; ?>"></i><span class="app-sidebar-item-title"><?php echo JText::_($item->title); ?></span>
				<span class="badge"></span>
			</a>

			<?php if (isset($item->childs) && $item->childs) { ?>
			<ul class="dropdown-menu<?php echo $item->active ? ' in' : '';?>" id="menu-<?php echo $item->uid;?>" data-sidebar-child>

				<?php foreach ($item->childs as $child) { ?>
					<li class="<?php echo $child->active ? 'active' : '';?>">
						<a href="<?php echo $child->link;?>">
							<span class="app-sidebar-item-title"><?php echo JText::_($child->title); ?></span>
						</a>
						<span class="badge"><?php echo $child->count > 0 ? $child->count : ''; ?></span>
					</li>
				<?php } ?>
			</ul>
			<?php } ?>
		</li>
		<?php } ?>
	</ul>
</div>