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
				<?php echo $this->fd->html('filter.daterange', $states->dateRange, 'dateRange', 'COM_PP_FILTER_PAYMENT_DATE'); ?>
			</div>
		</div>
		
		<?php echo $this->html('filter.plans', 'plan_id', $states->plan_id, []); ?>

		<?php echo $this->html('filter.status', 'status', $states->status, 'invoice', 'none', ['none' => 'COM_PAYPLANS_STATUS_SELECT']); ?>

		<?php echo $this->fd->html('filter.limit', $states->limit); ?>
	</div>

	<div class="panel-table">
		<table class="app-table table">
			<thead>
				<tr>
					<?php if ($this->tmpl != 'component') { ?>
					<th width="1%" class="center">
						<?php echo $this->html('grid.checkall'); ?>
					</th>
					<?php } ?>

					<th width="10%">
						<?php echo $this->fd->html('table.sort', 'COM_PP_TABLE_COLUMN_INVOICE', 'invoice_id', $states->ordering, $states->direction); ?>
					</th>

					<?php if ($this->tmpl != 'component') { ?>
					<th class="center" width="10%">
						<?php echo $this->fd->html('table.sort', 'COM_PP_INVOICE_INVOICE_SERIAL', 'serial', $states->ordering, $states->direction); ?>
					</th>
					<?php } ?>
					
					<th class="center" width="20%">
						<?php echo $this->fd->html('table.sort', 'COM_PP_TABLE_COLUMN_USER', 'name', $states->ordering, $states->direction); ?>
					</th>
					
					<th class="center" width="10%">
						<?php echo $this->fd->html('table.sort', 'COM_PP_TABLE_COLUMN_SUBTOTAL', 'subtotal', $states->ordering, $states->direction); ?>
					</th>
					
					<th class="center" width="10%">
						<?php echo $this->fd->html('table.sort', 'COM_PP_TABLE_COLUMN_TOTAL', 'total', $states->ordering, $states->direction); ?>
					</th>
					
					<th class="center" width="5%">
						<?php echo $this->fd->html('table.sort', 'COM_PP_TABLE_COLUMN_STATE', 'status', $states->ordering, $states->direction); ?>
					</th>
					
					<?php if ($this->tmpl != 'component') { ?>
					<th class="center" width="20%">
						<?php echo $this->fd->html('table.sort', 'COM_PP_TABLE_COLUMN_PAYMENT_DATE', 'paid_date', $states->ordering, $states->direction); ?>
					</th>

					<th class="center" width="5%">
						<?php echo $this->fd->html('table.sort', 'COM_PP_TABLE_COLUMN_ID', 'invoice_id', $states->ordering, $states->direction); ?>
					</th>
					<?php } ?>
				</tr>
			</thead>

			<tbody>
				<?php if ($invoices) { ?>
					<?php $i = 0; ?>
					<?php foreach ($invoices as $invoice) { ?>
					<tr>
						<?php if ($this->tmpl != 'component') { ?>
						<th class="center">
							<?php echo $this->html('grid.id', $i, $invoice->getId()); ?>
						</th>
						<?php } ?>

						<td>
							<a href="index.php?option=com_payplans&view=invoice&layout=form&id=<?php echo $invoice->getId();?>" data-pp-row data-id="<?php echo $invoice->getId();?>">
								<?php echo $invoice->getKey();?>
							</a>
						</td>

						<?php if ($this->tmpl != 'component') { ?>
						<td class="center">
							<?php echo $invoice->getSerial();?>
						</td>
						<?php } ?>

						<td class="hidden-phone center">
							<a href="index.php?option=com_payplans&view=user&layout=form&id=<?php echo $invoice->buyer->id;?>"><?php echo $invoice->buyer->getName();?></a> (<?php echo $invoice->buyer->getEmail();?>)
						</td>

						<td class="center">
							<?php echo $invoice->getSubtotal();?>
						</td>

						<td class="center">
							<?php echo $invoice->getTotal();?>
						</td>

						<td class="hidden-phone center whitespace-nowrap">
							<?php echo $this->fd->html('label.standard', $invoice->getStatusName(), $invoice->getStatusLabelClass()); ?>
						</td>

						<?php if ($this->tmpl != 'component') { ?>
						<td class="hidden-phone center">
							<?php if ($invoice->isPaid()) { ?>
								<?php echo PP::date($invoice->paid_date, true)->toDisplay(JText::_('DATE_FORMAT_LC2')); ?>
							<?php } else { ?>
								&mdash;
							<?php } ?>
						</td>

						<td class="pp-word-wrap center">
							<?php echo $invoice->getId();?>
						</td>
						<?php } ?>
					</tr>
					<?php $i++; ?>
					<?php } ?>
				<?php } ?>


				<?php if (!$invoices) { ?>
					<?php echo $this->html('grid.emptyBlock', 'COM_PP_INVOICES_EMPTY', 9); ?>
				<?php } ?>
			</tbody>

			<?php echo $this->html('grid.pagination', $pagination, 9); ?>
		</table>
	</div>

	<?php echo $this->html('form.action', 'invoice'); ?>
	<?php echo $this->fd->html('form.hidden', 'ordering', $states->ordering, '', 'data-fd-table-ordering'); ?>
	<?php echo $this->fd->html('form.hidden', 'direction', $states->direction, '', 'data-fd-table-direction'); ?>
</form>
