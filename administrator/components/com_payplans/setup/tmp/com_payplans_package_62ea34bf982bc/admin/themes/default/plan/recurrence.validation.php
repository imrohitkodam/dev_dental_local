<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="pp-recurr-validation row-fluid">
	<table class="app-table table">
		<thead>
			<tr>
				<th width="10%"><?php echo JText::_('COM_PP_PLAN_EDIT_RECURRENCE_APP_NAME');?></th>
				<th width="10%" class="hidden-phone center"><?php echo JText::_('COM_PP_PLAN_EDIT_RECURRENCE_UNIT');?></th>
				<th width="10%" class="hidden-phone center"><?php echo JText::_('COM_PAYPLANS_PLANS_EDIT_RECURRENCE_PERIOD');?></th>
				<th width="25%" class="center"><?php echo JText::_('COM_PAYPLANS_PLANS_EDIT_RECURRENCE_COUNT');?></th>
				<th width="45%" class="hidden-phone center"><?php echo JText::_('COM_PAYPLANS_PLANS_EDIT_RECURRING_MESSAGE');?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($time as $app => $recurringTime) { ?>
				<tr>
					<td class=""><?php echo $app."\t"; ?></td>
					<td class="hidden-phone center"><?php echo $recurringTime['period']."\t"; ?></td>
					<td class="hidden-phone center"><?php echo $recurringTime['unit']."\t";	?></td>
					<td class="center"><?php echo $recurringTime['frequency']."\t";	?></td>
					<td class="hidden-phone"><?php if(isset($recurringTime['message'])){echo $recurringTime['message']."\t";} ?></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
	<div class="o-alert o-alert--info pp-recurr-validation-value row-fluid">
		<p><?php echo JText::_('COM_PAYPLANS_PLANS_EDIT_RECURRENCE_COUNT_MSG');?></p>
		<code><strong><?php echo JText::_('COM_PAYPLANS_NA')?></strong></code>&nbsp;&nbsp;<span ><?php echo JText::_('COM_PAYPLANS_NOT_APPLICABLE');?></span>
	</div>
</div>
<?php

