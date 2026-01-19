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
					<th width="1%">
						<?php echo $this->html('grid.checkall'); ?>
					</th>
					<th width="10%">
						<?php echo JText::_('COM_PP_TABLE_COLUMN_TITLE'); ?>
					</th>

					<th width="35%">
						<?php echo JText::_('COM_PP_TABLE_COLUMN_DESC'); ?>
					</th>

					<th width="35%"  class="center">
						<?php echo JText::_('COM_PP_TABLE_COLUMN_OVERRIDDEN_LOCATION'); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JText::_('COM_PP_TABLE_COLUMN_PREVIEW'); ?>
					</th>
					<th width="10%" class="center">
						<?php echo JText::_('COM_PP_TABLE_COLUMN_MODIFIED'); ?>
					</th>
				</tr>
			</thead>

			<tbody>
				<?php $i = 0; ?>
				<?php foreach ($files as $file) { ?>
				<tr>
					<td class="center">
						<?php echo $this->html('grid.id', $i, base64_encode($file->relative)); ?>
					</td>
					<td>
						<a href="index.php?option=com_payplans&view=mailer&layout=edit&file=<?php echo base64_encode($file->relative);?>">
							<?php echo $file->name;?>
						</a>
					</td>
					<td>
						<?php echo $file->desc;?>
					</td>
					<td class="center">
						<?php echo $file->override ? str_ireplace(JPATH_ROOT, '', $file->overridePath) : '&mdash;'; ?>
					</td>
					<td class="center">
						<?php if ($file->structure) { ?>
							&mdash;
						<?php } else { ?>
							<a href="javascript:void(0);" data-preview="<?php echo base64_encode($file->relative);?>"><?php echo JText::_('COM_PP_PREVIEW'); ?></a>
						<?php } ?>
					</td>
					<td class="center">
						<?php echo $this->html('grid.published', $file, 'files', 'override', array(), array(0 => 'COM_PP_EMAILS_NOT_MODIFIED', 1 => 'COM_PP_EMAILS_MODIFIED'), array(), false); ?>
					</td>
				</tr>
				<?php $i++; ?>
				<?php } ?>
			</tbody>
		</table>
	</div>
	<?php echo $this->html('form.action', 'mailer'); ?>
</form>
 
