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
<label for="fd-search" class="sr-only"><?php echo $placeholder; ?></label>

<?php echo $this->fd->html('form.text', $queryName, $query, 'fd-search', [
	'customClass' => 'fd-toolbar__search-input ' . $class,
	'placeholder' => $placeholder,
	'attr' => 'data-search-input autocomplete="off" data-fd-component="' . $component . '"'
]); ?>

<div class="t-hidden" data-fd-toolbar-dropdown>
	<div id="fd">
		<div class="<?php echo FDT::getAppearance();?> <?php echo FDT::getAccent();?>">
			<div class="o-dropdown divide-y divide-gray-200 w-full" data-fd-dropdown-wrapper>
				<div class="o-dropdown__hd px-md py-md" data-fd-dropdown-header>
					<div class="font-bold text-sm text-gray-800"><?php echo JText::_($header); ?></div>
				</div>
				<div class="o-dropdown__bd py-sm px-xs overflow-y-auto max-h-[380px] divide-y divide-gray-200 space-y-smx" data-fd-dropdown-body>
					<div class="px-sm py-sm hover:no-underline text-gray-800">
						<?php echo $this->fd->html('placeholder.line', 1); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>