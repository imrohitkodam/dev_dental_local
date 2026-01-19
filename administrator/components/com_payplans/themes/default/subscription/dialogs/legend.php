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
<dialog>
	<width>400</width>
	<height>150</height>
	<selectors type="json">
	{
		"{closeButton}" : "[data-close-button]",
		"{submitButton}" : "[data-submit-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{closeButton} click": function() {
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo $title; ?></title>
	<content>
		<div class="row-fluid">
			<table class="table">
				<thead class="well">
					<tr>
						<th class="span5"><h4><?php echo JText::_('COM_PAYPLANS_SUBSCRIPTION_STATUS_NAME');?></h4></th>
						<th class="span7"><h4><?php echo JText::_('COM_PAYPLANS_SUBSCRIPTION_STATUS_DESCRIPTION');?></h4></th>
					</tr>
				</thead>

			<tbody>
				<?php $status = PayplansStatus::getStatusOf('subscription');?>
				<?php foreach ($status as $subscription): ?>
					<tr>
						<td class="span5"><span><strong><?php echo JText::_('COM_PAYPLANS_STATUS_'.$subscription); ?></strong></span></td>
						<td class="span7"><span><?php echo JText::_('COM_PAYPLANS_STATUS_'.$subscription.'_DESC'); ?></span></td>
					</tr>
				<?php endforeach;?>

			</tbody>	
			</table>
		</div>
	</content>
	<buttons>
		<?php echo $this->fd->html('dialog.button', 'COM_EASYBLOG_CANCEL_BUTTON', 'default', ['attributes' => 'data-close-button']); ?>
		<?php echo $this->fd->html('dialog.button', 'COM_EASYBLOG_FEATURE_BUTTON', 'primary', ['attributes' => 'data-submit-button']); ?>
	</buttons>
</dialog>
