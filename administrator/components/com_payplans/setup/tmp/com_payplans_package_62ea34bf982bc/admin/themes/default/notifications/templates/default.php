<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
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
					<th>
						<?php echo JText::_('COM_PP_TABLE_COLUMN_FILE'); ?>
					</th>
					<th width="60%" class="center">
						<?php echo JText::_('COM_PP_TABLE_COLUMN_OVERRIDDEN_LOCATION'); ?>
					</th>
				</tr>
			</thead>

			<tbody>
				<?php if ($files) { ?>
					<?php $i = 0; ?>
					<?php foreach ($files as $file) { ?>
					<tr>
						<td>
							<a href="index.php?option=com_payplans&view=notifications&layout=editFile&file=<?php echo urlencode($file->name);?>"><?php echo $file->name;?></a>
						</td>
						<td class="center">
							<?php echo $file->override ? str_ireplace(JPATH_ROOT, '', $file->overridePath) : '&mdash;'; ?>
						</td>
					</tr>
					<?php } ?>
				<?php } ?>
			</tbody>
		</table>
	</div>

	<?php echo $this->html('form.action', 'notifications'); ?>
	<?php echo $this->html('form.returnUrl'); ?>
</form>
 
