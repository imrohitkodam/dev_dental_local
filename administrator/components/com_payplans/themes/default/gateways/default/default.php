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
<form action="index.php?option=com_payplans&view=gateways<?php echo $states->type ? '&paymentype=' . $states->type : '';?>" method="post" name="adminForm" id="adminForm" data-fd-grid>
	<div class="app-filter-bar">
		<?php echo $this->fd->html('filter.search', $states->search, 'search'); ?>

		<?php echo $this->fd->html('filter.published', 'published', $states->published, ['selectText' => 'COM_PP_SELECT_STATE', 'valueType' => 'numeric']); ?>

		<?php echo $this->fd->html('filter.lists', 'type', $paymentTypes,  $states->type, ['initial' => 'COM_PP_FILTER_SELECT_TYPE', 'identicalMatch' => true]); ?>
		
		<?php echo $this->fd->html('filter.limit', $states->limit); ?>
	</div>

	<div class="panel-table">
		<table class="app-table table">
			<thead>
				<tr>
					<th width="1%" class="t-text--center">
						<?php echo $this->html('grid.checkAll'); ?>
					</th>
					
					<th>
						<?php echo JText::_('COM_PP_TABLE_COLUMN_TITLE'); ?>
					</th>
					
					<th width="15%" class="t-text--center">
						<?php echo JText::_('COM_PP_TABLE_COLUMN_STATE'); ?>
					</th>

					<th width="20%" class="t-text--center">
						<?php echo JText::_('COM_PP_TABLE_COLUMN_TYPE'); ?>
					</th>

					<?php if ($states->type == 'all') { ?>
					<th width="5%" class="t-text--center">
						<?php echo $this->html('grid.sort', 'ordering', JText::_('COM_PP_TABLE_COLUMN_ORDERING'), $states); ?>
						<?php echo $this->html('grid.order', $apps, 'gateways'); ?>
					</th>
					<?php } ?> 

					<th width="5%" class="t-text--center">
						<?php echo JText::_('COM_PP_TABLE_COLUMN_ID'); ?>
					</th>
				</tr>
			</thead>

			<tbody>
				<?php if ($apps) { ?>
					<?php $i = 0; ?>
					<?php foreach ($apps as $app) { ?>
					<tr>
						<td class="t-text--center">
							<?php echo $this->html('grid.id', $i, $app->app_id); ?>
						</td>
						<td>
							<a href="<?php echo JRoute::_('index.php?option=com_payplans&view=gateways&layout=form&id=' . $app->app_id);?>"><?php echo JText::_($app->title);?></a>
						</td>
						<td class="t-text--center">
							<?php echo $this->html('grid.published', $app, 'app', 'published'); ?>
						</td>
						<td class="t-text--center"> 
							<?php echo $app->type;?>
						</td>
						<?php if ($states->type == 'all') { ?>
						<td class="center order"> 
							<?php $current = $i + 1;?>

							<?php echo $this->fd->html('table.order', $pagination, 'order', $i, $saveOrder, [
									'rowIndex' => $i,
									'showOrderUpIcon' => $current !== 1,
									'showOrderDownIcon' => $current !== count($apps),
									'orderUpTask' => 'gateways.moveUp',
									'orderDownTask' => 'gateways.moveDown',
									'accessControl' => $states->ordering,
									'total' => count($apps)
								]); ?>
						</td>
						<?php } ?>
						<td class="t-text--center">
							<?php echo $app->app_id;?>
						</td>
					</tr>
					<?php $i++; ?>
					<?php } ?>
				<?php } else { ?>
					<?php echo $this->html('grid.emptyBlock', 'COM_PP_GATEWAYS_EMPTY', 5); ?>
				<?php } ?>
			</tbody>

			<tfoot>
				<?php echo $this->html('grid.pagination', $pagination, 5); ?>
			</tfoot>
		</table>
	</div>

	<?php echo $this->html('form.action', 'subscription'); ?>
	<?php echo $this->fd->html('form.hidden', 'ordering', $states->ordering, '', 'data-fd-table-ordering'); ?>
	<?php echo $this->fd->html('form.hidden', 'direction', $states->direction, '', 'data-fd-table-direction'); ?>
	<?php echo $this->html('form.returnUrl', 'return'); ?>
</form>
 
