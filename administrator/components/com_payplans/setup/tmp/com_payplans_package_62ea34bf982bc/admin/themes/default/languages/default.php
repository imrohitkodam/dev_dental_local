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
<form id="adminForm" name="adminForm" action="index.php" method="post" data-fd-grid>
	<div class="panel-table">
		<?php echo $this->fd->html('alert.standard', 'COM_PP_LANGUAGES_TRANSLATE_MESSAGE', 'warning', ['dismissible' => false, 'button' => $this->fd->html('button.link', 'https://stackideas.com/translators', 'COM_PP_LANGUAGES_BE_A_TRANSLATOR', 'primary', 'sm', ['icon' => 'fdi fa fa-external-link-alt', 'class' => 'ml-2xs'], true)]); ?>

		<table class="app-table table">
			<thead>
				<tr>
					<th width="1%">
						<?php echo $this->html('grid.checkall'); ?>
					</th>
					<th>
						<?php echo JText::_('COM_PP_TABLE_COLUMN_TITLE'); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JText::_('COM_PP_TABLE_COLUMN_LOCALE'); ?>
					</th>
					<th width="15%" class="center">
						<?php echo JText::_('COM_PP_TABLE_COLUMN_STATE'); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JText::_('COM_PP_TABLE_COLUMN_PROGRESS'); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JText::_('COM_PP_TABLE_COLUMN_LAST_UPDATED'); ?>
					</th>
					<th width="1%" class="center">
						<?php echo JText::_('COM_PP_TABLE_COLUMN_ID'); ?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php if ($languages) { ?>
					<?php $i = 0; ?>
					<?php foreach ($languages as $language) { ?>
					<tr>
						<td class="center">
							<?php echo $this->html('grid.id', $i, $language->id); ?>
						</td>
						<td>
							<?php echo $language->title;?>
						</td>
						<td class="center">
							<?php echo $language->locale;?>
						</td>
						<td class="center">
							<?php if ($language->isInstalled()) { ?>
							<b class="text-success">
								<?php echo JText::_('COM_PP_INSTALLED'); ?>
							</b>
							<?php } ?>

							<?php if ($language->requiresUpdating()) { ?>
							<b class="text-danger">
								<?php echo JText::_('COM_PP_REQUIRES_UPDATING'); ?>
							</b>
							<?php } ?>

							<?php if (!$language->isInstalled()){ ?>
							<span class="">
								<?php echo JText::_('COM_PP_NOT_INSTALLED'); ?>
							</span>
							<?php } ?>
						</td>
						<td class="center">
							<?php echo !$language->progress ? 0 : $language->progress;?> %
						</td>
						<td class="center">
							<?php echo $language->updated; ?>
						</td>
						<td class="center">
							<?php echo $language->id; ?>
						</td>
					</tr>
					<?php $i++; ?>
					<?php } ?>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="8">
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<?php echo $this->html('form.action'); ?>
</form>