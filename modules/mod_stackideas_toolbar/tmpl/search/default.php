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
<div class="fd-toolbar__item fd-toolbar__item--search">
	<div id="fd-toolbar-search" class="fd-toolbar__search">
		<form name="fd-toolbar-search" data-fd-search-toolbar-form method="post" action="<?php echo JURI::root(); ?>" class="fd-toolbar__search-form">

			<?php echo FDT::themes()->html('search.categories'); ?>

			<?php echo FDT::themes()->html('search.filter'); ?>

			<?php echo FDT::themes()->html('search.input'); ?>

			<?php echo $this->fd->html('form.hidden', 'option', $component); ?>
			<?php echo $this->fd->html('form.hidden', 'controller', 'search'); ?>
			<?php echo $this->fd->html('form.hidden', 'task', $task); ?>
			<?php echo $this->fd->html('form.hidden', 'Itemid', $itemid); ?>
			<?php echo $this->fd->html('form.token'); ?>

			<div class="fd-toolbar__search-submit-btn">
				<?php echo $this->fd->html('button.submit', JText::_('MOD_SI_TOOLBAR_SEARCH'), 'default', '', [
					'icon' => 'fdi fa fa-search',
					'class' => 'fd-toolbar__link fd-toolbar__btn-search'
				]);?>
			</div>
			<div class="fd-toolbar__search-close-btn">
				<a href="javascript:void(0);" class="" data-fd-toolbar-search-toggle>
					<i aria-hidden="true" class="fdi fa fa-times"></i>
					<span class="sr-only">x</span>
				</a>
			</div>
		</form>
	</div>
</div>