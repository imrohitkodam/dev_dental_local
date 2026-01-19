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
			<?php echo $this->fd->html('panel.heading', 'COM_PP_CONFIG_GENERAL_REGISTRATION'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'registration_force_redirection', 'COM_PP_CONFIG_GENERAL_REGISTRATION_FORCE_REDIRECTION'); ?>
				
				<?php echo $this->fd->html('settings.dropdown', 'default_form_order', 'COM_PP_CONFIG_GENERAL_DEFAULT_FORM_ORDER', [
					'login' => 'COM_PP_FORM_ORDER_LOGIN',
					'register' => 'COM_PP_FORM_ORDER_REGISTER'
				]); ?>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md rounded-md">
					<?php echo $this->fd->html('form.label', 'COM_PAYPLANS_REGISTRATION_TYPE_LABEL', 'registrationType'); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.registrationType', 'registrationType', $this->config->get('registrationType', 'auto')); ?>
					</div>
				</div>

				<?php echo $this->fd->html('settings.toggle', 'registration_es_social', 'COM_PP_ES_ALLOW_SOCIAL_LOGIN', '', '', '', 'data-es-social', ['wrapperClass' => $this->config->get('registrationType') == 'easysocial' ? '' : 't-hidden']); ?>
				<?php echo $this->fd->html('settings.toggle', 'registration_skip_ptype', 'COM_PP_JOMSOCIAL_SKIP_PROFILETYPE_SELECTION', '', '', '', 'data-jom-social', ['wrapperClass' => $this->config->get('registrationType') == 'jomsocial' ? '' : 't-hidden']); ?>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md rounded-md <?php echo $this->config->get('registrationType') == 'jomsocial' ? '' : 't-hidden';?>" data-jom-social>
					<?php echo $this->fd->html('form.label', 'COM_PP_JOMSOCIAL_DEFAULT_PROFILETYPE', 'js_default_profiletype'); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.jomsocialMultiprofile', 'js_default_profiletype', $this->config->get('js_default_profiletype')); ?>
					</div>
				</div>

				<?php echo $this->fd->html('settings.dropdown', 'account_verification', 'COM_PP_CONFIG_GENERAL_REGISTRATION_VERIFICATION', [
						'user' => 'COM_PP_REQUIRE_VERIFICATION',
						'admin' => 'COM_PP_REQUIRE_MODERATION',
						'auto' => 'COM_PP_AUTO_ACTIVATION',
						'active_subscription' => 'COM_PP_REQUIRE_ACTIVE_SUBSCRIPTION'
					], '', '', function() {

						$registrationAccountVerification = 'self';

						if ($this->config->get('account_verification') === 'admin') {
							$registrationAccountVerification = 'administrator';
						}
						
						return $this->fd->html('alert.standard', JText::sprintf('COM_PP_CONFIG_GENERAL_REGISTRATION_VERIFICATION_NOTICE', $registrationAccountVerification), 'warning', [
							'dismissible' => false,
							'customClass' => $this->config->get('account_verification') == 'user' || $this->config->get('account_verification') == 'admin' ? 'mt-md' : 'mt-md t-hidden',
							'attributes' => 'data-pp-accountverification'
						]);
					}, [
					'wrapperAttributes' => 'data-pp-auto',
					'wrapperClass' => $this->config->get('registrationType') !== 'auto' ? 't-hidden' : ''
				]); ?>

				<?php echo $this->fd->html('settings.toggle', 'autologin', 'COM_PP_CONFIG_AUTO_LOGIN', '', '', '', 'data-pp-autologin', [
					'wrapperClass' => $this->config->get('account_verification') === 'auto' && ($this->config->get('registrationType') == 'auto' || $this->config->get('registrationType') == 'active_subscription' ) ? '' : 't-hidden'
				]); ?>

				<?php echo $this->fd->html('settings.toggle', 'joomla_password_validation', 'PLG_PAYPLANSREGISTRATION_AUTO_JOOMLA_PASSWORD_VALIDATION', '', '', '', 'data-pp-auto', [
					'wrapperClass' => $this->config->get('registrationType') !== 'auto' ? 't-hidden' : ''
				]); ?>

				<?php echo $this->fd->html('settings.toggle', 'send_password', 'PLG_PAYPLANSREGISTRATION_AUTO_SEND_PASSWORD', '', '', '', 'data-pp-auto', [
					'wrapperClass' => $this->config->get('registrationType') !== 'auto' ? 't-hidden' : ''
				]); ?>

				<?php echo $this->fd->html('settings.toggle', 'show_fullname', 'COM_PP_SHOW_FULL_NAME', '', '', '', 'data-pp-auto', [
					'wrapperClass' => $this->config->get('registrationType') !== 'auto' ? 't-hidden' : ''
				]); ?>

				<?php echo $this->fd->html('settings.toggle', 'show_username', 'COM_PP_SHOW_USERNAME', '', '', '', 'data-pp-auto', [
					'wrapperClass' => $this->config->get('registrationType') !== 'auto' ? 't-hidden' : ''
				]); ?>

				<?php echo $this->fd->html('settings.toggle', 'show_confirmpassword', 'COM_PP_SHOW_CONFIRM_PASSWORD', '', '', '', 'data-pp-auto', [
					'wrapperClass' => $this->config->get('registrationType') !== 'auto' ? 't-hidden' : ''
				]); ?>

				<?php echo $this->fd->html('settings.toggle', 'show_address', 'COM_PP_CONFIG_CHECKOUT_ASK_ADDRESS', '', '', '', 'data-pp-auto', [
					'wrapperClass' => $this->config->get('registrationType') !== 'auto' ? 't-hidden' : ''
				]); ?>

				<?php echo $this->fd->html('settings.toggle', 'show_country', 'COM_PP_CONFIG_CHECKOUT_ASK_COUNTRY', '', '', '', 'data-pp-auto', [
					'wrapperClass' => $this->config->get('registrationType') !== 'auto' ? 't-hidden' : ''
				]); ?>

				<?php echo $this->fd->html('settings.toggle', 'show_language', 'COM_PP_CONFIG_CHECKOUT_ASK_LANGUAGE', '', '', function(){ 
					return $this->fd->html('alert.extended', 'COM_PP_NOTE', 'COM_PP_CONFIG_CHECKOUT_ASK_LANGUAGE_NOTE', 'warning', [
						'dismissible' => false,
						'class' => 'mt-md'
					]);
				}, 'data-pp-auto', [
					'wrapperClass' => $this->config->get('registrationType') !== 'auto' ? 't-hidden' : ''
				]); ?>


				<?php echo $this->fd->html('settings.toggle', 'notify_admin', 'PLG_PAYPLANSREGISTRATION_AUTO_NOTIFY_ADMIN', '', '', '', 'data-pp-auto', [
					'wrapperClass' => $this->config->get('registrationType') !== 'auto' ? 't-hidden' : ''
				]); ?>

				<?php echo $this->fd->html('settings.text', 'activation_redirect_url', 'PLG_PAYPLANSREGISTRATION_AUTO_REDIRECT_URL', '', [
					'visible' => $this->config->get('registrationType') === 'auto',
					'wrapperAttributes' => 'data-pp-auto'
				], function() {
						return $this->fd->html('alert.standard', JText::_('PLG_PAYPLANSREGISTRATION_AUTO_REDIRECT_URL_NOTICE'), 'warning', [
							'dismissible' => false,
							'customClass' => 'mt-md'
						]);
					}); ?>
			</div>
		</div>
	</div>

	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_CONFIG_CAPTCHA_REGISTRATION'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'show_captcha', 'COM_PP_DISPLAY_RECAPTCHA'); ?>
				<?php echo $this->fd->html('settings.toggle', 'recaptcha_invisible', 'COM_PP_USE_INVISIBLE_RECAPTCHA'); ?>
				<?php echo $this->fd->html('settings.text', 'recaptcha_sitekey', 'COM_PP_RECAPTCHA_SITE_KEY'); ?>
				<?php echo $this->fd->html('settings.text', 'recaptcha_secretkey', 'COM_PP_RECAPTCHA_SECRET_KEY'); ?>

				<?php echo $this->fd->html('settings.dropdown', 'recaptcha_theme', 'COM_PP_RECAPTCHA_THEME', [
					'light' => 'Light',
					'dark' => 'Dark'
				]); ?>
				
				<?php echo $this->fd->html('settings.dropdown', 'default_recaptcha_language', 'COM_PP_RECAPTCHA_LANGUAGE', [
					'auto' => 'COM_PP_RECAPTCHA_LANGUAGE_AUTO',
					'none' => 'COM_PP_RECAPTCHA_LANGUAGE_SELECT'
				]); ?>

				<?php echo $this->fd->html('settings.dropdown', 'recaptcha_language', 'COM_PP_RECAPTCHA_SELECT_LANGUAGE', PP::captcha()->getRecaptchaLanguages(), '', '', '', [
					'wrapperClass' => $this->config->get('default_recaptcha_language') === 'none' ? '' : 't-hidden',
					'wrapperAttributes' => 'data-recaptcha_language'
				]); ?>
			</div>
		</div>
	</div>
</div>
