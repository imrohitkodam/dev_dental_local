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
		 <?php echo $this->fd->html('filter.search', $states->search); ?>

		<?php if ($this->tmpl != 'component') { ?>
			<?php echo $this->html('filter.plans', 'plan_id', $states->plan_id, ['without' => true]); ?>
		<?php } ?>

		<?php echo $this->fd->html('filter.spacer'); ?>

		<?php echo $this->fd->html('filter.limit', $states->limit); ?>
	</div>

	<div class="panel-table">
		<table class="app-table table">
			<thead>
				<tr>
					<?php if ($this->tmpl != 'component') { ?>
						<th width="1%" class="center">
							<?php echo $this->fd->html('table.checkAll'); ?>
						</th>
					<?php } ?>

					<th>
						<?php echo $this->fd->html('table.sort', 'COM_PP_TABLE_COLUMN_NAME', 'name', $states->ordering, $states->direction); ?>
					</th>

					<th width="15%">
						<?php echo $this->fd->html('table.sort', 'COM_PP_TABLE_COLUMN_USERNAME', 'username', $states->ordering, $states->direction); ?>
					</th>

					<th width="15%">
						<?php echo $this->fd->html('table.sort', 'COM_PP_TABLE_COLUMN_EMAIL', 'email', $states->ordering, $states->direction); ?>
					</th>

					<?php if ($this->tmpl != 'component') { ?>
					<th class="center" width="10%">
						<?php echo JText::_('COM_PP_TABLE_COLUMN_PLANS');?>
					</th>
					<?php } ?>

					<th width="5%" class="center">
						<?php echo $this->fd->html('table.sort', 'COM_PP_TABLE_COLUMN_ID', 'id', $states->ordering, $states->direction); ?>
					</th>
				</tr>
			</thead>

			<tbody>
				<?php if ($users) { ?>
					<?php $i = 0; ?>
					<?php foreach ($users as $user) { ?>
					<tr>
						<?php if ($this->tmpl != 'component') { ?>
							<th class="center">
								<?php echo $this->fd->html('table.id', $i, $user->getId()); ?>
							</th>
						<?php } ?>

						<td>
							<a href="index.php?option=com_payplans&view=user&layout=form&id=<?php echo $user->getId();?>" 
								data-pp-user-item
								data-title="<?php echo $this->html('string.escape', $user->getDisplayName());?>"
								data-id="<?php echo $user->getId();?>"
							>
								<?php echo $user->getDisplayName();?>
							</a>
						</td>

						<td>
							<?php echo $user->getUserName();?>
						</td>

						<td>
							<?php echo $user->getEmail();?>
						</td>

						<?php if ($this->tmpl != 'component') { ?>
							<?php $totalPlans = 0; ?>
							<?php foreach ($user->getSubscriptions() as $subscription) { ?>
								<?php $order = $subscription->getOrder(); ?>
								<?php if ($order->getStatus() != 0 ) { ?>
									<?php $totalPlans++; ?>
								<?php } ?>
							<?php } ?>
							<td class="center">
								<a href="index.php?option=com_payplans&view=user&layout=form&activeTab=subscriptions&id=<?php echo $user->getId();?>">
									<?php echo JText::sprintf('COM_PP_VIEW_PLANS', $totalPlans);?>
								</a>
							</td>
						<?php } ?>
						
						<td class="center">
							<?php echo $user->getId();?>
						</td>
					</tr>
					<?php $i++; ?>
					<?php } ?>
				<?php } ?>

				<?php if (!$users) { ?>
					<?php echo $this->html('grid.emptyBlock', 'COM_PAYPLANS_ADMIN_BLANK_USER_MSG', 9); ?>
				<?php } ?>
			</tbody>

			<?php echo $this->html('grid.pagination', $pagination, 9); ?>
		</table>
	</div>

	<?php echo $this->html('form.action', 'subscription'); ?>
	<?php echo $this->fd->html('form.hidden', 'ordering', $states->ordering, '', 'data-fd-table-ordering'); ?>
	<?php echo $this->fd->html('form.hidden', 'direction', $states->direction, '', 'data-fd-table-direction'); ?>
	<?php echo $this->html('form.hidden', 'apply_plan_id', '', ['data-apply-plan-id' => '']); ?>
</form>