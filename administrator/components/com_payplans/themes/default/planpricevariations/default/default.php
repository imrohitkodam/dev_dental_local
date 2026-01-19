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

	<div class="panel-table">
		<table class="app-table table">
			<thead>
				<tr>
					<th width="1%" class="center">
						<?php echo $this->html('grid.checkall'); ?>
					</th>

					<th width="25%">
						<?php echo JText::_('COM_PP_TABLE_COLUMN_TITLE'); ?>
					</th>

					<th>
						<?php echo JText::_('COM_PP_TABLE_COLUMN_DESCRIPTION'); ?>
					</th>

					<th width="20%" class="center">
						<?php echo JText::_('COM_PP_TABLE_COLUMN_STATUS'); ?>
					</th>

					<th width="1%" class="center">
						<?php echo JText::_('COM_PP_TABLE_COLUMN_ID'); ?>
					</th>
				</tr>
			</thead>

			<tbody>
				<?php if ($planPriceVariations) { ?>
					<?php $i = 0; ?>
					<?php foreach ($planPriceVariations as $planPriceVariation) { ?>
					<tr>
						<td class="center">
							<?php echo $this->html('grid.id', $i++, $planPriceVariation->app_id); ?>
						</td>

						<td>
							<a href="<?php echo JRoute::_('index.php?option=com_payplans&view=planpricevariations&layout=form&id=' . $planPriceVariation->app_id);?>"><?php echo $planPriceVariation->title;?></a>
						</td>

						<td>
							<?php if ($planPriceVariation->description) { ?>
								<?php echo $planPriceVariation->description;?>
							<?php } else { ?>
								&mdash;
							<?php } ?>
						</td>
						
						<td class="center">
							<?php echo $this->html('grid.published', $planPriceVariation, 'planpricevariations', 'published');?>
						</td>

						<td class="center">
							<?php echo $planPriceVariation->app_id;?>
						</td>
					</tr>
					<?php } ?>
				<?php } ?>


				<?php if (!$planPriceVariations) { ?>
					<?php echo $this->html('grid.emptyBlock', 'COM_PP_PLAN_PRICE_VARIATIONS_BLANK', 5, true); ?>
				<?php } ?>
			</tbody>

			<?php echo $this->html('grid.pagination', $pagination, 5); ?>

		</table>
	</div>

	<?php echo $this->html('form.action', 'planpricevariations'); ?>


</form>
