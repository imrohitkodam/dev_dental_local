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
<a href="javascript:void(0);" class="fd-toolbar-btn <?php echo $class; ?>" title="<?php echo JText::_('MOD_SI_TOOLBAR_CATEGORIES'); ?>"
	data-fd-dropdown="toolbar"
	data-fd-dropdown-offset="[0, 0]"
	data-fd-dropdown-trigger="click"
	data-fd-dropdown-placement="bottom-start"
	data-fd-dropdown-content="action/categories"
	data-fd-component="<?php echo $component; ?>"
	>
	<span><?php echo JText::_('MOD_SI_TOOLBAR_CATEGORIES'); ?>: &nbsp;</span>
	<span class="font-normal" data-fd-search-filter><?php echo JText::_('MOD_SI_TOOLBAR_ALL_CATEGORIES');?></span>
	<i class="fdi fa fa-chevron-down ml-2xs"></i>
</a>
<div class="hidden">
	<div id="fd" class="">
		<div class="<?php echo FDT::getAppearance();?> <?php echo FDT::getAccent();?>">
			<div class="o-dropdown divide-y divide-gray-200 md:w-[400px] " data-fd-dropdown-wrapper>
				<div class="o-dropdown__bd py-sm px-xs overflow-y-auto max-h-[380px] divide-y divide-gray-200 space-y-smx" data-fd-dropdown-body data-fd-toolbar-dropdown-menus>
					<div class="px-sm py-sm hover:no-underline text-gray-800">
						<?php echo $this->fd->html('placeholder.line', 1); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo $this->fd->html('form.hidden', 'category_id', '', '', 'data-fd-search-category-id'); ?>