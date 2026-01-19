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

	<div class="app-filter-bar">
		<?php echo $this->fd->html('filter.search', $states->search, 'search'); ?>

		<?php echo $this->fd->html('filter.published', 'published', $states->published, ['selectText' => 'COM_PP_SELECT_STATE', 'valueType' => 'numeric']); ?>
		
		<?php echo $this->fd->html('filter.lists', 'visible', [
			'all' => 'COM_PAYPLANS_FILTERS_SELECT_VISIBLE_STATE',
			'invisible' => 'COM_PAYPLANS_FILTERS_OFF_VISIBLE',
			'visible' => 'COM_PAYPLANS_FILTERS_ON_VISIBLE'
		], $states->visible); ?>

		<?php echo $this->html('filter.group', 'parent', $states->parent, array(), array('none' => JText::_('COM_PP_SELECT_PARENT'))); ?>

		<?php echo $this->fd->html('filter.limit', $states->limit); ?>
	</div>


	<div class="panel-table">
		<table class="app-table table">
			<thead>
				<tr>
					<th width="1%" class="center">
						<?php echo $this->html('grid.checkall'); ?>
					</th>

					<th>
						<?php echo $this->html('grid.sort', 'title', 'COM_PP_GROUP_GRID_GROUP_TITLE', $states);?>
					</th>

					<th width="5%" class="hidden-phone center">
						<?php echo $this->html('grid.sort', 'published', 'COM_PP_GROUP_GRID_GROUP_PUBLISHED', $states);?>
					</th>
					
					<th width="5%" class="hidden-phone center">
						<?php echo $this->html('grid.sort', 'visible', 'COM_PP_GROUP_GRID_GROUP_VISIBLE', $states);?>
					</th>

					<th width="15%" class="hidden-phone center">
						<?php echo $this->html('grid.sort', 'ordering', 'COM_PP_GROUP_GRID_GROUP_ORDERING', $states); ?>
						<?php echo $this->html('grid.order' , $rows, 'group'); ?>
					</th>

					<th width="1%" class="hidden-phone center">
						<?php echo JText::_('COM_PP_TABLE_COLUMN_ID'); ?>
					</th>
				</tr>
			</thead>

			<tbody>
				<?php if ($this->config->get('useGroupsForPlan', false)) { ?>
					<?php if ($rows) { ?>
						<?php $i = 0; ?>
						<?php foreach ($rows as $row) { ?>
						<tr>
							<th class="center">
								<?php echo $this->fd->html('table.id', $i, $row->group_id); ?>
							</th>

							<td class="hidden-phone pp-word-wrap">
								<a href="index.php?option=com_payplans&view=group&layout=form&id=<?php echo $row->group_id;?>"><?php echo JText::_($row->title);?></a>
							</td>

							<td width="15%" class="hidden-phone center">
								<?php echo $this->html('grid.published', $row, 'group', 'published'); ?>
							</td>

							<td width="15%" class="hidden-phone center">
								<?php echo $this->html('grid.published', $row, 'group', 'visible', array(0 => 'visible', 1 => 'invisible')); ?>
							</td>

							<td class="center order">

								<?php $current = $i + 1;?>

								<?php echo $this->fd->html('table.order', $pagination, 'order', $i, $saveOrder, [
										'rowIndex' => $i,
										'showOrderUpIcon' => $current !== 1,
										'showOrderDownIcon' => $current !== count($rows),
										'orderUpTask' => 'group.moveUp',
										'orderDownTask' => 'group.moveDown',
										'accessControl' => $states->ordering,
										'total' => count($rows)
									]); ?>
							</td>

							<td class="center">
								<?php echo $row->group_id;?>
							</td>
						</tr>
						<?php $i++; ?>
						<?php } ?>
					<?php } ?>

				<?php if (!$rows) { ?>
					<?php echo $this->html('grid.emptyBlock', 'COM_PP_GROUPS_EMPTY', 6); ?>
				<?php } ?>

				<?php } else { ?>
					<?php echo $this->html('grid.emptyBlock', JText::sprintf('COM_PP_GROUPS_FEATURE_DISABLE', $redirectLink), 6); ?>
				<?php } ?>
			</tbody>

			<?php echo $this->html('grid.pagination', $pagination, 6); ?>
		</table>
	</div>
	<?php echo $this->html('form.action'); ?>
	<?php echo $this->fd->html('form.hidden', 'ordering', $states->ordering, '', 'data-fd-table-ordering'); ?>
	<?php echo $this->fd->html('form.hidden', 'direction', $states->direction, '', 'data-fd-table-direction'); ?>
</form>
