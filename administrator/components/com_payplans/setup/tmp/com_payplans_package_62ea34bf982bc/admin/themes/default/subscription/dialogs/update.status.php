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
	<width>500</width>
	<height>280</height>
	<selectors type="json">
	{
		"{close}": "[data-close-button]",
		"{form}": "[data-dialog-form]",
		"{submit}": "[data-submit-button]",
		"{statusInput}": "[data-update-status-input]",
		"{messageWrapper}": "[data-message-wrapper]",
		"{noneMessage}": "[data-none-message]",
		"{activeMessage}": "[data-active-message]",
		"{inactiveMessage}": "[data-inactive-message]",
		"{expireMessage}": "[data-expire-message]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{close} click": function() {
			this.parent.close();
		},

		"{submit} click": function() {
			this.form().submit();
		},

		"{statusInput} change": function(element) {
			var value = element.val();

			this.messageWrapper().addClass('t-hidden');
			this.activeMessage().addClass('t-hidden');
			this.inactiveMessage().addClass('t-hidden');

			if (value == <?php echo PP_SUBSCRIPTION_ACTIVE;?>) {
				this.activeMessage().removeClass('t-hidden');
				this.messageWrapper().removeClass('t-hidden');
			}

			if (value == <?php echo PP_SUBSCRIPTION_HOLD;?>) {
				this.inactiveMessage().removeClass('t-hidden');
				this.messageWrapper().removeClass('t-hidden');
			}
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_PP_UPDATE_STATUS_DIALOG_TITLE'); ?></title>
	<content>
		<form action="<?php echo JRoute::_('index.php');?>" method="post" class="o-form-horizontal" data-dialog-form>
			<p class="t-lg-mb--xl">
				<?php echo JText::_('COM_PP_UPDATE_STATUS_DIALOG_INFORMATION');?>
			</p>

			<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md rounded-md">
				<?php echo $this->fd->html('form.label', 'COM_PP_UPDATE_STATUS_NEW_STATUS', 'status'); ?>
				<div class="flex-grow">
					<?php echo $this->html('form.status', 'status', '', 'subscription', 'status', false, array('data-update-status-input' => ""), array(PP_SUBSCRIPTION_NONE)); ?>
				</div>
			</div>

			<div class="o-form-group o-alert o-alert--warning t-lg-mt--xl t-hidden" data-message-wrapper>
				<div class="t-hidden" data-active-message>
					<?php echo JText::_('COM_PP_UPDATE_STATUS_ACTIVE'); ?>
				</div>

				<div class="t-hidden" data-inactive-message>
					<?php echo JText::_('COM_PP_UPDATE_STATUS_INACTIVE'); ?>
				</div>
			</div>

			<?php echo $this->html('form.ids', 'cid', $ids); ?>
			<?php echo $this->html('form.action', 'subscription', 'updateStatus'); ?>
		</form>
	</content>
	<buttons>
		<?php echo $this->fd->html('dialog.button', 'COM_PP_CANCEL_BUTTON', 'default', ['attributes' => 'data-close-button']); ?>
		<?php echo $this->fd->html('dialog.button', 'COM_PP_UPDATE_BUTTON', 'primary', ['attributes' => 'data-submit-button']); ?>
	</buttons>
</dialog>