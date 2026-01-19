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
	<width>450</width>
	<height>200</height>
	<selectors type="json">
	{
		"{closeButton}" : "[data-close-button]",
		"{submitButton}" : "[data-submit-button]",
		"{form}": "[data-submit-form]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{closeButton} click": function() {
			this.parent.close();
		},

		"{submitButton} click": function(element) {
			this.form().submit();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_PAYPLANS_USER_DELETE_CONFIRM_WINDOW_TITLE'); ?></title>
	<content>
		<form action="<?php echo JRoute::_('index.php');?>" method="post" data-submit-form>
			<p>
				<?php echo JText::_('COM_PAYPLANS_USER_DELETE_CONFIRM_WINDOW_MSG'); ?>
			</p>
			<?php echo $this->html('form.action', 'user', 'deleteUser'); ?>
			<?php echo $this->html('form.hidden', 'user_id', $userId); ?>
		</form>
	</content>
	<buttons>
		<?php echo $this->fd->html('dialog.button', 'COM_PP_CLOSE_BUTTON', 'default', ['attributes' => 'data-close-button']); ?>
		<?php echo $this->fd->html('dialog.button', 'COM_PP_YES_BUTTON', 'danger', ['attributes' => 'data-submit-button']); ?>
	</buttons>
</dialog>