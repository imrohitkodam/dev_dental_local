<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
JHtml::script('administrator/components/com_tjlms/assets/js/tjlmsvalidator.js');
?>
<button class="btn" type="button" onclick="document.getElementById('batch-group-id').value=''" data-dismiss="modal">
	<?php echo JText::_('JCANCEL'); ?>
</button>
<button class="btn btn-success" type="button" onclick="valid_dates('batch_start_date','batch_due_date','batchAssign');">
	<?php echo JText::_('COM_TJLMS_JTOOLBAR_BATCH_ASSIGN'); ?>
</button>
