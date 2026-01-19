<?php
/**
* @package		Payplans
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form name="adminForm" id="adminForm" class="importForm" method="POST" data-table-grid enctype="multipart/form-data">
	<div class="grid grid-cols-1 md:grid-cols-12 gap-md">
		<div class="col-span-1 md:col-span-6 w-auto">
			<div class="panel">
				<?php echo $this->fd->html('panel.heading', 'COM_PP_USER_IMPORT_FROM_CSV', 'COM_PP_USER_IMPORT_FROM_CSV_DESC', '/administrators/users/users-import'); ?>

				<div class="panel-body">
					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_PP_USER_IMPORT_SELECT_CSV_FILE', 'user_import_csv'); ?>

						<div class="flex-grow">
							<input type="file" name="user_import_csv" id="user_import_csv" class="input" style="width:265px;" accept=".csv" data-user-import-file />
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_PP_USER_IMPORT_SELECT_PLANS', 'plans'); ?>

						<div class="flex-grow">
							<?php echo $this->html('form.plans', 'plan', '', true, false, ['data-user-import-plans'], [], ['theme' => 'fd']); ?>
						</div>
					</div>

					<?php echo $this->fd->html('settings.dropdown', 'subscription_status', 'COM_PP_USER_IMPORT_SUBSCRIPTION_STATUS', [
						'inherit' => 'Inherit',
						'Active' => 'Active',
						'Expired' => 'Expired',
						'Inactive' => 'Inactive',
						'None' => 'None'
					]); ?>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_PP_USER_IMPORT_SUBSCRIPTION_START_DATE', 'subscription_start_date'); ?>

						<div class="flex-grow">
							<?php echo $this->fd->html('form.datetimepicker', 'subscription_start_date', '', ['enableTime' => false]); ?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_PP_USER_IMPORT_SUBSCRIPTION_EXPIRATION_DATE', 'subscription_expiration_date'); ?>

						<div class="flex-grow">
							<?php echo $this->fd->html('form.datetimepicker', 'subscription_expiration_date', '', ['enableTime' => false]); ?>
						</div>
					</div>

					<?php echo $this->fd->html('settings.textarea', 'subscription_note', 'COM_PP_USER_IMPORT_SUBSCRIPTION_NOTE'); ?>

					<div class="mt-20 text-right">
						<button class="o-btn o-btn--primary" type="submit"><?php echo JText::_('COM_PP_USER_IMPORT_BUTTON');?> &raquo;</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php echo $this->html('form.action', 'user', 'importCSV'); ?>
</form>