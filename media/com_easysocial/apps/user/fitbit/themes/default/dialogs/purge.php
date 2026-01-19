<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<dialog>
	<width>450</width>
	<height>150</height>
	<selectors type="json">
	{
		"{purge}": "[data-purge-button]",
		"{cancel}": "[data-cancel-button]",
		"{form}": "[data-fitbit-form]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{cancel} click": function() {
			this.parent.close();
		},

		"{purge} click": function() {
			this.form().submit();
		}
	}
	</bindings>
	<title><?php echo JText::_('APP_FITBIT_PURGE_LOGS'); ?></title>
	<content>
		<div>
			<p style="margin-bottom: 40px;"><?php echo JText::_('APP_FITBIT_PURGE_LOGS_INFO'); ?></p>

			<form method="post" data-fitbit-form>
				<?php echo $this->html('form.action', 'apps', 'controller'); ?>
				<?php echo $this->html('form.hidden', 'appController', 'fitbit'); ?>
				<?php echo $this->html('form.hidden', 'appTask', 'purge'); ?>
				<?php echo $this->html('form.hidden', 'appId', $appId); ?>
			</form>
		</div>
	</content>
	<buttons>
		<button type="button" class="btn btn-es-default" data-cancel-button><?php echo JText::_('COM_ES_CANCEL'); ?></button>
		<button type="button" class="btn btn-es-danger" data-purge-button><?php echo JText::_('APP_FITBIT_PURGE'); ?></button>
	</buttons>
</dialog>
