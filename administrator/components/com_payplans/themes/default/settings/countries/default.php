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
					<?php if ($this->tmpl != 'component') { ?>
					<th width="1%" class="center">
						<?php echo $this->html('grid.checkall'); ?>
					</th>
					<?php } ?>

					<th>
						<?php echo JText::_('COM_PP_TABLE_COLUMN_TITLE'); ?>
					</th>

					<th width="10%" class="center">
						<?php echo JText::_('COM_PP_TABLE_COLUMN_DEFAULT'); ?>
					</th>

					<th width="10%" class="center">
						<?php echo JText::_('COM_PP_TABLE_COLUMN_PUBLISHED'); ?>
					</th>

					<th width="10%" class="center">
						<?php echo JText::_('ISOCODE 2');?>
					</th>

					<th width="10%" class="center">
						<?php echo JText::_('ISOCODE 3');?>
					</th>

					<th width="1%" class="hidden-phone center">
						<?php echo JText::_('COM_PP_TABLE_COLUMN_ID'); ?>
					</th>
				</tr>
			</thead>

			<tbody>
				<?php $i = 0; ?>
				<?php foreach ($countries as $country) { ?>
				<tr>
					<td class="center">
						<?php echo $this->html('grid.id', $i++, $country->country_id); ?>
					</td>
					<td>
						<?php echo $country->title;?>
					</td>
					<td class="center">
						<?php echo $this->html('grid.featured', $country, 'country', 'default'); ?>
					</td>
					<td class="center">
						<?php echo $this->html('grid.published', $country, 'country', 'published'); ?>
					</td>
					<td class="center">
						<?php echo $country->isocode2;?>
					</td>
					<td class="center">
						<?php echo $country->isocode3;?>
					</td>
					<td class="center">
						<?php echo $country->country_id;?>
					</td>
				</tr>
				<?php } ?>
			</tbody>

			<?php echo $this->html('grid.pagination', $pagination, 6); ?>
		</table>
	</div>

	<?php echo $this->html('form.action', 'country'); ?>
</form>
