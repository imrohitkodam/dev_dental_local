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
<div class="panel-table">
	<table class="app-table table">
		<thead>
			<tr>
				<th>
					<?php echo JText::_('COM_PP_TABLE_COLUMN_TITLE');?>
				</th>
				<th class="center">
					<?php echo JText::_('COM_PP_TABLE_COLUMN_VALUE'); ?>
				</th>
				<th class="center">
					<?php echo JText::_('COM_PP_TABLE_COLUMN_COUNT'); ?>
				</th>
				<th class="center" width="1%">
					<?php echo JText::_('COM_PP_TABLE_COLUMN_ID'); ?>
				</th>
			</tr>
		</thead>

		<tbody>
			<?php if ($resources) { ?>
				<?php foreach ($resources as $resource) { ?>
				<tr>
					<td>
						<?php echo $resource->title;?>
					</td>

					<td class="center">
						<?php echo $resource->value;?>
					</td>
					<td class="center">
						<?php echo $resource->count; ?>
					</td>
					<td class="center">
						<?php echo $resource->resource_id; ?>
					</td>
				</tr>		
				<?php } ?>
			<?php } ?>

		</tbody>
	</table>
</div>