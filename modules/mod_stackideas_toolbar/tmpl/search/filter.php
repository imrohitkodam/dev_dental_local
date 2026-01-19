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
<a href="javascript:void(0);" class="fd-toolbar-btn <?php echo $class; ?>" title="<?php echo JText::_('MOD_SI_TOOLBAR_FILTER'); ?>"
	data-fd-search-dropdown="toolbar"
	data-fd-dropdown-offset="[0, 0]"
	data-fd-dropdown-trigger="click"
	data-fd-dropdown-placement="bottom-start"
	>
	<span><?php echo JText::_('MOD_SI_TOOLBAR_FILTER'); ?></span>
	<i class="fdi fa fa-chevron-down ml-2xs"></i>
</a>
<div class="t-hidden">
	<div id="fd" class="">
		<div class="<?php echo FDT::getAppearance();?> <?php echo FDT::getAccent();?>">
			<div class="o-dropdown  divide-y divide-gray-200 md:w-[320px]" data-fd-dropdown-wrapper>
				<div class="o-dropdown__hd px-md py-sm font-bold">
					<?php echo JText::_('MOD_SI_TOOLBAR_SHOW_RESULT_FILTER'); ?>
					<div class="flex divide-x-2x space-x-xs">
						<div class="">
							<a class="fd-link" data-fd-filter="select" href="javascript:void(0);"><?php echo JText::_('MOD_SI_TOOLBAR_CHECK_ALL'); ?></a> 
						</div>
						<div class="">|</div>
						<div class="">
							<a class="fd-link" data-fd-filter="deselect" href="javascript:void(0);"><?php echo JText::_('MOD_SI_TOOLBAR_UNCHECK_ALL'); ?></a>
						</div>
					</div>
				</div>
				<div class="o-dropdown__bd px-md py-sm" data-fd-dropdown-body data-fd-toolbar-dropdown-menus>
					<div class="grid grid-cols-2 gap-sm w-full">
						<?php foreach ($filters as $filter) { ?>
							<div class="">
								<label class="o-form-check">
									<input class="fd-custom-check" type="checkbox" value="<?php echo $filter->alias; ?>" id="search-filter-<?php echo $filter->id;?>" name="filtertypes[]" data-fd-filtertypes>
									<span class="o-form-check__text truncate">
										<?php echo $filter->displayTitle;?>
									</span>
								</label>
							</div>
						<?php } ?>
					</div>
				</div>	
			</div>
		</div>
	</div>
</div>