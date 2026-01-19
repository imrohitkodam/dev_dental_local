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

		<?php echo $this->html('filter.apps', 'type', $states->type, 'app', array(), array(), array('typeAsTitle' => true)); ?>

		<?php echo $this->fd->html('filter.spacer'); ?>

		<?php echo $this->fd->html('filter.limit', $states->limit); ?>
	</div>

	<div class="panel-table">
		<table class="app-table table">
			<thead>
				<tr>
				   <th width="1%" class="center">
						<?php echo $this->html('grid.checkAll'); ?>
					</th>
					
					<th>
						<?php echo JText::_('COM_PP_TABLE_COLUMN_TITLE'); ?>
					</th>
					
					<th width="15%" class="center">
						<?php echo JText::_('COM_PP_TABLE_COLUMN_STATE'); ?>
					</th>

					<th width="20%" class="center">
						<?php echo JText::_('COM_PP_TABLE_COLUMN_TYPE'); ?>
					</th>

					<th width="1%" class="center">
						<?php echo $this->html('grid.sort', 'app_id', 'COM_PP_TABLE_COLUMN_ID', $states->direction, $states->ordering); ?>
					</th>
				</tr>
			</thead>

			<tbody>
				<?php if ($apps) { ?>
					<?php $i = 0; ?>
					<?php foreach ($apps as $app) { ?>
					<tr>
						<td class="center">
							<?php echo $this->html('grid.id', $i++, $app->app_id); ?>
						</td>
						<td>
							<a href="<?php echo JRoute::_('index.php?option=com_payplans&view=app&layout=form&id=' . $app->app_id);?>"><?php echo JText::_($app->title);?></a>
						</td>
						<td class="center">
							<?php echo $this->html('grid.published', $app, 'app', 'published'); ?>
						</td>
						<td class="center"> 
							<?php echo $app->type;?>
						</td>
						<td>
							<?php echo $app->app_id;?>
						</td>
					</tr>
					<?php } ?>
				<?php } else { ?>
					<?php echo $this->html('grid.emptyBlock', 'COM_PP_APPS_EMPTY', 5); ?>
				<?php } ?>
			</tbody>

			<tfoot>
				<?php echo $this->html('grid.pagination', $pagination, 5); ?>
			</tfoot>
		</table>
	</div>

	<?php echo $this->html('form.action', 'subscription'); ?>
	<?php echo $this->html('form.hidden', 'ordering', $states->ordering); ?>
	<?php echo $this->html('form.hidden', 'direction', $states->direction); ?>
	<?php echo $this->html('form.returnUrl'); ?>
</form>
 
