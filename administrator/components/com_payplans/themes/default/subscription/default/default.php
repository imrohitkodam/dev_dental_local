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

		<div class="app-filter-bar__cell app-filter-bar__cell--divider-left">
			<div class="app-filter-bar__search-input-group">
				<?php echo $this->fd->html('filter.daterange', $states->dateRange, 'dateRange', 'COM_PP_FILTER_SUBSCRPTION_DATE'); ?>
			</div>
		</div>

		<?php echo $this->html('filter.plans', 'plan_id', $states->plan_id, []); ?>
		
		<?php echo $this->html('filter.status', 'status', $states->status, 'subscription', '', ['none' => 'COM_PAYPLANS_STATUS_SELECT']); ?>

		<?php echo $this->fd->html('filter.spacer'); ?>

		<?php echo $this->fd->html('filter.limit', $states->limit); ?>
	</div>
	
	<div class="panel-table">
		<table class="app-table table" data-table>
			<thead>
				<tr>
					<?php if ($this->tmpl != 'component') { ?>
					<th class="center"  width="1%">
						<?php echo $this->html('grid.checkAll'); ?>
					</th>
					<?php } ?>

					<th width="10%">
						<?php echo JText::_('COM_PP_TABLE_COLUMN_SUBSCRIPTION'); ?>
					</th>

					<th class="center">
						<?php echo $this->fd->html('table.sort', 'COM_PP_TABLE_COLUMN_USER', 'name', $states->ordering, $states->direction); ?>
					</th>

					<th width="15%" class="center">
						<?php echo $this->fd->html('table.sort', 'COM_PP_TABLE_COLUMN_PLAN', 'plan_id', $states->ordering, $states->direction); ?>
					</th>

					<th width="10%" class="center">
						<?php echo $this->fd->html('table.sort', 'COM_PP_TABLE_COLUMN_AMOUNT', 'total', $states->ordering, $states->direction); ?>
					</th>

					<th width="10%" class="center">
						<?php echo $this->fd->html('table.sort', 'COM_PP_TABLE_COLUMN_STATE', 'status', $states->ordering, $states->direction); ?>
					</th>

					<?php if ($this->tmpl != 'component') { ?>
					
					<th width="15%" class="center">
						<?php echo $this->fd->html('table.sort', 'COM_PP_TABLE_COLUMN_EXPIRE_DATE', 'expiration_date', $states->ordering, $states->direction); ?>
					</th>
					<?php } ?>

					<?php if ($this->tmpl != 'component') { ?>
					<th class="center" width="5%">
						<?php echo $this->fd->html('table.sort', 'COM_PP_TABLE_COLUMN_ID', 'subscription_id', $states->ordering, $states->direction); ?>
					</th>
					<?php } ?>
				</tr>
			</thead>

			<tbody>
				<?php if ($subscriptions) { ?>
					<?php $i = 0; ?>
					<?php foreach ($subscriptions as $subscription) { ?>
					<tr>
						<?php if ($this->tmpl != 'component') { ?>
						<td class="center">
							<?php echo $this->html('grid.id', $i, $subscription->getId()); ?>
						</td>
						<?php } ?>

						<td>
							<a href="index.php?option=com_payplans&view=subscription&layout=form&id=<?php echo $subscription->getId();?>"
								data-pp-row
								data-id="<?php echo $subscription->getId();?>"
								data-title="<?php echo $subscription->getTitle();?>"
								data-order-id="<?php echo $subscription->order->getId();?>"
								>
								<?php echo $subscription->getKey(); ?>
							</a>
						</td>

						<td class="center">
							<?php echo $subscription->buyer->getName();?> (<?php echo $subscription->buyer->getEmail();?>)
						</td>

						<td class="center">
							<?php echo JText::_($subscription->getTitle());?>
						</td>

						<td class="center">
							<?php echo $this->html('html.amount', $subscription->getTotal(), $subscription->order->getCurrency()); ?>
						</td>

						<td class="center whitespace-nowrap">
							<?php echo $this->fd->html('label.standard', $subscription->getLabel(), $subscription->getStatusLabelClass()); ?>
						</td>

						<?php if ($this->tmpl != 'component') { ?>

						<td class="center">
							<?php if ($subscription->getExpirationDate()) { ?>
								<?php echo PP::date($subscription->expiration_date, true)->toDisplay(JText::_('DATE_FORMAT_LC2')); ?>
							<?php } else { ?>
								&mdash;
							<?php } ?>
						</td>
						
						<td class="center">
							<?php echo $subscription->getId(); ?>
						</td>
						<?php } ?>
					</tr>
					<?php $i++; ?>
					<?php } ?>
				<?php } ?>


				<?php if (!$subscriptions) { ?>
					<?php echo $this->html('grid.emptyBlock', 'COM_PP_SUBSCRIPTIONS_EMPTY', 9); ?>
				<?php } ?>
			</tbody>

			<?php echo $this->html('grid.pagination', $pagination, 9); ?>
		</table>
	</div>

	<?php echo $this->html('form.action', 'subscription'); ?>
	<?php echo $this->fd->html('form.hidden', 'ordering', $states->ordering, '', 'data-fd-table-ordering'); ?>
	<?php echo $this->fd->html('form.hidden', 'direction', $states->direction, '', 'data-fd-table-direction'); ?>
</form>