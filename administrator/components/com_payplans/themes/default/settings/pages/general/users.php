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
<div class="grid grid-cols-1 md:grid-cols-12 gap-md">
	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_CONFIG_GENERAL_FEATURES'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'user_edit_preferences', 'COM_PP_ALLOW_USER_EDIT_PREFERENCES'); ?>
				<?php echo $this->fd->html('settings.toggle', 'user_edit_customdetails', 'COM_PP_ALLOW_USER_EDIT_CUSTOMDETAILS'); ?>
				<?php echo $this->fd->html('settings.toggle', 'user_delete_orders', 'COM_PP_ALLOW_USER_DELETE_INCOMPLETE_ORDER'); ?>
				<?php echo $this->fd->html('settings.dropdown', 'users_avatar_source', 'COM_PP_SETTINGS_USERS_AVATAR_SOURCE', [
					'default' => 'Default',
					'easysocial' => 'EasySocial',
					'easyblog' => 'EasyBlog',
					'easydiscuss' => 'EasyDiscuss',
					'gravatar' => 'Gravatar'
				]); ?>
				<?php echo $this->fd->html('settings.toggle', 'user_delete_account', 'COM_PP_ALLOW_USER_TO_DELETE_ACCOUNT'); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_CONFIG_ACCOUNT_DOWNLOADS'); ?>
			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'users_download', 'COM_PP_ALLOW_USER_DOWNLOAD_DATA'); ?>
				<?php echo $this->fd->html('settings.text', 'users_download_expiry', 'COM_PP_USER_DOWNLOAD_EXPIRY', '', [
					'postfix' => 'Days',
					'size' => 5
				], '', 'text-center'); ?>
			</div>
		</div>
	</div>

	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_CONFIG_EASYSOCIAL'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'integrate_es_custom_fields', 'COM_PP_INTEGRATE_EASYSOCIAL_CUSTOM_FIELDS'); ?>
				<?php echo $this->fd->html('settings.text', 'unique_key_address', 'COM_PP_EASYSOCIAL_ADDRESS_CUSTOM_FIELD_UNIQUE_KEY_SETTING'); ?>		
				<?php echo $this->fd->html('settings.text', 'unique_key_company_name', 'COM_PP_EASYSOCIAL_COMPANY_NAME_CUSTOM_FIELD_UNIQUE_KEY_SETTING'); ?>
				<?php echo $this->fd->html('settings.text', 'unique_key_vat_id', 'COM_PP_EASYSOCIAL_TAX_ID_CUSTOM_FIELD_UNIQUE_KEY_SETTING'); ?>
				<?php echo $this->fd->html('settings.text', 'unique_key_shipping_address', 'COM_PP_EASYSOCIAL_SHIPPING_ADDRESS_CUSTOM_FIELD_UNIQUE_KEY_SETTING'); ?>			
				<?php echo $this->fd->html('settings.toggle', 'allow_edit_field', 'COM_PP_EASYSOCIAL_CUSTOM_FIELD_EDIT_DISALLOWED_SETTING'); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_CONFIG_USER_LOGIN_REDIRECTION'); ?>
			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'users_login_redirection', 'COM_PP_ALLOW_LOGIN_REDIRECTION'); ?>
				<?php echo $this->fd->html('settings.text', 'users_subscribers_redirect', 'COM_PP_SUBSCRIBERS_LOGIN_REDIRECTION_URL'); ?>
				<?php echo $this->fd->html('settings.text', 'users_nonsubscribers_redirect', 'COM_PP_NONSUBSCRIBERS_LOGIN_REDIRECTION_URL'); ?>
			</div>
		</div>
	</div>
</div>
