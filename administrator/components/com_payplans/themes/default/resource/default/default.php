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
<form method="post" name="adminForm" id="adminForm" data-fd-grid>

	<?php if ($renderFilterBar) { ?>
		<div class="app-filter-bar">
			<?php echo $this->fd->html('filter.limit', $states->limit); ?>
		</div>
	<?php } ?>

	<div class="panel-table">
		<table class="app-table table">
			<thead>
				<tr>
					<?php if ($editable) { ?>
					<th width="1%" class="center">
						<?php echo $this->html('grid.checkall'); ?>
					</th>
					<?php } ?>

					<th>
						<?php if ($sortable) { ?>
							<?php echo $this->html('grid.sort', 'resource', 'COM_PAYPLANS_RESOURCE_GRID_TITLE', $states); ?>
						<?php } else { ?>
							<?php echo JText::_('COM_PAYPLANS_RESOURCE_GRID_TITLE'); ?>
						<?php } ?>
					</th>

					<th width="10%" class="center">
						<?php if ($sortable) { ?>
							<?php echo $this->html('grid.sort', 'user_id', 'COM_PP_TABLE_COLUMN_USER', $states);?>
						<?php } else { ?>
							<?php echo JText::_('COM_PP_TABLE_COLUMN_USER');?>
						<?php } ?>
					</th>

					<th width="30%" class="center">
						<?php if ($sortable) { ?>
							<?php echo $this->html('grid.sort', 'subscription_ids', 'COM_PAYPLANS_RESOURCE_GRID_SUBSCRIPTION_IDS', $states);?>
						<?php } else { ?>
							<?php echo JText::_('COM_PAYPLANS_RESOURCE_GRID_SUBSCRIPTION_IDS'); ?>
						<?php } ?>
					</th>

					<th width="10%" class="center">
						<?php if ($sortable) { ?>
							<?php echo $this->html('grid.sort', 'value', 'COM_PAYPLANS_RESOURCE_GRID_VALUE', $states); ?>
						<?php } else { ?>
							<?php echo JText::_('COM_PAYPLANS_RESOURCE_GRID_VALUE'); ?>
						<?php } ?>
					</th>
					
					<th width="10%" class="center">
						<?php if ($sortable) { ?>
							<?php echo $this->html('grid.sort', 'count', 'COM_PAYPLANS_RESOURCE_GRID_COUNT', $states);?>
						<?php } else { ?>
							<?php echo JText::_('COM_PAYPLANS_RESOURCE_GRID_COUNT') ?>
						<?php } ?>
					</th>

					<th width="1%" class="center">
						<?php if ($sortable) { ?>
							<?php echo JText::_('COM_PP_TABLE_COLUMN_ID'); ?>
						<?php } else { ?>
							<?php echo JText::_('COM_PP_TABLE_COLUMN_ID'); ?>
						<?php } ?>
					</th>
				</tr>
			</thead>

			<tbody>
				<?php if ($resources) { ?>
					<?php $i = 0; ?>
					<?php foreach ($resources as $resource) { ?>
					<tr>
						<?php if ($editable) { ?>
						<th class="center">
							<?php echo $this->html('grid.id', $i, $resource->resource_id); ?>
						</th>
						<?php } ?>

						<td class="pp-word-wrap">
							<?php if ($editable) { ?>
								<a href="index.php?option=com_payplans&view=resource&layout=form&id=<?php echo $resource->resource_id;?>">
									<?php echo $resource->title;?>
								</a>
							<?php } else { ?>
								<?php echo $resource->title;?>
							<?php } ?>
						</td>

						<td class="center">
							<?php echo $resource->user_id;?>
						</td>

						<td class="center">
							<?php echo $resource->subscription_ids;?>
						</td>

						<td class="center">
							<?php echo $resource->value; ?>
						</td>

						<td class="center">
							<?php echo $resource->count;?>
						</td>

						<td class="center">
							<?php echo $resource->resource_id;?>
						</td>
					</tr>
					<?php $i++; ?>
					<?php } ?>
				<?php } ?>


				<?php if (!$resources) { ?>
					<?php echo $this->html('grid.emptyBlock', 'COM_PP_RESOURCE_EMPTY', 11); ?>
				<?php } ?>
			</tbody>

			<?php if ($pagination && ($pagination instanceof PPPagination)) { ?>
				<?php echo $this->html('grid.pagination', $pagination, 11); ?>
			<?php } ?>
		</table>
	</div>

	<?php if ($form) { ?>
		<?php echo $this->html('form.action', 'resource'); ?>
		<?php echo $this->fd->html('form.hidden', 'ordering', $states->ordering, '', 'data-fd-table-ordering'); ?>
		<?php echo $this->fd->html('form.hidden', 'direction', $states->direction, '', 'data-fd-table-direction'); ?>
	<?php } ?>
</form>
