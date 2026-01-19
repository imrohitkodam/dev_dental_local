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
						<?php echo JText::_('COM_PP_TABLE_COLUMN_TITLE'); ?>
					</th>

					<th width="10%" class="center">
						<?php echo JText::_('COM_PP_TABLE_COLUMN_USED'); ?>
					</th>

					<th width="10%" class="center">
						<?php echo JText::_('COM_PP_TABLE_COLUMN_STATUS'); ?>
					</th>

					<th width="15%" class="center">
						<?php echo JText::_('COM_PP_TABLE_COLUMN_CODE'); ?>
					</th>

					<th width="20%" class="center">
						<?php echo JText::_('COM_PP_TABLE_COLUMN_TYPE'); ?>
					</th>
					<th width="1%" class="center">
						<?php echo JText::_('COM_PP_TABLE_COLUMN_ID'); ?>
					</th>
				</tr>
			</thead>

			<tbody>
				<?php if ($discounts) { ?>
					<?php $i = 0; ?>
					<?php foreach ($discounts as $discount) { ?>
					<tr>
						<td class="center">
							<?php echo $this->html('grid.id', $i++, $discount->getId()); ?>
						</td>

						<td>
							<a href="<?php echo JRoute::_('index.php?option=com_payplans&view=discounts&layout=form&id=' . $discount->getId());?>"><?php echo $discount->title;?></a>
						</td>

						<td class="center">
							<?php echo $discount->getCounter();?>
						</td>

						<td class="center">
							<?php echo $this->html('grid.published', $discount, 'discounts', 'published'); ?>
						</td>

						<td class="center">
							<?php echo $discount->getCouponCode();?>
						</td>

						<td class="center">
							<?php echo $discount->getCouponTypeLabel();?>
						</td>

						<td class="center">
							<?php echo $discount->getId();?>
						</td>
					</tr>
					<?php } ?>
				<?php } ?>


				<?php if (!$discounts) { ?>
					<?php echo $this->html('grid.emptyBlock', 'COM_PP_DISCOUNTS_BLANK', 7); ?>
				<?php } ?>
			</tbody>

			<?php echo $this->html('grid.pagination', $pagination, 7); ?>

		</table>
	</div>

	<?php echo $this->html('form.action', 'discounts'); ?>
</form>
