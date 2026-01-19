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
	<height>250</height>
	<selectors type="json">
	{
		"{cancelButton}": "[data-cancel-button]",
		"{submitButton}": "[data-submit-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{cancelButton} click": function() {
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo JText::_('COM_PP_DIALOG_ATTACHMENT_DELETE_TITLE'); ?></title>
	<content>
		<p>
			<?php echo JText::_('COM_PP_DIALOG_ATTACHMENT_DELETE_DESC');?>
		</p>
	</content>
	<buttons>
		<?php echo $this->fd->html('dialog.button', 'COM_PP_CLOSE_BUTTON', 'default', ['attributes' => 'data-close-button']); ?>
		<?php echo $this->fd->html('dialog.button', 'COM_PP_DELETE_BUTTON', 'primary', ['attributes' => 'data-submit-button']); ?>
	</buttons>
</dialog>