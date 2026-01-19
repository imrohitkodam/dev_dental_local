<?php
/**
* @package      StackIdeas
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* StackIdeas Toolbar is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="fd-toolbar__o-nav-item md:flex" >
	<a href="javascript:void(0);" class="fd-toolbar__link" 
		data-fd-dropdown="toolbar"
		data-fd-dropdown-placement="<?php echo FDT::renderDropdownPlacement(); ?>" 
		data-fd-dropdown-offset="[0, 0]"
		data-fd-dropdown-trigger="click"
		data-fd-dropdown-max-width

		data-fd-tooltip="toolbar"
		data-fd-tooltip-title="<?php echo JText::_('MOD_SI_TOOLBAR_LOGIN_BUTTON_TOOLTIP'); ?>"
		data-fd-tooltip-placement="top"
		>
		<i aria-hidden="true" class="fdi fa fa-user-lock"></i>
		<span class="sr-only"><?php echo JText::_('MOD_SI_TOOLBAR_LOGIN_BUTTON_TOOLTIP'); ?></span>
	</a>

	<div class="hidden" data-fd-toolbar-dropdown="">
		<div id="fd">
			<div class="<?php echo FDT::getAppearance();?> <?php echo FDT::getAccent();?>">
				<div class="o-dropdown divide-y divide-gray-300 md:w-[320px]">
					<div class="o-dropdown__hd px-md py-md">
						<div class="font-bold text-sm text-gray-800">
							<?php echo JText::_('MOD_SI_TOOLBAR_SIGN_IN_HEADING');?>
						</div>

						<?php if ($isRegistrationEnabled) { ?>
						<div class="text-xs text-gray-500">
							<?php echo JText::sprintf('MOD_SI_TOOLBAR_NEW_USERS_REGISTRATION', $registrationLink, 'fd-link');?>
						</div>
						<?php } ?>
					</div>
					<div class="o-dropdown__bd px-md py-sm" data-fd-toolbar-dropdown-menus>
						<form action="<?php echo JRoute::_('index.php');?>" class="space-y-sm" method="post">
							<?php echo $this->fd->html('form.floatingLabel', $usernameField, 'username', 'text', '', '', [
								'autocomplete' => 'username'
							]); ?>

							<?php echo $this->fd->html('form.floatingLabel', 'MOD_SI_TOOLBAR_PASSWORD', 'password', 'password', '', '', [
								'autocomplete' => 'current-password'
							]); ?>

							<?php if ($hasTwoFactor) { ?>
								<?php echo $this->fd->html('form.floatingLabel', 'MOD_SI_TOOLBAR_SECRET_KEY', 'secretkey'); ?>
							<?php } ?>

							<div class="flex flex-col space-y-sm">
								<label class="o-form-check">
									<input class="fd-custom-check" type="checkbox" name="remember" id="fd-remember">
									<span class="o-form-check__text"><?php echo JText::_('MOD_SI_TOOLBAR_REMEMBER_ME');?></span>
								</label>
								

								<div class="">
									<?php echo $this->fd->html('button.submit', JText::_('MOD_SI_TOOLBAR_SIGN_IN'), 'primary', 'default', ['block' => true]); ?>
								</div>
							</div>

							<?php if ($jfbconnect) { ?>
								<div class="o-row">
									<?php echo $jfbconnect;?>
								</div>
							<?php } ?>

							<?php echo FDT::themes()->html('sso'); ?>

							<?php echo $this->fd->html('form.hidden', 'option', 'com_users'); ?>
							<?php echo $this->fd->html('form.hidden', 'task', 'user.login'); ?>
							<?php echo $this->fd->html('form.hidden', 'return', $returnUrl); ?>
							<?php echo $this->fd->html('form.token'); ?>
						</form>
					</div>
					<div class="o-dropdown__ft py-sm px-xs">
						<div class="flex justify-center divide-x divide-gray-300">
							<a href="<?php echo $remindUsernameLink?>" class="fd-link px-sm text-xs"><?php echo JText::_('MOD_SI_TOOLBAR_FORGOT_USERNAME');?></a> 
							<a href="<?php echo $resetPasswordLink?>" class="fd-link px-sm text-xs"><?php echo JText::_('MOD_SI_TOOLBAR_FORGOT_PASSWORD');?></a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
