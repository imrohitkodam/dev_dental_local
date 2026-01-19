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
<form action="<?php echo JRoute::_('index.php');?>" method="post" data-preference-form enctype="multipart/form-data">
	<div class="t-lg-mb--xl">
		<div class="">
			<?php if ($this->config->get('discounts_referral')) { ?>
				<div class="o-form-section space-y-md">
					<div class="bg-gray-50 rounded-md px-md py-lg">
						<div class="text-lg text-gray-800 font-bold">
							<?php echo JText::_('COM_PP_YOUR_REFERRAL_CODE');?>
						</div>
						<div class="text-gray-500">
							<?php echo JText::_('COM_PP_REFERRAL_DESC_USER');?>
						</div>
					</div>
			
					<div class="flex px-md py-2xl flex-col md:flex-row gap-sm">
						<div class="md:w-[320px] flex-shrink-0">
							<?php echo $this->fd->html('form.label', JText::_('COM_PP_REFERRAL_CODE'), '', '', '', false, ['columns' => 5]); ?>
						</div>

						<div class="flex-grow space-y-xs">
							<div class="flex gap-xs">
								<div class="flex-grow">
									<div class="o-form-group space-y-xs">
										<div class="o-input-group">
											<input type="text" class="o-form-control" disabled="disabled" value="<?php echo PP::getKeyFromID($user->id);?>" data-pp-referral-code />
											<button type="button" class="o-btn o-btn--default-o" data-pp-referral-copy>
												<i class="fdi far fa-clipboard"></i>&nbsp; <?php echo JText::_('COM_PP_COPY');?>
											</button>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>

			<div class="o-form-section space-y-md">
				<div class="bg-gray-50 rounded-md px-md py-lg">
					<div class="text-lg text-gray-800 font-bold">
						<?php echo JText::_('COM_PP_ACCOUNT_PREFERENCES');?>
					</div>
				</div>
			
				<?php echo $this->html('floatLabel.dashboard.standard', 'COM_PP_NAME', 'name',  $user->getName()); ?>

				<div class="o-form-divider border-t border-solid border-gray-300 mx-md"></div>

				<?php echo $this->html('floatLabel.dashboard.standard', 'COM_PP_USERNAME', 'username',  $user->getUsername()); ?>
				<div class="o-form-divider border-t border-solid border-gray-300 mx-md"></div>

				<?php echo $this->html('floatLabel.dashboard.standard', 'COM_PP_EMAIL_ADDRESS', 'email',  $user->getEmail()); ?>
				<div class="o-form-divider border-t border-solid border-gray-300 mx-md"></div>
					
				<?php if ($this->config->get('show_billing_details')) { ?>
					<?php if (!$hideCompanyNameAndVat) { ?>
						<div class="" data-userpreference-business>
								
							<?php echo $this->html('floatLabel.dashboard.standard', 'COM_PP_CHECKOUT_BUSINESS_NAME', 'preference[business_name]', $business->name, '', '', [], !$canEditBusinessDetails); ?>
							<div class="o-form-divider border-t border-solid border-gray-300 mx-md"></div>
									
							<?php echo $this->html('floatLabel.dashboard.standard', 'COM_PP_CHECKOUT_TAX_ID', 'preference[tin]',  $business->vat, '', '', [], !$canEditBusinessDetails); ?>
							<div class="o-form-divider border-t border-solid border-gray-300 mx-md"></div>
						</div>
						<?php } ?>

					<?php echo $this->html('floatLabel.dashboard.standard', 'COM_PP_CHECKOUT_SHIPPING_ADDRESS', 'preference[shipping_address]',  $business->shipping, '', '', [], !$canEditBusinessDetails); ?>
					<div class="o-form-divider border-t border-solid border-gray-300 mx-md"></div>

					<div class="flex px-md py-2xl flex-col md:flex-row gap-sm">
						<div class="md:w-[320px] flex-shrink-0">
							<?php echo $this->fd->html('form.label', JText::_('COM_PP_CHECKOUT_BUSINESS_ADDRESS'), '', '', '', false, ['columns' => 6]); ?>
						</div>
						<div class="flex-grow space-y-xs">

							<div class="flex gap-xs">
								<div class="flex-grow space-y-md">
									<?php echo $this->html('floatLabel.dashboard.text', 'COM_PP_CHECKOUT_BUSINESS_ADDRESS', 'preference[business_address]',  $business->address, '', '', [], !$canEditBusinessDetails); ?>

									<div class="grid grid-cols-1 md:grid-cols-12 gap-md">
										<div class="md:col-span-6">
											<?php echo $this->fd->html('form.label', JText::_('COM_PP_CHECKOUT_CITY'), '', '', '', false, ['columns' => 6]); ?>

											<?php echo $this->html('floatLabel.dashboard.text', 'COM_PP_CHECKOUT_CITY', 'preference[business_city]',  $business->city, '', '', [], !$canEditBusinessDetails); ?>			
										</div>

										<div class="md:col-span-6">
											<?php echo $this->fd->html('form.label', JText::_('COM_PP_CHECKOUT_ZIP'), '', '', '', false, ['columns' => 6]); ?>

											<?php echo $this->html('floatLabel.dashboard.text', 'COM_PP_CHECKOUT_ZIP', 'preference[business_zip]',  $business->zip, '', '', [], !$canEditBusinessDetails); ?>
										</div>
									</div>

									<div class="grid grid-cols-1 md:grid-cols-12 gap-md">
										<div class="md:col-span-6">
											<?php echo $this->fd->html('form.label', JText::_('COM_PP_CHECKOUT_STATE'), '', '', '', false, ['columns' => 6]); ?>

											<?php echo $this->html('floatLabel.dashboard.text', 'COM_PP_CHECKOUT_STATE', 'preference[business_state]',  $business->state, '', '', [], !$canEditBusinessDetails); ?>
										</div>

										<div class="md:col-span-6">
											<?php echo $this->fd->html('form.label', JText::_('COM_PP_CHECKOUT_COUNTRY'), '', '', '', false, ['columns' => 6]); ?>

											<?php echo $this->html('floatlabel.dashboard.country',  'COM_PP_CHECKOUT_COUNTRY', 'preference[business_country]',  $business->country, '', [], !$canEditBusinessDetails); ?>	
										</div>
									</div>
								</div>
							</div>
					</div>
				<?php } ?>
			</div>

			<div class="o-form-section space-y-md">
				<div class="bg-gray-50 rounded-md px-md py-lg">
					<div class="text-lg text-gray-800 font-bold">
						<?php echo JText::_('COM_PP_ACCOUNT_CHANGE_PASSWORD');?>
					</div>
				</div>

				<div class="flex px-md py-2xl flex-col md:flex-row gap-sm" data-pp-form-group>
					<?php if (!$this->isMobile()) { ?>
						<div class="md:w-[320px] flex-shrink-0">
							<?php echo $this->fd->html('form.label', JText::_('COM_PP_PASSWORD_LABEL'), '', '', '', false, ['columns' => 6]); ?>
						</div>
					<?php } ?>

					<div class="flex-grow space-y-xs">
						<?php echo $this->html('floatLabel.dashboard.password', 'COM_PP_PASSWORD', 'password', ''); ?>
						<?php echo $this->html('floatLabel.dashboard.password', 'COM_PP_RECONFIRM_PASSWORD', 'password2', ''); ?>
					</div>
				</div>

			</div>
			
			<?php if ($this->config->get('user_edit_customdetails') && $customDetails) { ?>
				<?php foreach ($customDetails as $customDetail) { ?>
					<?php echo $customDetail->renderForm($user, 'dashboard'); ?>
				<?php } ?>
			<?php } ?>

			<div class="o-form-section space-y-md">
				<div class="o-form-divider border-t border-solid border-gray-300 mx-md"></div>
				<div class="flex flex-col md:flex-row px-md py-md gap-md">
					<div class="flex-grow">
						<?php echo $this->fd->html('button.link', PPR::_('index.php?option=com_payplans&view=dashboard'), 'COM_PP_CANCEL_BUTTON', 'default', 'md'); ?>
					</div>

					<div class="">
						<?php echo $this->fd->html('button.standard', 'COM_PP_UPDATE_PREFERENCES_BUTTON', 'primary', 'sm', ['attributes' => 'data-preference-submit']); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php echo $this->html('form.action', '', 'user.save'); ?>
</form>

<?php if ($this->config->get('user_delete_account')) { ?>
	<div class="o-form-section space-y-md">
		<div class="bg-gray-50 rounded-md px-md py-lg">
			<div class="text-lg text-gray-800 font-bold"><?php echo JText::_('COM_PP_ACCOUNT_DELETION');?></div>
			<div class="text-gray-500"><?php echo JText::_('COM_PP_ACCOUNT_DELETION_DESC');?></div>
		</div>

		<div class="flex px-md py-2xl flex-col md:flex-row gap-sm">
			<div class="">
				<?php echo JText::_('COM_PP_ACCOUNT_DELETION_INFORMATION');?>
			</div>
		</div>

	</div>
	<div class="o-form-section space-y-md">
			<div class="flex flex-col md:flex-row px-md py-md gap-md">
				<div class="flex-grow">
					<?php echo $this->fd->html('button.standard', 'COM_PP_DELETE_USER_ACCOUNT', 'danger', 'sm', ['outline' => true, 'attributes' => 'data-delete-user data-user-id='.$user->getId()]); ?>
				</div>		
			</div>
		</div>
	</div>
<?php } ?>