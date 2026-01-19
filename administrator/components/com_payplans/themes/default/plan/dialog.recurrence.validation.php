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
	<width>800</width>
	<height>350</height>
	<selectors type="json">
	{
		"{cancelButton}"  : "[data-cancel-button]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{cancelButton} click": function()
		{
			this.parent.close();
		}
	}
	</bindings>
	<title><?php echo JText::_( 'COM_PP_PLAN_EDIT_RECURRENCE_VALIDATION_TITLE' ); ?></title>
	<content>
		<p><?php echo $content; ?></p>
	</content>
	<buttons>
		<?php echo $this->fd->html('dialog.button', 'COM_PAYPLANS_AJAX_CANCEL_BUTTON', 'default', ['attributes' => 'data-cancel-button']); ?>
	</buttons>
</dialog>