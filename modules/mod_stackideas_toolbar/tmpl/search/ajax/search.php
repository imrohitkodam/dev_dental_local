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
<?php if ($items) { ?>
	<?php foreach ($items as $item) { ?>
		<div class="" <?= $item->attributes ?>>
			<a href="<?= $item->link?>" class="flex hover:bg-gray-100 px-md py-md hover:no-underline text-gray-800">
				<?php if ($item->avatar) { ?>
					<span class="mr-sm">
						<div class="o-avatar o-avatar--sm <?= $item->element == 'users' ? 'o-avatar--rounded' : '' ?>">
							<div class="o-avatar__content">
								<img src="<?= $item->avatar?>" width="16" height="16"/>
							</div>
						</div>
					</span>
				<?php } ?>
				<?= $item->text ?>
			</a>
		</div>
	<?php } ?>
<?php } ?>

<?= $this->fd->html('html.emptyList', 'MOD_SI_TOOLBAR_NO_RECORD_FOUND', [
	'icon' => 'fdi fa fa-search',
	'class' => (!$items) ? 'block' : '', 
	'attributes' => 'data-fd-empty'
]); ?>