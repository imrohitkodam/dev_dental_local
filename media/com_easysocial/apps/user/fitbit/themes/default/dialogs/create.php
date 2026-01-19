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
	<height>250</height>
	<selectors type="json">
	{
		"{save}": "[data-save-button]",
		"{cancel}": "[data-cancel-button]",
		"{form}": "[data-fitbit-form]"
	}
	</selectors>
	<bindings type="javascript">
	{
		"{cancel} click": function() {
			this.parent.close();
		},

		"{save} click": function() {
			this.form().submit();
		}
	}
	</bindings>
	<title><?php echo JText::_('APP_FITBIT_FORM_CREATE_TITLE'); ?></title>
	<content>
		<div>
			<p style="margin-bottom: 40px;"><?php echo JText::_('APP_FITBIT_FORM_CREATE_INFO'); ?></p>

			<form method="post" data-fitbit-form>
				<div class="o-form-horizontal">
					<div class="o-form-group">
						<?php echo $this->html('form.label', 'APP_USER_FITBIT_FORM_DATE'); ?>

						<div class="o-control-input">
							<?php echo $this->html('form.calendar', 'date', JFactory::getDate()->format('d-m-Y'), 'date', '', false, 'DD-MM-YYYY', false, false); ?>
						</div>
					</div>

					<div class="o-form-group">
						<?php echo $this->html('form.label', 'APP_USER_FITBIT_FORM_STEPS'); ?>

						<div class="o-control-input">
							<input type="number" id="steps" name="steps" placeholder="5" step="1" class="o-form-control" />
						</div>
					</div>
				</div>

				<?php echo $this->html('form.action', 'apps', 'controller'); ?>
				<?php echo $this->html('form.hidden', 'appController', 'fitbit'); ?>
				<?php echo $this->html('form.hidden', 'appTask', 'save'); ?>
				<?php echo $this->html('form.hidden', 'appId', $appId); ?>
			</form>
		</div>
	</content>
	<buttons>
		<button type="button" class="btn btn-es-default" data-cancel-button><?php echo JText::_('COM_ES_CANCEL'); ?></button>
		<button type="button" class="btn btn-es-primary" data-save-button><?php echo JText::_('Save'); ?></button>
	</buttons>
</dialog>
