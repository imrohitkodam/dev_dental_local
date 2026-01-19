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
<?php echo $this->output('site/checkout/default/login', [
	'showTwoFactor' => $showTwoFactor, 
	'twoFactors' => $twoFactors
]); ?>
<div class="pp-checkout-item t-hidden" data-pp-register>
	<div class="pp-checkout-item__title">
		<div class="flex">
			<div class="flex-grow">
				<?php echo mb_strtoupper(JText::_('COM_PP_CHECKOUT_CREATE_NEW_ACCOUNT'));?>
			</div>
			<div class="flex-shrink-0">
				<div style="font-weight: normal;">
					<?php echo JText::_('COM_PP_CHECKOUT_ALREADY_HAVE_ACCOUNT');?> <a href="javascript:void(0);" class="no-underline" data-pp-login-link><?php echo JText::_('COM_PP_CHECKOUT_LOGIN');?></a>
				</div>
			</div>
		</div>
	</div>
	<div class="pp-checkout-item__content space-y-sm">
		<?php if ($this->config->get('show_fullname')) { ?>
			<?php echo $this->html('floatlabel.text', 'COM_PP_CHECKOUT_YOUR_NAME', 'register_name', $data['register_name'], '', [
				'autocomplete' => 'off', 'data-register-name' => '']); ?>
		<?php } ?>

		<?php if ($this->config->get('show_username')) { ?>
			<?php echo $this->html('floatlabel.text', 'COM_PP_CHECKOUT_USERNAME', 'register_username', $data['register_username'], '', [
				'autocomplete' => 'off', 'data-register-username' => '']); ?>
		<?php } ?>

		<div class="grid md:grid-cols-1 gap-sm">
			<div>
				<?php echo $this->html('floatlabel.text', 'COM_PP_CHECKOUT_EMAIL_ADDRESS', 'register_email', $data['register_email'], '',['autocomplete' => 'off', 'data-register-email' => '']); ?>
			</div>

			<div>
				<?php echo $this->html('floatlabel.password', 'COM_PP_CHECKOUT_REGISTER_PASSWORD', 'register_password', '', '', ['autocomplete' => 'off', 'data-register-password' => '']); ?>
			</div>

			<?php if ($this->config->get('show_confirmpassword')) { ?>
			<div>
				<?php echo $this->html('floatlabel.password', 'COM_PP_CHECKOUT_REGISTER_RECONFIRM_PASSWORD', 'register_password2', '', '', ['autocomplete' => 'off', 'data-register-password2' => '']); ?>
			</div>
			<?php } ?>
		</div>

		<?php if ($this->config->get('show_address')) { ?>
			<?php echo $this->html('floatLabel.text', 'COM_PP_CHECKOUT_ADDRESS', 'address', $data['address']); ?>

			<div class="grid md:grid-cols-3 gap-sm">
				<div>
					<?php echo $this->html('floatLabel.text', 'COM_PP_CHECKOUT_CITY', 'city', $data['city']); ?>
				</div>

				<div>
					<?php echo $this->html('floatLabel.text', 'COM_PP_CHECKOUT_STATE', 'state', $data['state']); ?>
				</div>

				<div>
					<?php echo $this->html('floatLabel.text', 'COM_PP_CHECKOUT_ZIP', 'zip', $data['zip']); ?>
				</div>
			</div>
		<?php } ?>

		<?php if ($this->config->get('show_country')) { ?>
			<?php echo $this->html('floatlabel.country',  'COM_PP_CHECKOUT_COUNTRY', 'country', $data['country']); ?>
		<?php } ?>

		<?php if ($this->config->get('show_language')) { ?>
			<?php echo $this->html('floatlabel.language',  'COM_PP_CHECKOUT_LANGUAUGE', 'language', $data['language']); ?>
		<?php } ?>

		<?php if ($this->config->get('show_captcha')) { ?>
			<?php echo PP::captcha()->html();?>
		<?php } ?>
	</div>
</div>