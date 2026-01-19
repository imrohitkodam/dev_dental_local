<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
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
			<?php echo $this->html('panel.heading', 'COM_PP_USER_FORM_DETAILS'); ?>

			<div class="panel-body">
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PAYPLANS_USER_EDIT_USER_ID', ''); ?>

					<div class="flex-grow">
						<?php echo $user->getId();?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PAYPLANS_USER_EDIT_USERNAME', ''); ?>

					<div class="flex-grow">
						<a href="<?php echo JRoute::_('index.php?option=com_users&task=user.edit&id=' . $user->getId());?>"><?php echo $user->getUsername();?></a>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PAYPLANS_USER_EDIT_USER_NAME', ''); ?>

					<div class="flex-grow">
						<?php echo $user->getName();?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PAYPLANS_USER_EDIT_USER_EMAIL', ''); ?>

					<div class="flex-grow">
						<?php echo $user->getEmail();?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PAYPLANS_USER_EDIT_USER_REGISTERDATE', ''); ?>

					<div class="flex-grow">
						<?php echo $user->getRegisterDate();?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PAYPLANS_USER_EDIT_USER_LASTVISITDATE', ''); ?>

					<div class="flex-grow">
						<?php echo $user->getLastvisitDate();?>
					</div>
				</div>

				<?php if ($this->config->get('registrationType') == 'auto' && $this->config->get('show_address')) { ?>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_PP_USER_EDIT_FORM_ADSRESS', ''); ?>

						<div class="flex-grow">
							<?php echo $this->html('form.text', 'address', $user->getAddress());?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_PP_USER_EDIT_FORM_CITY', ''); ?>

						<div class="flex-grow">
							<?php echo $this->html('form.text', 'city', $user->getCity()); ?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_PP_USER_EDIT_FORM_STATE', ''); ?>

						<div class="flex-grow">
							<?php echo $this->html('form.text', 'state', $user->getState()); ?>
						</div>
					</div>

					<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
						<?php echo $this->fd->html('form.label', 'COM_PP_USER_EDIT_FORM_ZIPCODE', ''); ?>

						<div class="flex-grow">
							<?php echo $this->html('form.text', 'zipcode', $user->getZipcode()); ?>
						</div>
					</div>
				<?php } ?>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_USER_FORM_COUNTRY_RESIDENCE', ''); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.country', 'country', $user->getCountry());?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_USER_FORM_NOTES', ''); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.textarea', 'params[user_notes]', $params->get('user_notes', ''));?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_PP_USER_FORM_BUSINESS'); ?>

			<div class="panel-body">
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_USER_FORM_BUSINESS_NAME', ''); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.text', 'preference[business_name]', $preferences->get('business_name', ''));?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_USER_FORM_TIN', ''); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.text', 'preference[tin]', $preferences->get('tin', ''));?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_USER_FORM_ADDRESS', ''); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.textarea', 'preference[business_address]', $preferences->get('business_address', ''));?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_USER_FORM_CITY', ''); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.textarea', 'preference[business_city]', $preferences->get('business_city', ''));?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_USER_FORM_STATE', ''); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.textarea', 'preference[business_state]', $preferences->get('business_state', ''));?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_USER_FORM_ZIP', ''); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.textarea', 'preference[business_zip]', $preferences->get('business_zip', ''));?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_USER_FORM_SHIPPING_ADDRESS', ''); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.textarea', 'preference[shipping_address]', $preferences->get('shipping_address', ''));?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>