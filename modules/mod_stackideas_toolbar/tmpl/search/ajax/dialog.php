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
<dialog>
	<width>400</width>
	<height>700</height>
	<selectors type="json">
	{
		"{closeButton}": "[data-close-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"init": function() {
			window.tb.dropdown('[data-fd-dialog] [data-fd-dropdown="toolbar"]');
			window.tb.dropdownSearch('[data-fd-dialog] [data-fd-search-dropdown="toolbar"]');
		}
	}
	</bindings>
	<title>
		<?php echo JText::_('MOD_SI_TOOLBAR_SEARCH');?>
	</title>
	<content>
		<form name="fd-toolbar-search" data-fd-search-toolbar-form method="post" action="<?php echo JRoute::_('index.php?option=' . $component . '&controller=search&task=' . $task . '&Itemid=' . $itemid); ?>" class="">

			<div class="fd-toolbar-dialog space-y-md">
				<div class="fd-toolbar__search-filter" data-fd-dialog>
					<?php echo FDT::themes()->html('search.categories', ['class' => 'w-full']); ?>
				</div>

				<div class="fd-toolbar__search-filter" data-fd-dialog>
					<?php echo FDT::themes()->html('search.filter', ['class' => 'w-full']); ?>
				</div>

				<div class="flex items-center" data-fd-dialog>
					<div class="flex-grow">
						<?php echo FDT::themes()->html('search.input', ['class' => 'w-full']); ?>
					</div>
					<div>
						<div class="fd-toolbar__search-submit-btn">
							<?php echo $this->fd->html('button.submit', '', 'default', '', [
								'icon' => 'fdi fa fa-search',
								'class' => 'fd-toolbar__link fd-toolbar__btn-search'
							]);?>
						</div>
					</div>
				</div>

				<?php echo $this->fd->html('form.hidden', 'option', $component); ?>
				<?php echo $this->fd->html('form.hidden', 'controller', 'search'); ?>
				<?php echo $this->fd->html('form.hidden', 'task', $task); ?>
				<?php echo $this->fd->html('form.hidden', 'Itemid', $itemid); ?>
				<?php echo $this->fd->html('form.token'); ?>
			</div>
		</form>
	</content>
</dialog>
